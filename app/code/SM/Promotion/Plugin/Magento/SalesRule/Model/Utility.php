<?php

/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Promotion
 *
 * Date: March, 01 2021
 * Time: 6:34 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Promotion\Plugin\Magento\SalesRule\Model;

class Utility
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    /**
     * DayOfWeekRule constructor.
     *
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     */
    public function __construct(
        \Magento\Framework\Stdlib\DateTime\DateTime $date
    ) {
        $this->date = $date;
    }

    /**
     * Check if rule can be applied for specific address/quote/customer
     *
     * @param \Magento\SalesRule\Model\Utility   $subject
     * @param \Closure                           $proceed
     * @param \Magento\SalesRule\Model\Rule      $rule
     * @param \Magento\Quote\Model\Quote\Address $address -
     *
     * @return bool
     */
    public function aroundCanProcessRule(\Magento\SalesRule\Model\Utility $subject, \Closure $proceed, $rule, $address)
    {
        /** @var null|string $dayOfWeek */

        $dayOfWeek = $rule->getData('day_of_week');
        if (!empty($dayOfWeek)) {
            $days = explode(",", $dayOfWeek);

            $today = $this->date->date('N');
            if (!in_array($today, $days)) {
                return false;
            }
        }

        return $proceed($rule, $address);
    }

    /**
     * @param \Magento\SalesRule\Model\Utility                   $subject
     * @param callable                                           $proceed
     * @param \Magento\SalesRule\Model\Rule\Action\Discount\Data $discountData
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem       $item
     * @param float                                              $qty
     */
    public function aroundMinFix(
        \Magento\SalesRule\Model\Utility $subject,
        callable $proceed,
        \Magento\SalesRule\Model\Rule\Action\Discount\Data $discountData,
        \Magento\Quote\Model\Quote\Item\AbstractItem $item,
        $qty
    ) {
        $itemPrice     = $subject->getItemPrice($item);
        $baseItemPrice = $subject->getItemBasePrice($item);

        $itemDiscountAmount     = $item->getDiscountAmount();
        $itemBaseDiscountAmount = $item->getBaseDiscountAmount();

        $discountAmount     = min($discountData->getAmount(), $itemPrice * $qty);
        $baseDiscountAmount = min($discountData->getBaseAmount(), $baseItemPrice * $qty);

        $discountAmount     = min($discountAmount + $itemDiscountAmount, $itemPrice * $item->getQty());
        $baseDiscountAmount = min($baseDiscountAmount + $itemBaseDiscountAmount, $baseItemPrice * $item->getQty());

        $discountData->setAmount($discountAmount);
        $discountData->setBaseAmount($baseDiscountAmount);
    }
}
