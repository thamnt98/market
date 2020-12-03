<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace SM\Checkout\Model\Checkout\Type;

use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Directory\Model\AllowedCountries;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Quote\Model\Quote\Address;
use Magento\Quote\Model\Quote\Item;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order\Email\Sender\OrderSender;
use Psr\Log\LoggerInterface;
use SM\Checkout\Helper\OrderReferenceNumber;

/**
 * Multishipping checkout model
 *
 * @api
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.CookieAndSessionMisuse)
 * @since 100.0.2
 */
class Multishipping extends \Magento\Framework\DataObject
{
    protected $quote;
    protected $customer;
    /**
     * Quote shipping addresses items cache
     *
     * @var array
     */
    protected $_quoteShippingAddressesItems;

    /**
     * Core event manager proxy
     *
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager = null;

    /**
     * Core store config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Framework\Session\Generic
     */
    protected $_session;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Quote\Model\Quote\AddressFactory
     */
    protected $_addressFactory;

    /**
     * @var \Magento\Payment\Model\Method\SpecificationInterface
     */
    protected $paymentSpecification;

    /**
     * Initialize dependencies.
     *
     * @var \Magento\Multishipping\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $_orderFactory;

    /**
     * @var AddressRepositoryInterface
     */
    protected $addressRepository;

    /**
     * @var OrderSender
     */
    protected $orderSender;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var \Magento\Framework\Api\FilterBuilder
     */
    protected $filterBuilder;

    /**
     * @var \Magento\Quote\Model\Quote\Address\ToOrder
     */
    protected $quoteAddressToOrder;

    /**
     * @var \Magento\Quote\Model\Quote\Item\ToOrderItem
     */
    protected $quoteItemToOrderItem;

    /**
     * @var \Magento\Quote\Model\Quote\Payment\ToOrderPayment
     */
    protected $quotePaymentToOrderPayment;

    /**
     * @var \Magento\Quote\Model\Quote\Address\ToOrderAddress
     */
    protected $quoteAddressToOrderAddress;

    /**
     * @var \Magento\Quote\Model\Quote\TotalsCollector
     */
    protected $totalsCollector;

    /**
     * @var \Magento\Quote\Api\Data\CartExtensionFactory
     */
    private $cartExtensionFactory;

    /**
     * @var AllowedCountries
     */
    private $allowedCountryReader;

    /**
     * @var \Magento\Quote\Model\Quote\ShippingAssignment\ShippingAssignmentProcessor
     */
    private $shippingAssignmentProcessor;

    /**
     * @var Multishipping\PlaceOrderFactory
     */
    private $placeOrderFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    private $dataObjectHelper;
    /**
     * @var OrderReferenceNumber
     */
    protected $referenceNumber;

    /**
     * Multishipping constructor.
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param AddressRepositoryInterface $addressRepository
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Session\Generic $session
     * @param \Magento\Quote\Model\Quote\AddressFactory $addressFactory
     * @param Address\ToOrder $quoteAddressToOrder
     * @param Address\ToOrderAddress $quoteAddressToOrderAddress
     * @param \Magento\Quote\Model\Quote\Payment\ToOrderPayment $quotePaymentToOrderPayment
     * @param \Magento\Quote\Model\Quote\Item\ToOrderItem $quoteItemToOrderItem
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \SM\Checkout\Model\Method\Specification\Composite $paymentSpecification
     * @param \Magento\Multishipping\Helper\Data $helper
     * @param OrderSender $orderSender
     * @param PriceCurrencyInterface $priceCurrency
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Framework\Api\FilterBuilder $filterBuilder
     * @param \Magento\Quote\Model\Quote\TotalsCollector $totalsCollector
     * @param array $data
     * @param \Magento\Quote\Api\Data\CartExtensionFactory|null $cartExtensionFactory
     * @param AllowedCountries|null $allowedCountryReader
     * @param \Magento\Multishipping\Model\Checkout\Type\Multishipping\PlaceOrderFactory|null $placeOrderFactory
     * @param LoggerInterface|null $logger
     * @param OrderReferenceNumber $referenceNumber
     * @param \Magento\Framework\Api\DataObjectHelper|null $dataObjectHelper
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        AddressRepositoryInterface $addressRepository,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Session\Generic $session,
        \Magento\Quote\Model\Quote\AddressFactory $addressFactory,
        \Magento\Quote\Model\Quote\Address\ToOrder $quoteAddressToOrder,
        \Magento\Quote\Model\Quote\Address\ToOrderAddress $quoteAddressToOrderAddress,
        \Magento\Quote\Model\Quote\Payment\ToOrderPayment $quotePaymentToOrderPayment,
        \Magento\Quote\Model\Quote\Item\ToOrderItem $quoteItemToOrderItem,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \SM\Checkout\Model\Method\Specification\Composite $paymentSpecification,
        \Magento\Multishipping\Helper\Data $helper,
        OrderSender $orderSender,
        PriceCurrencyInterface $priceCurrency,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        \Magento\Quote\Model\Quote\TotalsCollector $totalsCollector,
        array $data = [],
        \Magento\Quote\Api\Data\CartExtensionFactory $cartExtensionFactory = null,
        AllowedCountries $allowedCountryReader = null,
        \Magento\Multishipping\Model\Checkout\Type\Multishipping\PlaceOrderFactory $placeOrderFactory = null,
        LoggerInterface $logger = null,
        \SM\Checkout\Helper\OrderReferenceNumber $referenceNumber,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper = null,
        \Magento\Framework\Serialize\SerializerInterface $serializer
    ) {
        $this->_eventManager = $eventManager;
        $this->_scopeConfig = $scopeConfig;
        $this->_session = $session;
        $this->_addressFactory = $addressFactory;
        $this->_storeManager = $storeManager;
        $this->paymentSpecification = $paymentSpecification;
        $this->helper = $helper;
        $this->_checkoutSession = $checkoutSession;
        $this->_customerSession = $customerSession;
        $this->_orderFactory = $orderFactory;
        $this->addressRepository = $addressRepository;
        $this->orderSender = $orderSender;
        $this->priceCurrency = $priceCurrency;
        $this->quoteRepository = $quoteRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->quoteAddressToOrder = $quoteAddressToOrder;
        $this->quoteItemToOrderItem = $quoteItemToOrderItem;
        $this->quotePaymentToOrderPayment = $quotePaymentToOrderPayment;
        $this->quoteAddressToOrderAddress = $quoteAddressToOrderAddress;
        $this->totalsCollector = $totalsCollector;
        $this->referenceNumber = $referenceNumber;
        $this->serializer = $serializer;
        $this->cartExtensionFactory = $cartExtensionFactory ?: ObjectManager::getInstance()
            ->get(\Magento\Quote\Api\Data\CartExtensionFactory::class);
        $this->allowedCountryReader = $allowedCountryReader ?: ObjectManager::getInstance()
            ->get(AllowedCountries::class);
        $this->placeOrderFactory = $placeOrderFactory ?: ObjectManager::getInstance()
            ->get(\Magento\Multishipping\Model\Checkout\Type\Multishipping\PlaceOrderFactory::class);
        $this->logger = $logger ?: ObjectManager::getInstance()
            ->get(LoggerInterface::class);
        $this->dataObjectHelper = $dataObjectHelper ?: ObjectManager::getInstance()
            ->get(\Magento\Framework\Api\DataObjectHelper::class);
        parent::__construct($data);
        //$this->_init();
    }

    /**
     * Initialize multishipping checkout.
     *
     * Split virtual/not virtual items between default billing/shipping addresses
     *
     * @return \Magento\Multishipping\Model\Checkout\Type\Multishipping
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function _init()
    {
        /**
         * reset quote shipping addresses and items
         */
        $quote = $this->getQuote();
        if (!$this->getCustomer()->getId()) {
            return $this;
        }

