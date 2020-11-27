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

namespace SM\Checkout\Override\Magento_Checkout\Block\Cart;

class Grid extends \Magento\Checkout\Block\Cart\Grid
{
    /**
     * @var \Magento\Quote\Model\ResourceModel\Quote\Item\Collection
     */
    protected $itemCollection;

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
}
