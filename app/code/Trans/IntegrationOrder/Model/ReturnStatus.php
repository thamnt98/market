<?php
/**
 * @category Trans
 * @package  Trans_IntegrationOrder
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Hadi <ashadi.sejati@ctcorpdigital.co.id>
 * @author   Randy <muhammad.randy@ctcorpdigital.co.id>
 *
 * Copyright © 2020 CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationOrder\Model;

use Magento\Rma\Model\Item\Attribute\Source\Status;
use Trans\IntegrationOrder\Api\Data\IntegrationOrderReturnInterface;
use Trans\IntegrationOrder\Api\ReturnStatusInterface;

/**
 * @api
 * Class ReturnStatus
 */
class ReturnStatus implements ReturnStatusInterface {

	/**
	 * @var \Magento\Framework\HTTP\Client\Curl
	 */
	protected $curl;

	/**
	 * @var \Trans\Core\Helper\Data
	 */
	protected $coreHelper;

	/**
	 * @var \Trans\IntegrationOrder\Helper\Integration
	 */
	protected $integrationHelper;

	/**
	 * @var \Trans\IntegrationOrder\Helper\Data
	 */
	protected $dataHelper;

	/**
	 * @var \Trans\IntegrationOrder\Helper\Config
	 */
	protected $configHelper;

	/**
	 * @var \Trans\IntegrationOrder\Api\Data\IntegrationOrderReturnInterface
	 */
	protected $integrationOrderReturnInterfaceFactory;

	/**
	 * @var \Trans\IntegrationOrder\Api\IntegrationOrderReturnRepositoryInterface
	 */
	protected $integrationOrderReturnRepositoryInterface;

	/**
	 * @param \Magento\Framework\HTTP\Client\Curl $curl
	 * @param \Magento\Rma\Api\Data\RmaInterfaceFactory $rmaInterfaceFactory
	 * @param \Magento\Rma\Api\Data\RmaInterfaceFactory $rmaInterfaceFactory,
	 * @param \Magento\Rma\Api\RmaRepositoryInterface $rmaRepositoryInterface,
	 * @param \Magento\Rma\Api\Data\ItemInterfaceFactory $rmaItemInterfaceFactory,
	 * @param \Trans\Integration\Helper\Curl $curlHelper,
	 * @param \Trans\Core\Helper\Data $coreHelper,
	 * @param \Trans\IntegrationOrder\Helper\Integration $integrationHelper,
	 * @param \Trans\IntegrationOrder\Helper\Data $dataHelper,
	 * @param \Trans\IntegrationOrder\Helper\Config $configHelper,
	 * @param \Trans\IntegrationOrder\Api\IntegrationOrderStatusRepositoryInterface $statusRepositoryInterface,
	 * @param \Trans\IntegrationOrder\Api\Data\IntegrationOrderReturnInterfaceFactory $integrationOrderReturnInterfaceFactory,
	 * @param \Trans\IntegrationOrder\Api\IntegrationOrderReturnRepositoryInterface $integrationOrderReturnRepositoryInterface,
	 * @param \Trans\IntegrationOrder\Api\IntegrationOrderStatusRepositoryInterface $orderStatusRepository
	 */
	public function __construct(
		\Magento\Framework\HTTP\Client\Curl $curl,
		\Magento\Rma\Api\Data\RmaInterfaceFactory $rmaInterfaceFactory,
		\Magento\Rma\Api\RmaRepositoryInterface $rmaRepositoryInterface,
		\Magento\Rma\Api\Data\ItemInterfaceFactory $rmaItemInterfaceFactory,
		\Trans\Integration\Helper\Curl $curlHelper,
		\Trans\Core\Helper\Data $coreHelper,
		\Trans\IntegrationOrder\Helper\Integration $integrationHelper,
		\Trans\IntegrationOrder\Helper\Data $dataHelper,
		\Trans\IntegrationOrder\Helper\Config $configHelper,
		\Trans\IntegrationOrder\Api\IntegrationOrderStatusRepositoryInterface $statusRepositoryInterface,
		\Trans\IntegrationOrder\Api\Data\IntegrationOrderReturnInterfaceFactory $integrationOrderReturnInterfaceFactory,
		\Trans\IntegrationOrder\Api\IntegrationOrderReturnRepositoryInterface $integrationOrderReturnRepositoryInterface,
		\Trans\IntegrationOrder\Api\IntegrationOrderStatusRepositoryInterface $orderStatusRepository
	) {
		$this->curl                                      = $curl;
		$this->curlHelper                                = $curlHelper;
		$this->rmaInterfaceFactory                       = $rmaInterfaceFactory;
		$this->rmaRepositoryInterface                    = $rmaRepositoryInterface;
		$this->rmaItemInterfaceFactory                   = $rmaItemInterfaceFactory;
		$this->coreHelper                                = $coreHelper;
		$this->integrationHelper                         = $integrationHelper;
		$this->dataHelper                                = $dataHelper;
		$this->configHelper                              = $configHelper;
		$this->statusRepositoryInterface                 = $statusRepositoryInterface;
		$this->integrationOrderReturnInterfaceFactory    = $integrationOrderReturnInterfaceFactory;
		$this->integrationOrderReturnRepositoryInterface = $integrationOrderReturnRepositoryInterface;
		$this->orderStatusRepository                     = $orderStatusRepository;
		$this->logger                                    = $dataHelper->getLogger();
	}

