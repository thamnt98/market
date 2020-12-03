<?php
/**
 * Created by PhpStorm.
 * User: thelightsp
 * Date: 3/31/20
 * Time: 10:58 AM
 */

namespace SM\Checkout\Block;

use Magento\Checkout\Model\CompositeConfigProvider;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\View\Element\Template\Context;
use SM\FreshProductApi\Api\Data\FreshProductInterface;
use SM\FreshProduct\Helper\Data as FreshHelper;

class Onepage extends \Magento\Checkout\Block\Onepage
{
    /**
     * @var \SM\Checkout\Helper\Config
     */
    protected $helperConfig;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resourceConnection;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $readAdapter;

    /**
     * @var \Trans\LocationCoverage\Model\CityFactory
     */
    protected $cityFactory;

    /**
     * @var \Trans\LocationCoverage\Model\DistrictFactory
     */
    protected $districtFactory;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $serializer;

    /**
     * @var FreshHelper
     */
    protected $freshHelper;

    /**
     * @var \SM\Checkout\Model\CheckoutProviderHandle
     */
    protected $checkoutProviderHandle;

    /**
     * Onepage constructor.
     * @param Context $context
     * @param FormKey $formKey
     * @param CompositeConfigProvider $configProvider
     * @param array $layoutProcessors
     * @param array $data
     * @param \SM\Checkout\Helper\Config $helperConfig
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     * @param \Trans\LocationCoverage\Model\CityFactory $cityFactory
     * @param \Trans\LocationCoverage\Model\DistrictFactory $districtFactory
     * @param \Magento\Framework\Serialize\Serializer\Json|null $serializer
     * @param \Magento\Framework\Serialize\SerializerInterface|null $serializerInterface
     * @param FreshHelper $freshHelper
     * @param \SM\Checkout\Model\CheckoutProviderHandle $checkoutProviderHandle
     */
    public function __construct(
        Context $context,
        FormKey $formKey,
        CompositeConfigProvider $configProvider,
        array $layoutProcessors = [],
        array $data = [],
        \SM\Checkout\Helper\Config $helperConfig,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Trans\LocationCoverage\Model\CityFactory $cityFactory,
        \Trans\LocationCoverage\Model\DistrictFactory $districtFactory,
        FreshHelper $freshHelper,
        \SM\Checkout\Model\CheckoutProviderHandle $checkoutProviderHandle,
        \Magento\Framework\Serialize\Serializer\Json $serializer = null,
        \Magento\Framework\Serialize\SerializerInterface $serializerInterface = null
    ) {
        parent::__construct(
            $context,
            $formKey,
            $configProvider,
            $layoutProcessors,
            $data,
            $serializer,
            $serializerInterface
        );
        $this->helperConfig = $helperConfig;
        $this->resourceConnection = $resourceConnection;
        $this->readAdapter = $this->resourceConnection->getConnection('core_read');
        $this->cityFactory = $cityFactory;
        $this->districtFactory = $districtFactory;
        $this->serializer = $serializer;
        $this->freshHelper = $freshHelper;
        $this->checkoutProviderHandle = $checkoutProviderHandle;
    }

    /**
     * @inheritdoc
     */
    public function getJsLayout()
    {
        return $this->serializer->serialize($this->jsLayout);
    }

