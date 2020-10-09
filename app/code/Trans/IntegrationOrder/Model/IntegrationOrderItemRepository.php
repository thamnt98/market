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
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Message\ManagerInterface;
use Trans\IntegrationOrder\Api\Data\IntegrationOrderItemInterface;
use Trans\IntegrationOrder\Api\IntegrationOrderItemRepositoryInterface;
use Trans\IntegrationOrder\Model\ResourceModel\IntegrationOrderItem as IntegrationOrderItemResourceModel;
use Trans\IntegrationOrder\Model\ResourceModel\IntegrationOrderItem\CollectionFactory;

/**
 * Class IntegrationOrderItemRepository
 */
class IntegrationOrderItemRepository implements IntegrationOrderItemRepositoryInterface {
	/**
	 * @var array
	 */
	private $instances = [];

	/**
	 * @var IntegrationOrderItemResourceModel
	 */
	private $integrationOrderItemResourceModel;

	/**
	 * @var IntegrationOrderItemInterface
	 */
	private $integrationOrderItemInterface;

	/**
	 * @var IntegrationOrderItemFactory
	 */
	private $integrationOrderItemFactory;

	private $collectionFactory;

	/**
	 * @var ManagerInterface
	 */
	private $messageManager;

	/**
	 * IntegrationOrderRepository constructor.
	 * @param IntegrationOrderItemResourceModel $integrationOrderItemResourceModel
	 * @param IntegrationOrderItemInterface $integrationOrderItemInterface
	 * @param IntegrationOrderItemFactory $integrationOrderItemFactory
	 * @param CollectionFactory $collectionFactory
	 * @param ManagerInterface $messageManager
	 */
	public function __construct(
		IntegrationOrderItemResourceModel $integrationOrderItemResourceModel,
		IntegrationOrderItemInterface $integrationOrderItemInterface,
		IntegrationOrderItemFactory $integrationOrderItemFactory,
		CollectionFactory $collectionFactory,
		ManagerInterface $messageManager
	) {
		$this->integrationOrderItemResourceModel = $integrationOrderItemResourceModel;
		$this->integrationOrderItemInterface     = $integrationOrderItemInterface;
		$this->integrationOrderItemFactory       = $integrationOrderItemFactory;
		$this->collectionFactory                 = $collectionFactory;
		$this->messageManager                    = $messageManager;
	}

	/**
	 * @param IntegrationOrderItemInterface $integrationOrderItemInterface
	 * @return IntegrationOrderItemInterface
	 * @throws \Exception
	 */
	public function save(IntegrationOrderItemInterface $integrationOrderItemInterface) {
		try {
			$this->integrationOrderItemResourceModel->save($integrationOrderItemInterface);
		} catch (Exception $e) {
			$this->messageManager
				->addExceptionMessage(
					$e,
					'There was a error while saving the order item ' . $e->getMessage()
				);
		}

		return $integrationOrderItemInterface;
	}

	/**
	 * @param $omsOrderItemId
	 * @return array
	 */
	public function getById($omsOrderItemId) {
		if (!isset($this->instances[$omsOrderItemId])) {
			$order = $this->integrationOrderItemFactory->create();
			$this->integrationOrderItemResourceModel->load($order, $omsOrderItemId);
			$this->instances[$omsOrderItemId] = $order;
		}
		return $this->instances[$omsOrderItemId];
	}

	/**
	 * Retrieve data by order id
	 *
	 * @param string $orderId
	 * @return IntegrationOrderItemInterface
	 */
	public function getByOrderId($orderId) {
		$collection = $this->collectionFactory->create();
		$collection->addFieldToFIlter(IntegrationOrderItemInterface::ORDER_ID, $orderId);

		if (!$collection->getSize()) {
			throw new NoSuchEntityException(__('Requested Order Items doesn\'t exist'));
		}

		return $collection;
	}

	/**
	 * @param IntegrationOrderItemInterface $integrationOrderItemInterface
	 * @return bool
	 * @throws \Exception
	 */
	public function delete(IntegrationOrderItemInterface $integrationOrderItemInterface) {
		$id = $integrationOrderItemInterface->getId();
		try {
			unset($this->instances[$id]);
			$this->integrationOrderItemResourceModel->delete($integrationOrderItemInterface);
		} catch (Exception $e) {
			$this->messageManager
				->addExceptionMessage($e, 'There was a error while deleting the order item');
		}
		unset($this->instances[$id]);
		return true;
	}

	/**
	 * @param $omsOrderItemId
	 * @return bool
	 * @throws \Exception
	 */
	public function deleteById($omsOrderItemId) {
		$order = $this->getById($omsOrderItemId);
		return $this->delete($order);
	}
}