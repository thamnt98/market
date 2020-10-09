<?php

namespace SM\FlashSale\Observer;

use \Magento\Checkout\Model\Cart;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\App\RequestInterface;

class ApplyEventOnQuoteItemSetQty extends \Magento\CatalogEvent\Observer\ApplyEventOnQuoteItemSetQty
{
    protected $historyFactory;
    protected $messageManager;
    protected $productCollection;
    protected $request;
    protected $registry;
    protected $customerSession;

    public function __construct(
        \Magento\CatalogEvent\Helper\Data $catalogEventData,
        \Magento\CatalogEvent\Model\Category\EventList $eventList,
        \SM\FlashSale\Model\HistoryFactory $historyFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\Registry $registry,
        RequestInterface $request,
        \Magento\Customer\Model\SessionFactory $customerSession
    ) {
        $this->historyFactory = $historyFactory;
        $this->messageManager = $messageManager;
        $this->request = $request;
        $this->registry = $registry;
        $this->customerSession = $customerSession;
        parent::__construct($catalogEventData, $eventList);
    }

    /**
     * Applies events to product collection
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void|$this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->catalogEventData->isEnabled()) {
            return $this;
        }

        $item = $observer->getEvent()->getItem();
        /* @var $item \Magento\Quote\Model\Quote\Item */
        if ($item->getQuote()) {
            $this->_initializeEventsForQuoteItems($item->getQuote());
        }

        if ($item->getEventId() != null) {
            $event = $item->getEvent();
            if ($event) {
                $currentProduct = $item->getProduct();
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
                    $item->save();
                } else {
                    if ($currentProduct->getData('is_flashsale') &&
                        $currentProduct->getData('flashsale_qty') > 0 &&
                        $currentProduct->getData('flashsale_qty_per_customer') > 0) {
                        $history = $this->historyFactory->create();
                        $customer = $this->customerSession->create();
                        $collection = $history->getCollection()
                            ->addFieldToFilter('event_id', $item->getEvent()->getId())
                            ->addFieldToFilter('item_id', $item->getProduct()->getId())
                            ->addFieldToFilter('customer_id', $customer->getId());

                        $itemTotalBuy = 0;
                        $itemCustomerBuy = 0;
                        foreach ($collection as $historyItem) {
                            $itemCustomerBuy = $historyItem->getData('item_qty_purchase');
                            $itemTotalBuy += $historyItem->getData('item_qty_purchase');
                        }

                        $flashSaleLimit = $currentProduct->getData('flashsale_qty');
                        $flashSaleCustomerLimit = $currentProduct->getData('flashsale_qty_per_customer');

                        $qtyNow = $item->getQty();

                        $availableQty = $flashSaleLimit - $itemTotalBuy;
                        $availableCustomerQty = $flashSaleCustomerLimit - $itemCustomerBuy;

                        if ($availableQty > 0 && $availableCustomerQty > 0) {
                            $message = "";

                            if ($qtyNow <= $availableQty) {
                                if ($qtyNow > $availableCustomerQty) {
                                    $qtyNow = $availableCustomerQty;
                                    $message = __("You exceeded the maximum quantity of Surprise Deals product. The excess items have been removed & can be purchased later in normal price.");
                                }
                            } else {
                                $qtyNow = $availableQty;
                                if ($qtyNow > $availableCustomerQty) {
                                    $qtyNow = $availableCustomerQty;
                                }
                                $message = __("You exceeded the maximum quantity of Surprise Deals product. The excess items have been removed & can be purchased later in normal price.");
                            }

                            if ($message != "") {
                                if (!$this->registry->registry("flashsale_message")) {
                                    $this->registry->register("flashsale_message", 1);
                                }
                                $this->messageManager->addWarningMessage($message);
                            }
                            if ($currentProduct->getSpecialPrice()) {
                                $price = $currentProduct->getSpecialPrice();
                            } else {
                                $price = $currentProduct->getPrice();
                            }

                            $item->setCustomPrice($price);
                            $item->setOriginalCustomPrice($price);
                            $item->setData($item::KEY_QTY, $qtyNow);
                            $item->getProduct()->setIsSuperMode(true);
                        } else {
                            $item->setEventId(null);
                            if ($item->getParentItem()) {
                                $item->getParentItem()->setEventId(null);
                            }
                            $item->setCustomPrice($currentProduct->getPrice());
                            $item->setOriginalCustomPrice($currentProduct->getPrice());
                            $item->getProduct()->setIsSuperMode(true);
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
}
