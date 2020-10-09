<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_FlashSale
 *
 * Date: September, 16 2020
 * Time: 3:53 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\FlashSale\Model\Customer;

class Calculation
{
    /**
     * @var \SM\FlashSale\Model\ResourceModel\History\CollectionFactory
     */
    protected $historyCollFact;

    /**
     * @var \SM\FlashSale\Observer\ProductEventApplier
     */
    protected $eventApplier;

    /**
     * @var array
     */
    protected $prices = [];

    /**
     * Calculation constructor.
     *
     * @param \SM\FlashSale\Observer\ProductEventApplier                  $eventApplier
     * @param \SM\FlashSale\Model\ResourceModel\History\CollectionFactory $historyCollFact
     */
    public function __construct(
        \SM\FlashSale\Observer\ProductEventApplier $eventApplier,
        \SM\FlashSale\Model\ResourceModel\History\CollectionFactory $historyCollFact
    ) {
        $this->historyCollFact = $historyCollFact;
        $this->eventApplier = $eventApplier;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem $item
     *
     * @return float
     */
    public function getFlashSalePrice($item)
    {
        if (!isset($this->prices[$item->getId()])||empty($item->getId())) {
            $product = $item->getProduct();
            $event = $this->getFlashSaleEvent($product);
            $limit = $product->getData('flashsale_qty');
            $customerLimit = $product->getData('flashsale_qty_per_customer');
            if (!$event) {
                if (empty($item->getId())) {
                    return (float)$product->getFinalPrice($item->getQty());
                }
                $this->prices[$item->getId()] = (float) $product->getFinalPrice($item->getQty());
            } else {
                $customerId = $item->getQuote()->getCustomerId();
                $itemTotalBuy = $itemCustomerBuy = 0;

                /** @var \SM\FlashSale\Model\ResourceModel\History\Collection $collection */
                $collection = $this->historyCollFact->create()
                    ->addFieldToFilter('event_id', $event->getId())
                    ->addFieldToFilter('item_id', $product->getId());

                foreach ($collection as $historyItem) {
                    if ($customerId == $historyItem->getData("customer_id")) {
                        $itemCustomerBuy = $historyItem->getData('item_qty_purchase');
                    }

                    $itemTotalBuy += $historyItem->getData('item_qty_purchase');
                }

                if (($limit - $itemTotalBuy) > 0 && ($customerLimit - $itemCustomerBuy) > 0) {
                    if (empty($item->getId())) {
                        return  (float) $product->getFinalPrice($item->getQty());
                    }
                    $this->prices[$item->getId()] = (float) $product->getFinalPrice($item->getQty());
                } else {
                    if (empty($item->getId())) {
                        return (float) $product->getPrice();
                    }
                    $this->prices[$item->getId()] = (float) $product->getPrice();
                }
            }
        }

        return $this->prices[$item->getId()];
    }

    /**
     * @param $product
     *
     * @return \Magento\CatalogEvent\Model\Event|null
     */
    public function getFlashSaleEvent($product)
    {
        $this->eventApplier->applyEventToProduct($product);
        $limit = $product->getData('flashsale_qty');
        $customerLimit = $product->getData('flashsale_qty_per_customer');
        if (!$product->getEvent() ||
            $product->getEvent()->getStatus() !== \Magento\CatalogEvent\Model\Event::STATUS_OPEN ||
            !$product->getData('is_flashsale') ||
            $limit < 1 ||
            $customerLimit < 1
        ) {
            return null;
        }

        return $product->getEvent();
    }
}