	/**
	 * prepare oms header
	 *
	 * @return array
	 */
	protected function getHeader() {
		$token                    = $this->integrationHelper->getToken();
		$headers['dest']          = $this->configHelper->getOmsDest();
		$headers['Content-Type']  = 'application/json';
		$headers['Authorization'] = 'Bearer ' . $token;

		return $headers;
	}

	/**
	 * send Return data to OMS
	 * @param  string $orderId
	 * @param  string $returnStore
	 * @param  mixed $orderItems
	 * @param  string $comment
	 * @param  int $reason
	 * @return mixed
	 */
	public function sendReturn($orderId, $returnStore, $orderItems, $comment, $reason) {
		$dataItems = [];
		foreach ($orderItems as $items) {
			$itemOrder["sku"]             = $items["sku"];
			$itemOrder["item_name"]       = $items["item_name"];
			$itemOrder["quantity"]        = $items["quantity"];
			$itemOrder["item_paid_price"] = $items["item_paid_price"];
			$itemOrder["item_disc_price"] = $items["item_disc_price"];
			$itemOrder["item_sub_total"]  = $items["item_sub_total"];
			$dataItems[]                  = $itemOrder;
		}
		$dataReturn = array(
			'order_id' => $orderId,
			'return_store' => $returnStore,
			'order_items' => $dataItems,
			'comment' => $comment,
			'reason' => $reason,
		);
		try {
			$url      = $this->configHelper->getOmsBaseUrl() . $this->configHelper->getOmsReturnApi();
			$headers  = $this->getHeader();
			$dataJson = json_encode($dataReturn);
			$datas    = $this->curlHelper->post($url, $headers, $dataJson);
			$this->logger->info('$headers Return : ' . json_encode($headers));
			$this->logger->info('$dataJson Return : ' . $dataJson);
			$this->logger->info('$response Return : ' . $datas);
		} catch (\Exception $e) {
			$this->logger->info('Capture Return error = ' . $e->getMessage());
		};

		$format     = 'Y-m-d H:i:s';
		$returnDate = $this->coreHelper->getTimezone()->date(new \DateTime())->format($format);

		/* try to get reference number by reference_order_id */
		$loadByRefOrderId = $this->statusRepositoryInterface->loadDataByRefOrderId($orderId);
		$refNumber        = $loadByRefOrderId->getReferenceNumber();
		$entityId         = $loadByRefOrderId->getEntityId(); // from sales_order
		$storeId          = $loadByRefOrderId->getStoreId(); // from sales_order
		$customerId       = $loadByRefOrderId->getCustomerId(); // from sales_order
		$customerEmail    = $loadByRefOrderId->getCustomerEmail(); // from sales_order

		/* preparing data item to save in table rma_item_magento */
		$dataItem      = $this->integrationOrderReturnRepositoryInterface->loadItemByOrderId($entityId);
		$itemId        = $dataItem->getItemId();
		$productName   = $dataItem->getName();
		$productSku    = $dataItem->getSku();
		$productOption = $dataItem->getProductOption();

		/* save data reason magento_rma_item_entity_varchar */
		$eavAttributeCode = $this->integrationOrderReturnRepositoryInterface->loadAttributeByCode('reason_other');
		$attributeId      = $eavAttributeCode->getAttributeId();
		$frontEndLabel    = $eavAttributeCode->getFrontendLabel();

		/* create array RMA Item */
		$createItemRma = $this->rmaItemInterfaceFactory->create();
		$itemsRma      = array(
			'qty_requested' => $createItemRma->setQtyRequested($items["quantity"]),
			'status' => $createItemRma->setStatus(Status::STATE_PENDING),
			'order_item_id' => $createItemRma->setOrderItemId($itemId),
			'product_name' => $createItemRma->setProductName($productName),
			'product_sku' => $createItemRma->setProductSku($productSku),
			'product_admin_name' => $createItemRma->setProductAdminName($productName),
			'product_admin_sku' => $createItemRma->setProductAdminSku($productSku),
			'product_option' => $createItemRma->setProductOption($productOption),
			'reason' => $createItemRma->setReason($attributeId),
			'resolution' => $createItemRma->setResolution(IntegrationOrderReturnInterface::STATUS_RESOLUTION),
			'condition' => $createItemRma->setCondition(IntegrationOrderReturnInterface::STATUS_ITEM_CONDITION),
		);

		/* save data to custom table integration_oms_return */
		$returnDataSave = $this->integrationOrderReturnInterfaceFactory->create();
		$returnDataSave->setReferenceNumber($refNumber);
		$returnDataSave->setOrderId($orderId);
		$returnDataSave->setSku($items["sku"]);
		$returnDataSave->setStore($returnStore);
		$returnDataSave->setQtyInitiated($items["quantity"]);
		$returnDataSave->setReturnReason($comment);
		$returnDataSave->setItemCondition(IntegrationOrderReturnInterface::STATUS_ITEM_CONDITION);
		$returnDataSave->setResolution(IntegrationOrderReturnInterface::STATUS_RESOLUTION);
		$returnDataSave->setStatus(Status::STATE_PENDING);
		$returnDataSave->setCreatedAt($returnDate);
		$savingData = $this->integrationOrderReturnRepositoryInterface->save($returnDataSave);

		/* save data to rma_magento table */
		$rmaDataSave = $this->rmaInterfaceFactory->create();
		$rmaDataSave->setStatus(Status::STATE_PENDING);
		$rmaDataSave->setDateRequested($returnDate);
		$rmaDataSave->setOrderId($entityId);
		$rmaDataSave->setOrderIncrementId($orderId);
		$rmaDataSave->setStoreId($storeId);
		$rmaDataSave->setCustomerId($customerId);
		$rmaDataSave->setCustomerCustomEmail($customerEmail);
		$rmaDataSave->setItems($itemsRma);
		$savingRma = $this->rmaRepositoryInterface->save($rmaDataSave);

		$response   = $this->curl->getBody();
		$jsonString = stripcslashes($response);

		return json_decode($jsonString, true);
	}

