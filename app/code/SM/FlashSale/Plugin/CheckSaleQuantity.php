<?php

namespace SM\FlashSale\Plugin;
use Magento\Catalog\Model\Product;
use Magento\Framework\Controller\ResultFactory;

class CheckSaleQuantity {

    protected $historyFactory;
    protected $_customerSession;
    protected $messageManager;
    protected $productCollectionFactory;
    protected $resultRedirect;
    protected $categoryEventList;
    protected $responseFactory;
    protected $url;
    protected $cart;

    protected $productFactory;

    public function __construct(
        \Magento\Customer\Model\SessionFactory $customerSession,
        \SM\FlashSale\Model\HistoryFactory $historyFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        ResultFactory $result,
        \Magento\Framework\App\ResponseInterface $responseFactory,
        \Magento\Framework\UrlInterface $url,
        \Magento\CatalogEvent\Model\Category\EventList $eventList,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory
    ) {
        $this->historyFactory = $historyFactory;
        $this->_customerSession = $customerSession;
        $this->messageManager = $messageManager;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->resultRedirect = $result;
        $this->categoryEventList = $eventList;
        $this->responseFactory = $responseFactory;
        $this->url = $url;
        $this->cart = $cart;
        $this->productFactory = $productFactory;

    }

    public function aroundConvert(\Magento\Quote\Model\Quote\Item\ToOrderItem $subject, \Closure $proceed, $item, $data = []){

        if ($item->getQuote()) {
            $this->_initializeEventsForQuoteItems($item->getQuote());
        }

        if ($item->getEventId() != null) {
            $isSale = true;
            $customer = $this->_customerSession->create();
            $currentProduct = $this->productFactory->create()->load($item->getProductId());

            $event = $item->getEvent();

            if ($event) {
                if ($event->getStatus() !== \Magento\CatalogEvent\Model\Event::STATUS_OPEN) {
                    $item->setCustomPrice($currentProduct->getPrice());
                    $item->setOriginalCustomPrice($currentProduct->getPrice());
                    $item->getProduct()->setIsSuperMode(true);
                    $item->setEventId(null);
                    $item->setEvent(null);
                    $item->save();
                    $isSale = false;
                } else {
                    if ($currentProduct->getData('is_flashsale') &&
                        $currentProduct->getData('flashsale_qty') > 0 &&
                        $currentProduct->getData('flashsale_qty_per_customer') > 0) {

                        $history = $this->historyFactory->create();
                        $collection = $history->getCollection()
                            ->addFieldToFilter('event_id', $item->getEvent()->getId())
                            ->addFieldToFilter('item_id', $item->getProduct()->getId());

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

                        $qtyNow = $item->getQty();

                        $availableQty = $flashSaleLimit - $itemTotalBuy;
                        $availableCustomerQty = $flashSaleCustomerLimit - $itemCustomerBuy;

                        if ($availableQty > 0 && $availableCustomerQty > 0) {
                            $message = "";
                            if ($qtyNow <= $availableQty) {
                                if ($qtyNow > $availableCustomerQty) {
                                    $qtyNow = $availableCustomerQty;
                                    $message = __("Only $qtyNow items available  for sale now. So you can only purchase $qtyNow item with special price");
                                }
                            }else{
                                $qtyNow = $availableQty;
                                if($qtyNow > $availableCustomerQty){
                                    $qtyNow = $availableCustomerQty;
                                }
                                $message = __("Only $qtyNow items available  for sale now. So you can only purchase $qtyNow item with special price");
                            }

                            if($message != "") {
                                $this->messageManager->addWarningMessage($message);
                            }
                            if($currentProduct->getSpecialPrice()){
                                $price = $currentProduct->getSpecialPrice();
                            }else{
                                $price = $currentProduct->getPrice();
                            }

                            $item->setCustomPrice($price);
                            $item->setOriginalCustomPrice($price);
                            $item->setData($item::KEY_QTY,$qtyNow);
                            $item->getProduct()->setIsSuperMode(true);

                            $qtyCustomer = $history->getCollection()
                                ->addFieldToFilter('event_id', $item->getEvent()->getId())
                                ->addFieldToFilter('item_id', $item->getProduct()->getId())
                                ->addFieldToFilter('customer_id', $customer->getId())->getFirstItem();
                            $updatedQty = $qtyNow;

                            if ($qtyCustomer->getId()) {
                                $updatedQty = $qtyCustomer->getData('item_qty_purchase') + $qtyNow;
                            }
                            $dataFlashsaleHistory = $customer->getFlashSaleData() ?? [];

                            $dataFlashsaleHistory[] = [
                                'event_id' => $item->getEvent()->getId(),
                                'item_id' => $item->getProduct()->getId(),
                                'item_qty_purchase' => $updatedQty,
                            ];
                            $customer->setFlashSaleData($dataFlashsaleHistory);
                        }else{
                            $item->setEventId(null);
                            if ($item->getParentItem()) {
                                $item->getParentItem()->setEventId(null);
                            }
                            $item->setCustomPrice($currentProduct->getPrice());
                            $item->setOriginalCustomPrice($currentProduct->getPrice());
                            $item->getProduct()->setIsSuperMode(true);
                        }
                    }else{
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

        return $proceed($item,$data);
    }


    protected function _initializeEventsForQuoteItems($quote)
    {
        if (!$quote->getEventInitialized()) {
            $quote->setEventInitialized(true);
            $eventIds = array_diff($quote->getItemsCollection()->getColumnValues('event_id'), [0]);

            if (!empty($eventIds)) {
                $collection = $this->categoryEventList->getEventCollection();
                $collection->addFieldToFilter('event_id', ['in' => $eventIds]);
                foreach ($collection as $event) {
                    $items = $quote->getItemsCollection()->getItemsByColumnValue('event_id', $event->getId());
                    foreach ($items as $quoteItem) {
                        $quoteItem->setEvent($event);
                    }
                }
            }
        }
    }
}