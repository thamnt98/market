<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Notification
 *
 * Date: October, 05 2020
 * Time: 6:52 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Notification\Observer;

use Magento\Quote\Model\Quote\Item as QuoteItem;

class QuoteItemSaveBefore implements \Magento\Framework\Event\ObserverInterface
{
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var QuoteItem $item */
        $item = $observer->getEvent()->getData('item');
        if ($item && $item->getId() && $item->getQty() != $item->getOrigData(QuoteItem::KEY_QTY)) {
            $item->setData('qty_updated_at', date('y-m-d H:i:s'));
        }
    }
}