	/**
	 * From Oms initiate update return to magento
	 * @param  string $returnId
	 * @param  string $returnStore
	 * @param  int $status
	 * @param  int $action
	 * @param  mixed $orderItems
	 * @return mixed
	 */
	public function returnInitiate($returnId, $returnStore, $status, $action, $orderItems) {
		$idsOrder = $this->orderStatusRepository->loadDataByRefOrderId($returnId);
		if (!$idsOrder->getReferenceOrderId()) {
			throw new \Magento\Framework\Webapi\Exception(__('Order ID doesn\'t exist, please make sure again.'));
		}
		$orderItem = [];
		foreach ($orderItems as $itemData) {
			$item['sku']                = $itemData['sku'];
			$item['sku_basic']          = $itemData['sku_basic'];
			$item['quantity']           = $itemData['quantity'];
			$item['quantity_allocated'] = $itemData['quantity_allocated'];
			$item['comment']            = $itemData['comment'];
			$orderItem[]                = $item;
		}
		$request = [
			'return_id' => $returnId,
			'return_store' => $returnStore,
			'status' => $status,
			'action' => $action,
			'order_items' => $orderItem,
		];

		$orderIds = $idsOrder->getReferenceOrderId();
		$stat     = IntegrationOrderReturnInterface::STATUS_PROGRESS;
		$act      = IntegrationOrderReturnInterface::ACTION_PROGRESS;

		if ($status == $stat && $action == $act) {
			$result = [
				"response" => "code : 200",
				'return_id' => "Return Id : " . $request['return_id'],
				'message' => "Return Initiated : Return Request is being progressed by CS",
			];
		} else {
			throw new \Magento\Framework\Webapi\Exception(__('Please re-check status and action sequence before submit'), 400);
		}

		$format           = 'Y-m-d H:i:s';
		$returnUpdateDate = $this->coreHelper->getTimezone()->date(new \DateTime())->format($format);

		/* fetch data from rma_magento table */
		$loadDataRmaByOrderId = $this->integrationOrderReturnRepositoryInterface->loadRmaByOrderId($returnId);
		$rmaEntityId          = $loadDataRmaByOrderId->getEntityId();

		/* fetch data from magento_rma_item_entity table */
		$updateItemRma   = $this->integrationOrderReturnRepositoryInterface->loadRmaItemByEntityId($rmaEntityId);
		$rmaEntityIdItem = $updateItemRma->getRmaEntityId();
		if ($rmaEntityIdItem) {
			$arrayUpdateItem = array(
				'qty_authorized' => $updateItemRma->setQtyAuthorized($itemData["quantity_allocated"]),
			);

		}
		/* update item to table magento_rma_item_entity*/
		$loadDataRmaByOrderId->setItems($arrayUpdateItem);
		$updateRma = $this->rmaRepositoryInterface->save($loadDataRmaByOrderId);

		/* update item to table integration_oms_return*/
		$updateReturnData = $this->integrationOrderReturnRepositoryInterface->loadDataReturnByOrderid($returnId);
		if ($returnId) {
			$updateReturnData->setQtyInprogress($itemData['quantity_allocated']);
			$updateReturnData->setStatus(Status::STATE_AUTHORIZED);
			$updateReturnData->setUpdatedAt($returnUpdateDate);

			$saveUpdateReturn = $this->integrationOrderReturnRepositoryInterface->save($updateReturnData);
		} else {
			throw new \Magento\Framework\Webapi\Exception(__('Return not initiated'), 400);
		}
		return $result;
	}

