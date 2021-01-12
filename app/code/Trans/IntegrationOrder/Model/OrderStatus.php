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
	 * @var \Magento\Framework\App\ResourceConnection
	 */
	protected $resource;

	/**
	 * @var \Magento\Sales\Model\OrderRepository
	 */
	protected $orderRepository;

	/**
	 * @var \Magento\Sales\Model\Service\InvoiceService
	 */
	protected $invoiceService;

	/**
	 * @var \Magento\Framework\DB\Transaction
	 */
	protected $transaction;

	/**
	 * @var \Magento\Framework\Registry
	 */
	protected $registry;

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
	 * @param \Magento\Framework\App\ResourceConnection $resource
	 * @param \Magento\Framework\Event\ManagerInterface $eventManager
	 * @param \Magento\Sales\Model\Convert\OrderFactory $orderConvert
	 * @param \Magento\Sales\Model\OrderRepository $orderRepository
	 * @param \Magento\Framework\HTTP\Client\Curl $curl
	 * @param \Magento\InventoryApi\Api\GetSourceItemsBySkuInterface $sourceItemsBySku
	 * @param \Magento\Shipping\Model\ShipmentNotifierFactory $shipmentNotify
	 * @param \Magento\Sales\Api\Data\OrderInterface $orderInterfaceFactory
	 * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepoInterface
	 * @param \Magento\Sales\Api\Data\ShipmentTrackInterfaceFactory $trackInterface
	 * @param \Magento\Sales\Api\Data\OrderItemInterfaceFactory $orderItemFactory
	 * @param \Magento\Sales\Model\Service\InvoiceService $invoiceService
	 * @param \Magento\Framework\DB\Transaction $transaction
	 * @param \Magento\Framework\Registry $registry
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
		\Magento\Framework\App\ResourceConnection $resource,
		\Magento\Framework\Event\ManagerInterface $eventManager,
		\Magento\Sales\Model\Order $order,
		\Magento\Sales\Model\OrderRepository $orderRepository,
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
		\Magento\Backend\App\Action\Context $context,
		\Magento\Sales\Model\Order\Email\Sender\CreditmemoSender $creditmemoSender,
		\Magento\Sales\Controller\Adminhtml\Order\CreditmemoLoader $creditmemoLoader,
		\Magento\Sales\Api\CreditmemoManagementInterfaceFactory $creditMemoInterfaceFactory,
		\Magento\Sales\Model\Service\CreditmemoService $creditmemoService,
		\Magento\Sales\Model\Order\CreditmemoFactory $creditmemoFactory,
		\Magento\Sales\Model\RefundOrder $refundOrder,
		\Magento\Sales\Model\Order\Creditmemo\ItemCreationFactory $itemCreationFactory,
		\Magento\Sales\Model\Order\Invoice $invoice,
		\Magento\Sales\Model\Service\InvoiceService $invoiceService,
		\Magento\Sales\Model\Order\InvoiceRepositoryFactory $invoiceFactory,
		\Magento\Framework\DB\Transaction $transaction,
		\Magento\Framework\Registry $registry,
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
		\Trans\IntegrationOrder\Api\Data\RefundInterfaceFactory $refundInterface,
		\Trans\Mepay\Helper\Payment\Transaction $transactionMegaHelper
	) {
		$this->eventManager                       = $eventManager;
		$this->order                              = $order;
		$this->orderRepository                    = $orderRepository;
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
		$this->creditmemoSender                   = $creditmemoSender;
		$this->creditmemoLoader                   = $creditmemoLoader;
		$this->creditMemoInterfaceFactory         = $creditMemoInterfaceFactory;
		$this->creditmemoService                  = $creditmemoService;
		$this->creditmemoFactory                  = $creditmemoFactory;
		$this->refundOrder                        = $refundOrder;
		$this->itemCreationFactory                = $itemCreationFactory;
		$this->invoice                            = $invoice;
		$this->invoiceFactory                     = $invoiceFactory;
		$this->transactionMegaHelper              = $transactionMegaHelper;
		$this->invoiceService                     = $invoiceService;
		$this->resource                           = $resource;
		$this->transaction                        = $transaction;
		$this->registry                           = $registry;

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
		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/order-status.log');
		$logger = new \Zend\Log\Logger();
		$logger->addWriter($writer);
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
		$logger->info('Trans\IntegrationOrder\Model\OrderStatus' . '. Order-Id: ' . $entityIdSalesOrder . '. Before Status: ' . $loadDataOrder->getStatus());
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
		if ($loadDataOrder && $data->getFeStatusNo() == $configStatus->getNumberFailedDelivery()) {
			$loadDataOrder->setStatus($configStatus->getFailedDeliveryStatus());
			$saveDataToStatusHistory->setParentId($entityIdSalesOrder);
			$saveDataToStatusHistory->setStatus($configStatus->getFailedDeliveryStatus());
			$saveDataToStatusHistory->setComment($data->getFeStatus() . $data->getFeSubStatus());
			$saveDataToStatusHistory->setEntityName('order');
		}

		$this->orderRepoInterface->save($loadDataOrder);
		$this->orderStatusHistoryRepoInterface->save($saveDataToStatusHistory);
		$logger->info('Trans\IntegrationOrder\Model\OrderStatus' . '. Order-Id: ' . $entityIdSalesOrder . '. After Status: ' . $loadDataOrder->getStatus());
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
		if ($loadDataOrder && $data->getFeStatusNo() == $configStatus->getNumberFailedDelivery()) {
			$loadDataOrder->setStatus($configStatus->getFailedDeliveryStatus());
			$saveDataToStatusHistory->setParentId($entityIdSalesOrder);
			$saveDataToStatusHistory->setStatus($configStatus->getFailedDeliveryStatus());
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
		$this->loggerOrder->info(__FUNCTION__ . ' start');

		$this->loggerOrder->info('$orderId = ' . $orderId);
		$this->loggerOrder->info('$status = ' . $status);
		$this->loggerOrder->info('$action = ' . $action);
		$this->loggerOrder->info('$subAction = ' . $subAction);
		$this->loggerOrder->info('$orderItems = ' . print_r($orderItems, true));

		$childOrder     = $this->order->loadByAttribute('reference_order_id', $orderId);
		$orderData      = $this->statusRepo->loadByOrderIds($orderId);
		$refNumber      = $orderData->getReferenceNumber();
		$parentIdFetch  = $this->transactionMegaHelper->getSalesOrderArrayParent($refNumber);
		$parentEntityId = $parentIdFetch['entity_id'];
		$data           = $this->statusRepo->loadByIdSubAction($status, $action, $subAction);

		if (!$orderData->getOrderId()) {
			throw new \Magento\Framework\Webapi\Exception(__('Order ID doesn\'t exist, please make sure again.'));
		}

		$orderItem    = [];
		$refunded     = [];
		$skusRefunded = [];
		foreach ($orderItems as $itemData) {
			$allocatedQty = $itemData['quantity_allocated'];

			$orderItem[] = $itemData;

			if ($itemData['quantity_allocated'] < $itemData['quantity']) {
				$refunded[$itemData['sku']] = $itemData;
				$skusRefunded[]             = $itemData['sku'];
			}
		}

		$request = array(
			'order_id' => $orderId,
			'status' => $status,
			'action' => $action,
			'sub_action' => $subAction,
			'order_items' => $orderItem,
		);

		$orderIds = $orderData->getOrderId();
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

		foreach ($request['order_items'] as $allocatedItems) {
			$itemOrders = $this->statusRepo->loadByOrderId($orderId);
			foreach ($itemOrders as $itemOrder) {
				if ($itemOrder->getSKU() === $allocatedItems['sku']) {
					$itemOrder->setQtyAllocated($allocatedItems['quantity_allocated']);
					$itemOrder->setItemStatus($allocatedItems['item_status']);
				}
				$itemOrderSave = $this->statusRepo->saveItem($itemOrder);
			}
			// if ($itemOrder->getQty() != $qtyOrdered) {
			//  throw new \Magento\Framework\Webapi\Exception(__('Invalid quantity order. Please checking again.'), 400);
			// }
			if ($allocatedItems['quantity_allocated'] > $itemOrder->getQty()) {
				throw new \Magento\Framework\Webapi\Exception(__('Quantity allocated is greater than quantity order. Please check again.'), 400);
			}
		}

		$configStatus               = $this->orderConfig;
		$loadDataOrder              = $this->statusRepo->loadDataByRefOrderId($orderId);
		$entityIdSalesOrder         = $loadDataOrder->getEntityId();
		$loadDataOrderStatusHistory = $this->statusRepo->loadDataByParentOrderId($entityIdSalesOrder);
		$saveDataToStatusHistory    = $this->orderStatusHistoryInterfaceFactory->create();

		$this->loggerOrder->info('=========== prepare order history ===========');

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
		if ($loadDataOrder && $data->getFeStatusNo() == $configStatus->getNumberFailedDelivery()) {
			$loadDataOrder->setStatus($configStatus->getFailedDeliveryStatus());
			$saveDataToStatusHistory->setParentId($entityIdSalesOrder);
			$saveDataToStatusHistory->setStatus($configStatus->getFailedDeliveryStatus());
			$saveDataToStatusHistory->setComment($data->getFeStatus() . $data->getFeSubStatus());
			$saveDataToStatusHistory->setEntityName('order');
		}
		$this->loggerOrder->info('=========== prepare order history end ===========');

		$this->loggerOrder->info('=========== update state & status sales order start ===========');
		$this->orderRepoInterface->save($loadDataOrder);
		$this->loggerOrder->info('=========== update state & status sales order end ===========');

		$this->loggerOrder->info('=========== save data order history magento start ===========');
		$this->orderStatusHistoryRepoInterface->save($saveDataToStatusHistory);
		$this->loggerOrder->info('=========== save data order history magento end ===========');

		if ($status == 2 && $action == 2 && $subAction == 7 || $status == 2 && $action == 99 && $subAction == 0) {
			if (!empty($skusRefunded) and !empty($refunded)) {
				$salesOrderItemsChild = $this->getSalesOrderItems($skusRefunded, $childOrder->getEntityId());
				$salesOrderItems      = $this->getSalesOrderItems($skusRefunded, $parentEntityId);

				$itemIds = [];
				foreach ($salesOrderItems as $item) {
					$dataItem['item_id'] = $item['item_id'];
					$dataItem['qty']     = $refunded[$item['sku']]['quantity'] - $refunded[$item['sku']]['quantity_allocated'];
					$itemIds[]           = $dataItem;
				}

				$itemIdsChild = [];
				foreach ($salesOrderItemsChild as $item) {
					$dataItem['item_id'] = $item['item_id'];
					$dataItem['qty']     = $refunded[$item['sku']]['quantity'] - $refunded[$item['sku']]['quantity_allocated'];
					$itemIdsChild[]      = $dataItem;
				}

				if (!empty($itemIds)) {
					$this->loggerOrder->info('===== Credit Memo ===== Start');

					try {
						$this->createInvoice($childOrder); //invoice for child order
					} catch (\Exception $e) {
						$this->loggerOrder->info('invoice child order fail : ' . $e->getMessage());
					}

					try {
						$this->loggerOrder->info('child credit memo');
						$childmemo         = $this->creditMemos($childOrder->getId(), $itemIdsChild);
						$childCreditEncode = json_encode($childmemo);
						$this->loggerOrder->info('child $creditEncode : ' . $childCreditEncode);
					} catch (\Exception $e) {
						$this->loggerOrder->info('child $creditEncode : ' . $e->getMessage());
					}

					try {
						$this->loggerOrder->info('parent credit memo');
						$parentOrder  = $this->orderRepository->get($parentEntityId);
						$credit       = $this->creditMemos($parentEntityId, $itemIds, $orderId);
						$creditEncode = json_encode($credit);
						$this->loggerOrder->info('parent $creditEncode : ' . $creditEncode);
					} catch (\Exception $e) {
						$this->loggerOrder->info('parent credit memo : ' . $e->getMessage());
					}

					$this->loggerOrder->info('===== Credit Memo ===== End');
				}
			}
		}

		if ($orderIds) {
			$this->loggerOrder->info('=========== save data order history oms start ===========');
			$model = $this->historyInterface->create();
			$model->setReferenceNumber($orderData->getReferenceNumber());
			$model->setOrderId($orderIds);
			$model->setFeStatusNo($data->getFeStatusNo());
			$model->setFeSubStatusNo($data->getFeSubStatusNo());

			$this->statusRepo->saveHistory($model);
			$this->loggerOrder->info('=========== save data order history oms end ===========');
		}

		$this->loggerOrder->info('=========== custom capture refund start ===========');
		/* =================== START CAPTURE REFUND =======================*/

		/**
		 * preparing data for refund PG
		 */
		$paymentMethod     = $loadDataOrder->getPayment()->getMethod();
		$channelId         = $this->configPg->getPaymentChannelId($paymentMethod);
		$serviceCode       = $this->configPg->getPaymentChannelRefundServicecode($paymentMethod);
		$urlPg             = $this->configPg->getApiBaseUrl($paymentMethod) . '/' . Config::REFUND_POST_URL;
		$loadItemByOrderId = $this->statusRepo->loadByOrderId($orderId);

		$trxAmount = (int) $loadDataOrder->getGrandTotal();
		/** Load Item By Order Id */
		$fetchData = $this->statusRepo->loadItemByOrderIds($entityIdSalesOrder);
		$itemId    = $fetchData->getItemId();

		/**
		 * trigger capture - refund by payment method
		 */
		$matrixAdjusmentAmount = 0;
		if ($status == 2 && $action == 2 && $subAction == 7 || $status == 2 && $action == 99 && $subAction == 0) {
			if ($paymentMethod === 'sprint_bca_va' || $paymentMethod === 'sprint_permata_va' || $paymentMethod === 'trans_mepay_va') {
				$this->loggerOrder->info('=========== refund VA start ===========');
				foreach ($loadItemByOrderId as $itemOrder) {
					$paidPriceOrder = $itemOrder->getPaidPrice();
					$qtyOrder       = $itemOrder->getQty();
					$qtyAllocated   = $itemOrder->getQtyAllocated();
					$amount         = ($paidPriceOrder / $qtyOrder) * ($qtyOrder - $qtyAllocated);

					$matrixAdjusmentAmount = $matrixAdjusmentAmount + $amount;

					// $this->loggerOrder->info('===== Credit Memo ===== Start');

					// $credit       = $this->creditMemos($parentEntityId, $itemId, $qtyAllocated);
					// $creditEncode = json_encode($credit);

					// $this->loggerOrder->info('$creditEncode : ' . $creditEncode);
					// $this->loggerOrder->info('===== Credit Memo ===== End');

				}

				/* update quantity adjusment */
				$url            = $this->orderConfig->getOmsBaseUrl() . $this->orderConfig->getOmsPaymentStatusApi();
				$headers        = $this->getHeader();
				$dataAdjustment = array(
					'reference_number' => $refNumber,
					'order_id' => $orderId,
					'status' => 3,
					'amount_adjustment' => ceil($matrixAdjusmentAmount),

				);
				$dataJson = json_encode($dataAdjustment);
				$this->loggerOrder->info($dataJson);

				$this->loggerOrder->info('=========== refund VA start ===========');

				$this->curl->setOption(CURLOPT_RETURNTRANSFER, true);
				$this->curl->setOption(CURLOPT_CUSTOMREQUEST, 'PATCH');
				$this->curl->setHeaders($headers);
				$this->curl->post($url, $dataJson);
				$responseOrder = $this->curl->getBody();
				$this->loggerOrder->info('$url : ' . $url);
				$this->loggerOrder->info('$headers : ' . json_encode($headers));
				$this->loggerOrder->info('$responseOrder : ' . $responseOrder);
				$objOrder = json_decode($responseOrder);
				$this->loggerOrder->info('Body: ' . $dataJson . '. Response: ' . $responseOrder);
				$json_string = stripcslashes($responseOrder);
				// if ($objOrder->code == 200) {
				// 	return $result;
				// }
				$this->loggerOrder->info('response = ' . $json_string);
				$this->loggerOrder->info('=========== refund VA end ===========');
			}
			/* End Non CC*/

			if ($paymentMethod === 'sprint_mega_cc' || $paymentMethod === 'sprint_allbankfull_cc' || $paymentMethod === 'sprint_mega_debit' || $paymentMethod === 'trans_mepay_cc') {
				$this->loggerOrder->info('=========== refund CC DEBIT start ===========');
				/**
				 * prepare data array refund send to PG
				 */
				$refTrxNumber = RefundInterface::PREFIX_REFUND . $this->helperData->genRefNumber() . $orderId;
				foreach ($loadItemByOrderId as $itemOrder) {
					$paidPriceOrder = $itemOrder->getPaidPrice();
					$qtyOrder       = $itemOrder->getQty();
					$qtyAllocated   = $itemOrder->getQtyAllocated();
					$amount         = ($paidPriceOrder / $qtyOrder) * ($qtyOrder - $qtyAllocated);

					$matrixAdjusmentAmount = $matrixAdjusmentAmount + $amount;
				}

				if ($paymentMethod === 'trans_mepay_cc') {
					$this->eventManager->dispatch(
						'refund_with_mega_payment',
						[
							'order_id' => $orderId,
							'reference_number' => $refNumber,
							'amount' => $trxAmount,
							'new_amount' => $trxAmount - $matrixAdjusmentAmount,
							'adjustment_amount' => $matrixAdjusmentAmount,
						]
					);
				}

				/* update quantity adjusment */
				$url            = $this->orderConfig->getOmsBaseUrl() . $this->orderConfig->getOmsPaymentStatusApi();
				$headers        = $this->getHeader();
				$dataAdjustment = array(
					'reference_number' => $refNumber,
					'order_id' => $orderId,
					'status' => 3,
					'amount_adjustment' => ceil($matrixAdjusmentAmount),

				);
				$dataJson = json_encode($dataAdjustment);
				$this->loggerOrder->info($dataJson);

				$this->loggerOrder->info('=========== refund CC DEBIT start ===========');

				$this->curl->setOption(CURLOPT_RETURNTRANSFER, true);
				$this->curl->setOption(CURLOPT_CUSTOMREQUEST, 'PATCH');
				$this->curl->setHeaders($headers);
				$this->curl->post($url, $dataJson);
				$responseOrder = $this->curl->getBody();
				$this->loggerOrder->info('$url = ' . $url);
				$this->loggerOrder->info('$headers : ' . json_encode($headers));
				$this->loggerOrder->info('$responseOrder : ' . $responseOrder);
				$objOrder = json_decode($responseOrder);
				$this->loggerOrder->info('Body: ' . $dataJson . '. Response: ' . $responseOrder);
				// $json_string = stripcslashes($responseOrder);
				// if ($objOrder->code == 200) {
				// 	return json_decode($responseOrder, true);
				// }

				if (!empty($skusRefunded) and !empty($refunded)) {
					/* save to table sales_order_item */
					if ($fetchData->getSku()) {
						$saveOrderItem = $this->orderItemFactory->create();
					}
					// $fetchData->setQtyRefunded((float) $itemData['quantity_allocated']);
					// $this->orderItemRepository->save($fetchData);

					foreach ($refunded as $sku => $refundedItem) {
						try {
							/* save to table integration_oms_refund */
							$saveRefundData = $this->refundInterface->create();
							$saveRefundData->setOrderRefNumber($orderData->getReferenceNumber());
							$saveRefundData->setRefundTrxNumber($refTrxNumber);
							$saveRefundData->setOrderId($orderId);
							// $saveRefundData->setSku($item['sku']);
							$saveRefundData->setSku($sku);
							$saveRefundData->setQtyRefund($refundedItem['quantity_allocated']);
							$saveRefundData->setAmountRefundOrder($matrixAdjusmentAmount);

							$this->refundRepository->save($saveRefundData);
						} catch (\Exception $e) {
							continue;
						}
					}
				}

				$this->loggerOrder->info('=========== refund CC DEBIT end ===========');
			}
		}

		$this->loggerOrder->info(__FUNCTION__ . ' end');
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
		$this->loggerOrder->info(__FUNCTION__ . ' start');
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
				$result['code'] = 200;
				$result['body'] = [
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
				$result['code'] = 200;
				$result['body'] = [
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
			$this->loggerOrder->info('=========== prepare salesorder history Pickup ===========');
			$pickUp = $loadDataOrder->setStatus($configStatus->getPickupByCustomerStatus());
			$saveDataToStatusHistory->setParentId($entityIdSalesOrder);
			$saveDataToStatusHistory->setStatus($configStatus->getPickupByCustomerStatus());
			$saveDataToStatusHistory->setComment($loadStatusPickByCust->getFeStatus() . $loadStatusPickByCust->getFeSubStatus());
			$saveDataToStatusHistory->setEntityName('order');

			try {
				$this->orderRepoInterface->save($pickUp);
			} catch (Exception $e) {
				sleep(30);

				try {
					$this->orderRepoInterface->save($pickUp);
				} catch (\Exception $e) {
					$this->loggerOrder->info('response AWB Error Pickup= ' . $e);
				}
			}
			$this->orderStatusHistoryRepoInterface->save($saveDataToStatusHistory);
			$this->loggerOrder->info('=========== prepare salesorder history end Pickup ===========');
		}

		if ($orderData->getOrderType() === self::DELIVERY) {
			$this->loggerOrder->info('=========== prepare salesorder history Delivery start ===========');
			$deliveries = $loadDataOrder->setStatus($configStatus->getInProcessWaitingPickupStatus());
			$saveDataToStatusHistory->setParentId($entityIdSalesOrder);
			$saveDataToStatusHistory->setStatus($configStatus->getInProcessWaitingPickupStatus());
			$saveDataToStatusHistory->setComment($loadStatusDelivery->getFeStatus() . $loadStatusDelivery->getFeSubStatus());
			$saveDataToStatusHistory->setEntityName('order');
			try {
				$this->orderRepoInterface->save($deliveries);
			} catch (Exception $e) {
				sleep(30);

				try {
					$this->orderRepoInterface->save($deliveries);
				} catch (\Exception $e) {
					$this->loggerOrder->info('response AWB Error Delivery= ' . $e);

				}
			}
			$this->orderStatusHistoryRepoInterface->save($saveDataToStatusHistory);
			$this->loggerOrder->info('=========== prepare salesorder history Delivery end ===========');

		}

		$historyOrderUpdated = false;
		$this->loggerOrder->info('=========== save data order history oms start ===========');
		if (!$historyOrder->getOrderId() && $orderData->getOrderType() === self::DELIVERY) {
			$historyOrder->setOrderId($request['order_id']);
			$historyOrder->setReferenceNumber($idsOrder->getReferenceNumber());
			$historyOrder->setAwbNumber($request['logistic_number']);
			$historyOrder->setLogCourierNo($request['logistic_courier']);
			$historyOrder->setFeStatusNo($loadStatusDelivery->getFeStatusNo());
			$historyOrder->setFeSubStatusNo($loadStatusDelivery->getFeSubStatusNo());
			$historyOrder->setUpdatedAt($orderDate);
			$historyOrderUpdated = true;

		} elseif (!$historyOrder->getOrderId() && $orderData->getOrderType() === self::PICK_UP_BY_CUSTOMER) {
			$historyOrder->setOrderId($request['order_id']);
			$historyOrder->setReferenceNumber($idsOrder->getReferenceNumber());
			$historyOrder->setAwbNumber($request['order_id']);
			$historyOrder->setLogCourierNo(self::LOGISTIC_COURIER);
			$historyOrder->setFeStatusNo($loadStatusPickByCust->getFeStatusNo());
			$historyOrder->setFeSubStatusNo($loadStatusPickByCust->getFeSubStatusNo());
			$historyOrder->setUpdatedAt($orderDate);
			$historyOrderUpdated = true;
		}
		if ($historyOrderUpdated) {
			$this->statusRepo->saveHistory($historyOrder);
		}
		$this->loggerOrder->info('=========== save data order history oms end ===========');

		/* Updating data to table shipment */
		$getEntityIds   = $loadDataOrder->getEntityId(); // entity id from sales_order
		$getCustomerIds = $loadDataOrder->getCustomerId(); // customer id from sales_order

		// Load the order increment ID
		$this->loggerOrder->info('=========== Do Shipment Start ===========');
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
		$this->loggerOrder->info('=========== Do Shipment End ===========');

		$this->loggerOrder->info(__FUNCTION__ . ' end');
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

	/**
	 * Prepare store data for Credit Memo
	 *
	 * @param Magento\Sales\Model\Order $order
	 * @param string $orderItemId
	 * @param string|null $refOrderId
	 */
	protected function creditMemos($orderId, $orderItemIds, $refOrderId = null) {
		$this->loggerOrder->info(__FUNCTION__ . ' start.');
		$order = $this->orderRepository->get($orderId);

		if ($order instanceof \Magento\Sales\Model\Order == false) {
			return false;
		}

		if ($order->getData('is_parent')) {
			$this->updateOrderStatus($orderId, 'in_process', 'processing');
		}

		$creditMemoData                        = [];
		$creditMemoData['do_offline']          = 1;
		$creditMemoData['adjustment_positive'] = 0;
		$creditMemoData['adjustment_negative'] = 0;
		$creditMemoData['comment_text']        = 'Refund';
		$creditMemoData['send_email']          = 1;

		$totalQty = 0;
		foreach ($orderItemIds as $item) {
			$itemToCredit[$item['item_id']] = ['qty' => $item['qty']];
			$qty                            = $item['qty'];
			$totalQty += $qty;
		}

		$orderItems    = $order->getAllItems();
		$totalQtyOrder = 0;

		foreach ($orderItems as $orderItem) {
			$totalQtyOrder = $totalQtyOrder + $orderItem->getQtyOrdered();
		}

		if ($totalQtyOrder != $totalQty) {
			$creditMemoData['shipping_amount'] = 0;
		}

		if ($order->getData('is_parent') && $refOrderId != null) {
			$childOrder = $this->order->loadByAttribute('reference_order_id', $refOrderId);

			$childOrderItems    = $childOrder->getAllItems();
			$totalQtyChildOrder = 0;

			foreach ($childOrderItems as $childOrderItem) {
				$totalQtyChildOrder = $totalQtyChildOrder + $childOrderItem->getQtyOrdered();
			}

			if ($totalQty == $totalQtyChildOrder) {
				$creditMemoData['shipping_amount'] = $childOrder->getData('shipping_amount');
			}
		}

		$creditMemoData['items'] = $itemToCredit;

		$this->loggerOrder->info('Credit memo param = ' . print_r($creditMemoData, true));

		try {
			$this->creditmemoLoader->setOrderId($order->getId()); //pass order id
			$this->creditmemoLoader->setCreditmemo($creditMemoData);

			$this->loggerOrder->info('$orderId = ' . $order->getId());

			$creditmemo = $this->creditmemoLoader->load();

			if (!$creditmemo) {
				$this->loggerOrder->info('Credit memo fail to load.');
				$this->registry->unregister('current_creditmemo');
				return $creditMemoData;
			}

			$creditmemo->setTotalQty($totalQty);

			if ($creditmemo) {
				if (!$creditmemo->isValidGrandTotal()) {
					throw new \Magento\Framework\Exception\LocalizedException(
						__('The credit memo\'s total must be positive.')
					);
				}

				if (!empty($creditMemoData['comment_text'])) {
					$creditmemo->addComment(
						$creditMemoData['comment_text'],
						isset($creditMemoData['comment_customer_notify']),
						isset($creditMemoData['is_visible_on_front'])
					);

					$creditmemo->setCustomerNote($creditMemoData['comment_text']);
					$creditmemo->setCustomerNoteNotify(isset($creditMemoData['comment_customer_notify']));
				}

				$creditmemo->getOrder()->setCustomerNoteNotify(!empty($creditMemoData['send_email']));
				$creditmemo->getOrder()->setCustomerNoteNotify(!empty($creditMemoData['send_email']));

				// $creditmemo->setInvoice($invoiceobj);

				$creditmemoManagement = $this->creditMemoInterfaceFactory->create();
				$creditmemoManagement->refund($creditmemo, (bool) $creditMemoData['do_offline']);

				if (!empty($creditMemoData['send_email'])) {
					$this->creditmemoSender->send($creditmemo);
				}
				$this->loggerOrder->info('You created the credit memo.');
			}

			$this->checkOrderItemStatus($order);
		} catch (\Exception $e) {
			$this->loggerOrder->info('Credit memo check = ' . $e->getMessage());
		}

		$this->registry->unregister('current_creditmemo');
		$this->loggerOrder->info(__FUNCTION__ . ' end.');
		return $creditMemoData;
	}

	/**
	 * Save invoice
	 *
	 * @param \Magento\Sales\Api\Data\OrderInterface $order
	 * @return \Magento\Sales\Model\Order\Invoice|bool
	 */
	protected function createInvoice($order) {
		$this->loggerOrder->info('****** start create invoice ******');
		if ($order instanceof \Magento\Sales\Api\Data\OrderInterface) {
			try {
				$invoiceItem = [];
				foreach ($order->getAllItems() as $item) {
					$invoiceItem[$item->getId()] = (int) $item->getQtyOrdered();
				}

				$items = $invoiceItem;

				$invoice = $this->invoiceService->prepareInvoice($order, $items);

				$invoice->setShippingAmount($order->getData('shipping_amount'));
				$invoice->setSubtotal($order->getData('subtotal'));
				$invoice->setBaseSubtotal($order->getData('base_subtotal'));
				$invoice->setGrandTotal($order->getData('grand_total'));
				$invoice->setBaseGrandTotal($order->getData('base_grand_total'));
				$invoice->register();
				$invoice->pay();

				$invoice->getOrder()->setIsInProcess(true);

				$invoice->save();

				$transactionSave = $this->transaction->addObject(
					$invoice
				)->addObject(
					$invoice->getOrder()
				);
				$transactionSave->save();

				/**
				 * Allow forced creditmemo just in case if it wasn't defined before
				 */
				if (!$order->hasForcedCanCreditmemo()) {
					try {
						$order->setTotalPaid($order->getData('grand_total'));
						$order->setBaseTotalPaid($order->getData('grand_total'));

						$order->setForcedCanCreditmemo(true);
						$order->setIsInProcess(true);
						$order->setState(\Magento\Sales\Model\Order::STATE_PROCESSING);
						$order->setStatus('in_process');
						$order->save();
					} catch (\Exception $e) {
						$this->loggerOrder->info($e->getTraceAsString());
					}

					if ($order->getData('grand_total') != null || $order->getData('grand_total') == 0) {
						$this->payOrder($order);
					}
				}

				$this->loggerOrder->info('****** end create invoice ******');
				return $invoice;
			} catch (\Exception $e) {
				$this->loggerOrder->info('Error ' . __FUNCTION__ . ' ' . $e->getMessage());
			} catch (LocalizedException $e) {
				$this->loggerOrder->info('Error ' . __FUNCTION__ . ' ' . $e->getMessage());
			}
		}

		$this->loggerOrder->info('****** end create invoice ******');
		return false;
	}

	/**
	 * get sales order items
	 *
	 * @param array $skus
	 * @param int $orderId
	 */
	protected function getSalesOrderItems($skus, $orderId) {
		$connection = $this->resource->getConnection();
		$table      = $connection->getTableName('sales_order_item');

		$query = $connection->select();
		$query->from(
			$table,
			['*']
		)->where('order_id = ?', $orderId)->where('sku in (?)', $skus);

		$collection = $connection->fetchAll($query);

		return $collection;
	}

	/**
	 * get sales order item
	 *
	 * @param array $skus
	 * @param int $orderId
	 */
	protected function getSalesOrderItem($sku, $orderId) {
		$connection = $this->resource->getConnection();
		$table      = $connection->getTableName('sales_order_item');

		$query = $connection->select();
		$query->from(
			$table,
			['*']
		)->where('order_id = ?', $orderId)->where('sku = ?', $sku);

		$collection = $connection->fetchRow($query);

		return $collection;
	}

	/**
	 * get sales order item
	 *
	 * @param int $orderId
	 * @return bool
	 */
	protected function checkInvoiceData($orderId) {
		$connection = $this->resource->getConnection();
		$table      = $connection->getTableName('sales_invoice');

		$query = $connection->select();
		$query->from(
			$table,
			['*']
		)->where('order_id = ?', $orderId);

		$collection = $connection->fetchRow($query);

		if ($collection) {
			return true;
		}

		return false;
	}

	/**
	 * update sales order data
	 *
	 * @param \Magento\Sales\Api\Data\OrderInterface $order
	 * @return void
	 */
	protected function payOrder($order) {
		$connection = $this->resource->getConnection();
		$table      = $connection->getTableName('sales_order');

		$data  = ["status" => "in_process", "state" => \Magento\Sales\Model\Order::STATE_PROCESSING, "total_paid" => $order->getData('grand_total'), "base_total_paid" => $order->getData('grand_total')];
		$where = ['entity_id = ?' => $order->getId()];

		$connection->update($table, $data, $where);
	}

	/**
	 * check order item status
	 *
	 * @param \Magento\Sales\Api\Data\Order $order
	 * @return void|bool
	 */
	protected function checkOrderItemStatus($order) {
		$connection = $this->resource->getConnection();
		$table      = $connection->getTableName('sales_order_item');

		$query = $connection->select();
		$query->from(
			$table,
			['*']
		)->where('order_id = ?', $order->getId());

		$collection = $connection->fetchAll($query);
		if ($collection) {
			$ordered  = 0;
			$refunded = 0;
			foreach ($collection as $row) {
				$ordered  = $ordered + (int) $row['qty_ordered'];
				$refunded = $refunded + (int) $row['qty_refunded'];
			}

			$check = $ordered - $refunded;

			$status = 'in_process';
			$state  = 'processing';
			if ($check == 0) {
				$status = 'order_canceled';
				$state  = 'canceled';
			}

			$this->updateOrderStatus($order->getId(), $status, $state);
			$this->updateOrderStatusHistory($order->getId(), $status);
		}
	}

	protected function updateStatuses($order, $status, $state) {
		$refNumber   = $order->getData('reference_number');
		$parentOrder = $this->transactionMegaHelper->getSalesOrderArrayParent($refNumber);

		$this->updateOrderStatus($order->getId(), $status, $state);
		$this->updateOrderStatusHistory($order->getId(), $status);

		if ($parentOrder) {
			$this->updateOrderStatus($parentOrder['entity_id'], $status, $state);
			$this->updateOrderStatusHistory($parentOrder['entity_id'], $status);
		}
	}

	/**
	 * update order status
	 *
	 * @param int $orderId
	 * @param string $status
	 * @param string $state
	 * @return void
	 */
	protected function updateOrderStatus($orderId, $status, $state) {
		$connection = $this->resource->getConnection();
		$table      = $connection->getTableName('sales_order');

		$data  = ["status" => $status, "state" => $state];
		$where = ['entity_id = ?' => $orderId];

		$connection->update($table, $data, $where);
	}

	/**
	 * update order status history
	 *
	 * @param int $orderId
	 * @param string $status
	 * @return void
	 */
	protected function updateOrderStatusHistory($orderId, $status) {
		$connection = $this->resource->getConnection();
		$table      = $connection->getTableName('sales_order_status_history');

		$data  = ["status" => $status];
		$where = ['parent_id = ?' => $orderId, 'entity_name = ?' => 'creditmemo'];

		$connection->update($table, $data, $where);
	}
}
