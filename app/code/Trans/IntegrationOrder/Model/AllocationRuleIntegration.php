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

use Trans\IntegrationOrder\Api\OrderAllocationRuleInterface;

/**
 * @api
 * Class AllocationRuleIntegration
 */
class AllocationRuleIntegration implements OrderAllocationRuleInterface {

	/**
	 * @var \Magento\Framework\HTTP\Client\Curl
	 */
	protected $curl;

	/**
	 * @var \Magento\Framework\UrlInterface
	 */
	protected $url;

	/**
	 * @var \Trans\IntegrationOrder\Helper\Data
	 */
	protected $dataHelper;

	/**
	 * @var \Trans\IntegrationOrder\Helper\Config
	 */
	protected $configHelper;

	/**
	 * @var \Trans\IntegrationOrder\Helper\Integration
	 */
	protected $integrationHelper;

	/**
	 * @var \Trans\IntegrationOrder\Api\Data\IntegrationOrderAllocationRuleInterfaceFactory
	 */
	protected $allocationRuleInterfaceFactory;

	/**
	 * @var \Trans\IntegrationOrder\Api\IntegrationOrderAllocationRuleRepositoryInterface
	 */
	protected $orderAllocationRepo;

	/**
	 * @param \Magento\Framework\HTTP\Client\Curl $curl
	 * @param \Magento\Framework\UrlInterface $url
	 * @param \Trans\Integration\Helper\Curl $curlHelper
	 * @param \Trans\IntegrationOrder\Helper\Integration $integrationHelper
	 * @param \Trans\IntegrationOrder\Helper\Data $dataHelper
	 * @param \Trans\IntegrationOrder\Helper\Config $configHelper
	 * @param \Trans\IntegrationOrder\Api\Data\IntegrationOrderAllocationRuleInterfaceFactory $oarInterfaceFactory
	 * @param \Trans\IntegrationOrder\Api\IntegrationOrderAllocationRuleRepositoryInterface $orderAllocationRepo
	 */