	/**
	 * Updated return from in-progress to approved or rejected
	 * @param  string $returnId
	 * @param  int $status
	 * @param  int $action
	 * @param  int $subAction
	 * @param  mixed $orderItems
	 * @return mixed
	 */
	public function returnProgress($returnId, $status, $action, $subAction, $orderItems) {
		$idsOrder = $this->orderStatusRepository->loadDataByRefOrderId($returnId);
		if (!$idsOrder->getReferenceOrderId()) {
			throw new \Magento\Framework\Webapi\Exception(__('Order ID doesn\'t exist, please make sure again.'));
		}
		$orderItem = [];
		foreach ($orderItems as $itemData) {
			$item['sku']                = $itemData['sku'];
			$item['sku_basic']          = $itemData['sku_basic'];
			$item['quantity']           = $itemData['quantity'];
			$item['quantity_allocated'] = $itemData['quantity_allocated'];
			$item['comment']            = $itemData['comment'];
			$orderItem[]                = $item;
		}
		$request = [
			'return_id' => $returnId,
			'status' => $status,
			'action' => $action,
			'sub_action' => $subAction,
			'order_items' => $orderItem,
		];
		$orderIds       = $idsOrder->getReferenceOrderId();
		$stat           = IntegrationOrderReturnInterface::STATUS_APPROVED_AND_REJECTED;
		$act            = IntegrationOrderReturnInterface::ACTION_APPROVED_AND_REJECTED;
		$subActApproved = IntegrationOrderReturnInterface::SUBACTION_APPROVED;
		$subActRejected = IntegrationOrderReturnInterface::SUBACTION_REJECTED;

		if ($status == $stat && $action == $act && $subAction == $subActApproved) {
			$result = [
				"response" => "code : 200",
				'return_id' => "Return Id : " . $request['return_id'],
				'message' => "Return Is Approved : Return Request is approved by Transmart",
			];
		} elseif ($status == $stat && $action == $act && $subAction == $subActRejected) {
			$result = [
				"response" => "code : 200",
				'return_id' => "Return Id : " . $request['return_id'],
				'message' => "Return Is Rejected : Return Request is rejected by Transmart",
			];
		} else {
			throw new \Magento\Framework\Webapi\Exception(__('Please re-check status and action sequence before submit'), 400);
		}

		$format           = 'Y-m-d H:i:s';
		$returnUpdateDate = $this->coreHelper->getTimezone()->date(new \DateTime())->format($format);

		/* fetch data from rma_magento table */
		$loadDataRmaByOrderId = $this->integrationOrderReturnRepositoryInterface->loadRmaByOrderId($returnId);
		$rmaEntityId          = $loadDataRmaByOrderId->getEntityId();

		/* fetch data from magento_rma_item_entity table */
		$updateItemRma   = $this->integrationOrderReturnRepositoryInterface->loadRmaItemByEntityId($rmaEntityId);
		$rmaEntityIdItem = $updateItemRma->getRmaEntityId();
		if ($rmaEntityIdItem && $subAction == $subActApproved) {
			$arrayUpdateItem = array(
				'qty_approved' => $updateItemRma->setQtyApproved($itemData["quantity_allocated"]),
			);
		} elseif ($rmaEntityIdItem && $subAction == $subActRejected) {
			$arrayUpdateItem = array(
				'qty_returned' => $updateItemRma->setQtyReturned($itemData["quantity_allocated"]),
			);
		}
		/* update item to table magento_rma_item_entity*/
		$loadDataRmaByOrderId->setItems($arrayUpdateItem);
		$updateRma = $this->rmaRepositoryInterface->save($loadDataRmaByOrderId);

		$updateReturnData = $this->integrationOrderReturnRepositoryInterface->loadDataReturnByOrderid($returnId);
		if ($returnId && $subAction == $subActApproved) {
			$updateReturnData->setQtyApproved($itemData['quantity_allocated']);
			$updateReturnData->setStatus(Status::STATE_APPROVED);
			$updateReturnData->setUpdatedAt($returnUpdateDate);

		} elseif ($returnId && $subAction == $subActRejected) {
			$updateReturnData->setQtyRejected($itemData['quantity_allocated']);
			$updateReturnData->setStatus(Status::STATE_REJECTED);
			$updateReturnData->setUpdatedAt($returnUpdateDate);
		} else {
			throw new \Magento\Framework\Webapi\Exception(__('Return not initiated'), 400);
		}
		$saveUpdateReturn = $this->integrationOrderReturnRepositoryInterface->save($updateReturnData);

		return $result;
	}

