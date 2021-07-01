<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Checkout
 *
 * Date: November, 26 2020
 * Time: 4:11 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Checkout\Override\MagentoCheckout\Block\Cart;

class Grid extends \Magento\Checkout\Block\Cart\Grid
{
    /**
     * @var \Magento\Quote\Model\ResourceModel\Quote\Item\Collection
     */
    protected $itemCollection;

    /**
     * @var int
     */
    protected $itemsCount;

    /**
     * @override
     * @return \Magento\Quote\Model\ResourceModel\Quote\Item\Collection
     */
    public function getItemsForGrid()
    {
        if (!$this->itemCollection) {
            $this->itemCollection = parent::getItemsForGrid();
            $this->itemCollection->addFieldToFilter('is_virtual', (int)$this->getQuote()->getIsVirtual());
        }

        return $this->itemCollection;
    }

    /**
     * @override
     * @return int
     */
    public function getItemsCount()
    {
        if (is_null($this->itemsCount)) {
            $this->itemsCount = 0;
            /** @var \Magento\Quote\Model\Quote\Item $item */
            foreach ($this->getQuote()->getItemsCollection() as $key => $item) {
                if ($item->getParentItemId() ||
                    $item->isDeleted() ||
                    ($this->getQuote()->getIsVirtual() && !$item->getIsVirtual()) ||
                    (!$this->getQuote()->getIsVirtual() && $item->getIsVirtual())
                ) {
                    continue;
                }

                ++$this->itemsCount;
            }
        }

        return $this->itemsCount;
    }

    /**
     * @return array|\Magento\Framework\DataObject[]
     */
    public function getItems()
    {
        return $this->getItemsForGrid()->getItems();
    }
}
