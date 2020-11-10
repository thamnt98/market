<?php
/**
 * @category Trans
 * @package  Trans_IntegrationOrder
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Muhammad Randy <muhammad.randy@ctcorpdigital.co.id>
 *
 * Copyright © 2020 CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationOrder\Model;

use Trans\IntegrationOrder\Api\Data\RefundInterface;
use Trans\IntegrationOrder\Api\OrderStatusInterface;
use Trans\Sprint\Helper\Config;

/**
 * Class OrderStatus
 */
class OrderStatus implements OrderStatusInterface {

	/**
	 * @var \Trans\Core\Helper\Data
	 */
	protected $coreHelper;

	/**
	 * @var \Trans\IntegrationOrder\Api\IntegrationOrderStatusRepositoryInterface
	 */
	protected $statusRepo;

	/**
	 * @var \Trans\IntegrationOrder\Api\Data\IntegrationOrderHistoryInterfaceFactory
	 */
	protected $historyInterface;

	/**
	 * @var \Trans\IntegrationOrder\Api\IntegrationOrderPaymentRepositoryInterface
	 */
	protected $orderPaymentRepo;

	/**
	 * Order Status Construct Data
	 * @param \Magento\Framework\Event\ManagerInterface $eventManager
	 * @param \Magento\Sales\Model\Convert\OrderFactory $orderConvert
	 * @param \Magento\Framework\HTTP\Client\Curl $curl
	 * @param \Magento\InventoryApi\Api\GetSourceItemsBySkuInterface $sourceItemsBySku
	 * @param \Magento\Shipping\Model\ShipmentNotifierFactory $shipmentNotify
	 * @param \Magento\Sales\Api\Data\OrderInterface $orderInterfaceFactory
	 * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepoInterface
	 * @param \Magento\Sales\Api\Data\ShipmentTrackInterfaceFactory $trackInterface
	 * @param \Magento\Sales\Api\Data\OrderItemInterfaceFactory $orderItemFactory
	 * @param \Trans\Core\Helper\Data $coreHelper
	 * @param \Trans\IntegrationOrder\Helper\Config $orderConfig
	 * @param \Trans\IntegrationOrder\Helper\Data $helperData
	 * @param \Trans\IntegrationOrder\Api\IntegrationOrderRepositoryInterface $orderRepo
	 * @param \Trans\IntegrationOrder\Api\IntegrationOrderStatusRepositoryInterface $statusRepo
	 * @param \Trans\IntegrationOrder\Api\Data\IntegrationOrderHistoryInterfaceFactory $historyInterface
	 * @param \Trans\IntegrationOrder\Api\IntegrationOrderPaymentRepositoryInterface $orderPaymentRepo
	 * @param \Trans\Sprint\Helper\Config $configPg
	 * @param \Trans\IntegrationOrder\Api\RefundRepositoryInterface $refundRepository
	 * @param \Trans\IntegrationOrder\Api\RefundInterfaceFactory $refundInterface
	 */

	public function __construct(
		\Magento\Framework\Event\ManagerInterface $eventManager,
		\Magento\Sales\Model\Convert\OrderFactory $orderConvert,
		\Magento\Framework\HTTP\Client\Curl $curl,
		\Magento\InventoryApi\Api\GetSourceItemsBySkuInterface $sourceItemsBySku,
		\Magento\Shipping\Model\ShipmentNotifierFactory $shipmentNotify,
		\Magento\Sales\Api\Data\OrderInterfaceFactory $orderInterfaceFactory,
		\Magento\Sales\Api\OrderRepositoryInterface $orderRepoInterface,
		\Magento\Sales\Api\Data\OrderStatusHistoryInterfaceFactory $orderStatusHistoryInterfaceFactory,
		\Magento\Sales\Api\OrderStatusHistoryRepositoryInterface $orderStatusHistoryRepoInterface,
		\Magento\Sales\Api\Data\ShipmentTrackInterfaceFactory $trackInterface,
		\Magento\Sales\Api\Data\OrderItemInterfaceFactory $orderItemFactory,
		\Magento\Sales\Api\OrderItemRepositoryInterface $orderItemRepository,
		\Trans\Core\Helper\Data $coreHelper,
		\Trans\IntegrationOrder\Helper\Integration $integrationHelper,
		\Trans\IntegrationOrder\Helper\Config $orderConfig,
		\Trans\IntegrationOrder\Helper\Data $helperData,
		\Trans\IntegrationOrder\Api\IntegrationOrderRepositoryInterface $orderRepo,
		\Trans\IntegrationOrder\Api\IntegrationOrderStatusRepositoryInterface $statusRepo,
		\Trans\IntegrationOrder\Api\Data\IntegrationOrderHistoryInterfaceFactory $historyInterface,
		\Trans\IntegrationOrder\Api\IntegrationOrderPaymentRepositoryInterface $orderPaymentRepo,
		\Trans\Sprint\Helper\Config $configPg,
		\Trans\IntegrationOrder\Api\RefundRepositoryInterface $refundRepository,
		\Trans\IntegrationOrder\Api\Data\RefundInterfaceFactory $refundInterface
	) {
		$this->eventManager                       = $eventManager;
		$this->orderConvert                       = $orderConvert;
		$this->curl                               = $curl;
		$this->integrationHelper                  = $integrationHelper;
		$this->sourceItemsBySku                   = $sourceItemsBySku;
		$this->shipmentNotify                     = $shipmentNotify;
		$this->orderInterfaceFactory              = $orderInterfaceFactory;
		$this->orderRepoInterface                 = $orderRepoInterface;
		$this->orderStatusHistoryInterfaceFactory = $orderStatusHistoryInterfaceFactory;
		$this->orderStatusHistoryRepoInterface    = $orderStatusHistoryRepoInterface;
		$this->trackInterface                     = $trackInterface;
		$this->orderItemFactory                   = $orderItemFactory;
		$this->orderItemRepository                = $orderItemRepository;
		$this->coreHelper                         = $coreHelper;
		$this->orderConfig                        = $orderConfig;
		$this->helperData                         = $helperData;
		$this->orderRepo                          = $orderRepo;
		$this->statusRepo                         = $statusRepo;
		$this->historyInterface                   = $historyInterface;
		$this->orderPaymentRepo                   = $orderPaymentRepo;
		$this->configPg                           = $configPg;
		$this->refundRepository                   = $refundRepository;
		$this->refundInterface                    = $refundInterface;

		$this->loggerOrder = $helperData->getLogger();
	}

