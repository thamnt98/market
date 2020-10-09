<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Promotion
 *
 * Date: September, 17 2020
 * Time: 4:11 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Promotion\Plugin\AmastyRules\Model\Rule\Action;

class Discount
{
    /**
     * @param \Amasty\Rules\Model\Rule\Action\Discount\AbstractRule $subject
     * @param \Magento\SalesRule\Model\Rule\Action\Discount\Data    $discountData
     * @param \Magento\SalesRule\Model\Rule                         $rule
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem          $item
     *
     * @return array
     */
    public function beforeAfterCalculate(
        \Amasty\Rules\Model\Rule\Action\Discount\AbstractRule $subject,
        $discountData,
        $rule,
        $item
    ) {
        if ($item instanceof \Magento\Quote\Model\Quote\Address\Item && $item->getQuoteItem()) {
            $qItem = $item->getQuoteItem();
        } else {
            $qItem = $item;
        }

        return [$discountData, $rule, $qItem];
    }
}
