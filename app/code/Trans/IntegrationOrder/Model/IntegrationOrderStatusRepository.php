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
use Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory as CollectionItemMagento;
use Magento\Sales\Model\ResourceModel\Order\Shipment\Track\CollectionFactory as ModelShipmentTrack;
use Magento\Sales\Model\ResourceModel\Order\Status\History\CollectionFactory as ModelHistoryStatus;
use Trans\IntegrationOrder\Api\Data\IntegrationOrderHistoryInterface;
use Trans\IntegrationOrder\Api\Data\IntegrationOrderItemInterface;
use Trans\IntegrationOrder\Api\Data\IntegrationOrderStatusInterface;
use Trans\IntegrationOrder\Api\IntegrationOrderStatusRepositoryInterface;
use Trans\IntegrationOrder\Model\IntegrationOrderStatusFactory;
use Trans\IntegrationOrder\Model\ResourceModel\IntegrationOrderHistory as HistoryResourceModel;
use Trans\IntegrationOrder\Model\ResourceModel\IntegrationOrderHistory\CollectionFactory as HistoryCollection;
use Trans\IntegrationOrder\Model\ResourceModel\IntegrationOrderItem as ItemResourceModel;
use Trans\IntegrationOrder\Model\ResourceModel\IntegrationOrderItem\CollectionFactory as ItemCollection;
use Trans\IntegrationOrder\Model\ResourceModel\IntegrationOrderStatus as StatusResourceModel;
use Trans\IntegrationOrder\Model\ResourceModel\IntegrationOrderStatus\CollectionFactory;

/**
 * Class IntegrationOrderStatusRepository
 */
class IntegrationOrderStatusRepository implements IntegrationOrderStatusRepositoryInterface {
	/**
	 * @var array
	 */
	private $instances = [];

	/**
	 * @var StatusResourceModel
	 */
	private $statusResourceModel;

	/**
	 * @var HistoryResourceModel
	 */
	private $historyResourceModel;

	/**
	 * @var ModelHistoryStatus
	 */
	private $modelHistoryStatus;

	/**
	 * @var ItemResourceModel
	 */
	private $itemResourceModel;

	/**
	 * @var IntegrationOrderStatusInterface
	 */
	private $integrationOrderStatusInterface;

	/**
	 * @var IntegrationOrderHistoryInterface
	 */
	private $integrationOrderHistoryInterface;

	/**
	 * @var IntegrationOrderItemInterface
	 */
	private $integrationOrderItemInterface;

	/**
	 * @var IntegrationOrderStatusFactory
	 */
	private $integrationOrderStatusFactory;

	private $collectionFactory;

	/**
	 * @var HistoryCollection
	 */
	private $historyCollection;

	/**
	 * @var ItemCollection
	 */
	private $itemCollection;

	/**
	 * @var ManagerInterface
	 */
	private $messageManager;

	/**
	 * IntegrationOrderStatusRepository constructor.
	 * @param ModelOrderMagento $modelOrderMagento
	 * @param ModelShipmentTrack $modelShipmentTrack
	 * @param StatusResourceModel $statusResourceModel
	 * @param HistoryResourceModel $historyResourceModel
	 * @param ModelHistoryStatus $modelHistoryStatus
	 * @param IntegrationOrderStatusInterface $integrationOrderStatusInterface
	 * @param IntegrationOrderHistoryInterface $integrationOrderHistoryInterface
	 * @param IntegrationOrderItemInterface $integrationOrderItemInterface
	 * @param IntegrationOrderStatusFactory $integrationOrderStatusFactory
	 * @param CollectionFactory $collectionFactory
	 * @param HistoryCollection $historyCollection
	 * @param ItemCollection $itemCollection
	 * @param ManagerInterface $messageManager
	 */
	public function __construct(
		ModelOrderMagento $modelOrderMagento,
		ModelShipmentTrack $modelShipmentTrack,
		StatusResourceModel $statusResourceModel,
		CollectionItemMagento $collectionItemMagento,
		HistoryResourceModel $historyResourceModel,
		ModelHistoryStatus $modelHistoryStatus,
		ItemResourceModel $itemResourceModel,
		IntegrationOrderStatusInterface $integrationOrderStatusInterface,
		IntegrationOrderItemInterface $integrationOrderItemInterface,
		IntegrationOrderHistoryInterface $integrationOrderHistoryInterface,
		IntegrationOrderStatusFactory $integrationOrderStatusFactory,
		HistoryCollection $historyCollection,
		CollectionFactory $collectionFactory,
		ItemCollection $itemCollection,
		ManagerInterface $messageManager
	) {
		$this->modelOrderMagento                = $modelOrderMagento;
		$this->modelShipmentTrack               = $modelShipmentTrack;
		$this->statusResourceModel              = $statusResourceModel;
		$this->collectionItemMagento            = $collectionItemMagento;
		$this->historyResourceModel             = $historyResourceModel;
		$this->modelHistoryStatus               = $modelHistoryStatus;
		$this->itemResourceModel                = $itemResourceModel;
		$this->integrationOrderStatusInterface  = $integrationOrderStatusInterface;
		$this->integrationOrderHistoryInterface = $integrationOrderHistoryInterface;
		$this->integrationOrderItemInterface    = $integrationOrderItemInterface;
		$this->integrationOrderStatusFactory    = $integrationOrderStatusFactory;
		$this->collectionFactory                = $collectionFactory;
		$this->historyCollection                = $historyCollection;
		$this->itemCollection                   = $itemCollection;
		$this->messageManager                   = $messageManager;
	}