	const PICK_UP_BY_CUSTOMER         = '1';
	const DELIVERY                    = '2';
	const PICK_UP_BY_CUSTOMER_MAPPING = 51; //front end number pick up by customer status mapping
	const DELIVERY_MAPPING            = 21; //front end number delivery status mapping
	const LOGISTIC_COURIER            = 0; //if pick up by customer

	/**
	 * prepare oms header
	 *
	 * @return array
	 */
	protected function getHeader() {
		$token                    = $this->integrationHelper->getToken();
		$headers['dest']          = $this->orderConfig->getOmsDest();
		$headers['Content-Type']  = 'application/json';
		$headers['Authorization'] = 'Bearer ' . $token;

		return $headers;
	}

	/**
	 * Get Source Item By SKU for Multi Inventory Source
	 * @param  string $sku
	 * @return string
	 */
	public function getSourceItemBySku($sku) {
		return $this->sourceItemsBySku->execute($sku);
	}

	/**
	 * Get Data Status without sub_action
	 *
	 * @param string $orderId
	 * @param int $status
	 * @param int $action
	 */
	public function statusNonSubAction($orderId, $status, $action) {
		$idsOrder = $this->statusRepo->loadByOrderIds($orderId);
		$data     = $this->statusRepo->loadByIdNonSubAction($status, $action);
		if (!$idsOrder->getOrderId()) {
			throw new \Magento\Framework\Webapi\Exception(__('Order ID doesn\'t exist, please make sure again.'));
		}
		$request = [
			'order_id' => $orderId,
			'status' => $status,
			'action' => $action,
		];
		$orderIds = $idsOrder->getOrderId();
		$stat     = $data->getStatusOms();
		$act      = $data->getActionOms();

		if ($status == $stat && $action == $act) {
			$result = [
				"message" => "code : 200",
				'order_id' => "Order Id : " . $request['order_id'],
				'fe_status_no' => "FE Status No : " . $data->getFeStatusNo(),
				'fe_status' => "FE Status : " . $data->getFeStatus(),
				'fe_sub_status_no' => "FE Sub Status No : " . $data->getFeSubStatusNo(),
				'fe_sub_status' => "FE Sub Status : " . $data->getFeSubStatus(),
			];
		} else {
			throw new \Magento\Framework\Webapi\Exception(__('Please re-check status and action sequence before submit'), 400);
		}
		$configStatus               = $this->orderConfig;
		$loadDataOrder              = $this->statusRepo->loadDataByRefOrderId($orderId);
		$entityIdSalesOrder         = $loadDataOrder->getEntityId();
		$loadDataOrderStatusHistory = $this->statusRepo->loadDataByParentOrderId($entityIdSalesOrder);
		$saveDataToStatusHistory    = $this->orderStatusHistoryInterfaceFactory->create();

		/* Updating data status order in core magento table */
		if ($loadDataOrder && $data->getFeStatusNo() == $configStatus->getNumberInProcess()) {
			$loadDataOrder->setStatus($configStatus->getInProcessOrderStatus());
			$saveDataToStatusHistory->setParentId($entityIdSalesOrder);
			$saveDataToStatusHistory->setStatus($configStatus->getInProcessOrderStatus());
			$saveDataToStatusHistory->setComment($data->getFeStatus() . $data->getFeSubStatus());
			$saveDataToStatusHistory->setEntityName('order');
		}
		if ($loadDataOrder && $data->getFeStatusNo() == $configStatus->getNumberInDelivery()) {
			$loadDataOrder->setStatus($configStatus->getInDeliveryOrderStatus());
			$saveDataToStatusHistory->setParentId($entityIdSalesOrder);
			$saveDataToStatusHistory->setStatus($configStatus->getInDeliveryOrderStatus());
			$saveDataToStatusHistory->setComment($data->getFeStatus() . $data->getFeSubStatus());
			$saveDataToStatusHistory->setEntityName('order');
		}
		if ($loadDataOrder && $data->getFeStatusNo() == $configStatus->getNumberDelivered()) {
			$loadDataOrder->setStatus($configStatus->getDeliveredOrderStatus());
			$saveDataToStatusHistory->setParentId($entityIdSalesOrder);
			$saveDataToStatusHistory->setStatus($configStatus->getDeliveredOrderStatus());
			$saveDataToStatusHistory->setComment($data->getFeStatus() . $data->getFeSubStatus());
			$saveDataToStatusHistory->setEntityName('order');
		}
		if ($loadDataOrder && $data->getFeStatusNo() == $configStatus->getNumberOrderCanceled()) {
			$loadDataOrder->setStatus($configStatus->getOrderCanceledStatus());
			$saveDataToStatusHistory->setParentId($entityIdSalesOrder);
			$saveDataToStatusHistory->setStatus($configStatus->getOrderCanceledStatus());
			$saveDataToStatusHistory->setComment($data->getFeStatus() . $data->getFeSubStatus());
			$saveDataToStatusHistory->setEntityName('order');
		}
		if ($loadDataOrder && $data->getFeStatusNo() == $configStatus->getNumberPickUpByCustomer()) {
			$loadDataOrder->setStatus($configStatus->getPickupByCustomerStatus());
			$saveDataToStatusHistory->setParentId($entityIdSalesOrder);
			$saveDataToStatusHistory->setStatus($configStatus->getPickupByCustomerStatus());
			$saveDataToStatusHistory->setComment($data->getFeStatus() . $data->getFeSubStatus());
			$saveDataToStatusHistory->setEntityName('order');
		}
		if ($loadDataOrder && $data->getFeStatusNo() == $configStatus->getNumberInTransit()) {
			$loadDataOrder->setStatus($configStatus->getInTransitStatus());
			$saveDataToStatusHistory->setParentId($entityIdSalesOrder);
			$saveDataToStatusHistory->setStatus($configStatus->getInTransitStatus());
			$saveDataToStatusHistory->setComment($data->getFeStatus() . $data->getFeSubStatus());
			$saveDataToStatusHistory->setEntityName('order');
		}
		if ($loadDataOrder && $data->getFeStatusNo() == $configStatus->getNumberInProcessWaitingPickup()) {
			$loadDataOrder->setStatus($configStatus->getInProcessWaitingPickupStatus());
			$saveDataToStatusHistory->setParentId($entityIdSalesOrder);
			$saveDataToStatusHistory->setStatus($configStatus->getInProcessWaitingPickupStatus());
			$saveDataToStatusHistory->setComment($data->getFeStatus() . $data->getFeSubStatus());
			$saveDataToStatusHistory->setEntityName('order');
		}

		$this->orderRepoInterface->save($loadDataOrder);
		$this->orderStatusHistoryRepoInterface->save($saveDataToStatusHistory);

		if ($orderIds) {
			$model = $this->historyInterface->create();
			$model->setReferenceNumber($idsOrder->getReferenceNumber());
			$model->setOrderId($orderIds);
			$model->setFeStatusNo($data->getFeStatusNo());
			$model->setFeSubStatusNo($data->getFeSubStatusNo());

			$this->statusRepo->saveHistory($model);
		}
		return $result;
	}