	/**
	 * Cancel Return From OMS
	 * @param  string $returnId
	 * @param  int $status
	 * @param  int $action
	 * @param  int $subAction
	 * @return mixed
	 */
	public function returnCancel($returnId, $status, $action, $subAction) {
		$idsOrder = $this->orderStatusRepository->loadDataByRefOrderId($returnId);
		if (!$idsOrder->getReferenceOrderId()) {
			throw new \Magento\Framework\Webapi\Exception(__('Order ID doesn\'t exist, please make sure again.'));
		}

		$request = [
			'return_id' => $returnId,
			'status' => $status,
			'action' => $action,
			'sub_action' => $subAction,
		];

		$orderIds = $idsOrder->getReferenceOrderId();
		$stat     = IntegrationOrderReturnInterface::STATUS_CANCEL;
		$act      = IntegrationOrderReturnInterface::ACTION_CANCEL;
		$subAct   = IntegrationOrderReturnInterface::SUBACTION_CANCEL;

		if ($status == $stat && $action == $act && $subAction == $subAct) {
			$result = [
				"response" => "code : 200",
				'return_id' => "Return Id : " . $request['return_id'],
				'message' => "Return Is Canceled : Return Request has been Cancelled",
			];
		} else {
			throw new \Magento\Framework\Webapi\Exception(__('Please re-check status and action sequence before submit'), 400);
		}

		return $result;
	}
}
