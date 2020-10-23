<?php

namespace SM\Checkout\Block\Checkout;

use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Magento\Sales\Model\Order;
use SM\Checkout\Helper\Config;
use SM\Checkout\Helper\Payment;
use SM\Sales\Model\ParentOrderRepository;
use Trans\LocationCoverage\Model\CityRepository;
use Trans\LocationCoverage\Model\DistrictRepository;
use Trans\Sprint\Helper\Config as SprintHelper;

/**
 * Class Success
 * @package SM\Checkout\Block\Checkout
 */
class Success extends \Magento\Checkout\Block\Onepage\Success
{

    /**
     * @var \Magento\Sales\Model\Order
     */
    protected $order;
    /**
     * @var \Trans\Sprint\Api\SprintResponseRepositoryInterface
     */
    protected $sprintResponseRepository;
    /**
     * @var \Trans\Sprint\Api\SprintPaymentFlagRepositoryInterface
     */
    protected $sprintPaymentFlagRepository;
    /**
     * @var SprintHelper
     */
    protected $sprintConfig;
    /**
     * @var SprintHelper
     */
    protected $sprintPaymentLogo;
    /**
     * @var \Trans\Sprint\Helper\Data
     */
    protected $sprintHelperData;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;
    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $priceHelper;
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;
    /**
     * @var SourceRepositoryInterface
     */
    protected $sourceRepository;
    /**
     * @var \Magento\InventoryApi\Api\Data\SourceInterface
     */
    protected $pickupAtStore;

    /**
     * @var Config
     */
    protected $checkoutConfigHelper;
    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $imageHelper;
    /**
     * @var TimezoneInterface
     */
    protected $timeZone;
    /**
     * @var CityRepository
     */
    protected $cityRepository;
    /**
     * @var DistrictRepository
     */
    protected $districtRepository;
    /**
     * @var Payment
     */
    protected $paymentHelper;
    /**
     * @var ParentOrderRepository
     */
    protected $parentOrderRepository;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $orderCollectionFactory;