	/**
	 * Get Data Status with sub_action
	 *
	 * @param string $orderId
	 * @param int $status
	 * @param int $action
	 * @param int $subAction
	 */
	public function statusWithSubAction($orderId, $status, $action, $subAction) {
		$idsOrder = $this->statusRepo->loadByOrderIds($orderId);
		$data     = $this->statusRepo->loadByIdSubAction($status, $action, $subAction);
		if (!$idsOrder->getOrderId()) {
			throw new \Magento\Framework\Webapi\Exception(__('Order ID doesn\'t exist, please make sure again.'));
		}
		$request = [
			'order_id' => $orderId,
			'status' => $status,
			'action' => $action,
			'sub_action' => $subAction,
		];
		$orderIds = $idsOrder->getOrderId();
		$stat     = $data->getStatusOms();
		$act      = $data->getActionOms();
		$subAct   = $data->getSubActionOms();

		if ($status == $stat && $action == $act && $subAction == $subAct) {
			$result = [
				"message" => "code : 200",
				'order_id' => "Order Id : " . $request['order_id'],
				'fe_status_no' => "FE Status No : " . $data->getFeStatusNo(),
				'fe_status' => "FE Status : " . $data->getFeStatus(),
				'fe_sub_status_no' => "FE Sub Status No : " . $data->getFeSubStatusNo(),
				'fe_sub_status' => "FE Sub Status : " . $data->getFeSubStatus(),
			];
		} else {
			throw new \Magento\Framework\Webapi\Exception(__('Please re-check status, action and subaction sequence before submit'), 400);
		}

		$configStatus               = $this->orderConfig;
		$loadDataOrder              = $this->statusRepo->loadDataByRefOrderId($orderId);
		$entityIdSalesOrder         = $loadDataOrder->getEntityId();
		$loadDataOrderStatusHistory = $this->statusRepo->loadDataByParentOrderId($entityIdSalesOrder);
		$saveDataToStatusHistory    = $this->orderStatusHistoryInterfaceFactory->create();

		/* Updating data status order in core magento table */
		if ($loadDataOrder && $data->getFeStatusNo() == $configStatus->getNumberInProcess()) {
			$loadDataOrder->setStatus($configStatus->getInProcessOrderStatus());
			$saveDataToStatusHistory->setParentId($entityIdSalesOrder);
			$saveDataToStatusHistory->setStatus($configStatus->getInProcessOrderStatus());
			$saveDataToStatusHistory->setComment($data->getFeStatus() . $data->getFeSubStatus());
			$saveDataToStatusHistory->setEntityName('order');
		}
		if ($loadDataOrder && $data->getFeStatusNo() == $configStatus->getNumberInDelivery()) {
			$loadDataOrder->setStatus($configStatus->getInDeliveryOrderStatus());
			$saveDataToStatusHistory->setParentId($entityIdSalesOrder);
			$saveDataToStatusHistory->setStatus($configStatus->getInDeliveryOrderStatus());
			$saveDataToStatusHistory->setComment($data->getFeStatus() . $data->getFeSubStatus());
			$saveDataToStatusHistory->setEntityName('order');
		}
		if ($loadDataOrder && $data->getFeStatusNo() == $configStatus->getNumberDelivered()) {
			$loadDataOrder->setStatus($configStatus->getDeliveredOrderStatus());
			$saveDataToStatusHistory->setParentId($entityIdSalesOrder);
			$saveDataToStatusHistory->setStatus($configStatus->getDeliveredOrderStatus());
			$saveDataToStatusHistory->setComment($data->getFeStatus() . $data->getFeSubStatus());
			$saveDataToStatusHistory->setEntityName('order');
		}
		if ($loadDataOrder && $data->getFeStatusNo() == $configStatus->getNumberOrderCanceled()) {
			$loadDataOrder->setStatus($configStatus->getOrderCanceledStatus());
			$saveDataToStatusHistory->setParentId($entityIdSalesOrder);
			$saveDataToStatusHistory->setStatus($configStatus->getOrderCanceledStatus());
			$saveDataToStatusHistory->setComment($data->getFeStatus() . $data->getFeSubStatus());
			$saveDataToStatusHistory->setEntityName('order');
		}
		if ($loadDataOrder && $data->getFeStatusNo() == $configStatus->getNumberPickUpByCustomer()) {
			$loadDataOrder->setStatus($configStatus->getPickupByCustomerStatus());
			$saveDataToStatusHistory->setParentId($entityIdSalesOrder);
			$saveDataToStatusHistory->setStatus($configStatus->getPickupByCustomerStatus());
			$saveDataToStatusHistory->setComment($data->getFeStatus() . $data->getFeSubStatus());
			$saveDataToStatusHistory->setEntityName('order');
		}
		if ($loadDataOrder && $data->getFeStatusNo() == $configStatus->getNumberInTransit()) {
			$loadDataOrder->setStatus($configStatus->getInTransitStatus());
			$saveDataToStatusHistory->setParentId($entityIdSalesOrder);
			$saveDataToStatusHistory->setStatus($configStatus->getInTransitStatus());
			$saveDataToStatusHistory->setComment($data->getFeStatus() . $data->getFeSubStatus());
			$saveDataToStatusHistory->setEntityName('order');
		}
		if ($loadDataOrder && $data->getFeStatusNo() == $configStatus->getNumberInProcessWaitingPickup()) {
			$loadDataOrder->setStatus($configStatus->getInProcessWaitingPickupStatus());
			$saveDataToStatusHistory->setParentId($entityIdSalesOrder);
			$saveDataToStatusHistory->setStatus($configStatus->getInProcessWaitingPickupStatus());
			$saveDataToStatusHistory->setComment($data->getFeStatus() . $data->getFeSubStatus());
			$saveDataToStatusHistory->setEntityName('order');
		}

		$this->orderRepoInterface->save($loadDataOrder);
		$this->orderStatusHistoryRepoInterface->save($saveDataToStatusHistory);

		if ($orderIds) {
			$model = $this->historyInterface->create();
			$model->setReferenceNumber($idsOrder->getReferenceNumber());
			$model->setOrderId($orderIds);
			$model->setFeStatusNo($data->getFeStatusNo());
			$model->setFeSubStatusNo($data->getFeSubStatusNo());

			$this->statusRepo->saveHistory($model);
		}
		return $result;
	}