	/**
	 * @param IntegrationOrderStatusInterface $integrationOrderStatusInterface
	 * @return IntegrationOrderStatusInterface
	 * @throws \Exception
	 */
	public function save(IntegrationOrderStatusInterface $integrationOrderStatusInterface) {
		try {
			$this->statusResourceModel->save($integrationOrderStatusInterface);
		} catch (Exception $e) {
			$this->messageManager
				->addExceptionMessage(
					$e,
					'There was a error while saving the status ' . $e->getMessage()
				);
		}

		return $integrationOrderStatusInterface;
	}

	/**
	 * @param IntegrationOrderHistoryInterface $integrationOrderHistoryInterface
	 * @return IntegrationOrderHistoryInterface
	 * @throws \Exception
	 */
	public function saveHistory(IntegrationOrderHistoryInterface $integrationOrderHistoryInterface) {
		try {
			$this->historyResourceModel->save($integrationOrderHistoryInterface);
		} catch (Exception $e) {
			$this->messageManager
				->addExceptionMessage(
					$e,
					'There was a error while saving the status ' . $e->getMessage()
				);
		}

		return $integrationOrderHistoryInterface;
	}

	/**
	 * @param IntegrationOrderItemInterface $integrationOrderItemInterface
	 * @return IntegrationOrderItemInterface
	 * @throws \Exception
	 */
	public function saveItem(IntegrationOrderItemInterface $integrationOrderItemInterface) {
		try {
			$this->itemResourceModel->save($integrationOrderItemInterface);
		} catch (Exception $e) {
			$this->messageManager
				->addExceptionMessage(
					$e,
					'There was a error while saving the item ' . $e->getMessage()
				);
		}

		return $integrationOrderItemInterface;
	}

	/**
	 * @param $statusId
	 * @return array
	 */
	public function getById($statusId) {
		if (!isset($this->instances[$statusId])) {
			$order = $this->integrationOrderStatusFactory->create();
			$this->statusResourceModel->load($order, $statusId);
			$this->instances[$statusId] = $order;
		}
		return $this->instances[$statusId];
	}

	/**
	 * {@inheritdoc}
	 */
	public function loadByIdNonSubAction($status, $action) {
		$collection = $this->collectionFactory->create();
		$collection->addFieldToFilter(IntegrationOrderStatusInterface::OMS_STATUS_NO, $status);
		$collection->addFieldToFilter(IntegrationOrderStatusInterface::OMS_ACTION_NO, $action);

		return $collection->getFirstItem();
	}

	/**
	 * {@inheritdoc}
	 */
	public function loadByIdSubAction($status, $action, $subAction) {
		$collection = $this->collectionFactory->create();
		$collection->addFieldToFilter(IntegrationOrderStatusInterface::OMS_STATUS_NO, $status);
		$collection->addFieldToFilter(IntegrationOrderStatusInterface::OMS_ACTION_NO, $action);
		$collection->addFieldToFilter(IntegrationOrderStatusInterface::OMS_SUB_ACTION_NO, $subAction);

		return $collection->getFirstItem();
	}

	/**
	 * {@inheritdoc}
	 */
	public function loadByAWB($awb) {
		$collection = $this->historyCollection->create();
		$collection->addFieldToFilter(IntegrationOrderHistoryInterface::AWB_NUMBER, $awb);

		return $collection->getFirstItem();
	}

	/**
	 * {@inheritdoc}
	 */
	public function loadByOrderId($orderId) {
		$collection = $this->itemCollection->create();
		$collection->addFieldToFilter(IntegrationOrderItemInterface::ORDER_ID, $orderId);

		return $collection;
	}

	/**
	 * Able to Save Order to History Table by Order Id
	 * @param string $orderIds
	 * @return string
	 */
	public function loadByOrderIds($orderIds) {
		$collection = $this->historyCollection->create();
		$collection->addFieldToFilter(IntegrationOrderHistoryInterface::ORDER_ID, $orderIds);

		return $collection->getFirstItem();
	}

	/**
	 * {@inheritdoc}
	 */
	public function loadDataByRefOrderId($orderId) {
		$collection = $this->modelOrderMagento->create();
		$collection->addFieldToFilter('reference_order_id', $orderId);

		return $collection->getFirstItem();
	}

	/**
	 * {@inheritdoc}
	 */
	public function loadDataByParentOrderId($parentId) {
		$collection = $this->modelHistoryStatus->create();
		$collection->addFieldToFilter('parent_id', $parentId);

		return $collection->getFirstItem();
	}

	/**
	 * {@inheritdoc}
	 */
	public function loadDataByFeStatusNo($feStatusNo) {
		$collection = $this->collectionFactory->create();
		$collection->addFieldToFilter(IntegrationOrderStatusInterface::FE_STATUS_NO, $feStatusNo);

		return $collection->getFirstItem();
	}

	/**
	 * {@inheritdoc}
	 */
	public function loadItemByOrderIds($orderId) {
		$collection = $this->collectionItemMagento->create();
		$collection->addFieldToFilter('order_id', $orderId);

		return $collection->getFirstItem();
	}
}
