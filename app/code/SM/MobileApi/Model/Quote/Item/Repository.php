<?php
namespace SM\MobileApi\Model\Quote\Item;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\CatalogEvent\Model\Event as SaleEvent;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Message\MessageInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartItemInterfaceFactory;
use Magento\Quote\Model\Quote\Item\CartItemOptionsProcessor;

class Repository implements \SM\MobileApi\Api\CartItemRepositoryInterface
{
    protected $tokenUserContext;

    protected $customer;

    protected $categoryEventId = null;

    protected $event = null;

    protected $historyFactory;

    protected $categoryEventList;

    protected $productStock;

    protected $customerSession;

    protected $cartMessageFactory;

    /**
     * @var \SM\MobileApi\Api\CartInterface
     */
    private $mbCartRepository;

    /**
     * Quote repository.
     *
     * @var CartRepositoryInterface
     */
    protected $quoteRepository;

    public function __construct(
        CartRepositoryInterface $quoteRepository,
        ProductRepositoryInterface $productRepository,
        CartItemInterfaceFactory $itemDataFactory,
        CartItemOptionsProcessor $cartItemOptionsProcessor,
        \Magento\Webapi\Model\Authorization\TokenUserContext $tokenUserContext,
        CustomerRepositoryInterface $customer,
        \SM\FlashSale\Model\HistoryFactory $historyFactory,
        \Magento\CatalogEvent\Model\Category\EventList $categoryEventList,
        $cartItemProcessors = [],
        \SM\MobileApi\Model\Quote\Item\Stock $productStock,
        \SM\Checkout\Model\Data\CartMessageFactory $cartMessageFactory,
        Session $customerSession,
        \SM\MobileApi\Api\CartInterface $mbCartRepository
    ) {
        $this->customerSession = $customerSession;
        $this->tokenUserContext = $tokenUserContext;
        $this->customer = $customer;
        $this->historyFactory = $historyFactory;
        $this->categoryEventList = $categoryEventList;
        $this->productStock = $productStock;
        $this->cartMessageFactory = $cartMessageFactory;
        $this->mbCartRepository = $mbCartRepository;
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * @inheridoc
     */
    public function update(\Magento\Quote\Api\Data\CartItemInterface $cartItem)
    {
        return $this->save($cartItem, true);
    }

    /**
     * @inheridoc
     */
    public function addToCart(\Magento\Quote\Api\Data\CartItemInterface $cartItem)
    {
        return $this->save($cartItem);
    }

    /**
     * @param \Magento\Quote\Api\Data\CartItemInterface $cartItem
     * @param bool $isUpdate
     * @return \Magento\Quote\Api\Data\CartItemInterface|\SM\MobileApi\Api\Data\CartItemInterface
     * @throws InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Webapi\Exception
     */
    protected function save(\Magento\Quote\Api\Data\CartItemInterface $cartItem, $isUpdate = false)
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $cartId = $cartItem->getQuoteId();
        if (!$cartId) {
            throw new InputException(
                __('"%fieldName" is required. Enter and try again.', ['fieldName' => 'quoteId'])
            );
        }
        $message = "";
        $quote = $this->quoteRepository->getActive($cartId);
        if ($cartItem->getData('item_id')) {
            $item = $quote->getItemById($cartItem->getData('item_id'));
            $availableStock = $this->productStock->getStock($item);

            if ($availableStock <= 0) {
                $extensionAttributes = $item->getExtensionAttributes();
                $cartMessageFactory = $this->cartMessageFactory->create();
                $cartMessageFactory->setMessage(__('This item is out of stock. Please remove it and find something else.'));
                $cartMessageFactory->setMessageType(MessageInterface::TYPE_WARNING);
                $extensionAttributes->setCartMessage($cartMessageFactory);
                $item->setExtensionAttributes($extensionAttributes);
            } else {
                if ($cartItem->getQty() > $availableStock) {
                    $cartItem->setData('qty', $availableStock);
                    $extensionAttributes = $item->getExtensionAttributes();
                    $cartMessageFactory = $this->cartMessageFactory->create();
                    $cartMessageFactory->setMessage(__('The quantity has been adjusted due to stock limitation.'));
                    $cartMessageFactory->setMessageType(MessageInterface::TYPE_WARNING);
                    $extensionAttributes->setCartMessage($cartMessageFactory);
                    $item->setExtensionAttributes($extensionAttributes);
                }
            }

            $this->_initializeEventsForQuoteItems($quote);
            $customerId = $this->tokenUserContext->getUserId();
            if (!$customerId) {
                $customerId = $this->customerSession->getCustomerId();
            }
            $customer = $this->customer->getById($customerId);
            $currentProduct = $item->getProduct();
            if ($this->categoryEventId != null && array_search($this->categoryEventId, $currentProduct->getCategoryIds()) !== false) {
                $event = $this->event;
                if ($event) {
                    if ($event->getStatus() !== \Magento\CatalogEvent\Model\Event::STATUS_OPEN) {
                        $item->setHasError(true)->setMessage(__('This product is no longer on sale.'));
                        $item->getQuote()->setHasError(
                            true
                        )->addMessage(
                            __('Some of these products can no longer be sold.')
                        );

                        $item->setHasError(false);
                        $item->getQuote()->setHasError(false);

                        $item->setCustomPrice($currentProduct->getPrice());
                        $item->setOriginalCustomPrice($currentProduct->getPrice());
                        $item->getProduct()->setIsSuperMode(true);
                        $item->setEventId(null);
                        $item->setEvent(null);
                    } else {
                        if ($currentProduct->getData('is_flashsale') &&
                            $currentProduct->getData('flashsale_qty') > 0 &&
                            $currentProduct->getData('flashsale_qty_per_customer') > 0) {
                            $history = $this->historyFactory->create();
                            $collection = $history->getCollection()
                                ->addFieldToFilter('event_id', $event->getId())
                                ->addFieldToFilter('item_id', $currentProduct->getId());

                            $itemTotalBuy = 0;
                            $itemCustomerBuy = 0;
                            foreach ($collection as $historyItem) {
                                if ($customer->getId() == $historyItem->getData("customer_id")) {
                                    $itemCustomerBuy = $historyItem->getData('item_qty_purchase');
                                }
                                $itemTotalBuy += $historyItem->getData('item_qty_purchase');
                            }

                            $flashSaleLimit = $currentProduct->getData('flashsale_qty');
                            $flashSaleCustomerLimit = $currentProduct->getData('flashsale_qty_per_customer');

                            $qtyNow = $cartItem->getQty();

                            $availableQty = $flashSaleLimit - $itemTotalBuy;
                            $availableCustomerQty = $flashSaleCustomerLimit - $itemCustomerBuy;

                            if ($availableQty > 0 && $availableCustomerQty > 0) {
                                if ($qtyNow <= $availableQty) {
                                    if ($qtyNow > $availableCustomerQty) {
                                        $qtyNow = $availableCustomerQty;
                                        $message = __("You exceeded the maximum quantity of Surprise Deals item. The excess items have been removed & can be purchased later in normal price.");
                                    }
                                } else {
                                    $qtyNow = $availableQty;
                                    if ($qtyNow > $availableCustomerQty) {
                                        $qtyNow = $availableCustomerQty;
                                        $message = __("You exceeded the maximum quantity of Surprise Deals item. The excess items have been removed & can be purchased later in normal price.");
                                    }
                                }
                                if ($currentProduct->getSpecialPrice()) {
                                    $price = $currentProduct->getSpecialPrice();
                                } else {
                                    $price = $currentProduct->getPrice();
                                }

                                $item->setCustomPrice($price);
                                $item->setOriginalCustomPrice($price);
                                $cartItem->setData('qty', $qtyNow);
                            } else {
                                $item->setEventId(null);
                                if ($item->getParentItem()) {
                                    $item->getParentItem()->setEventId(null);
                                }
                                $item->setCustomPrice($currentProduct->getPrice());
                                $item->setOriginalCustomPrice($currentProduct->getPrice());
                            }
                        } else {
                            $item->setEventId(null);
                        }
                    }
                } else {
                    /*
                     * If quote item has event id but event was
                     * not assigned to it then we should set event id to
                     * null as event was removed already
                     */
                    $item->setEventId(null);
                }
            }
        }

        $quoteItems = $quote->getItems();
        $quoteItems[] = $cartItem;
        $quote->setItems($quoteItems);
        $this->quoteRepository->save($quote->setTotalsCollectedFlag(true));
        $this->mbCartRepository->setQuote($quote);

        if ($message != "") {
            throw new \Magento\Framework\Webapi\Exception(__($message), 444, 404);
        }

        if ($isUpdate) {
            return $this->mbCartRepository->getItems($cartId, false);
        } else {
            return $quote->getLastAddedItem();
        }
    }

    protected function _initializeEventsForQuoteItems(\Magento\Quote\Model\Quote $quote)
    {
        if (!$quote->getEventInitialized()) {
            $quote->setEventInitialized(true);
            $event = $this->categoryEventList->getEventCollection()
                ->addFieldToFilter('status', SaleEvent::STATUS_OPEN)
                ->addVisibilityFilter()
                ->getFirstItem();
            $this->event = $event;
            $this->categoryEventId = $event->getCategoryId();
        }

        return $this;
    }
}