	/**
	 * Get Data Status with items
	 *
	 * @param string $orderId
	 * @param int $status
	 * @param int $action
	 * @param int $subAction
	 * @param string $orderItems
	 */
	public function statusOrderItems($orderId, $status, $action, $subAction, $orderItems) {
		$idsOrder = $this->statusRepo->loadByOrderIds($orderId);
		$data     = $this->statusRepo->loadByIdSubAction($status, $action, $subAction);
		if (!$idsOrder->getOrderId()) {
			throw new \Magento\Framework\Webapi\Exception(__('Order ID doesn\'t exist, please make sure again.'));
		}
		$orderItem = [];
		foreach ($orderItems as $itemData) {
			$item['sku']                = $itemData['sku'];
			$item['quantity']           = $itemData['quantity'];
			$item['quantity_allocated'] = $itemData['quantity_allocated'];
			$item['item_status']        = $itemData['item_status'];
			$orderItem[]                = $item;
		}
		$request = array(
			'order_id' => $orderId,
			'status' => $status,
			'action' => $action,
			'sub_action' => $subAction,
			'order_items' => $orderItem,
		);

		$orderIds = $idsOrder->getOrderId();
		$stat     = $data->getStatusOms();
		$act      = $data->getActionOms();
		$subAct   = $data->getSubActionOms();

		if ($status == $stat && $action == $act && $subAction == $subAct) {
			$result = [
				"code" => "code : 200",
				'message' => "Success to add allocated quantity",
				'fe_status_no' => "FE Status No : " . $data->getFeStatusNo(),
				'fe_status' => "FE Status : " . $data->getFeStatus(),
				'fe_sub_status_no' => "FE Sub Status No : " . $data->getFeSubStatusNo(),
				'fe_sub_status' => "FE Sub Status : " . $data->getFeSubStatus(),
			];
		} else {
			throw new \Magento\Framework\Webapi\Exception(__('Please re-check status, action and subaction sequence before submit'), 400);
		}

		$itemOrders = $this->statusRepo->loadByOrderId($request['order_id']);
		foreach ($itemOrders as $itemOrder) {
			if ($itemOrder->getSKU() === $item['sku']) {
				$itemOrder->setQtyAllocated($item['quantity_allocated']);
				$itemOrder->setItemStatus($item['item_status']);
			}
			// if ($itemOrder->getQty() != $item['quantity']) {
			// 	throw new \Magento\Framework\Webapi\Exception(__('Invalid quantity order. Please checking again.'), 400);
			// }
			if ($item['quantity_allocated'] > $itemOrder->getQty()) {
				throw new \Magento\Framework\Webapi\Exception(__('Quantity allocated is greater than quantity order. Please checking again.'), 400);
			}
		}
		$itemOrderSave = $this->statusRepo->saveItem($itemOrder);

		$configStatus               = $this->orderConfig;
		$loadDataOrder              = $this->statusRepo->loadDataByRefOrderId($orderId);
		$entityIdSalesOrder         = $loadDataOrder->getEntityId();
		$loadDataOrderStatusHistory = $this->statusRepo->loadDataByParentOrderId($entityIdSalesOrder);
		$saveDataToStatusHistory    = $this->orderStatusHistoryInterfaceFactory->create();

		/* Updating data status order in core magento table */
		if ($loadDataOrder && $data->getFeStatusNo() == $configStatus->getNumberInProcess()) {
			$loadDataOrder->setStatus($configStatus->getInProcessOrderStatus());
			$saveDataToStatusHistory->setParentId($entityIdSalesOrder);
			$saveDataToStatusHistory->setStatus($configStatus->getInProcessOrderStatus());
			$saveDataToStatusHistory->setComment($data->getFeStatus() . $data->getFeSubStatus());
			$saveDataToStatusHistory->setEntityName('order');
		}
		if ($loadDataOrder && $data->getFeStatusNo() == $configStatus->getNumberInDelivery()) {
			$loadDataOrder->setStatus($configStatus->getInDeliveryOrderStatus());
			$saveDataToStatusHistory->setParentId($entityIdSalesOrder);
			$saveDataToStatusHistory->setStatus($configStatus->getInDeliveryOrderStatus());
			$saveDataToStatusHistory->setComment($data->getFeStatus() . $data->getFeSubStatus());
			$saveDataToStatusHistory->setEntityName('order');
		}
		if ($loadDataOrder && $data->getFeStatusNo() == $configStatus->getNumberDelivered()) {
			$loadDataOrder->setStatus($configStatus->getDeliveredOrderStatus());
			$saveDataToStatusHistory->setParentId($entityIdSalesOrder);
			$saveDataToStatusHistory->setStatus($configStatus->getDeliveredOrderStatus());
			$saveDataToStatusHistory->setComment($data->getFeStatus() . $data->getFeSubStatus());
			$saveDataToStatusHistory->setEntityName('order');
		}
		if ($loadDataOrder && $data->getFeStatusNo() == $configStatus->getNumberOrderCanceled()) {
			$loadDataOrder->setStatus($configStatus->getOrderCanceledStatus());
			$saveDataToStatusHistory->setParentId($entityIdSalesOrder);
			$saveDataToStatusHistory->setStatus($configStatus->getOrderCanceledStatus());
			$saveDataToStatusHistory->setComment($data->getFeStatus() . $data->getFeSubStatus());
			$saveDataToStatusHistory->setEntityName('order');
		}
		if ($loadDataOrder && $data->getFeStatusNo() == $configStatus->getNumberPickUpByCustomer()) {
			$loadDataOrder->setStatus($configStatus->getPickupByCustomerStatus());
			$saveDataToStatusHistory->setParentId($entityIdSalesOrder);
			$saveDataToStatusHistory->setStatus($configStatus->getPickupByCustomerStatus());
			$saveDataToStatusHistory->setComment($data->getFeStatus() . $data->getFeSubStatus());
			$saveDataToStatusHistory->setEntityName('order');
		}
		if ($loadDataOrder && $data->getFeStatusNo() == $configStatus->getNumberInTransit()) {
			$loadDataOrder->setStatus($configStatus->getInTransitStatus());
			$saveDataToStatusHistory->setParentId($entityIdSalesOrder);
			$saveDataToStatusHistory->setStatus($configStatus->getInTransitStatus());
			$saveDataToStatusHistory->setComment($data->getFeStatus() . $data->getFeSubStatus());
			$saveDataToStatusHistory->setEntityName('order');
		}
		if ($loadDataOrder && $data->getFeStatusNo() == $configStatus->getNumberInProcessWaitingPickup()) {
			$loadDataOrder->setStatus($configStatus->getInProcessWaitingPickupStatus());
			$saveDataToStatusHistory->setParentId($entityIdSalesOrder);
			$saveDataToStatusHistory->setStatus($configStatus->getInProcessWaitingPickupStatus());
			$saveDataToStatusHistory->setComment($data->getFeStatus() . $data->getFeSubStatus());
			$saveDataToStatusHistory->setEntityName('order');
		}

		$this->orderRepoInterface->save($loadDataOrder);
		$this->orderStatusHistoryRepoInterface->save($saveDataToStatusHistory);

		if ($orderIds) {
			$model = $this->historyInterface->create();
			$model->setReferenceNumber($idsOrder->getReferenceNumber());
			$model->setOrderId($orderIds);
			$model->setFeStatusNo($data->getFeStatusNo());
			$model->setFeSubStatusNo($data->getFeSubStatusNo());

			$this->statusRepo->saveHistory($model);
		}

		/* =================== START CAPTURE REFUND =======================*/

		/**
		 * preparing data for refund PG
		 */
		$paymentMethod     = $loadDataOrder->getPayment()->getMethod();
		$channelId         = $this->configPg->getPaymentChannelId($paymentMethod);
		$serviceCode       = $this->configPg->getPaymentChannelRefundServicecode($paymentMethod);
		$reffId            = $idsOrder->getReferenceNumber();
		$trxAmount         = (int) $loadDataOrder->getGrandTotal();
		$url               = $this->configPg->getApiBaseUrl($paymentMethod) . '/' . Config::REFUND_POST_URL;
		$loadItemByOrderId = $this->statusRepo->loadByOrderId($orderId);

		/**
		 * trigger capture - refund by payment method
		 */
		if ($status == 2 && $action == 2 && $subAction == 7) {
			/* Start CC Bank Mega Auth Capture*/
			if ($paymentMethod === 'trans_mepay_cc') {
				foreach ($loadItemByOrderId as $itemOrder) {
					$paidPriceOrder        = $itemOrder->getPaidPrice();
					$qtyOrder              = $itemOrder->getQty();
					$qtyAllocated          = $itemOrder->getQtyAllocated();
					$matrixAdjusmentAmount = ($paidPriceOrder / $qtyOrder) * ($qtyOrder - $qtyAllocated);
				}
				$this->eventManager->dispatch(
					'refund_with_mega_payment',
					[
						'order_id' => $orderId,
						'amount' => $trxAmount,
						'new_amount' => $trxAmount - $matrixAdjusmentAmount,
					]
				);
			}
			/* End CC Bank Mega Auth Capture*/

			/* Start Non CC*/
			if ($paymentMethod === 'sprint_bca_va' || 'sprint_permata_va') {
				foreach ($loadItemByOrderId as $itemOrder) {
					$paidPriceOrder        = $itemOrder->getPaidPrice();
					$qtyOrder              = $itemOrder->getQty();
					$qtyAllocated          = $itemOrder->getQtyAllocated();
					$matrixAdjusmentAmount = ($paidPriceOrder / $qtyOrder) * ($qtyOrder - $qtyAllocated);
				}
				/* update quantity adjusment */
				$url            = $this->orderConfig->getOmsBaseUrl() . $this->orderConfig->getOmsPaymentStatusApi();
				$headers        = $this->getHeader();
				$dataAdjustment = array(
					'reference_number' => $reffId,
					'status' => 3,
					'amount_adjustment' => $matrixAdjusmentAmount,

				);
				$dataJson = json_encode($dataAdjustment);
				$this->loggerOrder->info($dataJson);

				$this->curl->setOption(CURLOPT_RETURNTRANSFER, true);
				$this->curl->setOption(CURLOPT_CUSTOMREQUEST, 'PATCH');
				$this->curl->setHeaders($headers);
				$this->curl->post($url, $dataJson);
				$responseOrder = $this->curl->getBody();
				$this->loggerOrder->info('$headers : ' . json_encode($headers));
				$this->loggerOrder->info('$responseOrder : ' . $responseOrder);
				$objOrder = json_decode($responseOrder);
				$this->loggerOrder->info('Body: ' . $dataJson . '. Response: ' . $responseOrder);
				$json_string = stripcslashes($responseOrder);
				if ($objOrder->code == 200) {
					return json_decode($responseOrder, true);
				}
			}
			/* End Non CC*/

			if ($paymentMethod === 'sprint_mega_cc' || 'sprint_allbankfull_cc' || 'sprint_mega_debit') {
				if ($itemData['quantity'] > $itemData['quantity_allocated']) {
					$this->helperData->sprintLog()->info('===== Capture Process ===== Start');

					/**
					 * prepare data array capture send to PG
					 */
					$dataCapture                      = array();
					$dataCapture['channelId']         = $channelId;
					$dataCapture['serviceCode']       = $serviceCode;
					$dataCapture['transactionNo']     = RefundInterface::PREFIX_CAPTURE . $orderId;
					$dataCapture['transactionReffId'] = $reffId;
					$dataCapture['transactionAmount'] = $trxAmount;
					$dataCapture['transactionType']   = RefundInterface::CAPTURE;
					try {
						$this->curl->setOption(CURLOPT_RETURNTRANSFER, true);
						$this->curl->post($url, $dataCapture);
					} catch (\Exception $e) {
						$this->logger->info('Capture Error = ' . $e->getMessage());
					};
					$saveOrderItem  = $this->orderInterfaceFactory->create();
					$response       = $this->curl->getBody();
					$obj            = json_decode($response);
					$responseStatus = $obj->transactionStatus;
					$success        = Config::TRANSACTION_STATUS_SUCCESS;
					$decline        = Config::TRANSACTION_STATUS_DECLINED;
					$pending        = Config::TRANSACTION_STATUS_NOPAYMENT;

					$this->helperData->sprintLog()->info('Response Data = ' . json_encode($obj));
					$this->helperData->sprintLog()->info('===== Capture Process ===== End');

					if ($responseStatus === $success || $decline || $pending) {
						$this->helperData->sprintLog()->info('===== Refund Process ===== Start');

						/**
						 * prepare data array refund send to PG
						 */
						foreach ($loadItemByOrderId as $itemOrder) {
							$paidPriceOrder        = $itemOrder->getPaidPrice();
							$qtyOrder              = $itemOrder->getQty();
							$qtyAllocated          = $itemOrder->getQtyAllocated();
							$matrixAdjusmentAmount = ($paidPriceOrder / $qtyOrder) * ($qtyOrder - $qtyAllocated);
						}
						$refTrxNumber = RefundInterface::PREFIX_REFUND . $this->helperData->genRefNumber() . $orderId;

						$dataRefund                      = array();
						$dataRefund['channelId']         = $channelId;
						$dataRefund['serviceCode']       = $serviceCode;
						$dataRefund['transactionNo']     = $refTrxNumber;
						$dataRefund['transactionReffId'] = RefundInterface::PREFIX_CAPTURE . $orderId;
						if ($itemData['quantity_allocated'] === 0) {
							$dataRefund['transactionAmount'] = $trxAmount;
							$saveOrderItem->setGrandTotal($trxAmount);
						} else {
							$dataRefund['transactionAmount'] = $matrixAdjusmentAmount;
							$saveOrderItem->setGrandTotal($matrixAdjusmentAmount);
						}
						$dataRefund['transactionType'] = RefundInterface::REFUND;
						try {
							$this->curl->setOption(CURLOPT_RETURNTRANSFER, true);
							$this->curl->post($url, $dataRefund);
						} catch (\Exception $e) {
							$this->logger->info('Refund error = ' . $e->getMessage());
						};
						$responseRf = $this->curl->getBody();
						$objRf      = json_decode($responseRf);

						/* update quantity adjusment */
						$url            = $this->orderConfig->getOmsBaseUrl() . $this->orderConfig->getOmsPaymentStatusApi();
						$headers        = $this->getHeader();
						$dataAdjustment = array(
							'reference_number' => $reffId,
							'status' => 3,
							'amount_adjustment' => $matrixAdjusmentAmount,

						);
						$dataJson = json_encode($dataAdjustment);
						$this->loggerOrder->info($dataJson);

						$this->curl->setOption(CURLOPT_RETURNTRANSFER, true);
						$this->curl->setOption(CURLOPT_CUSTOMREQUEST, 'PATCH');
						$this->curl->setHeaders($headers);
						$this->curl->post($url, $dataJson);
						$responseOrder = $this->curl->getBody();
						$this->loggerOrder->info('$headers : ' . json_encode($headers));
						$this->loggerOrder->info('$responseOrder : ' . $responseOrder);
						$objOrder = json_decode($responseOrder);
						$this->loggerOrder->info('Body: ' . $dataJson . '. Response: ' . $responseOrder);
						$json_string = stripcslashes($responseOrder);
						if ($objOrder->code == 200) {
							return json_decode($responseOrder, true);
						}

						/* save to table sales_order_item */
						$saveOrderItem = $this->orderItemFactory->create();
						$saveOrderItem->setQtyRefunded($itemData['quantity_allocated']);
						$this->orderItemRepository->save($saveOrderItem);

						/* save to table sales_order */
						$this->orderRepoInterface->save($saveOrderItem);

						/* save to table integration_oms_refund */
						$saveRefundData = $this->refundInterface->create();
						$saveRefundData->setOrderRefNumber($idsOrder->getReferenceNumber());
						$saveRefundData->setRefundTrxNumber($refTrxNumber);
						$saveRefundData->setOrderId($orderId);
						$saveRefundData->setSku($item['sku']);
						$saveRefundData->setQtyRefund($itemData['quantity_allocated']);
						$saveRefundData->setAmountRefundOrder($matrixAdjusmentAmount);

						$this->refundRepository->save($saveRefundData);

						$this->helperData->sprintLog()->info('Response Data = ' . json_encode($objRf));
						$this->helperData->sprintLog()->info('===== Refund Process ===== End');
					}
				}
			}
		}

		/* =================== END CAPTURE REFUND =======================*/

		return $result;
	}

