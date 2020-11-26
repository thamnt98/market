<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Notification
 *
 * Date: September, 09 2020
 * Time: 4:18 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Notification\Helper;

use SM\Notification\Model\Source\RedirectType as RedirectType;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const DEFAULT_IMAGE_FILE_ID = 'SM_Notification/img/notification_icon_default.png';

    const DIR_IMAGE_KEY = 'notification';

    const XML_GROUP_IMAGE                        = 'sm_notification/image/';
    const XML_DEFAULT_IMAGE                      = self::XML_GROUP_IMAGE . 'default';
    const XML_GROUP_IMAGE_ORDER_STATUS           = self::XML_GROUP_IMAGE . 'order_status/';
    const XML_IMAGE_ORDER_STATUS_COMPLETED       = self::XML_GROUP_IMAGE_ORDER_STATUS . 'completed';
    const XML_IMAGE_ORDER_STATUS_READY_TO_PICKUP = self::XML_GROUP_IMAGE_ORDER_STATUS . 'ready_to_pickup';
    const XML_IMAGE_ORDER_STATUS_DELIVERED       = self::XML_GROUP_IMAGE_ORDER_STATUS . 'delivered';
    const XML_IMAGE_ORDER_STATUS_IN_DELIVERY     = self::XML_GROUP_IMAGE_ORDER_STATUS . 'in_delivery';
    const XML_GROUP_IMAGE_PAYMENT                = self::XML_GROUP_IMAGE . 'payment/';
    const XML_IMAGE_PAYMENT_FAILED               = self::XML_GROUP_IMAGE_PAYMENT . 'failed';
    const XML_IMAGE_PAYMENT_SUCCESS              = self::XML_GROUP_IMAGE_PAYMENT . 'success';

    const XML_EVENT_TYPE = 'sm_notification/event_type_config/event_type';

    /**
     * @var \Magento\Framework\View\Asset\Repository
     */
    protected $assetRepo;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    protected $productRepository;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $state;

    /**
     * @var \Amasty\ShopbyBrand\Helper\Data
     */
    protected $brandHelper;
    
    /**
     * @var \SM\Help\Model\Url
     */
    protected $helpUrl;
    
    /**
     * @var \SM\Help\Model\TopicRepository
     */
    protected $helpRepository;

    /**
     * @var \SM\Notification\Model\Source\EventType
     */
    protected $eventTypeOptions;

    /**
     * Data constructor.
     *
     * @param \SM\Notification\Model\Source\EventType    $eventTypeOptions
     * @param \SM\Help\Model\Url                         $helpUrl
     * @param \SM\Help\Model\TopicRepository             $helpRepository
     * @param \Amasty\ShopbyBrand\Helper\Data            $brandHelper
     * @param \Magento\Framework\App\State               $state
     * @param \Magento\Catalog\Model\ProductRepository   $productRepository
     * @param \Magento\Framework\View\Asset\Repository   $assetRepo
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Helper\Context      $context
     */
    public function __construct(
        \SM\Notification\Model\Source\EventType $eventTypeOptions,
        \SM\Help\Model\Url $helpUrl,
        \SM\Help\Model\TopicRepository $helpRepository,
        \Amasty\ShopbyBrand\Helper\Data $brandHelper,
        \Magento\Framework\App\State $state,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Framework\View\Asset\Repository $assetRepo,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Helper\Context $context
    ) {
        parent::__construct($context);
        $this->assetRepo = $assetRepo;
        $this->storeManager = $storeManager;
        $this->productRepository = $productRepository;
        $this->state = $state;
        $this->brandHelper = $brandHelper;
        $this->helpUrl = $helpUrl;
        $this->helpRepository = $helpRepository;
        $this->eventTypeOptions = $eventTypeOptions;
    }

    /**
     * @param string $uri
     *
     * @return string
     */
    public function getNotificationImageUrl($uri)
    {
        if (strpos($uri, 'http') === 0) {
            return $uri;
        }

        try {
            $store = $this->storeManager->getStore();
        } catch (\Exception $e) {
            return $this->assetRepo->getUrl(self::DEFAULT_IMAGE_FILE_ID);
        }

        if (empty($uri)) {
            $defaultImg = $this->scopeConfig->getValue(
                self::XML_DEFAULT_IMAGE,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );

            if ($defaultImg) {
                $defaultUrl = $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA)
                    . self::DIR_IMAGE_KEY . '/' . $defaultImg;
            } else {
                $defaultUrl = $this->assetRepo->getUrl(self::DEFAULT_IMAGE_FILE_ID);
            }

            return $defaultUrl;
        } else {
            return $store->getBaseUrl() . $uri;
        }
    }

    /**
     * @param string          $path
     * @param string|int|null $store
     *
     * @return string
     */
    public function getConfigImage($path, $store = null)
    {
        return $this->scopeConfig->getValue(
            $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param string          $xmlPath
     * @param string|int|null $store
     *
     * @return string
     */
    public function getMediaPathImage($xmlPath, $store = null)
    {
        $config = $this->getConfigImage($xmlPath, $store);

        if (empty($config)) {
            return '';
        } else {
            return \Magento\Framework\UrlInterface::URL_TYPE_MEDIA . '/'
                . self::DIR_IMAGE_KEY . '/'
                . $config;
        }
    }

    /**
     * @return array
     */
    public function getEventEnable()
    {
        $result = [];
        $list = $this->getEventConfig();

        foreach ($list as $item) {
            if (isset($item['event_type']) &&
                isset($item['enable']) &&
                $item['enable']
            ) {
                $result[$item['event_type']] = $item;
            }
        }

        return $result;
    }

    /**
     * @param string $event
     *
     * @return string
     */
    public function getEventTitle($event)
    {
        $options = $this->eventTypeOptions->getTreeEvent();
        foreach ($options as $groups) {
            if (empty($groups['value'])) {
                continue;
            }

            foreach ($groups['value'] as $option) {
                if (!empty($option['value']) && $option['value'] === $event) {
                    return $option['label'] ?? '';
                }
            }
        }

        foreach ($options as $key => $option) {
            if ($key === $event) {
                return $option['label'] ?? '';
            }
        }

        return '';
    }

    /**
     * @return array
     */
    public function getEventConfig()
    {
        $list = $this->scopeConfig->getValue(
            self::XML_EVENT_TYPE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        if (empty($list)) {
            return [];
        } else {
            return json_decode($list, true);
        }
    }

    /**
     * @param string $type
     * @param        $id
     *
     * @return string
     */
    public function getRedirectUrl($type, $id)
    {
        try {
            switch ($type) {
                case RedirectType::TYPE_PDP:
                    return $this->productRepository->get($id)->getProductUrl();
                case RedirectType::TYPE_HELP_PAGE:
                    return $this->getHelpPageUrlById($id);
                case RedirectType::TYPE_GIFT_LIST:
                    return $this->_getUrl('giftregistry');
                case RedirectType::TYPE_SUBSCRIPTION_LIST:
                    return $this->_getUrl('amasty_recurring/customer/subscriptions');
                case RedirectType::TYPE_BRAND:
                    return $this->getBrandUrlByKey($id);
                case RedirectType::TYPE_VOUCHER_LIST:
                    return $this->_getUrl('myvoucher/voucher/');
                case RedirectType::TYPE_VOUCHER_DETAIL:
                    return $this->_getUrl('myvoucher/voucher/detail', ['id' => $id]);
                case RedirectType::TYPE_ORDER_DETAIL:
                    return $this->_getUrl('sales/order/physical', ['id' => $id]);
                case RedirectType::TYPE_SHOPPING_LIST:
                    return $this->_getUrl('shoppinglist');
                case RedirectType::TYPE_CART:
                    return $this->_getUrl('checkout/cart');
                case RedirectType::TYPE_HOME:
                    return $this->_getUrl('');
                default:
                    return '#';
            }
        } catch (\Exception $e) {
            return '#';
        }
    }

    /**
     * @return int
     */
    public function getStoreId()
    {
        try {
            return $this->storeManager->getStore()->getId();
        } catch (\Exception $e) {
            return $this->storeManager->getDefaultStoreView()->getId();
        }
    }

    /**
     * @return bool
     */
    public function isApiRequest()
    {
        try {
            if (strpos($this->state->getAreaCode(), 'webapi') === false) {
                return false;
            } else {
                return true;
            }
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @param string $key
     *
     * @return string
     */
    public function getBrandUrlByKey($key)
    {
        $url = '#';

        if ($key) {
            $urlKey = $this->brandHelper->getBrandUrlKey();
            $urlSuffix = $this->brandHelper->getSuffix();
            $url = $this->_getUrl('') . (!!$urlKey ? $urlKey . '/' . $key : $key) . $urlSuffix;
        }

        return $url;
    }

    /**
     * @param int $id
     *
     * @return string
     */
    public function getHelpPageUrlById($id)
    {
        try {
            /** @var \SM\Help\Model\Topic $help */
            $help = $this->helpRepository->getById($id);

            return $this->helpUrl->getTopicUrl($help);
        } catch (\Exception $e) {
            return '#';
        }
    }
}
