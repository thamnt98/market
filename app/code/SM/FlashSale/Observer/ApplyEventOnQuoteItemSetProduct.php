<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace SM\FlashSale\Observer;

use Magento\Catalog\Model\ProductFactory;
use Magento\CatalogEvent\Observer\ProductEventApplier;
use Magento\Framework\Event\ObserverInterface;

class ApplyEventOnQuoteItemSetProduct extends \Magento\CatalogEvent\Observer\ApplyEventOnQuoteItemSetProduct
{

    protected $historyFactory;
    protected $_customerSession;
    protected $messageManager;
    protected $productCollection;

    public function __construct(ProductFactory $product,
                                \Magento\CatalogEvent\Helper\Data $catalogEventData,
                                ProductEventApplier $eventApplier,
                                \Magento\Customer\Model\SessionFactory $customerSession,
                                \SM\FlashSale\Model\HistoryFactory $historyFactory,
                                \Magento\Framework\Message\ManagerInterface $messageManager)
    {
        $this->historyFactory = $historyFactory;
        $this->_customerSession   = $customerSession;
        $this->messageManager = $messageManager;
        $this->productCollection = $product;

        parent::__construct($catalogEventData, $eventApplier);
    }

    /**
     * Applies events to product collection
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->catalogEventData->isEnabled()) {
            return $this;
        }

        /* @var $product \Magento\Catalog\Model\Product */
        $product = $observer->getEvent()->getProduct();
        /* @var $quoteItem \Magento\Quote\Model\Quote\Item */
        $quoteItem = $observer->getEvent()->getQuoteItem();

        $this->eventApplier->applyEventToProduct($product);
        $customer = $this->_customerSession->create();

        if($customer->isLoggedIn()) {
            if ($product->getEvent() && $product->getEvent()->getStatus() == \Magento\CatalogEvent\Model\Event::STATUS_OPEN) {
                if ($product->getData('is_flashsale') &&
                    $product->getData('flashsale_qty') > 0 &&
                    $product->getData('flashsale_qty_per_customer') > 0) {

                    $history = $this->historyFactory->create();
                    $collection = $history->getCollection()
                        ->addFieldToFilter('event_id', $product->getEvent()->getId())
                        ->addFieldToFilter('item_id', $quoteItem->getProduct()->getId());

                    $itemTotalBuy = 0;
                    $itemCustomerBuy = 0;
                    foreach ($collection as $historyItem) {
                        if ($customer->getId() == $historyItem->getData("customer_id")) {
                            $itemCustomerBuy = $historyItem->getData('item_qty_purchase');
                        }
                        $itemTotalBuy += $historyItem->getData('item_qty_purchase');
                    }

                    $flashSaleLimit = $product->getData('flashsale_qty');
                    $flashSaleCustomerLimit = $product->getData('flashsale_qty_per_customer');

                    $qtyNow = $quoteItem->getQty();

                    $availableQty = $flashSaleLimit - $itemTotalBuy;
                    $availableCustomerQty = $flashSaleCustomerLimit - $itemCustomerBuy;

                    $currentProduct = $this->productCollection->create()->load($quoteItem->getProductId());

                    if ($availableQty > 0 && $availableCustomerQty > 0) {
                        $message = "";
                        if ($qtyNow <= $availableQty) {
                            if ($qtyNow > $availableCustomerQty) {
                                $qtyNow = $availableCustomerQty;
                                $message = __("You exceeded the maximum quantity of Surprise Deals item");
                            }
                        }else{
                            $qtyNow = $availableQty;
                            if($qtyNow > $availableCustomerQty){
                                $qtyNow = $availableCustomerQty;
                            }
                            $message = __("You exceeded the maximum quantity of Surprise Deals item");
                        }

                        $quoteItem->setEventId($product->getEvent()->getId());

                        if ($quoteItem->getParentItem()) {
                            $quoteItem->getParentItem()->setEventId($quoteItem->getEventId());
                        }

                        if($message != "") {
                            $this->messageManager->addWarningMessage($message);
                        }

                        if($currentProduct->getSpecialPrice()){
                            $price = $currentProduct->getSpecialPrice();
                        }else{
                            $price = $currentProduct->getPrice();
                        }

                        $quoteItem->setCustomPrice($price);
                        $quoteItem->setOriginalCustomPrice($price);
                        $quoteItem->setData($quoteItem::KEY_QTY,$qtyNow);
                        $quoteItem->getProduct()->setIsSuperMode(true);

                    }else{
                        $quoteItem->setEventId(null);

                        if ($quoteItem->getParentItem()) {
                            $quoteItem->getParentItem()->setEventId(null);
                        }

                        $quoteItem->setCustomPrice($currentProduct->getPrice());
                        $quoteItem->setOriginalCustomPrice($currentProduct->getPrice());
                        $quoteItem->getProduct()->setIsSuperMode(true);
                    }
                }
            }
        }
        return $this;
    }
}