	/**
	 * Get Data Status Update AWB
	 *
	 * @param string $orderId
	 * @param int $status
	 * @param int $action
	 * @param string $logisticNumber
	 * @param string $logisticCourier
	 */
	public function updateAWB($orderId, $status, $action, $logisticNumber, $logisticCourier) {
		$idsOrder = $this->statusRepo->loadByOrderIds($orderId);
		if (!$idsOrder->getOrderId()) {
			throw new \Magento\Framework\Webapi\Exception(__('Order ID doesn\'t exist, please make sure again.'));
		}
		$data      = $this->statusRepo->loadByIdNonSubAction($status, $action);
		$format    = 'Y-m-d H:i:s';
		$orderDate = $this->coreHelper->getDateNow();
		$request   = [
			'order_id' => $orderId,
			'status' => $status,
			'action' => $action,
			'logistic_number' => $logisticNumber,
			'logistic_courier' => $logisticCourier,
		];

		$orderIds = $idsOrder->getOrderId();
		$stat     = $data->getStatusOms();
		$act      = $data->getActionOms();

		$orderData            = $this->orderRepo->loadDataByOrderId($orderId);
		$loadStatusPickByCust = $this->statusRepo->loadDataByFeStatusNo(self::PICK_UP_BY_CUSTOMER_MAPPING);
		$loadStatusDelivery   = $this->statusRepo->loadDataByFeStatusNo(self::DELIVERY_MAPPING);

		if ($orderData->getOrderType() === self::PICK_UP_BY_CUSTOMER) {
			if ($status == $stat && $action == $act) {
				$result = [
					"message" => "code : 200",
					'order_id' => "Order Id : " . $request['order_id'],
					'log_number' => "Logistic Number : " . $request['order_id'],
					'log_courier' => "Logistic Courier : " . $request['logistic_courier'],
					'fe_status_no' => "FE Status No : " . $loadStatusPickByCust->getFeStatusNo(),
					'fe_status' => "FE Status : " . $loadStatusPickByCust->getFeStatus(),
					'fe_sub_status_no' => "FE Sub Status No : " . $loadStatusPickByCust->getFeSubStatusNo(),
					'fe_sub_status' => "FE Sub Status : " . $loadStatusPickByCust->getFeSubStatus() . $request['order_id'],
				];
			}
		} elseif ($orderData->getOrderType() === self::DELIVERY) {
			if ($status == $stat && $action == $act) {
				$result = [
					"message" => "code : 200",
					'order_id' => "Order Id : " . $request['order_id'],
					'log_number' => "Logistic Number : " . $request['logistic_number'],
					'log_courier' => "Logistic Courier : " . $request['logistic_courier'],
					'fe_status_no' => "FE Status No : " . $loadStatusDelivery->getFeStatusNo(),
					'fe_status' => "FE Status : " . $loadStatusDelivery->getFeStatus(),
					'fe_sub_status_no' => "FE Sub Status No : " . $loadStatusDelivery->getFeSubStatusNo(),
					'fe_sub_status' => "FE Sub Status : " . $loadStatusDelivery->getFeSubStatus() . $request['logistic_number'],
				];
			}
		} else {
			throw new \Magento\Framework\Webapi\Exception(__('Please re-check status and action sequence before submit'), 400);
		}

		$historyOrder = $this->statusRepo->loadByAWB($request['logistic_number']);
		if ($historyOrder->getAwbNumber()) {
			throw new \Magento\Framework\Webapi\Exception(__('AWB Number is already exist, cannot duplicate.'), 400);
		}

		$configStatus               = $this->orderConfig;
		$loadDataOrder              = $this->statusRepo->loadDataByRefOrderId($orderId);
		$entityIdSalesOrder         = $loadDataOrder->getEntityId();
		$loadDataOrderStatusHistory = $this->statusRepo->loadDataByParentOrderId($entityIdSalesOrder);
		$getIncrementId             = $loadDataOrder->getIncrementId();
		$shippingMethod             = $loadDataOrder->getShippingMethod();
		$shippingDescription        = $loadDataOrder->getShippingDescription();
		$saveDataToStatusHistory    = $this->orderStatusHistoryInterfaceFactory->create();

		/* Updating data status order in core magento table */
		if ($loadDataOrder->getStatus() != $configStatus->getPickupByCustomerStatus()) {
			$pickUp = $loadDataOrder->setStatus($configStatus->getPickupByCustomerStatus());
			$saveDataToStatusHistory->setParentId($entityIdSalesOrder);
			$saveDataToStatusHistory->setStatus($configStatus->getPickupByCustomerStatus());
			$saveDataToStatusHistory->setComment($loadStatusPickByCust->getFeStatus() . $loadStatusPickByCust->getFeSubStatus());
			$saveDataToStatusHistory->setEntityName('order');
			$this->orderRepoInterface->save($pickUp);
			$this->orderStatusHistoryRepoInterface->save($saveDataToStatusHistory);
		}
		if ($orderData->getOrderType() === self::DELIVERY) {
			$deliveries = $loadDataOrder->setStatus($configStatus->getInProcessWaitingPickupStatus());
			$saveDataToStatusHistory->setParentId($entityIdSalesOrder);
			$saveDataToStatusHistory->setStatus($configStatus->getInProcessWaitingPickupStatus());
			$saveDataToStatusHistory->setComment($loadStatusDelivery->getFeStatus() . $loadStatusDelivery->getFeSubStatus());
			$saveDataToStatusHistory->setEntityName('order');
			$this->orderRepoInterface->save($deliveries);
			$this->orderStatusHistoryRepoInterface->save($saveDataToStatusHistory);
		}

		if (!$historyOrder->getOrderId() && $orderData->getOrderType() === self::DELIVERY) {
			$historyOrder->setOrderId($request['order_id']);
			$historyOrder->setReferenceNumber($idsOrder->getReferenceNumber());
			$historyOrder->setAwbNumber($request['logistic_number']);
			$historyOrder->setLogCourierNo($request['logistic_courier']);
			$historyOrder->setFeStatusNo($loadStatusDelivery->getFeStatusNo());
			$historyOrder->setFeSubStatusNo($loadStatusDelivery->getFeSubStatusNo());
			$historyOrder->setUpdatedAt($orderDate);
		} elseif (!$historyOrder->getOrderId() && $orderData->getOrderType() === self::PICK_UP_BY_CUSTOMER) {
			$historyOrder->setOrderId($request['order_id']);
			$historyOrder->setReferenceNumber($idsOrder->getReferenceNumber());
			$historyOrder->setAwbNumber($request['order_id']);
			$historyOrder->setLogCourierNo(self::LOGISTIC_COURIER);
			$historyOrder->setFeStatusNo($loadStatusPickByCust->getFeStatusNo());
			$historyOrder->setFeSubStatusNo($loadStatusPickByCust->getFeSubStatusNo());
			$historyOrder->setUpdatedAt($orderDate);
		}
		$this->statusRepo->saveHistory($historyOrder);

		/* Updating data to table shipment */
		$getEntityIds   = $loadDataOrder->getEntityId(); // entity id from sales_order
		$getCustomerIds = $loadDataOrder->getCustomerId(); // customer id from sales_order

		// Load the order increment ID
		$order = $this->orderInterfaceFactory->create()->loadByIncrementId($getIncrementId);
		// Check if order can be shipped or has already shipped
		if (!$order->canShip()) {
			throw new \Magento\Framework\Exception\LocalizedException(
				__('You can\'t create an shipment.')
			);
		}

		// Initialize the order shipment object
		$convertOrder = $this->orderConvert->create();
		$shipment     = $convertOrder->toShipment($order);

		// Loop through order items
		foreach ($order->getAllItems() as $orderItem) {
			// Check if order item has qty to ship or is virtual
			if (!$orderItem->getQtyToShip() || $orderItem->getIsVirtual()) {
				continue;
			}
			$sku        = $orderItem->getSKU();
			$qty        = $orderItem->getQtyOrdered();
			$sourceCode = $this->getSourceItemBySku($sku);
			foreach ($sourceCode as $sourceCodeData) {
				if ($sourceCodeData->getSourceCode() != "default" && $sourceCodeData->getQuantity() > 0) {
					$sourceCodes = $sourceCodeData->getSourceCode();
					$shipment->getExtensionAttributes()->setSourceCode($sourceCodes);
				}
			}

			$qtyShipped = $orderItem->getQtyToShip();
			// Create shipment item with qty
			$shipmentItem = $convertOrder->itemToShipmentItem($orderItem)->setQty($qtyShipped);
			// Add shipment item to shipment
			$shipment->addItem($shipmentItem);
		}

		// Register shipment
		$shipment->register();
		// Add tracking information, Can add multiple tracking information
		if ($orderData->getOrderType() == self::PICK_UP_BY_CUSTOMER) {
			$data = [
				'carrier_code' => $shippingMethod,
				'title' => $shippingDescription,
				'number' => $orderId,
			];
		}

		if ($orderData->getOrderType() == self::DELIVERY) {
			$data = [
				'carrier_code' => $shippingMethod,
				'title' => $shippingDescription,
				'number' => $logisticNumber,
			];
		}
		$shipment->getOrder()->setIsInProcess(true);

		try {
			// set source code to MSI
			$track = $this->trackInterface->create()->addData($data);
			$shipment->addTrack($track)->save();

			// Save created shipment and order
			$shipment->save();
			$shipment->getOrder()->save();

			// Send email
			$this->shipmentNotify->create()->notify($shipment);
			$shipment->save();
		} catch (\Exception $e) {
			throw new \Magento\Framework\Exception\LocalizedException(
				__($e->getMessage())
			);
		}
		return $result;
	}

