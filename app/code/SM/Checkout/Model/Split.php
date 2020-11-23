<?php
/**
 * Created by PhpStorm.
 * User: thelightsp
 * Date: 5/13/20
 * Time: 5:11 PM
 */

namespace SM\Checkout\Model;

/**
 * Class Split
 * @package SM\Checkout\Model
 */
class Split
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    protected $serializer;

    /**
     * @var \Magento\Directory\Model\RegionFactory
     */
    protected $regionFactory;

    /**
     * @var \Trans\LocationCoverage\Model\CityFactory
     */
    protected $cityFactory;

    /**
     * @var \Trans\LocationCoverage\Model\DistrictFactory
     */
    protected $districtFactory;

    /**
     * @var \Trans\Integration\Helper\Curl
     */
    protected $curlHelper;

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
    protected $oarInterfaceFactory;

    /**
     * @var \Trans\IntegrationOrder\Model\ResourceModel\IntegrationOrderAllocationRule\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resourceConnection;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $readAdapter;

    /**
     * @var \SM\Checkout\Helper\Config
     */
    protected $helperConfig;

    /**
     * Split constructor.
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Serialize\SerializerInterface $serializer
     * @param \Magento\Directory\Model\RegionFactory $regionFactory
     * @param \Trans\LocationCoverage\Model\CityFactory $cityFactory
     * @param \Trans\LocationCoverage\Model\DistrictFactory $districtFactory
     * @param \Trans\Integration\Helper\Curl $curlHelper
     * @param \Trans\IntegrationOrder\Helper\Data $dataHelper
     * @param \Trans\IntegrationOrder\Helper\Config $configHelper
     * @param \Trans\IntegrationOrder\Helper\Integration $integrationHelper
     * @param \Trans\IntegrationOrder\Api\Data\IntegrationOrderAllocationRuleInterfaceFactory $oarInterfaceFactory
     * @param \Trans\IntegrationOrder\Model\ResourceModel\IntegrationOrderAllocationRule\CollectionFactory $collectionFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     * @param \SM\Checkout\Helper\Config $helperConfig
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Serialize\SerializerInterface $serializer,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \Trans\LocationCoverage\Model\CityFactory $cityFactory,
        \Trans\LocationCoverage\Model\DistrictFactory $districtFactory,
        \Trans\Integration\Helper\Curl $curlHelper,
        \Trans\IntegrationOrder\Helper\Data $dataHelper,
        \Trans\IntegrationOrder\Helper\Config $configHelper,
        \Trans\IntegrationOrder\Helper\Integration $integrationHelper,
        \Trans\IntegrationOrder\Api\Data\IntegrationOrderAllocationRuleInterfaceFactory $oarInterfaceFactory,
        \Trans\IntegrationOrder\Model\ResourceModel\IntegrationOrderAllocationRule\CollectionFactory $collectionFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \SM\Checkout\Helper\Config $helperConfig
    ) {
        $this->customerSession = $customerSession;
        $this->serializer = $serializer;
        $this->regionFactory = $regionFactory;
        $this->cityFactory = $cityFactory;
        $this->districtFactory = $districtFactory;
        $this->curlHelper = $curlHelper;
        $this->dataHelper = $dataHelper;
        $this->configHelper = $configHelper;
        $this->integrationHelper = $integrationHelper;
        $this->oarInterfaceFactory = $oarInterfaceFactory;
        $this->collectionFactory = $collectionFactory;
        $this->scopeConfig = $scopeConfig;
        $this->resourceConnection = $resourceConnection;
        $this->readAdapter = $this->resourceConnection->getConnection('core_read');
        $this->helperConfig = $helperConfig;
    }

    /**
     * @return mixed
     */
    protected function getHeader()
    {
        $token = $this->integrationHelper->getToken();
        $headers['dest'] = $this->configHelper->getOmsDest();
        $headers['Content-Type'] = 'application/json';
        $headers['Authorization'] = 'Bearer ' . $token;
        return $headers;
    }

    /**
     * @param int $id
     * @return string
     */
    public function getCityName($id)
    {
        $city = $this->cityFactory->create()->load($id);
        if ($city->getId()) {
            return $city->getCity();
        }
        return '';
    }

    /**
     * @param int $id
     * @return string
     */
    public function getDistrictName($id)
    {
        $district = $this->districtFactory->create()->load($id);
        if ($district->getId()) {
            return $district->getDistrict();
        }
        return '';
    }

    /**
     * @param int $id
     * @return string
     */
    public function getProvince($id)
    {
        $region = $this->regionFactory->create()->load($id);
        if ($region->getId()) {
            return $region->getName();
        }
        return '';
    }

    /**
     * @param $addressCollection
     * @return array
     */
    public function getAddressData($addressCollection)
    {
        $addressData = [];
        foreach ($addressCollection as $address) {
            $cityId = $address->getCity();
            $city = $this->getCityName($cityId);
            $regionId = $address->getRegionId();
            $province = $this->getProvince($regionId);
            $districtId = $address->getCustomAttribute('district') ? $address->getCustomAttribute('district')->getValue() : '';
            $district = $this->getDistrictName($districtId);
            $lat = $address->getCustomAttribute('latitude') ? $address->getCustomAttribute('latitude')->getValue() : 0;
            $long = $address->getCustomAttribute('longitude') ? $address->getCustomAttribute('longitude')->getValue() : 0;
            $addressData[$address->getId()] = [
                "address" => implode(",", $address->getStreet()),
                "district" => $district,
                "city" => $city,
                "province" => $province,
                "postcode" => $address->getPostcode(),
                "latitude" => (float)$lat,
                "longitude" => (float)$long,
            ];
        }
        return $addressData;
    }

    /**
     * @return string
     */
    public function getMerchantCode()
    {
        return $this->configHelper->getOmsMerchantId();
    }

    /**
     * @param array $data
     * @return array|mixed|string
     */
    public function getOarResponse($data)
    {
        //Bypass OAR because it is painful to get data from it
        $isTestMode = $this->scopeConfig->getValue('sm_checkout/checkout_oar/oar_active');
        if ($isTestMode) {
            return $this->byPassOar($data);
        }
        return $this->sendOAR($data);
    }

    /**
     * @param $data
     * @return array[]
     */
    public function byPassOar($data)
    {
        $sampleOrder = [
            'status' => 1,
            'message' => "",
            'data' => [
                'order_id' => 248.0,
                "order_id_origin" => 12,
                "merchant_code" => "TRTLMRT001",
                "destination" => [
                    "address" => "Jalan Pakis",
                    "province" => "DKI Jakarta",
                    "city" => "Kota Jakarta Barat",
                    "district" => "Senen",
                    "postcode" => "11610",
                    "latitude" => -6.272431,
                    "longitude" => 106.839511
                ],
                "is_own_courier" => false,
                "is_spo" => true,
                'items' => [],
                'store' => [
                    'store_code' => 10025,
                    'store_name' => 'Carrefour Permata Hijau',
                    'store_address' => 'Grand ITC Permata Hijau, Jl. Let. Jend. Supeno, Jakarta Selatan',
                    'code_name' => '',
                    'distance_radius' => 12,
                    'email' => 'customer_support@transmart.co.id',
                    'province' => 'DKI Jakarta',
                    'city' => 'Kota Jakarta Selatan',
                    'district' => 'Kebayoran Lama',
                    'phone' => 628123456789,
                    'store_zipcode' => 12210,
                    'latitude' => -6.220876,
                    'longitude' => 106.78447
                ],
                "warehouse" => [
                    "store_code" => "DC001",
                    "store_name" => "DC Tangerang Center",
                    "store_address" => "Banten, Tanggerang, Cikokol, 15117",
                    "email" => "customer_support@transmart.co.id",
                    "province" => "Banten",
                    "phone" => "628123456789",
                    "city" => "Kota Tangerang",
                    "district" => "Tangerang",
                    "store_zipcode" => "15117",
                    "latitude" => -6.207323,
                    "longitude" => 106.6294,
                    "code_name" => "",
                    "distance_radius" => 12
                ],
                'warehouse_source' => 'DC',
                'shipping_list' => [
                    [
                        'name' => 'Reguler',
                        'service_type' => 1,
                        'courier' => [
                            'id' => 4,
                            'name' => 'SAP Express',
                            'service_type' => 1,
                            'insurance' => 0,
                            'fee' => [
                                'base' => 500,
                                'additional' => 5000,
                                'total' => 5500
                            ],
                            'is_lowest' => 1
                        ]
                    ],
                    [
                        'name' => 'Same Day',
                        'service_type' => 2,
                        'courier' => [
                            'id' => 3,
                            'name' => 'Grab',
                            'service_type' => 2,
                            'insurance' => 0,
                            'fee' => [
                                'base' => 15000,
                                'additional' => 5000,
                                'total' => 20000
                            ],
                            'is_lowest' => 1
                        ]
                    ],
                    [
                        'name' => 'Scheduled',
                        'service_type' => 3,
                        'courier' => [
                            'id' => 3,
                            'name' => 'Grab',
                            'service_type' => 3,
                            'insurance' => 0,
                            'fee' => [
                                'base' => 10000,
                                'additional' => 5000,
                                'total' => 15000
                            ],
                            'is_lowest' => 1
                        ]
                    ],
                    [
                        'name' => 'Next Day',
                        'service_type' => 4,
                        'courier' => [
                            'id' => 4,
                            'name' => 'SAP Express',
                            'service_type' => 1,
                            'insurance' => 0,
                            'fee' => [
                                'base' => 8000,
                                'additional' => 5000,
                                'total' => 13000
                            ],
                            'is_lowest' => 1
                        ]
                    ],
                    [
                        'name' => 'DC',
                        'service_type' => 5,
                        'courier' => [
                            'id' => 5,
                            'name' => 'DC',
                            'service_type' => 5,
                            'insurance' => 0,
                            'fee' => [
                                'base' => 50000,
                                'additional' => 0,
                                'total' => 50000
                            ],
                            'is_lowest' => 1
                        ]
                    ],
                    [
                        'name' => 'TransCourier',
                        'service_type' => 6,
                        'courier' => [
                            'id' => 6,
                            'name' => 'Transmart Courier',
                            'service_type' => 6,
                            'insurance' => 0,
                            'fee' => [
                                'base' => 20000,
                                'additional' => 5000,
                                'total' => 25000
                            ],
                            'is_lowest' => 1
                        ]
                    ]
                ],
                //'total_weight' => 0,
                //'total_price' => 0,
                //'total_qty' => 0,
                //'is_warehouse' => ''
            ],
            //'total_weight' => 0,
            //'total_price' => 0,
            //'total_qty' => 0,
            //'is_warehouse' => ''
        ];

        $sampleResponse = [
            'content' => []
        ];
        //Assign correct sku to fake data
        $total = count($data);
        for ($i = 0; $i < $total; $i++) {
            $order = $sampleOrder;
            foreach ($data[$i]['items'] as $item) {
                $order['data']['items'][] =
                    [
                        'sku' => $item['sku'],
                        'sku_basic' => $item['sku'],
                        'price' => $item['price'],
                        'weight' => $item['weight'],
                        'quantity' => $item['quantity'],
                        'is_spo' => false,
                        'is_own_courier' => false
                    ];
                //$order['total_weight'] += $item['weight'];
                //$order['total_price'] += $item['price'];
                //$order['total_qty'] += $item['quantity'];
                /*if ($item['sku'] == '14346002001001') {
                    unset($order['data']['shipping_list'][0]);
                    unset($order['data']['shipping_list'][1]);
                    unset($order['data']['shipping_list'][2]);
                    unset($order['data']['shipping_list'][3]);
                    unset($order['data']['shipping_list'][5]);
                } elseif ($item['sku'] == 'Whiskas') {
                    unset($order['data']['shipping_list'][0]);
                    unset($order['data']['shipping_list'][2]);
                    unset($order['data']['shipping_list'][3]);
                    unset($order['data']['shipping_list'][4]);
                } else {
                    unset($order['data']['shipping_list'][0]);
                    unset($order['data']['shipping_list'][1]);
                    unset($order['data']['shipping_list'][2]);
                    unset($order['data']['shipping_list'][3]);
                    unset($order['data']['shipping_list'][4]);
                    unset($order['data']['shipping_list'][5]);
                }
                $order['data']['store']['store_code'] += $j;
                $sampleResponse['content'][] = $order;*/
            }
            $sampleResponse['content'][] = $order;
        }
        return $sampleResponse;
    }

    /**
     * @return array
     */
    public function getListMethodName()
    {
        return [
            1 => __('Regular (2-7 days)'),
            2 => __('Same day (3 hours)'),
            3 => __('Scheduling'),
            4 => __('Next day (1 day)'),
            //5 => __('Regular (2-7 days)'),
            //6 => __('Same day (3 hours)')
            5 => __('DC'),
            6 => __('Transmart Courier')
        ];
    }

    /**
     * @return array
     */
    public function getListMethodFakeName()
    {
        return [
            1 => __('Regular (2-7 days)'),
            2 => __('Same day (3 hours)'),
            3 => __('Scheduling'),
            4 => __('Next day (1 day)')
        ];
    }

    /**
     * @param $data
     * @return array|bool|float|int|mixed|string|null
     */
    protected function sendOAR($data)
    {
        $response = [];
        $url = $this->configHelper->getOmsBaseUrl() . $this->configHelper->getOmsOarApi();
        $header = $this->getHeader();
        $flagLog = 'Quote ID and Quote Address ID:';
        if (isset($data[0]['quote_address_id'])) {
            $flagLog .=  ' ' . $data[0]['quote_address_id'];
            unset($data[0]['quote_address_id']);
        }
        $dataJson = $this->serializer->serialize($data);
        $dataJsonLog = $dataJson;
        for ($x = 0; $x <= 2; $x++) {
            try {
                $responseOAR = $this->curlHelper->post($url, $header, $dataJson);
                $response = $responseOAR;
                if (is_string($responseOAR)) {
                    $response = $this->serializer->unserialize($responseOAR);
                }
                // check timing get OAR
                 $dateBegin = new DateTime();
                 $this->logger->info('$Date Begin OAR : ' . $dateBegin->getTimestamp());
                 sleep(5);
                 $dateEnd = new DateTime();
                 $this->logger->info('$Date End OAR : ' . $dateEnd->getTimestamp()
                 $diff = $dateEnd->getTimestamp() - $dateBegin->getTimestamp();
                 $this->logger->info('$Date Different OAR : ' . $diff);
                $this->writeSuccessLog($flagLog, $dataJsonLog, $responseOAR);
                break;
            } catch (\Exception $e) {
                $result = $this->handleException($e);
                if ($result['status'] == 'success') {
                    if (is_string($result['response'])) {
                        $response = $this->serializer->unserialize($result['response']);
                    } else {
                        $response = $result['response'];
                    }
                    $this->writeSuccessLog($flagLog, $dataJsonLog, $result['response']);
                    break;
                } else {
                    $this->writeErrorLog($flagLog, $dataJsonLog, $e);
                    $response['error'] = $e->getMessage();
                }
            }
        }
        return $response;
    }

    /**
     * @param $postCode
     * @return bool
     */
    public function checkShippingPostCode($postCode)
    {
        if ($this->helperConfig->isActiveFulfillmentStore()) {
            $table = $this->readAdapter->getTableName('omni_shipping_postcode');
            $select = $this->readAdapter->select()->from(
                [$table],
                ['post_code']
            )
                ->where('post_code = :post_code');
            $bind = [
                ':post_code' => $postCode,
            ];
            $result = $this->readAdapter->fetchCol($select, $bind);
            if (empty($result)) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param $e
     * @return string[]
     */
    protected function handleException($e)
    {
        $result = ['status' => 'error'];
        $responseOAR = $e->getMessage();
        try {
            $response = $this->serializer->unserialize($responseOAR);
            if (isset($response['content'])) {
                $result['status'] = 'success';
                $result['response'] = $response;
            }

        } catch (\Exception $exception) {
        }
        return $result;
    }

    /**
     * @param $flagLog
     * @param $dataJsonLog
     * @param $responseOAR
     */
    protected function writeSuccessLog($flagLog, $dataJsonLog, $responseOAR)
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/oar-response-success.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        if (is_array($responseOAR)) {
            try {
                $responseOAR = $this->serializer->serialize($responseOAR);
            } catch (\Exception $e) {
                $responseOAR = 'can not serialize';
            }
        }
        $logger->info($flagLog . '. Request: ' . $dataJsonLog . '. Response: ' . $responseOAR);
    }

    /**
     * @param $flagLog
     * @param $dataJsonLog
     * @param $e
     */
    protected function writeErrorLog($flagLog, $dataJsonLog, $e)
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/oar-response-error.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $response = $e->getMessage();
        if (is_array($response)) {
            try {
                $response = $this->serializer->serialize($response);
            } catch (\Exception $exception) {
                $response = 'can not serialize';
            }
        }
        $logger->info($flagLog . '. Request: ' . $dataJsonLog . '. Error: ' . $response);
    }
}
