<?php
/**
 * @category Trans
 * @package  Trans_IntegrationOrder
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Muhammad Randy <muhammad.randy@ctcorpdigital.co.id>
 *
 * Copyright © 2019 CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationOrder\Model;

use Trans\IntegrationOrder\Api\Data\IntegrationOrderInterface;
use Trans\IntegrationOrder\Api\OmsIntegrationInterface;

/**
 * @api
 * Class OmsIntegration
 */

class OmsIntegration implements OmsIntegrationInterface {

	/**
	 * @var \Magento\Framework\HTTP\Client\Curl
	 */
	protected $curl;

	/**
	 * @var \Magento\Framework\UrlInterface
	 */
	protected $url;

	/**
	 * @var \Magento\Sales\Api\OrderRepositoryInterface
	 */
	protected $orderRepoInterface;

	/**
	 * @var \Trans\Core\Helper\Customer
	 */
	protected $customerHelper;

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
	 * @var \Trans\IntegrationOrder\Api\Data\IntegrationOrderInterfaceFactory
	 */
	protected $orderIntegrationInterfaceFactory;

	/**
	 * @var \Trans\IntegrationOrder\Api\IntegrationOrderRepositoryInterface
	 */
	protected $orderRepo;

	/**
	 * @var \Trans\IntegrationOrder\Api\Data\IntegrationOrderItemInterfaceFactory
	 */
	protected $orderIntegrationItemInterfaceFactory;

	/**
	 * @var \Trans\IntegrationOrder\Api\IntegrationOrderItemRepositoryInterface
	 */
	protected $orderItemRepo;

	/**
	 * @var \Trans\IntegrationOrder\Api\Data\IntegrationOrderPaymentInterfaceFactory
	 */
	protected $orderIntPaymentInterfaceFactory;

	/**
	 * @var \Trans\IntegrationOrder\Api\IntegrationOrderPaymentRepositoryInterface
	 */
	protected $orderPaymentRepo;

	/**
	 * @var \Trans\IntegrationOrder\Api\IntegrationOrderAllocationRuleRepositoryInterface
	 */
	protected $orderAllocationRepo;

	/**
	 * @var \Trans\IntegrationOrder\Api\IntegrationOrderStatusRepositoryInterface
	 */
	protected $statusRepoInterface;

