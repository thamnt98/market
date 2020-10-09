<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Promotion
 *
 * Date: August, 18 2020
 * Time: 2:26 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Promotion\Plugin\Model\Rule\Action\Discount;

class SetOf
{
    public static $cache = [];

    /**
     * @param \Amasty\Rules\Model\Rule\Action\Discount\AbstractSetof $subject
     * @param \Magento\SalesRule\Model\Rule                          $rule
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem           $item
     * @param float                                                  $qty
     *
     * @return array
     */
    public function beforeCalculate($subject, $rule, $item, $qty)
    {
        if ($item instanceof \Magento\Quote\Model\Quote\Item) {
            $qItem = $item;
        } else {
            $qItem = $item->getQuoteItem();
        }

        if ($qItem) {
            if (!key_exists($rule->getId(), self::$cache) ||
                in_array($qItem->getId(), self::$cache[$rule->getId()])
            ) {
                $subject::$allItems = null;
                self::$cache[$rule->getId()][] = $qItem->getId();
            } else {
                self::$cache[$rule->getId()][] = $qItem->getId();
            }
        }

        return [$rule, $item, $qty];
    }
}