    /**
     * Success constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param Order\Config $orderConfig
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Trans\Sprint\Api\SprintResponseRepositoryInterface $sprintResponseRepository
     * @param \Trans\Sprint\Api\SprintPaymentFlagRepositoryInterface $sprintPaymentFlagRepository
     * @param SprintHelper $config
     * @param SprintHelper $paymentLogo
     * @param \Trans\Sprint\Helper\Data $sprintHelperData
     * @param \Magento\Framework\Pricing\Helper\Data $priceHelper
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param TimezoneInterface $timezone
     * @param SourceRepositoryInterface $sourceRepository
     * @param Config $checkoutConfigHelper
     * @param \Magento\Catalog\Helper\Image $image
     * @param CityRepository $cityRepository
     * @param DistrictRepository $districtRepository
     * @param Payment $paymentHelper
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Model\Order\Config $orderConfig,
        \Magento\Framework\App\Http\Context $httpContext,
        \Trans\Sprint\Api\SprintResponseRepositoryInterface $sprintResponseRepository,
        \Trans\Sprint\Api\SprintPaymentFlagRepositoryInterface $sprintPaymentFlagRepository,
        \Trans\Sprint\Helper\Config $config,
        SprintHelper $paymentLogo,
        \Trans\Sprint\Helper\Data $sprintHelperData,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        TimezoneInterface $timezone,
        SourceRepositoryInterface $sourceRepository,
        Config $checkoutConfigHelper,
        \Magento\Catalog\Helper\Image $image,
        CityRepository $cityRepository,
        DistrictRepository $districtRepository,
        Payment $paymentHelper,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $checkoutSession, $orderConfig, $httpContext, $data);
        $this->logger                      = $context->getLogger();
        $this->order                       = $this->_checkoutSession->getLastRealOrder();
        $this->sprintResponseRepository    = $sprintResponseRepository;
        $this->sprintPaymentFlagRepository = $sprintPaymentFlagRepository;
        $this->sprintConfig                = $config;
        $this->sprintPaymentLogo           = $paymentLogo;
        $this->sprintHelperData            = $sprintHelperData;
        $this->priceHelper                 = $priceHelper;
        $this->date                        = $date;
        $this->checkoutConfigHelper        = $checkoutConfigHelper;
        $this->sourceRepository            = $sourceRepository;
        $this->imageHelper                 = $image;
        $this->timeZone                    = $timezone;
        $this->cityRepository              = $cityRepository;
        $this->districtRepository          = $districtRepository;
        $this->paymentHelper = $paymentHelper;
        $this->orderCollectionFactory = $orderCollectionFactory;
    }

    public function getGrandTotal()
    {
        return $this->priceHelper->currency($this->order->getGrandTotal());
    }
    public function getSubTotal()
    {
        return $this->priceHelper->currency($this->order->getSubtotal());
    }

    public function getReferenceNumber()
    {
        return $this->order->getReferenceNumber();
    }

    public function getCreatedAt()
    {
        return $this->timeZone->date($this->order->getCreatedAt())->format('Y-m-d H:i:s');
    }

    public function getTitlePaymentMethod()
    {
        return $this->order->getPayment()->getMethodInstance()->getTitle();
    }

    public function getPaymentMethod()
    {
        return $this->order->getPayment()->getMethod();
    }

    public function getOrderId()
    {
        return $this->order->getId();
    }

    public function getOrderItems()
    {
        return $this->order->getAllItems();
    }

    public function getShippingFee()
    {
        return $this->priceHelper->currency($this->order->getBaseShippingInclTax());
    }

    public function getDiscountAmount()
    {
        return $this->priceHelper->currency($this->order->getDiscountAmount());
    }

    public function getSprintOrder()
    {
        return $this->sprintResponseRepository->getByTransactionNo($this->getReferenceNumber());
    }
    public function checkPaymentChannel()
    {
        $sprintOrder = $this->getSprintOrder();
        return $this->sprintConfig->getPaymentChannel($sprintOrder->getPaymentMethod());
    }

    public function isSucceed()
    {
        $paymentMethod = $this->getPaymentMethod();
        return $this->paymentHelper->isCredit($paymentMethod) || $this->paymentHelper->isInstallment($paymentMethod) || ($this->paymentHelper->isVirtualAccount($paymentMethod) && $this->isPaid());
    }

    public function isVirtualAccount()
    {
        return $this->paymentHelper->isVirtualAccount($this->getPaymentMethod());
    }

    public function getExpireTime()
    {
        $sprintOrder = $this->getSprintOrder();

        if ($sprintOrder->getId()) {
            return $this->date->timestamp($sprintOrder->getExpireDate());
        }

        return 0;
    }

    public function getExpireTimeString()
    {
        $sprintOrder = $this->getSprintOrder();

        if ($sprintOrder->getId()) {
            return $this->timeZone->date($sprintOrder->getExpireDate())->format('d F Y h:i A');
        }
        return '';
    }

    /**
     * Get payment code
     *
     * @return string
     */
    public function getPaycode()
    {
        try {
            $sprintOrder = $this->getSprintOrder();

            return $sprintOrder->getCustomerAccount();
        } catch (\Exception $e) {
            $this->logger->info('error : ' . $e->getMessage());
        }
    }

    public function getTimeLeft()
    {
        return $this->getExpireTime() - $this->date->gmtTimestamp()-$this->date->getGmtOffset();
    }

    public function getOrderStatus()
    {
        return $this->order->getStatus();
    }

    public function getOrderState()
    {
        return $this->order->getState();
    }

    /**
     * @return bool
     */
    public function isPaid()
    {
        return $this->getOrderStatus() === $this->getPaidStatus();
    }

    /**
     * @return string
     */
    public function getPaidStatus()
    {
        return $this->sprintConfig->getPaidState() ? $this->sprintConfig->getPaidState() : Order::STATE_PROCESSING;
    }

    public function getBlockHowToPay()
    {
        return $this->paymentHelper->getBlockHowToPay($this->getPaymentMethod(), true);
    }

    public function getLogoPayment()
    {
        return $this->paymentHelper->getLogoPayment($this->getPaymentMethod(), true)??null;
    }

    public function getWebsiteBanking()
    {
        $paymentMethod = $this->getPaymentMethod();
        return $this->paymentHelper->getWebsiteBanking($paymentMethod) ?: '#';
    }

    public function getShippingMethod()
    {
        return $this->order->getShippingMethod();
    }