	/**
	 * @param \Magento\Framework\HTTP\Client\Curl $curl
	 * @param \Magento\Framework\UrlInterface $url
	 * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepoInterface
	 * @param \Trans\Core\Helper\Customer $customerHelper
	 * @param \Trans\Core\Helper\Data $coreHelper
	 * @param \Trans\IntegrationOrder\Helper\Integration $integrationHelper
	 * @param \Trans\IntegrationOrder\Helper\Data $dataHelper
	 * @param \Trans\IntegrationOrder\Helper\Config $configHelper
	 * @param \Trans\IntegrationOrder\Api\Data\IntegrationOrderInterfaceFactory $orderIntegrationInterfaceFactory
	 * @param \Trans\IntegrationOrder\Api\IntegrationOrderRepositoryInterface $orderRepo
	 * @param \Trans\IntegrationOrder\Api\OrderHistoryInterface $historyInterface
	 * @param \Trans\IntegrationOrder\Api\Data\IntegrationOrderItemInterfaceFactory $orderIntegrationItemInterfaceFactory
	 * @param \Trans\IntegrationOrder\Api\IntegrationOrderItemRepositoryInterface $orderItemRepo
	 * @param \Trans\IntegrationOrder\Api\Data\IntegrationOrderPaymentInterfaceFactory $orderIntPaymentInterfaceFactory
	 * @param \Trans\IntegrationOrder\Api\IntegrationOrderPaymentRepositoryInterface $orderPaymentRepo
	 * @param \Trans\IntegrationOrder\Api\Data\IntegrationOrderAllocationRuleInterfaceFactory $oarInterfaceFactory
	 * @param \Trans\IntegrationOrder\Api\IntegrationOrderAllocationRuleRepositoryInterface $orderAllocationRepo
	 * @param \Trans\IntegrationOrder\Model\AllocationRuleIntegration $allocationRuleInt
	 * @param \Trans\IntegrationOrder\Api\IntegrationOrderStatusRepositoryInterface $statusRepoInterface
	 */
	public function __construct(
		\Magento\Framework\HTTP\Client\Curl $curl,
		\Magento\Framework\UrlInterface $url,
		\Magento\Framework\Event\ManagerInterface $eventManager,
		\Magento\Sales\Api\OrderRepositoryInterface $orderRepoInterface,
		\Trans\Integration\Helper\Curl $curlHelper,
		\Trans\Core\Helper\Customer $customerHelper,
		\Trans\Core\Helper\Data $coreHelper,
		\Trans\IntegrationOrder\Helper\Integration $integrationHelper,
		\Trans\IntegrationOrder\Helper\Data $dataHelper,
		\Trans\IntegrationOrder\Helper\Config $configHelper,
		\Trans\IntegrationOrder\Api\Data\IntegrationOrderInterfaceFactory $orderIntegrationInterfaceFactory,
		\Trans\IntegrationOrder\Api\IntegrationOrderRepositoryInterface $orderRepo,
		\Trans\IntegrationOrder\Api\Data\IntegrationOrderItemInterfaceFactory $orderIntegrationItemInterfaceFactory,
		\Trans\IntegrationOrder\Api\IntegrationOrderItemRepositoryInterface $orderItemRepo,
		\Trans\IntegrationOrder\Api\Data\IntegrationOrderPaymentInterfaceFactory $orderIntPaymentInterfaceFactory,
		\Trans\IntegrationOrder\Api\IntegrationOrderPaymentRepositoryInterface $orderPaymentRepo,
		\Trans\IntegrationOrder\Api\IntegrationOrderAllocationRuleRepositoryInterface $orderAllocationRepo,
		\Trans\IntegrationOrder\Api\IntegrationOrderStatusRepositoryInterface $statusRepoInterface
	) {
		$this->curl                                 = $curl;
		$this->url                                  = $url;
		$this->eventManager                         = $eventManager;
		$this->orderRepoInterface                   = $orderRepoInterface;
		$this->customerHelper                       = $customerHelper;
		$this->coreHelper                           = $coreHelper;
		$this->integrationHelper                    = $integrationHelper;
		$this->dataHelper                           = $dataHelper;
		$this->configHelper                         = $configHelper;
		$this->orderIntegrationInterfaceFactory     = $orderIntegrationInterfaceFactory;
		$this->orderRepo                            = $orderRepo;
		$this->orderIntegrationItemInterfaceFactory = $orderIntegrationItemInterfaceFactory;
		$this->orderItemRepo                        = $orderItemRepo;
		$this->orderIntPaymentInterfaceFactory      = $orderIntPaymentInterfaceFactory;
		$this->orderPaymentRepo                     = $orderPaymentRepo;
		$this->orderAllocationRepo                  = $orderAllocationRepo;
		$this->statusRepoInterface                  = $statusRepoInterface;
		$this->logger                               = $dataHelper->getLogger();
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
	 * hit oms create order
	 * @param \Trans\IntegrationOrder\Api\Data\IntegrationOrderInterface[] $order
	 * @return array
	 */
	public function createOrderOms($order) {
		foreach ($order as $dataOrder) {
			$orderItem                      = [];
			$billingOrder                   = [];
			$shippingOrder                  = [];
			$orders['reference_number']     = $dataOrder['reference_number'];
			$orders['account_name']         = $dataOrder['customer_name'];
			$orders['account_email']        = $dataOrder['customer_email'];
			$orders['account_phone_number'] = $dataOrder['customer_phone_number'];
			$orders['receiver_name']        = $dataOrder['receiver_name'];
			$orders['receiver_phone']       = $dataOrder['receiver_phone'];
			$orders['store_code']           = $dataOrder['store_code'];
			$orders['flag_spo']             = $dataOrder['flag_spo'];
			$orders['order_id']             = $dataOrder['order_id'];
			$orders['quote_id']             = $dataOrder['quote_id'];
			$orders['address_id']           = $dataOrder['address_id'];
			$orders['promotion_type']       = $dataOrder['promotion_type'];
			$orders['promotion_value']      = $dataOrder['promotion_value'];
			$orders['shipping_fee']         = $dataOrder['shipping_fee'];
			$orders['time_slot']            = $dataOrder['time_slot'];
			$orders['order_source']         = $dataOrder['order_source'];
			$orders['code_name']            = $dataOrder['code_name'];

			$paidPrice = 0;
			foreach ($dataOrder['order_items'] as $items) {
				$modelItem                = $this->orderIntegrationItemInterfaceFactory->create();
				$dataItem['sku_basic']    = $items['sku_basic'];
				$dataItem['sku']          = $items['sku'];
				$dataItem['quantity']     = $items['quantity'];
				$dataItem['ori_price']    = $items['ori_price'];
				$dataItem['sell_price']   = $items['sell_price'];
				$dataItem['disc_price']   = $items['disc_price'];
				$dataItem['sub_total']    = $items['sub_total'];
				$dataItem['paid_price']   = $items['paid_price'];
				$dataItem['coupon_code']  = $items['coupon_code'];
				$dataItem['coupon_val']   = $items['coupon_val'];
				$dataItem['weight']       = $items['weight'];
				$dataItem['total_weight'] = $items['total_weight'];
				$dataItem['is_warehouse'] = $items['is_warehouse'];
				$dataItem['is_fresh'] 	  = $items['is_fresh'];
				$orderItem[]              = $dataItem;

				$modelItem->setSku($items['sku']);
				$modelItem->setOrderId($dataOrder['order_id']);
				$modelItem->setQty($items['quantity']);
				$modelItem->setOriginalPrice($items['ori_price']);
				$modelItem->setPaidPrice($items['paid_price']);
				$modelItem->setSellingPrice($items['sell_price']);
				$modelItem->setSubtotal($items['sub_total']);
				$modelItem->setWeight($items['weight']);
				$modelItem->setTotalWeight($items['total_weight']);
				$modelItem->setGrandTotal($items['quantity'] * $items['ori_price']);
				$modelItem->setPromotionType($dataOrder['promotion_type']);
				$modelItem->setPromotionValue($dataOrder['promotion_value']);
				$modelItem->setIsWarehouse($items['is_warehouse']);
				$modelItem->setIsFresh($items['is_fresh']);

				$orderItemSave = $this->orderItemRepo->save($modelItem);

				$paidPrice += $items['paid_price'];
				$subTotal = $items['quantity'] * $items['sell_price'];
				// $promoValue = $subTotal - ($items['paid_price'] * $items['quantity']);
			}
			foreach ($dataOrder['billing'] as $billing) {
				$dataBilling['street'] = $billing['street'];
				$billingOrder[]        = $dataBilling;
			}

			foreach ($dataOrder['shipping'] as $shipping) {
				$dataShipping['street']    = $shipping['street'];
				$dataShipping['latitude']  = $shipping['latitude'];
				$dataShipping['longitude'] = $shipping['longitude'];
				$dataShipping['province']  = $shipping['province'];
				$dataShipping['city']      = $shipping['city'];
				$dataShipping['district']  = $shipping['district'];
				$dataShipping['zip_code']  = $shipping['zip_code'];
				$shippingOrder[]           = $dataShipping;
			}
			foreach ($dataOrder['payment'] as $payment) {
				$modelPayment                      = $this->orderIntPaymentInterfaceFactory->create();
				$dataPayment['master_payment_id1'] = (string) $payment['master_payment_id1'];
				$dataPayment['pay_ref_number1']    = $payment['pay_ref_number1'];
				$dataPayment['amount']             = $payment['amount'];
				$dataPayment['split_payment']      = 0;
				$dataPayment['currency']           = 1;
				$paymentOrder                      = $dataPayment;
			}

			$referenceNumber = $dataOrder['reference_number'];
			$quoteId         = $dataOrder['quote_id'];
			$orderId         = $this->dataHelper->getCustomerOrderId()->getIncrementId();
			$addressId       = $dataOrder['address_id'];
			$merchantCode    = $this->configHelper->getOmsMerchantId();
			$orderType       = $dataOrder['order_type']; // pickup in store atau delivery
			$format          = 'Y-m-d H:i:s';
			$orderDate       = $this->coreHelper->getTimezone()->date(new \DateTime())->format($format);
			$logisticType    = $dataOrder['courier']; // courier brand
			$custName        = $dataOrder['customer_name'];
			$custEmail       = $dataOrder['customer_email'];
			$custPhone       = $dataOrder['customer_phone_number'];

			$receiverName  = $dataOrder['receiver_name'];
			$receiverPhone = $dataOrder['receiver_phone'];

			$promoType  = $dataOrder['promotion_type']; // rule id;
			$promoValue = $dataOrder['promotion_value']; // promotion value

			$billingAddr  = $billing['street'];
			$shippingAddr = $shipping['street'];
			$latitude     = $shipping['latitude'];
			$longitude    = $shipping['longitude'];
			$province     = $shipping['province'];
			$city         = $shipping['city'];
			$district     = $shipping['district'];
			$zipCode      = $shipping['zip_code'];
			$timeSlot     = $dataOrder['time_slot']; //time slot

			$sourceChannel = IntegrationOrderInterface::SOURCE_CHANNELS; // online or offline from magento
			$sourceOrder   = $dataOrder['order_source']; // 1 = pos, 2 = web, or 3 = app
			$fullStore     = $dataOrder['store_code'];
			$flagSpo       = $dataOrder['flag_spo'];

			$shippingFee = $dataOrder['shipping_fee'];
			//$grandTotal  = $paidPrice + $shippingFee;
			$grandTotal = $dataOrder['grand_total'];

			$payRefNumber  = $payment['pay_ref_number1'];
			$splitPayment  = IntegrationOrderInterface::SPLIT_PAYMENTS;
			$amount        = $payment['amount'];
			$statusPayment = ''; // get data from payment status in sales_order magento
			$statusOrder   = ''; // get data from order status in sales_order magento

			/*smart osc start remove code*/
			/*$allocationRuleDataByQuoteId = $this->orderAllocationRepo->loadDataByQuoteId($dataOrder['quote_id']);
				                                                                                       ;
			*/
			/*smart osc end remove code*/

			/*smart osc start custom*/
			$warehouse       = $dataOrder['spo_detail'];
			$orderOriginId   = $dataOrder['order_origin_id'];
			$isSpo           = $dataOrder['is_spo'];
			$isOwnCourier    = $dataOrder['is_own_courier'];
			$warehouseSource = $dataOrder['warehouse_source'];
			$warehouseCode   = $dataOrder['warehouse_code']; // added new 26 Oct
			$codeName        = $dataOrder['code_name'];
			/*smart osc end custom*/

			/* Save Data Payment*/
			$modelPayment->setPaymentRefNumber1($payRefNumber);
			$modelPayment->setOrderId($dataOrder['order_id']);
			$modelPayment->setSplitPayment($splitPayment);
			$modelPayment->setAmountOfPayment1($amount);
			$modelPayment->setPaymentStatus($statusPayment);
			$modelPayment->setReferenceNumber($referenceNumber);
			$modelPayment->setTotalAmountPaid($amount);

			$orderPaymentSave = $this->orderPaymentRepo->save($modelPayment);
			$model            = $this->orderIntegrationInterfaceFactory->create();
			$request[]        = array(
				"order_id" => $dataOrder['order_id'],
				"order_id_origin" => $orderOriginId, //new
				"is_spo" => (int) $isSpo, //new
                "spo_type" => (string) $dataOrder->getSpoType(), //new
				"is_own_courier" => (int) $isOwnCourier, //new
				"warehouse_source" => $warehouseSource, //new
				"warehouse_code" => $warehouseCode, // added new 26 Oct
				"warehouse" => json_decode($warehouse), //new
				"code_name" => $codeName, //new
				"merchant_code" => $merchantCode,
                "logistic_courier" => $dataOrder->getLogisticCourier(), // smartosc custom
                "logistic_courier_name" => $dataOrder->getLogisticCourierName(), // smartosc custom
				"reference_number" => $referenceNumber,
				"promotion_type" => $promoType,
				"promotion_value" => $promoValue,
				"order_type" => $orderType,
				"order_date" => $orderDate,
				"logistic_courier_type" => $logisticType,
				"customer_email" => $custEmail,
				"customer_name" => $custName,
				"customer_phone" => $custPhone,
				"receiver_name" => $receiverName,
				"receiver_phone" => $receiverPhone,
				"billing_address" => $billingAddr,
				"shipping_address" => $shippingAddr,
				"latitude" => $latitude,
				"longitude" => $longitude,
				"province" => $province,
				"city" => $city,
				"district" => $district,
				"zip_code" => $zipCode,
				"source_channel" => $sourceChannel,
				"source_order" => $sourceOrder,
				"fulfillment_store" => $fullStore,
				"time_slot" => $timeSlot,
				"sub_total" => $paidPrice,
				"shipping_fee" => $shippingFee, // cart original price
                "shipping_fee_discount" => $dataOrder->getShippingFeeDiscount(), // smartosc custom
				"grand_total" => $grandTotal, // final price
				"flag_spo" => $flagSpo,
				"order_items" => $orderItem,
				"payment" => $paymentOrder,
			);

			$model->setOrderId($dataOrder['order_id']);
			$model->setMerchantId($merchantCode);
			$model->setShipmentDate($timeSlot);
			$model->setReferenceNumber($referenceNumber);
			$model->setOrderType($orderType);
			$model->setCourier($logisticType);
			$model->setAccountEmail($custEmail);
			$model->setAccountName($custName);
			$model->setAccountPhoneNumber($custPhone);
			$model->setReceiverName($receiverName);
			$model->setReceiverPhone($receiverPhone);
			$model->setBillingAddress($billingAddr);
			$model->setShippingAddress($shippingAddr);
			$model->setLatitude($latitude);
			$model->setLongitude($longitude);
			$model->setProvince($province);
			$model->setCity($city);
			$model->setDistrict($district);
			$model->setZipcode($zipCode);
			$model->setSourceChannel($sourceChannel);
			$model->setSourceOrder($sourceOrder);
			$model->setFulfillmentStore($fullStore);
			$model->setOrderStatus($statusOrder);
			$model->setFlagSpo($flagSpo);

			$orderSave = $this->orderRepo->save($model);

			/* Save to Order History */
			$saveOrder = $this->statusRepoInterface->loadByOrderIds($dataOrder['order_id']);
			if (!$saveOrder->getOrderId()) {
				$saveOrder->setReferenceNumber($referenceNumber);
				$saveOrder->setOrderId($dataOrder['order_id']);
				$saveOrder->setUpdatedAt($orderDate);

				$saveHistory = $this->statusRepoInterface->saveHistory($saveOrder);
			}
			/* Update Data to Table OMS OAR */
			$oar = $this->orderAllocationRepo->loadDataByAddressQuoteId($quoteId, $addressId);
			if ($quoteId) {
				$oar->setReferenceNumber($referenceNumber);
				$oar->setOrderId($dataOrder['order_id']);

				$oarSave = $this->orderAllocationRepo->save($oar);
			}
		}
		$url      = $this->configHelper->getOmsBaseUrl() . $this->configHelper->getOmsOrderApi();
		$headers  = $this->getHeader();
		$dataJson = json_encode($request);
		$this->curl->setOption(CURLOPT_RETURNTRANSFER, true);
		$this->curl->setHeaders($headers);
		$this->curl->post($url, $dataJson);
		$response = $this->curl->getBody();
		$this->logger->info('$headers : ' . json_encode($headers));
		$this->logger->info('$response : ' . $response);
		$obj = json_decode($response);
		$this->logger->info('Body: ' . $dataJson . '. Response: ' . $response);
		$json_string = stripcslashes($response);
		$this->eventManager->dispatch(
			'after_create_order_oms',
			[
				'reference_number' => $referenceNumber,
			]
		);

		if ($obj->code == 200) {
			return json_decode($response, true);
		}
		return json_decode($response, true);
	}
}