	public function __construct(
		\Magento\Framework\HTTP\Client\Curl $curl,
		\Magento\Framework\UrlInterface $url,
		\Trans\Integration\Helper\Curl $curlHelper,
		\Trans\IntegrationOrder\Helper\Data $dataHelper,
		\Trans\IntegrationOrder\Helper\Integration $integrationHelper,
		\Trans\IntegrationOrder\Helper\Config $configHelper,
		\Trans\IntegrationOrder\Api\Data\IntegrationOrderAllocationRuleInterface $oarInterface,
		\Trans\IntegrationOrder\Api\Data\IntegrationOrderAllocationRuleInterfaceFactory $oarInterfaceFactory,
		\Trans\IntegrationOrder\Api\IntegrationOrderAllocationRuleRepositoryInterface $orderAllocationRepo,
		\Trans\LocationCoverage\Model\AllLocationRepository $locationRepo
	) {
		$this->curl                = $curl;
		$this->url                 = $url;
		$this->curlHelper          = $curlHelper;
		$this->dataHelper          = $dataHelper;
		$this->integrationHelper   = $integrationHelper;
		$this->configHelper        = $configHelper;
		$this->oarInterface        = $oarInterface;
		$this->oarInterfaceFactory = $oarInterfaceFactory;
		$this->orderAllocationRepo = $orderAllocationRepo;
		$this->locationRepo        = $locationRepo;

		$this->logger = $dataHelper->getLogger();
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
	 * hit oms to get oar data
	 *
	 * @param \Trans\IntegrationOrder\Api\Data\IntegrationOrderAllocationRuleInterface[] $address
	 * @return mixed
	 */
	public function getOrderAllocationRule($address) {
		foreach ($address as $value) {
			$itemData            = [];
			$item['quote_id']    = $value['quote_id'];
			$item['customer_id'] = $value['customer_id'];
			$item['address_id']  = $value['address_id'];
			foreach ($value['items'] as $data) {
				$dataItem['sku']            = $data['sku'];
				$dataItem['sku_basic']      = $data['sku_basic'];
				$dataItem['quantity']       = $data['quantity'];
				$dataItem['weight']         = $data['weight'];
				$dataItem['price']          = $data['price'];
				$dataItem['is_spo']         = $data['is_spo'];
				$dataItem['is_own_courier'] = $data['is_own_courier']; // means is_fresh
			}
			$itemData[]        = $dataItem;
			$customerId        = $value['customer_id'];
			$quoteId           = $value['quote_id'];
			$isWarehouse       = $value['is_warehouse'];
			$merchantCode      = $this->configHelper->getOmsMerchantId();
			$addressCollection = $this->dataHelper->customerAddress()->getById($value['address_id']);
			$addressId         = $addressCollection->getId();

			$merchantCode = $this->configHelper->getOmsMerchantId();
			$shipAdd      = $addressCollection->getStreet();
			$shippingAddr = implode(" ", $shipAdd);
			$province     = $addressCollection->getRegion()->getRegion();
			$city         = $addressCollection->getCity();
			if (is_numeric($city)) {
				$datas = $this->locationRepo->cityName($city);
				$city  = $datas->getCity();
			}
			$postCode      = $addressCollection->getPostcode();
			$districtCheck = $addressCollection->getCustomAttribute('district');
			if ($districtCheck !== null) {
				$district = $districtCheck->getValue();
				if (is_numeric($district)) {
					$datas    = $this->locationRepo->districtName($district);
					$district = $datas->getDistrict();
				}
			} else {
				$customRedirect = $this->url->getUrl('customer/address');
				$this->dataHelper->message()->addErrorMessage(__('District is empty, please fill it before continue.'));
				$this->dataHelper->response()->create()->setRedirect($customRedirect)->sendResponse();
				// @codingStandardsIgnoreStart
				exit();
				// @codingStandardsIgnoreEnd
			}

			$latitudeCheck = $addressCollection->getCustomAttribute('latitude');
			if ($latitudeCheck !== null) {
				$latitude = $latitudeCheck->getValue();
			} else {
				$customRedirect = $this->url->getUrl('customer/address');
				$this->dataHelper->message()->addErrorMessage(__('Latitude is empty, please fill it before continue.'));
				$this->dataHelper->response()->create()->setRedirect($customRedirect)->sendResponse();
				// @codingStandardsIgnoreStart
				exit();
				// @codingStandardsIgnoreEnd
			}

			$longitudeCheck = $addressCollection->getCustomAttribute('longitude');
			if ($longitudeCheck !== null) {
				$longitude = $longitudeCheck->getValue();
			} else {
				$customRedirect = $this->url->getUrl('customer/address');
				$this->dataHelper->message()->addErrorMessage(__('Longitude is empty, please fill it before continue.'));
				$this->dataHelper->response()->create()->setRedirect($customRedirect)->sendResponse();
				// @codingStandardsIgnoreStart
				exit();
				// @codingStandardsIgnoreEnd
			}

			$destination = array(
				"address" => $shippingAddr,
				"province" => $province,
				"city" => $city,
				"district" => $district,
				"postcode" => $postCode,
				"latitude" => (float) $latitude,
				"longitude" => (float) $longitude,
			);

			$request[] = array(
				"order_id" => $addressId,
				"merchant_code" => $merchantCode,
				"destination" => $destination,
				"items" => $itemData,
				"total_weight" => $dataItem['weight'] * $dataItem['quantity'],
				"total_price" => $dataItem['price'] * $dataItem['quantity'],
				"total_qty" => $dataItem['quantity'],
			);
			$url      = $this->configHelper->getOmsBaseUrl() . $this->configHelper->getOmsOarApi();
			$headers  = $this->getHeader();
			$dataJson = json_encode($request);
			$this->curl->setHeaders($headers);
			$this->curl->post($url, $dataJson);
			$response = $this->curl->getBody();
			$this->logger->info('$headers OAR : ' . json_encode($headers));
			$this->logger->info('$dataJson OAR : ' . $dataJson);
			$this->logger->info('$response OAR : ' . $response);

			// check timing get OAR
			$dateBegin = new DateTime();
			$this->logger->info('$Date Begin OAR : ' . $dateBegin->getTimestamp());
			sleep(5);
			$dateEnd = new DateTime();
			$this->logger->info('$Date End OAR : ' . $dateEnd->getTimestamp()
			$diff = $dateEnd->getTimestamp() - $dateBegin->getTimestamp();
			$this->logger->info('$Date Different OAR : ' . $diff);

			$jsonString = stripcslashes($response);

			$obj = json_decode($response);

			/* Parsing Data Response OAR from OMS */
			foreach ($obj->content as $data) {
				try {
					/* Parsing data content */
					$message         = $data->message; // message response OAR
					$oarOrderId      = $data->data->order_id; // order id from oms
					$oarIdOrigin     = $data->data->order_id_origin;
					$isSpo           = $data->data->is_spo;
					$isOwnCourier    = $data->data->is_own_courier;
					$warehouseSource = $data->data->warehouse_source;

					/* Parsing data items order */
					foreach ($data->data->items as $itemsOrder) {
						$itemSku          = $itemsOrder->sku;
						$itemSkuBasic     = $itemsOrder->sku_basic;
						$itemWeight       = $itemsOrder->weight;
						$itemPrice        = $itemsOrder->price;
						$itemQty          = $itemsOrder->quantity;
						$itemIsSpo        = $itemsOrder->is_spo;
						$itemIsOwnCourier = $itemsOrder->is_own_courier;
					}

					$spoDetail    = json_encode($data->data->warehouse) ? json_encode($data->data->warehouse) : '';
					$storeSpoCode = $data->data->warehouse->store_code;
					// $storeSpoCode        = $data->data->warehouse->store_code ? $data->data->warehouse->store_code :  ;
					// $storeSpoName        = $data->data->warehouse->store_name;
					// $storeSpoAddress     = $data->data->warehouse->store_address;
					// $storeSpoEmail       = $data->data->warehouse->email;
					// $storeSpoProv        = $data->data->warehouse->province;
					// $storeSpoPhone       = $data->data->warehouse->phone;
					// $storeSpoCity        = $data->data->warehouse->city;
					// $storeSpoDistrict    = $data->data->warehouse->district;
					// $storeSpoZipcode     = $data->data->warehouse->store_zipcode;
					// $storeSpoLat         = $data->data->warehouse->latitude;
					// $storeSpoLng         = $data->data->warehouse->longitude;
					// $storeSpoCodename    = $data->data->warehouse->code_name;
					// $storeSpoDistanceRad = $data->data->warehouse->distance_radius;

					/* Parsing data store */
					$storeCode        = $data->data->store->store_code;
					$storeName        = $data->data->store->store_name;
					$storeAddress     = $data->data->store->store_address;
					$storeEmail       = $data->data->store->email;
					$storeProvince    = $data->data->store->province;
					$storePhone       = $data->data->store->phone;
					$storeCity        = $data->data->store->city;
					$storeDistrict    = $data->data->store->district;
					$storeZipcode     = $data->data->store->store_zipcode;
					$storeLat         = $data->data->store->latitude;
					$storeLng         = $data->data->store->longitude;
					$storeCodename    = $data->data->store->code_name;
					$storeDistanceRad = $data->data->store->distance_radius;

					/* Parsing data shipping list */
					foreach ($data->data->shipping_list as $shipping) {
						$shippingName          = $shipping->name; // Data Shipping Type
						$serviceType           = $shipping->service_type; // Data Service Type
						$shippingCourier       = $shipping->courier->name; // Data Shipping Courier Name
						$courierServiceType    = $shipping->courier->service_type; // Data Courier Service Type
						$courierInsurance      = $shipping->courier->insurance; // Data Courier Insurance
						$shippingFeeBase       = $shipping->courier->fee->base; // Data Courier Shipping Fee Base
						$shippingFeeAdditional = $shipping->courier->fee->additional; // Data Courier Shipping Fee Additional
						$shippingFeeTotal      = $shipping->courier->fee->total; // Data Shipping Fee Total
					}
				} catch (\Exception $e) {
					$this->logger->info($e->getMessage());
					continue;
				}

				/**
				 * Checking and Save Data to Table OMS OAR
				 */
				$oarCustomerId = $customerId . $oarOrderId;
				$modelOar      = $this->orderAllocationRepo->loadDataByAddressQuoteId($quoteId, $addressId);

				if (!$modelOar->getAddressId()) {
					$model = $this->oarInterfaceFactory->create();
				}
				$modelOar->setQuoteId($quoteId);
				$modelOar->setAddressId($addressId);
				$modelOar->setCustomerId($customerId);
				$modelOar->setOarCustomerId($oarCustomerId);
				$modelOar->setOarOrderId($oarOrderId);
				$modelOar->setStoreCode($storeCode);
				$modelOar->setIsSpo($isSpo);
				$modelOar->setIsOwnCourier($isOwnCourier);
				$modelOar->setWarehouseSource($warehouseSource);
				$modelOar->setCodeName($storeCodename);
				$modelOar->setSpoDetail($spoDetail);
				$modelOar->setOarOriginOrderId($oarIdOrigin);

				$oarSave = $this->orderAllocationRepo->save($modelOar);
			}
		}
		return json_decode($jsonString, true);
	}
}