    public function isPickupAtStore()
    {
        return $this->getShippingMethod() == 'store_pickup_store_pickup';
    }

    public function getPickupStoreName()
    {
        if (empty($this->pickupAtStore)) {
            $this->getPickupAtStore();
        }
        return $this->pickupAtStore->getName();
    }

    public function getPickupStoreInfo($data)
    {
        if (empty($this->pickupAtStore)) {
            $this->getPickupAtStore();
        }
        return  $this->pickupAtStore->getData($data)??'';
    }

    public function getPickupAtStore()
    {
        $storePickupCode = $this->order->getStorePickUp();
        $this->pickupAtStore= $this->sourceRepository->get($storePickupCode);
        return $this->pickupAtStore;
    }

    public function getStorePickupTime()
    {
        return $this->order->getStorePickUpTime();
    }

    public function getOrderIsVirtual()
    {
        return $this->order->getIsVirtual();
    }

    public function getTimeRedirect()
    {
        return $this->checkoutConfigHelper->getTimeRedirect();
    }

    public function getFullAddress()
    {
        return $this->getPickupStoreInfo('street') . ', ' . $this->getPickupStoreInfo('city') . ', ' . $this->getPickupStoreInfo('region') . ', ' . $this->getPickupStoreInfo('postcode');
    }

    public function getDeliveryAddress()
    {
        $address = $this->order->getShippingAddress();
        if (empty($address)) {
            return '';
        }
        return  'Deliver to: ' . $address->getAddressTag() . ' - ' . implode($address->getStreet(), ' ') . ', ' . $this->getCityName($address->getCity()) . ', ' . $this->getDistrictName($address->getDistrict()) . ' ' . $address->getPostcode();
    }

    public function getSecret()
    {
        /** @var \Trans\AllowLocation\Block\Locationallow $block */
        if ($block = $this->createBlock(\Trans\AllowLocation\Block\Locationallow::class)) {
            return $block->getSecret();
        }

        return '';
    }
    /**
     * @param        $block
     * @param string $name
     * @param array  $data
     *
     * @return \Magento\Framework\View\Element\BlockInterface|null
     */
    protected function createBlock($block, $data = [], $name = '')
    {
        try {
            return $this->getLayout()->createBlock($block, $name, $data);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function renderPrice($price)
    {
        return $this->priceHelper->currency($price);
    }
    public function getProductImageUrl($product)
    {
        return $this->imageHelper->init($product, 'product_thumbnail_image')->setImageFile($product->getFile())
                                                                            ->resize('74', '74')->getUrl();
    }
    /**
     * get rule id applied for GTM
     * @return float|string
     */
    public function getCouponCodeGTM()
    {
        return $this->order->getAppliedRuleIds() ?? "Not available";
    }

    /**
     * get Shipping fee for GTM
     * @return float
     */
    public function getShippingGTM()
    {
        return round($this->order->getBaseShippingInclTax());
    }

    public function getWeightUnit()
    {
        return $this->_scopeConfig->getValue(
            'general/locale/weight_unit',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @param int $id
     * @return string
     */
    public function getCityName($id)
    {
        $city = $this->cityRepository->getById($id);
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
        $district = $this->districtRepository->getById($id);
        if ($district->getId()) {
            return $district->getDistrict();
        }
        return '';
    }

    public function getTimeDisplayConfirmPage()
    {
        return ((int)$this->paymentHelper->getTimeDisplayConfirmPage())*1000;
    }

    public function getParentOrder()
    {
        return $this->parentOrderRepository->getById($this->order->getId());
    }

    public function getChildOrders($parentId)
    {
        return $this->orderCollectionFactory->create()->addFieldToSelect('*')->addFieldToFilter('parent_order', $parentId);
    }

    public function getChildOrderPickupAtStore($order)
    {
        $storePickupCode = $order->getStorePickUp();
        return $this->sourceRepository->get($storePickupCode);
    }

    public function getChildOrderDeliveryAddress($order)
    {
        $address = $order->getShippingAddress();
        if (empty($address)) {
            return '';
        }
        return  'Deliver to: ' . $address->getAddressTag() . ' - ' . implode($address->getStreet(), ' ') . ', ' . $this->getCityName($address->getCity()) . ', ' . $this->getDistrictName($address->getDistrict()) . ' ' . $address->getPostcode();
    }
}
