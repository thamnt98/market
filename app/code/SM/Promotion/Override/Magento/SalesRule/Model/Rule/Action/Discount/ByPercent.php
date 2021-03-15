<?php

/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Promotion
 *
 * Date: March, 01 2021
 * Time: 6:30 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Promotion\Override\Magento\SalesRule\Model\Rule\Action\Discount;

class ByPercent extends \Magento\SalesRule\Model\Rule\Action\Discount\ByPercent
{
    /**
     * @param \Magento\SalesRule\Model\Rule $rule
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem $item
     * @param float $qty
     * @param float $rulePercent
     * @return \Magento\SalesRule\Model\Rule\Action\Discount\Data
     */
    protected function _calculate($rule, $item, $qty, $rulePercent)
    {
        /** @var \Magento\SalesRule\Model\Rule\Action\Discount\Data $discountData */
        $discountData = $this->discountFactory->create();

        $itemPrice = $this->validator->getItemPrice($item);
        $baseItemPrice = $this->validator->getItemBasePrice($item);
        $itemOriginalPrice = $this->validator->getItemOriginalPrice($item);
        $baseItemOriginalPrice = $this->validator->getItemBaseOriginalPrice($item);

        $_rulePct = $rulePercent / 100;
        $discountData->setAmount(($qty * $itemPrice) * $_rulePct);
        $discountData->setBaseAmount(($qty * $baseItemPrice) * $_rulePct);
        $discountData->setOriginalAmount(($qty * $itemOriginalPrice) * $_rulePct);
        $discountData->setBaseOriginalAmount(($qty * $baseItemOriginalPrice) * $_rulePct);

        if (!$rule->getDiscountQty() || $rule->getDiscountQty() > $qty) {
            $discountPercent = min(100, $item->getDiscountPercent() + $rulePercent);
            $item->setDiscountPercent($discountPercent);
        }

        return $discountData;
    }
}
