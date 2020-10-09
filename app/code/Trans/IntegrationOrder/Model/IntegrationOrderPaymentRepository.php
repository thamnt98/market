<?php
/**
 * @category Trans
 * @package  Trans_IntegrationOrder
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Randy <muhammad.randy@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationOrder\Model;

use Magento\Framework\Exception\LocalizedException as Exception;
use Magento\Framework\Message\ManagerInterface;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as ModelOrderMagento;
use Trans\IntegrationOrder\Api\Data\IntegrationOrderPaymentInterface;
use Trans\IntegrationOrder\Api\IntegrationOrderPaymentRepositoryInterface;
use Trans\IntegrationOrder\Model\IntegrationOrderPaymentFactory;
use Trans\IntegrationOrder\Model\ResourceModel\IntegrationOrderPayment as IntegrationOrderPaymentResourceModel;
use Trans\IntegrationOrder\Model\ResourceModel\IntegrationOrderPayment\CollectionFactory;

/**
 * Class IntegrationOrderPaymentRepository
 */
class IntegrationOrderPaymentRepository implements IntegrationOrderPaymentRepositoryInterface {
	/**
	 * @var array
	 */
	private $instances = [];

	/**
	 * @var IntegrationOrderPaymentResourceModel
	 */
	private $integrationOrderPaymentResourceModel;

	/**
	 * @var IntegrationOrderPaymentInterface
	 */
	private $integrationOrderPaymentInterface;

	/**
	 * @var IntegrationOrderPaymentFactory
	 */
	private $integrationOrderPaymentFactory;

	private $collectionFactory;

	/**
	 * @var ManagerInterface
	 */
	private $messageManager;

	/**
	 * IntegrationOrderPaymentRepository constructor.
	 * @param IntegrationOrderPaymentResourceModel $integrationOrderPaymentResourceModel
	 * @param IntegrationOrderPaymentInterface $integrationOrderPaymentInterface
	 * @param IntegrationOrderPaymentFactory $integrationOrderPaymentFactory
	 * @param CollectionFactory $collectionFactory
	 * @param ManagerInterface $messageManager
	 */
	public function __construct(
		ModelOrderMagento $orderCollectionFactory,
		IntegrationOrderPaymentResourceModel $integrationOrderPaymentResourceModel,
		IntegrationOrderPaymentInterface $integrationOrderPaymentInterface,
		IntegrationOrderPaymentFactory $integrationOrderPaymentFactory,
		CollectionFactory $collectionFactory,
		ManagerInterface $messageManager
	) {
		$this->orderCollectionFactory               = $orderCollectionFactory;
		$this->integrationOrderPaymentResourceModel = $integrationOrderPaymentResourceModel;
		$this->integrationOrderPaymentInterface     = $integrationOrderPaymentInterface;
		$this->integrationOrderPaymentFactory       = $integrationOrderPaymentFactory;
		$this->collectionFactory                    = $collectionFactory;
		$this->messageManager                       = $messageManager;
	}

	/**
	 * @param IntegrationOrderPaymentInterface $integrationOrderPaymentInterface
	 * @return IntegrationOrderPaymentInterface
	 * @throws \Exception
	 */
	public function save(IntegrationOrderPaymentInterface $integrationOrderPaymentInterface) {
		try {
			$this->integrationOrderPaymentResourceModel->save($integrationOrderPaymentInterface);
		} catch (Exception $e) {
			$this->messageManager
				->addExceptionMessage(
					$e,
					'There was a error while saving the order ' . $e->getMessage()
				);
		}

		return $integrationOrderPaymentInterface;
	}

	/**
	 * @param $omsOrderPaymentId
	 * @return array
	 */
	public function getById($omsOrderPaymentId) {
		if (!isset($this->instances[$omsOrderPaymentId])) {
			$order = $this->integrationOrderPaymentFactory->create();
			$this->integrationOrderPaymentResourceModel->load($order, $omsOrderPaymentId);
			$this->instances[$omsOrderPaymentId] = $order;
		}
		return $this->instances[$omsOrderPaymentId];
	}

	/**
	 * {@inheritdoc}
	 */
	public function loadDataByReferenceNumber($refNumber) {
		$collection = $this->collectionFactory->create();
		$collection->addFieldToFilter(IntegrationOrderPaymentInterface::REFERENCE_NUMBER, $refNumber);

		return $collection->getFirstItem();
	}

	/**
	 * @param IntegrationOrderPaymentInterface $integrationOrderPaymentInterface
	 * @return bool
	 * @throws \Exception
	 */
	public function delete(IntegrationOrderPaymentInterface $integrationOrderPaymentInterface) {
		$id = $integrationOrderPaymentInterface->getId();
		try {
			unset($this->instances[$id]);
			$this->IntegrationOrderPaymentResourceModel->delete($integrationOrderPaymentInterface);
		} catch (Exception $e) {
			$this->messageManager
				->addExceptionMessage($e, 'There was a error while deleting the order');
		}
		unset($this->instances[$id]);
		return true;
	}

	/**
	 * @param $omsOrderPaymentId
	 * @return bool
	 * @throws \Exception
	 */
	public function deleteById($omsOrderPaymentId) {
		$order = $this->getById($omsOrderPaymentId);
		return $this->delete($order);
	}
}