        if ($this->getCheckoutSession()->getCheckoutState() === \Magento\Checkout\Model\Session::CHECKOUT_STATE_BEGIN
        ) {
            $this->getCheckoutSession()->setCheckoutState(true);
            /**
             * Remove all addresses
             */
            $addresses = $quote->getAllAddresses();
            foreach ($addresses as $address) {
                $quote->removeAddress($address->getId());
            }

            $defaultShippingId = $this->getCustomerDefaultShippingAddress();
            if ($defaultShippingId) {
                $quote->getShippingAddress()->importCustomerAddressData(
                    $this->addressRepository->getById($defaultShippingId)
                );

                foreach ($this->getQuoteItems() as $item) {
                    /**
                     * Items with parent id we add in importQuoteItem method.
                     * Skip virtual items
                     */
                    if ($item->getParentItemId() || $item->getProduct()->getIsVirtual()) {
                        continue;
                    }
                    $quote->getShippingAddress()->addItem($item);
                }
            }

            $defaultBillingAddressId = $this->getCustomerDefaultBillingAddress();
            if ($defaultBillingAddressId) {
                $quote->getBillingAddress()->importCustomerAddressData(
                    $this->addressRepository->getById($defaultBillingAddressId)
                );
                foreach ($this->getQuoteItems() as $item) {
                    if ($item->getParentItemId()) {
                        continue;
                    }
                    if ($item->getProduct()->getIsVirtual()) {
                        $quote->getBillingAddress()->addItem($item);
                    }
                }
            }
            $this->save();
        }
        return $this;
    }

    /**
     * Get quote items assigned to different quote addresses populated per item qty.
     *
     * Based on result array we can display each item separately
     *
     * @return array
     */
    public function getQuoteShippingAddressesItems()
    {
        if ($this->_quoteShippingAddressesItems !== null) {
            return $this->_quoteShippingAddressesItems;
        }
        $this->_quoteShippingAddressesItems = $this->getQuote()->getShippingAddressesItems();
        return $this->_quoteShippingAddressesItems;
    }

    /**
     * Remove item from address
     *
     * @param int $addressId
     * @param int $itemId
     * @return \Magento\Multishipping\Model\Checkout\Type\Multishipping
     */
    public function removeAddressItem($addressId, $itemId)
    {
        $address = $this->getQuote()->getAddressById($addressId);
        /* @var $address \Magento\Quote\Model\Quote\Address */
        if ($address) {
            $item = $address->getValidItemById($itemId);
            if ($item) {
                if ($item->getQty() > 1 && !$item->getProduct()->getIsVirtual()) {
                    $item->setQty($item->getQty() - 1);
                } else {
                    $address->removeItem($item->getId());
                }

                /**
                 * Require shipping rate recollect
                 */
                $address->setCollectShippingRates((bool)$this->getCollectRatesFlag());

                if (count($address->getAllItems()) == 0) {
                    $address->isDeleted(true);
                }

                $quoteItem = $this->getQuote()->getItemById($item->getQuoteItemId());
                if ($quoteItem) {
                    $newItemQty = $quoteItem->getQty() - 1;
                    if ($newItemQty > 0 && !$item->getProduct()->getIsVirtual()) {
                        $quoteItem->setQty($quoteItem->getQty() - 1);
                    } else {
                        $this->getQuote()->removeItem($quoteItem->getId());
                    }
                }
                $this->save();
            }
        }
        return $this;
    }

    /**
     * Assign quote items to addresses and specify items qty
     *
     * Array structure:
     * array(
     *      $quoteItemId => array(
     *          'qty'       => $qty,
     *          'address'   => $customerAddressId
     *      )
     * )
     *
     * @param array $info
     *
     * @return \Magento\Multishipping\Model\Checkout\Type\Multishipping
     * @throws LocalizedException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function setShippingItemsInformation($info)
    {
        if (is_array($info)) {
            $allQty = 0;
            $itemsInfo = [];
            foreach ($info as $itemData) {
                foreach ($itemData as $quoteItemId => $data) {
                    $allQty += $data['qty'];
                    $itemsInfo[$quoteItemId] = $data;
                }
            }

            $maxQty = $this->helper->getMaximumQty();
            if ($allQty > $maxQty) {
                throw new LocalizedException(
                    __(
                        "The maximum quantity can't be more than %1 when shipping to multiple addresses. "
                        . "Change the quantity and try again.",
                        $maxQty
                    )
                );
            }
            $quote = $this->getQuote();
            $addresses = $quote->getAllShippingAddresses();
            foreach ($addresses as $address) {
                $quote->removeAddress($address->getId());
            }

            foreach ($info as $itemData) {
                foreach ($itemData as $quoteItemId => $data) {
                    $this->_addShippingItem($quoteItemId, $data);
                }
            }
            $this->prepareShippingAssignment($quote);

            /**
             * Delete all not virtual quote items which are not added to shipping address
             * MultishippingQty should be defined for each quote item when it processed with _addShippingItem
             */
            foreach ($quote->getAllItems() as $_item) {
                if (!$_item->getProduct()->getIsVirtual() && !$_item->getParentItem() && !$_item->getMultishippingQty()
                ) {
                    $quote->removeItem($_item->getId());
                }
            }

            $billingAddress = $quote->getBillingAddress();
            if ($billingAddress) {
                $quote->removeAddress($billingAddress->getId());
            }

            $customerDefaultBillingId = $this->getCustomerDefaultBillingAddress();
            if ($customerDefaultBillingId) {
                $quote->getBillingAddress()->importCustomerAddressData(
                    $this->addressRepository->getById($customerDefaultBillingId)
                );
            }

            foreach ($quote->getAllItems() as $_item) {
                if (!$_item->getProduct()->getIsVirtual()) {
                    continue;
                }

                if (isset($itemsInfo[$_item->getId()]['qty'])) {
                    $qty = (int)$itemsInfo[$_item->getId()]['qty'];
                    if ($qty) {
                        $_item->setQty($qty);
                        $quote->getBillingAddress()->addItem($_item);
                    } else {
                        $_item->setQty(0);
                        $quote->removeItem($_item->getId());
                    }
                }
            }
            $this->getQuote()->setData('service_fee', 0)->getPayment()->setMethod(null);
            $this->save();
            $this->_eventManager->dispatch('checkout_type_multishipping_set_shipping_items', ['quote' => $quote]);
        }
        return $this;
    }

    /**
     * Add quote item to specific shipping address based on customer address id
     *
     * @param int $quoteItemId
     * @param array $data array('qty'=>$qty, 'address'=>$customerAddressId)
     *
     * @return \Magento\Multishipping\Model\Checkout\Type\Multishipping
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     *@throws LocalizedException
     */
    protected function _addShippingItem($quoteItemId, $data)
    {
        $qty = isset($data['qty']) ? (int)$data['qty'] : 1;
        //$qty       = $qty > 0 ? $qty : 1;
        $addressId = isset($data['address']) ? $data['address'] : false;
        $quoteItem = $this->getQuote()->getItemById($quoteItemId);

        if ($addressId && $quoteItem) {
            if (!$this->isAddressIdApplicable($addressId)) {
                throw new LocalizedException(__('Verify the shipping address information and continue.'));
            }

            /**
             * Skip item processing if qty 0
             */
            if ($qty === 0) {
                return $this;
            }
            $quoteItem->setMultishippingQty((int)$quoteItem->getMultishippingQty() + $qty);
            $quoteItem->setQty($quoteItem->getMultishippingQty());
            try {
                $address = $this->addressRepository->getById($addressId);
                // phpcs:ignore Magento2.CodeAnalysis.EmptyBlock
            } catch (\Exception $e) {
            }

            if (isset($address)) {
                if (isset($data['shipping_method'])) {
                    if (!isset($data['split_store_code'])) {
                        $data['split_store_code'] = 0;
                    }
                    if (!isset($data['oar_data']) || !isset($data['oar_data']['order_id'])) {
                        $data['oar_order_id'] = '0';
                    } else {
                        $data['oar_order_id'] = $data['oar_data']['order_id'];
                    }
                    if (!($quoteAddress = $this->getShippingAddressByCustomerAddressId($address->getId(), $data['shipping_method'], $data['oar_order_id']))
                    ) {
                        $quoteAddress = $this->_addressFactory->create()->importCustomerAddressData($address);
                        if ($data['shipping_method'] == 'store_pickup_store_pickup') {
                            $quoteAddress->setStorePickUp($data['store_pickup']);
                        } else {
                            $quoteAddress->setStorePickUp(null);
                        }
                        $quoteAddress->setPreShippingMethod($data['shipping_method'])->setSplitStoreCode($data['split_store_code'])->setOarOrderId($data['oar_order_id']);
                        $this->getQuote()->setIsMultiShipping(true)->addShippingAddress($quoteAddress);
                    }
                    $quoteAddress = $this->getShippingAddressByCustomerAddressId($address->getId(), $data['shipping_method'], $data['oar_order_id']);
                } else {
                    if (!($quoteAddress = $this->getQuote()->getShippingAddressByCustomerAddressId($address->getId()))
                    ) {
                        $quoteAddress = $this->_addressFactory->create()->importCustomerAddressData($address);
                        $this->getQuote()->setIsMultiShipping(true)->addShippingAddress($quoteAddress);
                    }

                    $quoteAddress = $this->getQuote()->getShippingAddressByCustomerAddressId($address->getId());
                }

                $quoteAddress->setCustomerAddressId($addressId);
                if (isset($data['oar_data'])) {
                    $quoteAddress->setOarData($this->serializer->serialize($data['oar_data']));
                }
                $quoteAddressItem = $quoteAddress->getItemByQuoteItemId($quoteItemId);
                if ($quoteAddressItem) {
                    $quoteAddressItem->setQty((int)($quoteAddressItem->getQty() + $qty));
                } else {
                    $quoteAddress->addItem($quoteItem, $qty);
                }
                /**
                 * Require shipping rate recollect
                 */
                $quoteAddress->setCollectShippingRates((bool)$this->getCollectRatesFlag());
            }
        }
        return $this;
    }

    /**
     * Get quote address by customer address ID.
     *
     * @param $addressId
     * @param $shippingMethod
     * @param $oarOrderId
     * @return bool|Address
     */
    public function getShippingAddressByCustomerAddressId($addressId, $shippingMethod, $oarOrderId)
    {
        /** @var \Magento\Quote\Model\Quote\Address $address */
        foreach ($this->getQuote()->getAddressesCollection() as $address) {
            if (!$address->isDeleted() &&
                $address->getAddressType() == Address::TYPE_SHIPPING &&
                $address->getCustomerAddressId() == $addressId &&
                (!$address->getPreShippingMethod() || $address->getPreShippingMethod() == $shippingMethod)
                && $address->getOarOrderId() == $oarOrderId
            ) {
                return $address;
            }
        }
        return false;
    }

    /**
     * Reimport customer address info to quote shipping address
     *
     * @param int $addressId customer address id
     *
     * @return \Magento\Multishipping\Model\Checkout\Type\Multishipping
     *@throws LocalizedException
     */
    public function updateQuoteCustomerShippingAddress($addressId)
    {
        if (!$this->isAddressIdApplicable($addressId)) {
            throw new LocalizedException(__('Verify the shipping address information and continue.'));
        }
        try {
            $address = $this->addressRepository->getById($addressId);
            // phpcs:ignore Magento2.CodeAnalysis.EmptyBlock
        } catch (\Exception $e) {
            //
        }
        if (isset($address)) {
            $quoteAddress = $this->getQuote()->getShippingAddressByCustomerAddressId($addressId);
            $quoteAddress->setCollectShippingRates(true)->importCustomerAddressData($address);
            $this->totalsCollector->collectAddressTotals($this->getQuote(), $quoteAddress);
            $this->quoteRepository->save($this->getQuote());
        }

        return $this;
    }

    /**
     * Reimport customer billing address to quote
     *
     * @param int $addressId customer address id
     *
     * @return \Magento\Multishipping\Model\Checkout\Type\Multishipping
     *@throws LocalizedException
     */
    public function setQuoteCustomerBillingAddress($addressId)
    {
        if (!$this->isAddressIdApplicable($addressId)) {
            throw new LocalizedException(__('Verify the billing address information and continue.'));
        }
        try {
            $address = $this->addressRepository->getById($addressId);
            // phpcs:ignore Magento2.CodeAnalysis.EmptyBlock
        } catch (\Exception $e) {
            //
        }
        if (isset($address)) {
            $quoteAddress = $this->getQuote()->getBillingAddress($addressId)->importCustomerAddressData($address);
            $this->totalsCollector->collectAddressTotals($this->getQuote(), $quoteAddress);
            //$this->getQuote()->collectTotals();
            //$this->quoteRepository->save($this->getQuote());
        }

        return $this;
    }

    /**
     * Assign shipping methods to addresses
     *
     * @param  array $methods
     *
     * @return \Magento\Multishipping\Model\Checkout\Type\Multishipping
     * @throws LocalizedException
     */
    public function setShippingMethods($methods)
    {
        $quote = $this->getQuote();
        $addresses = $quote->getAllShippingAddresses();
        /** @var  \Magento\Quote\Model\Quote\Address $address */
        foreach ($addresses as $address) {
            if (isset($methods[$address->getId()])) {
                $address->setShippingMethod($methods[$address->getId()]);
            } else {
                $address->setShippingMethod('');
            }
            $address->setCollectShippingRates(false);
        }
        $this->prepareShippingAssignment($quote);
        $this->collectVoucher();
        $this->save();
        return $this;
    }

    /**
     * collect Voucher
     */
    public function collectVoucher()
    {
        $quote = $this->getQuote();
        $couponCode = $this->getQuote()->getApplyVoucher();
        $newApplyCoupon = explode(',', $couponCode);

        if ($quote->getApplyVoucher() && $quote->getApplyVoucher() != '') {
            $oldApplyCoupon = explode(',', $quote->getApplyVoucher());
        } else {
            $oldApplyCoupon = [];
        }

        if (!empty(array_diff($oldApplyCoupon, $newApplyCoupon)) || !empty(array_diff($newApplyCoupon, $oldApplyCoupon))) {
            $this->getQuote()->setApplyVoucher($couponCode);
        }

        if ($quote->getCouponCode() && $quote->getCouponCode() != '') {
            $oldCoupon = explode(',', $quote->getCouponCode());
        } else {
            $oldCoupon = [];
        }

        if (!empty(array_diff($oldCoupon, $newApplyCoupon)) || !empty(array_diff($newApplyCoupon, $oldCoupon))) {
            $this->getQuote()->setCouponCode($couponCode);
        }
    }

    /**
     * Set payment method info to quote payment
     *
     * @param array $payment
     * @param int   $serviceFee
     *
     * @return Multishipping
     * @throws LocalizedException
     */
    public function setPaymentMethod($payment, $serviceFee = 0)
    {
        if (!isset($payment['method'])) {
            throw new LocalizedException(
                __("A payment method isn't defined. Verify and try again.")
            );
        } elseif (!$this->paymentSpecification->isSatisfiedBy($payment['method'])) {
            throw new LocalizedException(
                __(
                    "This payment method can't be used for shipping to multiple addresses. "
                    . "Change the payment method and try again."
                )
            );
        }
        $quote = $this->getQuote();
        $quote->setData('service_fee_request', $serviceFee);
        $quote->setData('service_fee', ((int)$quote->getGrandTotal() * $serviceFee)/100);
        $quote->getPayment()->importData($payment);
        // shipping totals may be affected by payment method
        if (!$quote->isVirtual() && $quote->getShippingAddress()) {
            foreach ($quote->getAllShippingAddresses() as $shippingAddress) {
                $shippingAddress->setCollectShippingRates(true);
            }
            $quote->setTotalsCollectedFlag(false)->collectTotals();
        }

        $this->quoteRepository->save($quote);

        return $this;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Address $address $address
     * @param \Magento\Quote\Model\Quote $quote
     * @return \Magento\Sales\Model\Order
     * @throws \Magento\Checkout\Exception
     */
    protected function _prepareOrder(\Magento\Quote\Model\Quote\Address $address, $quote)
    {
        $quote->unsReservedOrderId();
        $quote->reserveOrderId();
        $quote->collectTotals();
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->_orderFactory->create();

        $this->dataObjectHelper->mergeDataObjects(
            \Magento\Sales\Api\Data\OrderInterface::class,
            $order,
            $this->quoteAddressToOrder->convert($address)
        );

        $shippingMethodCode = $address->getShippingMethod();
        if (isset($shippingMethodCode) && !empty($shippingMethodCode)) {
            $rate = $address->getShippingRateByCode($shippingMethodCode);
            $shippingPrice = $rate->getPrice();
        } else {
            $shippingPrice = $order->getShippingAmount();
        }
        $store = $order->getStore();
        $amountPrice = $store->getBaseCurrency()
            ->convert($shippingPrice, $store->getCurrentCurrencyCode());
        $order->setBaseShippingAmount($shippingPrice);
        $order->setShippingAmount($amountPrice);

        $order->setQuote($quote);
        $order->setBillingAddress($this->quoteAddressToOrderAddress->convert($quote->getBillingAddress()));
        /* start custom*/
        $order->setDate($address->getDate());
        $order->setTime($address->getTime());
        $order->setStorePickUp($address->getStorePickUp());
        $order->setStorePickUpTime($address->getStorePickUpTime());
        $order->setStorePickUpDelivery($address->getStorePickUpDelivery());
        $order->setSplitStoreCode($address->getSplitStoreCode());
        $order->setOarData($address->getOarData());
        /*end*/
        $subtotal = 0;
        $baseSubtotal = 0;
        $discountAmount = 0;
        $baseDiscountAmount = 0;
        $addressSkus = [];
        /**
         * check if one of items freeshipping then this address free ship
         */
        foreach ($address->getAllItems() as $item) {
            $addressSkus[] = $item->getSku();
        }

        if ($address->getFreeShipping()) {
            $address->setShippingAmount(0);
            $address->setBaseShippingAmount(0);
            $address->setShippingInclTax(0);
            $address->setBaseShippingInclTax(0);
            $order->setBaseShippingAmount(0);
            $order->setShippingAmount(0);
            $order->setShippingInclTax(0);
            $order->setBaseShippingInclTax(0);
        } elseif ($address->getShippingDiscountAmount()) {
            $discountAmount += abs($address->getShippingDiscountAmount());
            $baseDiscountAmount += abs($address->getBaseShippingDiscountAmount());
            $order->setShippingDiscountAmount(abs($address->getShippingDiscountAmount()) * -1)
                ->setBaseShippingDiscountAmount(abs($address->getBaseShippingDiscountAmount()) * -1);
        }

        $order->setData(
            \SM\Promotion\Model\Data\Rule::SHIPPING_RULE_IDS_FIELD,
            $address->getData(\SM\Promotion\Model\Data\Rule::SHIPPING_RULE_IDS_FIELD)
        );


        if ($address->getAddressType() == 'billing') {
            $order->setIsVirtual(1);
        } else {
            $order->setShippingAddress($this->quoteAddressToOrderAddress->convert($address));
            $order->setShippingMethod($address->getShippingMethod());
        }

        $order->setPayment($this->quotePaymentToOrderPayment->convert($quote->getPayment()));
        if ($this->priceCurrency->round($address->getGrandTotal()) == 0) {
            $order->getPayment()->setMethod('free');
        }
        /** @var Item $item */
        foreach ($quote->getAllItems() as $item) {
            /**
             * mapping with SKU exist on address and convert to suborder
             */
            if (!in_array($item->getSku(), $addressSkus)) {
                continue;
            }

            $orderItem = $this->quoteItemToOrderItem->convert($item);
            if ($item->getParentItem()) {
                $orderItem->setParentItem($order->getItemByQuoteItemId($item->getParentItem()->getId()));
            } else {
                $subtotal += $item->getRowTotal();
                $baseSubtotal += $item->getBaseRowTotal();
            }
            $order->addItem($orderItem);
            $discountAmount += abs($item->getDiscountAmount());
            $baseDiscountAmount += abs($item->getBaseDiscountAmount());
        }

        $order->setSubtotal($subtotal);
        $order->setBaseSubtotal($baseSubtotal);
        $order->setDiscountAmount(-1 * $discountAmount);
        $order->setBaseDiscountAmount(-1 * $baseDiscountAmount);
        $order->setGrandTotal(
            $order->getSubtotal() + $order->getShippingAmount() + $order->getDiscountAmount()
        );
        $order->setBaseGrandTotal(
            $order->getBaseSubtotal() + $order->getBaseShippingAmount() - $order->getBaseDiscountAmount()
        );

        return $order;
    }

    /**
     * @param $quote
     * @return $this
     * @throws LocalizedException
     */
    protected function _validate($quote)
    {
        /** @var $paymentMethod \Magento\Payment\Model\Method\AbstractMethod */
        $paymentMethod = $quote->getPayment()->getMethodInstance();
        if (!$paymentMethod->isAvailable($quote)) {
            throw new LocalizedException(
                __("The payment method isn't selected. Enter the payment method and try again.")
            );
        }

        $addresses = $quote->getAllShippingAddresses();
        foreach ($addresses as $address) {
            $addressValidation = $address->validate();
            if ($addressValidation !== true) {
                throw new LocalizedException(
                    __('Verify the shipping address information and continue.')
                );
            }
            $method = $address->getShippingMethod();
            $rate = $address->getShippingRateByCode($method);
            if (!$method || !$rate) {
                throw new LocalizedException(
                    __('Set shipping methods for all addresses. Verify the shipping methods and try again.')
                );
            }

            // Checks if a country id present in the allowed countries list.
            if (!in_array($address->getCountryId(), $this->allowedCountryReader->getAllowedCountries())) {
                throw new LocalizedException(
                    __("Some addresses can't be used due to the configurations for specific countries.")
                );
            }
        }
        $addressValidation = $quote->getBillingAddress()->validate();
        if ($addressValidation !== true) {
            throw new LocalizedException(
                __('Verify the billing address information and continue.')
            );
        }
        return $this;
    }

    /**
     * @param $mainOrder
     * @param $quote
     * @param bool $isMobile
     * @return $this
     * @throws LocalizedException
     */
    public function createSuborders($mainOrder, $quote, $isMobile = false)
    {
        $quote->setIsActive(true);
        $quote->setInventoryProcessed(false);
        $orderIds = [];
        $this->_validate($quote);
        $shippingAddresses = $quote->getAllShippingAddresses();
        $orders = [];

        if ($quote->hasVirtualItems()) {
            $shippingAddresses[] = $quote->getBillingAddress();
        }

        try {
            $i = 1;
            $j = 1;
            foreach ($shippingAddresses as $address) {
                if ($address->getAddressType() == 'billing') {
                    continue;
                }
                $order = $this->_prepareOrder($address, $quote);
                $this->adjustDataSubOrder($order, $mainOrder, $j);
                $orders[] = $order;
                $this->_eventManager->dispatch(
                    'checkout_create_suborders_single',
                    ['order' => $order, 'address' => $address, 'quote' => $quote]
                );
                /**
                 * prepare set suborder reference nuumber
                 */
                if ($order->getShippingMethod() != 'store_pickup_store_pickup') {
                    $j++;
                }
                $i++;
            }
            $paymentProviderCode = $quote->getPayment()->getMethod();
            $placeOrderService = $this->placeOrderFactory->create($paymentProviderCode);
            $exceptionList = $placeOrderService->place($orders);
            $this->logExceptions($exceptionList);

            /** @var OrderInterface[] $failedOrders */
            $failedOrders = [];
            /** @var OrderInterface[] $successfulOrders */
            $successfulOrders = [];
            foreach ($orders as $order) {
                if (isset($exceptionList[$order->getIncrementId()])) {
                    $failedOrders[] = $order;
                } else {
                    $successfulOrders[] = $order;
                }
            }

            $placedAddressItems = [];
            foreach ($successfulOrders as $order) {
                $orderIds[$order->getId()] = $order->getIncrementId();
                $placedAddressItems = $this->getPlacedAddressItems($order);
            }

            $addressErrors = [];
            if (!empty($failedOrders)) {
                $this->removePlacedItemsFromQuote($shippingAddresses, $placedAddressItems);
                $addressErrors = $this->getQuoteAddressErrors(
                    $failedOrders,
                    $shippingAddresses,
                    $exceptionList
                );
            } else {
                if (!$isMobile) {
                    $this->_checkoutSession->setLastQuoteId($this->getQuote()->getId());
                }
                $quote->setIsActive(false);
                $this->quoteRepository->save($quote);
            }

            if (!$isMobile) {
                $this->getOrdersSuccessPage($orderIds, $addressErrors);
                $this->setOrderSuccess(count($successfulOrders));
                $this->setOrderFail(count($failedOrders));
                $this->getCheckoutSession()->clearQuote();
                $this->getCheckoutSession()->setDisplaySuccess(true);
            } else {
                $stringOrderIds = implode(',', array_keys($orderIds));
                $this->setOrderIdsSuccess($stringOrderIds);
            }
            return $this;
        } catch (\Exception $e) {
            $this->_eventManager->dispatch('checkout_suborder_refund_all', ['orders' => $orders]);
            throw $e;
        }

    }

    /**
     * @param $mainOrder
     * @param $quote
     * @throws \Magento\Checkout\Exception
     */
    public function createDigitalSuborder($mainOrder, $quote)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->_prepareOrder($quote->getBillingAddress(), $quote);
        $this->adjustDataSubOrder($order, $mainOrder, 1);
        $order->setPayment($this->quotePaymentToOrderPayment->convert($quote->getPayment()));
        $order->setBillingAddress($this->quoteAddressToOrderAddress->convert($quote->getBillingAddress()));
        $paymentProviderCode = $quote->getPayment()->getMethod();
        $placeOrderService = $this->placeOrderFactory->create($paymentProviderCode);
        $placeOrderService->place([$order]);
    }

    /**
     * @param null $orderIds
     * @param null $addressErrors
     */
    public function getOrdersSuccessPage($orderIds = null, $addressErrors = null)
    {
        $this->_session->setOrderIds($orderIds);
        $this->_session->setAddressErrors($addressErrors);
    }

    /**
     * adjust data suborder before creating
     * @param $order
     * @param $mainOrder
     * @param $i
     */
    public function adjustDataSubOrder($order, $mainOrder, $i)
    {
        $mainIncrementId = $mainOrder->getIncrementId();
        $mainId = $mainOrder->getId();
        $subOrderReference = $this->referenceNumber->generateSubOrderReferenceNumber($order, $mainOrder, $i);
        $order->setReferenceOrderId($subOrderReference);
        $order->setParentOrder($mainId);
        $order->setIsParent(0);
        $order->setIncrementId($mainIncrementId . '-' . $i);
        $order->setReferenceNumber($mainOrder->getReferenceNumber());
        $order->setReferencePaymentNumber($mainOrder->getReferencePaymentNumber());
    }
    /**
     * Logs exceptions.
     *
     * @param \Exception[] $exceptionList
     * @return void
     */
    private function logExceptions(array $exceptionList)
    {
        foreach ($exceptionList as $exception) {
            $this->logger->critical($exception);
        }
    }

    /**
     * Collect quote totals and save quote object
     *
     * @return \Magento\Multishipping\Model\Checkout\Type\Multishipping
     */
    public function save()
    {
        $this->getQuote()->setIsMultiShipping(true)->setTotalsCollectedFlag(false)->collectTotals();
        $this->quoteRepository->save($this->getQuote());
        return $this;
    }

    /**
     * Specify BEGIN state in checkout session whot allow reinit multishipping checkout
     *
     * @return \Magento\Multishipping\Model\Checkout\Type\Multishipping
     */
    public function reset()
    {
        $this->getCheckoutSession()->setCheckoutState(\Magento\Checkout\Model\Session::CHECKOUT_STATE_BEGIN);
        return $this;
    }

    /**
     * Check if quote amount is allowed for multishipping checkout
     *
     * @return bool
     */
    public function validateMinimumAmount()
    {
        $minimumOrderActive = $this->_scopeConfig->isSetFlag(
            'sales/minimum_order/active',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $minimumOrderMultiFlag = $this->_scopeConfig->isSetFlag(
            'sales/minimum_order/multi_address',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        if ($minimumOrderMultiFlag) {
            $result = !($minimumOrderActive && !$this->getQuote()->validateMinimumAmount());
        } else {
            $result = !($minimumOrderActive && !$this->validateMinimumAmountForAddressItems());
        }

        return $result;
    }

    /**
     * Get notification message for case when multishipping checkout is not allowed
     *
     * @return string
     */
    public function getMinimumAmountDescription()
    {
        $descr = $this->_scopeConfig->getValue(
            'sales/minimum_order/multi_address_description',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if (empty($descr)) {
            $descr = $this->_scopeConfig->getValue(
                'sales/minimum_order/description',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        }
        return $descr;
    }

    /**
     * Get minimum amount error.
     *
     * @return string
     */
    public function getMinimumAmountError()
    {
        $error = $this->_scopeConfig->getValue(
            'sales/minimum_order/multi_address_error_message',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if (empty($error)) {
            $error = $this->_scopeConfig->getValue(
                'sales/minimum_order/error_message',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        }
        return $error;
    }

    /**
     * Get order IDs created during checkout
     *
     * @param bool $asAssoc
     * @return array
     */
    public function getOrderIds($asAssoc = false)
    {
        $idsAssoc = $this->_session->getOrderIds();
        if ($idsAssoc !== null) {
            return $asAssoc ? $idsAssoc : array_keys($idsAssoc);
        }
        return [];
    }

    /**
     * Retrieve customer default billing address
     *
     * @return int|null
     */
    public function getCustomerDefaultBillingAddress()
    {
        $defaultAddressId = $this->getCustomer()->getDefaultBilling();
        return $this->getDefaultAddressByDataKey('customer_default_billing_address', $defaultAddressId);
    }

    /**
     * Retrieve customer default shipping address
     *
     * @return int|null
     */
    public function getCustomerDefaultShippingAddress()
    {
        $defaultAddressId = $this->getCustomer()->getDefaultShipping();
        return $this->getDefaultAddressByDataKey('customer_default_shipping_address', $defaultAddressId);
    }

    /**
     * Retrieve customer default address by data key
     *
     * @param string $key
     * @param string|null $defaultAddressIdFromCustomer
     * @return int|null
     */
    private function getDefaultAddressByDataKey($key, $defaultAddressIdFromCustomer)
    {
        $addressId = $this->getData($key);
        if ($addressId === null) {
            $addressId = $defaultAddressIdFromCustomer;
            if (!$addressId) {
                /** Default address is not available, try to find any customer address */
                $filter = $this->filterBuilder->setField('parent_id')
                    ->setValue($this->getCustomer()->getId())
                    ->setConditionType('eq')
                    ->create();
                $addresses = (array)($this->addressRepository->getList(
                    $this->searchCriteriaBuilder->addFilters([$filter])->create()
                )->getItems());
                if ($addresses) {
                    $address = reset($addresses);
                    $addressId = $address->getId();
                }
            }
            $this->setData($key, $addressId);
        }

        return $addressId;
    }

    /**
     * Retrieve checkout session model
     *
     * @return \Magento\Checkout\Model\Session
     */
    public function getCheckoutSession()
    {
        $checkout = $this->getData('checkout_session');
        if ($checkout === null) {
            $checkout = $this->_checkoutSession;
            $this->setData('checkout_session', $checkout);
        }
        return $checkout;
    }

    /**
     * @param $quote
     */
    public function setQuote($quote)
    {
        $this->quote = $quote;
    }

    /**
     * Retrieve quote model
     *
     * @return \Magento\Quote\Model\Quote
     */
    public function getQuote()
    {
        if ($this->quote) {
            return $this->quote;
        }
        return $this->getCheckoutSession()->getQuote();
    }

    /**
     * Retrieve quote items
     *
     * @return \Magento\Quote\Model\Quote\Item[]
     */
    public function getQuoteItems()
    {
        return $this->getQuote()->getAllItems();
    }

    /**
     * Retrieve customer session model
     *
     * @return \Magento\Customer\Model\Session
     */
    public function getCustomerSession()
    {
        return $this->_customerSession;
    }

    /**
     * @param $customerData
     */
    public function setCustomer($customerData)
    {
        $this->customer = $customerData;
    }

    /**
     * Retrieve customer object
     *
     * @return \Magento\Customer\Api\Data\CustomerInterface
     */
    public function getCustomer()
    {
        if ($this->customer) {
            return $this->customer;
        }
        return $this->_customerSession->getCustomerDataObject();
    }

    /**
     * Check if specified address ID belongs to customer.
     *
     * @param mixed $addressId
     * @return bool
     */
    protected function isAddressIdApplicable($addressId)
    {
        $applicableAddressIds = array_map(
            function ($address) {
                /** @var \Magento\Customer\Api\Data\AddressInterface $address */
                return $address->getId();
            },
            $this->getCustomer()->getAddresses()
        );

        return !is_numeric($addressId) || in_array($addressId, $applicableAddressIds);
    }

    /**
     * Prepare shipping assignment.
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @return \Magento\Quote\Model\Quote
     */
    private function prepareShippingAssignment($quote)
    {
        $cartExtension = $quote->getExtensionAttributes();
        if ($cartExtension === null) {
            $cartExtension = $this->cartExtensionFactory->create();
        }
        /** @var \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment */
        $shippingAssignment = $this->getShippingAssignmentProcessor()->create($quote);
        $shipping = $shippingAssignment->getShipping();

        $shipping->setMethod(null);
        $shippingAssignment->setShipping($shipping);
        $cartExtension->setShippingAssignments([$shippingAssignment]);

        return $quote->setExtensionAttributes($cartExtension);
    }

    /**
     * Get shipping assignment processor.
     *
     * @return \Magento\Quote\Model\Quote\ShippingAssignment\ShippingAssignmentProcessor
     */
    private function getShippingAssignmentProcessor()
    {
        if (!$this->shippingAssignmentProcessor) {
            $this->shippingAssignmentProcessor = ObjectManager::getInstance()
                ->get(\Magento\Quote\Model\Quote\ShippingAssignment\ShippingAssignmentProcessor::class);
        }
        return $this->shippingAssignmentProcessor;
    }

    /**
     * Validate minimum amount for "Checkout with Multiple Addresses" when
     * "Validate Each Address Separately in Multi-address Checkout" is No.
     *
     * @return bool
     */
    private function validateMinimumAmountForAddressItems()
    {
        $result = true;
        $storeId = $this->getQuote()->getStoreId();

        $minAmount = $this->_scopeConfig->getValue(
            'sales/minimum_order/amount',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
        $taxInclude = $this->_scopeConfig->getValue(
            'sales/minimum_order/tax_including',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );

        $this->getQuote()->collectTotals();
        $addresses = $this->getQuote()->getAllAddresses();

        $baseTotal = 0;
        foreach ($addresses as $address) {
            $taxes = $taxInclude ? $address->getBaseTaxAmount() : 0;
            $baseTotal += $address->getBaseSubtotalWithDiscount() + $taxes;
        }

        if ($baseTotal < $minAmount) {
            $result = false;
        }

        return $result;
    }

    /**
     * Remove successfully placed items from quote.
     *
     * @param \Magento\Quote\Model\Quote\Address[] $shippingAddresses
     * @param int[] $placedAddressItems
     * @return void
     */
    private function removePlacedItemsFromQuote(array $shippingAddresses, array $placedAddressItems)
    {
        foreach ($shippingAddresses as $address) {
            foreach ($address->getAllItems() as $addressItem) {
                if (in_array($addressItem->getQuoteItemId(), $placedAddressItems)) {
                    if ($addressItem->getProduct()->getIsVirtual()) {
                        $addressItem->isDeleted(true);
                    } else {
                        $address->isDeleted(true);
                    }

                    $this->decreaseQuoteItemQty($addressItem->getQuoteItemId(), $addressItem->getQty());
                }
            }
        }
        $this->save();
    }

    /**
     * Decrease quote item quantity.
     *
     * @param int $quoteItemId
     * @param int $qty
     * @return void
     */
    private function decreaseQuoteItemQty(int $quoteItemId, int $qty)
    {
        $quoteItem = $this->getQuote()->getItemById($quoteItemId);
        if ($quoteItem) {
            $newItemQty = $quoteItem->getQty() - $qty;
            if ($newItemQty > 0) {
                $quoteItem->setQty($newItemQty);
            } else {
                $this->getQuote()->removeItem($quoteItem->getId());
                $this->getQuote()->setIsMultiShipping(1);
            }
        }
    }

    /**
     * Returns quote address id that was assigned to order.
     *
     * @param OrderInterface $order
     * @param \Magento\Quote\Model\Quote\Address[] $addresses
     *
     * @return int
     * @throws NotFoundException
     */
    private function searchQuoteAddressId(OrderInterface $order, array $addresses): int
    {
        $items = $order->getItems();
        $item = array_pop($items);
        foreach ($addresses as $address) {
            foreach ($address->getAllItems() as $addressItem) {
                if ($addressItem->getQuoteItemId() == $item->getQuoteItemId()) {
                    return (int)$address->getId();
                }
            }
        }

        throw new NotFoundException(__('Quote address for failed order not found.'));
    }

    /**
     * Get quote address errors.
     *
     * @param OrderInterface[] $orders
     * @param \Magento\Quote\Model\Quote\Address[] $addresses
     * @param \Exception[] $exceptionList
     * @return string[]
     * @throws NotFoundException
     */
    private function getQuoteAddressErrors(array $orders, array $addresses, array $exceptionList): array
    {
        $addressErrors = [];
        foreach ($orders as $failedOrder) {
            if (!isset($exceptionList[$failedOrder->getIncrementId()])) {
                throw new NotFoundException(__('Exception for failed order not found.'));
            }
            $addressId = $this->searchQuoteAddressId($failedOrder, $addresses);
            $addressErrors[$addressId] = $exceptionList[$failedOrder->getIncrementId()]->getMessage();
        }

        return $addressErrors;
    }

    /**
     * Returns quote address item id.
     *
     * @param OrderInterface $order
     * @return array
     */
    private function getQuoteAddressItems(OrderInterface $order): array
    {
        $placedAddressItems = [];
        foreach ($order->getItems() as $orderItem) {
            $placedAddressItems[] = $orderItem->getQuoteItemId();
        }

        return $placedAddressItems;
    }

    /**
     * Returns placed address items
     *
     * @param OrderInterface $order
     * @return array
     */
    private function getPlacedAddressItems(OrderInterface $order): array
    {
        $placedAddressItems = [];
        foreach ($this->getQuoteAddressItems($order) as $key => $quoteAddressItem) {
            $placedAddressItems[$key] = $quoteAddressItem;
        }

        return $placedAddressItems;
    }
}