    /**
     * @return bool|string
     */
    public function getSerializedCheckoutConfig()
    {
        $data = $this->checkoutProviderHandle->handle();
        $data['currentUrl'] = $this->getRequest()->getRouteName() . '_' . $this->getRequest()->getControllerName() . '_' . $this->getRequest()->getActionName();
        $data = $data + $this->getCheckoutConfig();
        $customerAddressData = $data['customerData']['addresses'];
        $listPostCode = [];
        if ($this->helperConfig->isActiveFulfillmentStore()) {
            foreach ($customerAddressData as $addressId => $address) {
                if (isset($address['postcode']) && $address['postcode'] != '' && $address['postcode'] != '*****') {
                    $listPostCode[] = $address['postcode'];
                }
            }
            if (empty($listPostCode)) {
                $listPostCodeSupportShipping = [];
            } else {
                $listPostCodeSupportShipping = $this->checkShippingPostCode($listPostCode);
            }
            foreach ($customerAddressData as $addressId => $address) {
                $cityName = $this->getCityName($address['city']);
                $customerAddressData[$addressId]['custom_attributes']['city'] = ['attribute_code' => 'city', 'value' => $cityName];
                if (isset($customerAddressData[$addressId]['custom_attributes']['district'])) {
                    $districtData = $customerAddressData[$addressId]['custom_attributes']['district'];
                    if (is_numeric($districtData['value'])) {
                        $districtData['value'] = $this->getDistrictName($districtData['value']);
                    }
                    $customerAddressData[$addressId]['custom_attributes']['district'] = $districtData;
                }
                if (isset($address['postcode']) && $address['postcode'] != '' && in_array($address['postcode'], $listPostCodeSupportShipping)) {
                    $customerAddressData[$addressId]['custom_attributes']['support_shipping'] = ['attribute_code' => 'support_shipping', 'value' => true];
                } else {
                    $customerAddressData[$addressId]['custom_attributes']['support_shipping'] = ['attribute_code' => 'support_shipping', 'value' => false];
                }
            }
        } else {
            foreach ($customerAddressData as $addressId => $address) {
                $cityName = $this->getCityName($address['city']);
                $customerAddressData[$addressId]['custom_attributes']['city'] = ['attribute_code' => 'city', 'value' => $cityName];
                if (isset($customerAddressData[$addressId]['custom_attributes']['district'])) {
                    $districtData = $customerAddressData[$addressId]['custom_attributes']['district'];
                    if (is_numeric($districtData['value'])) {
                        $districtData['value'] = $this->getDistrictName($districtData['value']);
                    }
                    $customerAddressData[$addressId]['custom_attributes']['district'] = $districtData;
                }
                $customerAddressData[$addressId]['custom_attributes']['support_shipping'] = ['attribute_code' => 'support_shipping', 'value' => true];
            }
        }

        $data['customerData']['addresses'] = $customerAddressData;

        $quoteItemData = $data['quoteItemData'];
        $imageData = $data['imageData'];
        $quoteData = $data['quoteData'];
        $freshData = $this->freshProductData();

        foreach ($quoteItemData as $item) {
            foreach ($imageData as $key => $value) {
                if ($item['item_id'] == $key) {
                    $imageData[$key]['product_type'] = $item['product_type'];
                    //Add fresh product data
                    foreach ($freshData as $fresh) {
                        if ($this->validateData($fresh, $item['product'])) {
                            $imageData[$key][$fresh] = $item['product'][$fresh];
                        }
                    }

                    if ($this->validateData(FreshProductInterface::WEIGHT, $item['product'])) {
                        $imageData[$key]['fresh_weight'] = (float)$item['product'][FreshProductInterface::WEIGHT];
                    } else {
                        $imageData[$key]['fresh_weight'] = 0;
                    }

                    if (isset($item['product'][FreshProductInterface::OWN_COURIER])) {
                        if ($item['product'][FreshProductInterface::OWN_COURIER] == 1) {
                            $quoteData['has_fresh_item'] = 1;
                            $quoteData['fresh_tooltip'] = $this->getToolTip();
                        }
                    }

                    $imageData[$key]['is_warehouse'] = (bool)($item['product']['is_warehouse'] ?? false);
                }
            }
        }
        $data['imageData'] = $imageData;
        $data['quoteData'] = $quoteData;

        return  $this->serializer->serialize($data);
    }

    /**
     * @param int $id
     * @return string
     */
    protected function getCityName($id)
    {
        $city = $this->cityFactory->create()->load($id);
        if ($city->getId()) {
            return $city->getCity();
        }
        return '';
    }

    /**
     * @param $id
     * @return string
     */
    protected function getDistrictName($id)
    {
        $district = $this->districtFactory->create()->load($id);
        if ($district->getId()) {
            return $district->getDistrict();
        }
        return '';
    }

    /**
     * @return array
     */
    protected function freshProductData()
    {
        return [
            FreshProductInterface::OWN_COURIER,
            FreshProductInterface::PRICE_IN_KG,
            FreshProductInterface::SOLD_IN,
            FreshProductInterface::PROMO_PRICE_IN_KG,
            FreshProductInterface::BASE_PRICE_IN_KG,
            FreshProductInterface::IS_DECIMAL
        ];
    }

    /**
     * @param $key
     * @param $data
     * @return bool
     */
    protected function validateData($key, $data)
    {
        if (array_key_exists($key, $data)) {
            return true;
        }
        return false;
    }

    /**
     * @param $listPostCode
     * @return array
     */
    protected function checkShippingPostCode($listPostCode)
    {
        try {
            $table = $this->readAdapter->getTableName('omni_shipping_postcode');
            $select = $this->readAdapter->select()->from(
                [$table],
                ['post_code']
            )
                ->where(
                    'post_code IN (' . implode(',', $listPostCode) . ')'
                );
            return $this->readAdapter->fetchCol($select);
        } catch (\Exception $e) {
            return [];
        }
    }

    protected function getToolTip()
    {
        return $this->freshHelper->getTooltip();
    }
}
