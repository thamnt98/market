<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Promotion
 *
 * Date: August, 10 2020
 * Time: 10:47 AM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Promotion\Override\AmastyRules\Model\Rule\Action\Discount;

class BuyXGetNPercent extends \Amasty\Rules\Model\Rule\Action\Discount\BuyxgetnPerc
{
    /**
     * @param \Magento\SalesRule\Model\Rule                $rule
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem $item
     * @param float                                        $rulePercent
     *
     * @return \Magento\SalesRule\Model\Rule\Action\Discount\Data
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _calculate($rule, $item, $rulePercent)
    {
        /** @var \Magento\SalesRule\Model\Rule\Action\Discount\Data $discountData */
        $discountData = $this->discountFactory->create();

        // no conditions for Y elements
        if (!$rule->getAmrulesRule()->getPromoCats() && !$rule->getAmrulesRule()->getPromoSkus()) {
            return $discountData;
        }

        $rulePercent /= 100;
        $address = $item->getAddress();
        $triggerItems = $this->getTriggerElements($address, $rule);
        $realQty = $this->getTriggerElementQty($triggerItems);
        $maxQty = $this->getNQty($rule, $realQty);
        // find all allowed Y (discounted) elements and calculate total discount
        $passedItems = [];
        $lastId = 0;
        $currQty = 0;
        $allItems = $this->getSortedItems($address, $rule, self::DEFAULT_SORT_ORDER);
        $itemsId = $this->getItemsId($allItems);

        foreach ($allItems as $allItem) {
            if ($currQty >= $maxQty) {
                break;
            }

            // we always skip child items and calculate discounts inside parents
            if (!$this->canProcessItem($allItem, $triggerItems, $passedItems)) {
                continue;
            }
            // what should we do with bundles when we treat them as
            // separate items
            $passedItems[$allItem->getAmrulesId()] = $allItem->getAmrulesId();

            if (!$this->isDiscountedItem($rule, $allItem)) {
                continue;
            }

            $qty = $this->getItemQty($allItem);
            if (($qty == $currQty) && ($lastId == $item->getAmrulesId())) {
                continue;
            }

            $maxQty = min(
                floor(
                    $allItem->getQty() / $rule->getAmrulesRule()->getData('nqty')
                ) * $rule->getAmrulesRule()->getData('nqty'),
                $maxQty
            );
            $qty = min($maxQty - $currQty, $qty);
            $currQty += $qty;

            if (in_array($item->getAmrulesId(), $itemsId) && $allItem->getAmrulesId() === $item->getAmrulesId()) {
                $itemPrice = $this->rulesProductHelper->getItemPrice($item);
                $baseItemPrice = $this->rulesProductHelper->getItemBasePrice($item);
                $itemOriginalPrice = $this->rulesProductHelper->getItemOriginalPrice($item);
                $baseItemOriginalPrice = $this->rulesProductHelper->getItemBaseOriginalPrice($item);

                $discountData->setAmount($qty * $itemPrice * $rulePercent);
                $discountData->setBaseAmount($qty * $baseItemPrice * $rulePercent);
                $discountData->setOriginalAmount($qty * $itemOriginalPrice * $rulePercent);
                $discountData->setBaseOriginalAmount($qty * $baseItemOriginalPrice * $rulePercent);

                if (!$rule->getDiscountQty() || $rule->getDiscountQty() > $qty) {
                    $discountPercent = min(100, $item->getDiscountPercent() + $rulePercent * 100);
                    $item->setDiscountPercent($discountPercent);
                }
            }
        }

        return $discountData;
    }
}
