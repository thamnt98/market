<?php
/**
 * @category Trans
 * @package  Trans_IntegrationOrder
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Hadi <ashadi.sejati@ctcorpdigital.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationOrder\Model;

use Magento\Eav\Model\ResourceModel\Entity\Attribute\CollectionFactory as AttributeCollection;
use Magento\Framework\Exception\LocalizedException as Exception;
use Magento\Framework\Message\ManagerInterface;
use Magento\Rma\Model\ResourceModel\Item\CollectionFactory as RmaItemModel;
use Magento\Rma\Model\ResourceModel\Rma\CollectionFactory as RmaModel;
use Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory as ItemResourceModel;
use Trans\IntegrationOrder\Api\Data\IntegrationOrderReturnInterface;
use Trans\IntegrationOrder\Api\IntegrationOrderReturnRepositoryInterface;
use Trans\IntegrationOrder\Model\IntegrationOrderReturnFactory;
use Trans\IntegrationOrder\Model\ResourceModel\IntegrationOrderReturn as ReturnResourceModel;
use Trans\IntegrationOrder\Model\ResourceModel\IntegrationOrderReturn\CollectionFactory;

/**
 * Class IntegrationOrderReturnRepository
 */
class IntegrationOrderReturnRepository implements IntegrationOrderReturnRepositoryInterface {
	/**
	 * @var array
	 */
	private $instances = [];

	/**
	 * @var ReturnResourceModel
	 */
	private $returnResourceModel;

	/**
	 * @var IntegrationOrderReturnInterface
	 */
	private $integrationOrderReturnInterface;

	/**
	 * @var IntegrationOrderReturnFactory
	 */
	private $integrationOrderReturnFactory;

	/**
	 * @var CollectionFactory
	 */
	private $returnCollection;

	/**
	 * @var ManagerInterface
	 */
	private $messageManager;

	/**
	 * IntegrationOrderRetunRepository constructor.
	 * @param ReturnResourceModel $returnResourceModel
	 * @param IntegrationOrderReturnInterface $integrationOrderReturnInterface
	 * @param IntegrationOrderReturnFactory $integrationOrderReturnFactory
	 * @param CollectionFactory $returnCollection
	 * @param ManagerInterface $messageManager
	 */
	public function __construct(
		ReturnResourceModel $returnResourceModel,
		ItemResourceModel $itemResourceModel,
		RmaModel $rmaModel,
		RmaItemModel $rmaItemModel,
		AttributeCollection $eavAttribute,
		IntegrationOrderReturnInterface $integrationOrderReturnInterface,
		IntegrationOrderReturnFactory $integrationOrderReturnFactory,
		CollectionFactory $returnCollection,
		ManagerInterface $messageManager
	) {
		$this->returnResourceModel             = $returnResourceModel;
		$this->eavAttribute                    = $eavAttribute;
		$this->rmaModel                        = $rmaModel;
		$this->rmaItemModel                    = $rmaItemModel;
		$this->itemResourceModel               = $itemResourceModel;
		$this->integrationOrderReturnInterface = $integrationOrderReturnInterface;
		$this->integrationOrderReturnFactory   = $integrationOrderReturnFactory;
		$this->returnCollection                = $returnCollection;
		$this->messageManager                  = $messageManager;
	}

	/**
	 * @param IntegrationOrderReturnInterface $integrationOrderReturnInterface
	 * @return IntegrationOrderReturnInterface
	 * @throws \Exception
	 */
	public function save(IntegrationOrderReturnInterface $integrationOrderReturnInterface) {
		try {
			$this->returnResourceModel->save($integrationOrderReturnInterface);
		} catch (Exception $e) {
			$this->messageManager
				->addExceptionMessage(
					$e,
					'There was a error while saving ' . $e->getMessage()
				);
		}

		return $integrationOrderReturnInterface;
	}

	/**
	 * load data by sku and order id
	 * @param string $sku
	 * @param string $orderId
	 * @return array $resul
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function loadDataReturnBySku($sku, $orderId) {
		if (empty($sku)) {
			throw new StateException(__(
				'Parameter sku are empty !'
			));
		}

		if (empty($orderId)) {
			throw new StateException(__(
				'Parameter order id are empty !'
			));
		}

		$result     = NUll;
		$collection = $this->integrationOrderReturnFactory->create()->getCollection();
		$collection->addFieldToFilter(IntegrationOrderReturnInterface::SKU, $sku);
		$collection->addFieldToFilter(IntegrationOrderReturnInterface::ORDER_ID, $orderId);

		if ($collection->getSize()) {

			try {
				$result = $collection;
			} catch (\Exception $exception) {
				throw new StateException(__(
					"Error " . __FUNCTION__ . " : " . $exception->getMessage()
				));
			}
		}

		return $result;
	}

	/**
	 * load data by order id
	 * @param string $orderId
	 * @return string
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function loadDataReturnByOrderid($orderId) {
		$collection = $this->returnCollection->create();
		$collection->addFieldToFilter(IntegrationOrderReturnInterface::ORDER_ID, $orderId);

		return $collection->getFirstItem();
	}

	/**
	 * {@inheritdoc}
	 */
	public function loadItemByOrderId($orderId) {
		$collection = $this->itemResourceModel->create();
		$collection->addFieldToFilter('order_id', $orderId);

		return $collection->getFirstItem();
	}

	/**
	 * {@inheritdoc}
	 */
	public function loadAttributeByCode($attributeCode) {
		$collection = $this->eavAttribute->create();
		$collection->addFieldToFilter('attribute_code', $attributeCode);

		return $collection->getFirstItem();
	}

	/**
	 * {@inheritdoc}
	 */
	public function loadRmaItemByEntityId($rmaEntityId) {
		$collection = $this->rmaItemModel->create();
		$collection->addFieldToFilter('rma_entity_id', $rmaEntityId);

		return $collection->getFirstItem();
	}

	/**
	 * {@inheritdoc}
	 */
	public function loadRmaByOrderId($orderId) {
		$collection = $this->rmaModel->create();
		$collection->addFieldToFilter('order_increment_id', $orderId);

		return $collection->getFirstItem();
	}
}
