<?php

namespace SM\Sales\Helper\Email;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;

class Data extends AbstractHelper
{
    const CUSTOMER_NAME = 'customer_name';
    const ORDER = 'order';
    const ORDER_INCREMENT = 'order_increment';
    const ORDER_TOTAL = 'order_total';
    const VIRTUAL_ACCOUNT_NUMBER =  'virtual_account_number';
    const PAYMENT_METHOD_TITLE = 'payment_method_title';
    const ORDER_URL = 'order_url';
    const DELIVERY_METHOD = 'delivery_method';
    const EXPIRE_TIME =  'expire_time';
    const IS_VA_MEGA = 'is_va_mega';
    const IS_VA_BCA = 'is_va_bca';
    const STORE_SOURCE = 'store_source';
    const STORE_ADDRESS = 'store_address';
    const LOGO = 'logo';
    const HOW_TO_PAY = 'how_to_pay';
    const EXPIRE_TIME_STRING = 'expire_time_string';
    const FORMATTED_SHIPPING_ADDRESS = 'formattedShippingAddress';
    const ADDITIONAL_DATA = 'additional_data';
    const HOW_TO_PAY_LINK = "sm_sale/email/how_to_pay_link";
    const ORDER_RECEIVED_URL = 'order_received_url';
    const REVIEW_URL = 'review_url';
    const KEY_IS_STORE_PICKUP = 'is_store_pick_up';

    /**
     * @var \Magento\InventoryApi\Api\SourceRepositoryInterface
     */
    protected $sourceRepository;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var PriceHelper
     */
    protected $priceHelper;

    /**
     * @var \Magento\Sales\Model\Order\Address\Renderer
     */
    protected $addressRenderer;

    /**
     * EmailDataHelper constructor.
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\InventoryApi\Api\SourceRepositoryInterface $sourceRepository,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        PriceHelper $priceHelper,
        \Magento\Sales\Model\Order\Address\Renderer $addressRenderer
    ) {
        parent::__construct($context);
        $this->sourceRepository = $sourceRepository;
        $this->storeManager = $storeManager;
        $this->priceHelper = $priceHelper;
        $this->addressRenderer= $addressRenderer;
    }

    public function getStoreName($order)
    {
        try {
            $source = $this->getStoreSource($order);
            return $source->getName();
        } catch (\Exception $e) {
            return '';
        }
    }

    public function getStoreAddress($order)
    {
        try {
            $source = $this->getStoreSource($order);
            return $source->getStreet();
        } catch (\Exception $e) {
            return '';
        }
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getStoreSource($order)
    {
        if ($order->getData("source_code")) {
            $sourceCode = $order->getData("source_code");
        } else {
            $sourceCode = $order->getData("split_store_code");
        }

        return $this->sourceRepository->get($sourceCode);
    }

    public function getConfigData($type, $store)
    {
        return $this->scopeConfig->getValue(
            $type,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param $code
     * @return mixed|string
     */
    public function getPaymentLogo($code)
    {
        $logoUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $logo = "logo/paymentmethod/" . $this->getPaymentLogoConfig($code);
        return $logoUrl . $logo;
    }

    /**
     * @param $code
     * @return mixed|string
     */
    public function getPaymentLogoConfig($code)
    {
        return $this->scopeConfig->getValue('payment/'. $code .'/logo');
    }

    /**
     * Get currency symbol for current locale and currency code
     *
     * @param $price
     * @return string
     */
    public function getPrice($price)
    {
        return $this->priceHelper->currency($price, true, false);
    }

    /**
     * @param string $shippingMethod
     * @param string $shippingDescription
     * @return \Magento\Framework\Phrase|mixed|string
     */
    public function getDeliveryMethod($shippingMethod, $shippingDescription)
    {
        if ($shippingMethod == "store_pickup_store_pickup") {
            return __("Pick Up in Store");
        } else {
            $shippingDescription = explode(" - ", $shippingDescription);
            if (isset($shippingDescription[1])) {
                return $shippingDescription[1];
            }
        }
        return __("Not available");
    }

    public function getReorderUrl($order)
    {
        return $order->getStore()->getUrl(
            'sales/order/submitreorderall',
            [
                'parent_order_id' => $order->getId()
            ]
        );
    }

    /**
     * Render shipping address into html.
     *
     * @param $order
     * @return string|null
     */
    public function getFormattedShippingAddress($order)
    {
        return $order->getIsVirtual()
            ? null
            : $this->addressRenderer->format($order->getShippingAddress(), 'html');
    }

}