	/**
	 * OMS able to check status payment in Magento
	 *
	 * @param string $orderId
	 * @param int $status
	 * @param int $action
	 */
	public function checkPaymentStatus($orderId, $status, $action) {
		$idsOrder = $this->orderPaymentRepo->loadDataByOrderId($orderId);
		$data     = $this->statusRepo->loadByIdNonSubAction($status, $action);
		if (!$idsOrder->getOrderId()) {
			throw new \Magento\Framework\Webapi\Exception(__('Order ID doesn\'t exist, please make sure again.'));
		}
		$request = [
			'order_id' => $orderId,
			'status' => $status,
			'action' => $action,
		];
		$orderIds      = $idsOrder->getOrderId();
		$stat          = 1; // From OMS Status request payment
		$act           = 2 || 99; // From OMS Action request payment
		$paymentStatus = $idsOrder->getPaymentStatus();

		if ($status == $stat && $action == $act) {
			if ($action == 2 && $paymentStatus == 'processing') {
				$result = [
					'Payment status in Order Id : ' . $orderIds . ' has been Paid / Authorized.',
				];
			} elseif ($action == 99 && $paymentStatus == 'canceled') {
				$result = [
					'Payment status in Order Id : ' . $orderIds . ' has been Canceled.',
				];
			} else {
				throw new \Magento\Framework\Webapi\Exception(__('Payment still pending'), 400);
			}
		} else {
			throw new \Magento\Framework\Webapi\Exception(__('Please re-check status and action sequence before submit'), 400);
		}
		if ($orderIds) {
			$model = $this->historyInterface->create();
			$model->setReferenceNumber($idsOrder->getReferenceNumber());
			$model->setOrderId($orderIds);
			$model->setFeStatusNo($data->getFeStatusNo());
			$model->setFeSubStatusNo($data->getFeSubStatusNo());

			$saveHistory = $this->statusRepo->saveHistory($model);
		}
		return $result;
	}
}
