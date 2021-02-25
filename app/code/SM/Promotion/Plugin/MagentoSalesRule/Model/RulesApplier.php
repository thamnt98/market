<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Promotion
 *
 * Date: July, 21 2020
 * Time: 10:25 AM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Promotion\Plugin\MagentoSalesRule\Model;

class RulesApplier
{
    /**
     * @var \SM\MyVoucher\Model\AmastyRules\DiscountRegistry
     */
    protected $discountRegistry;

    /**
     * RulesApplier constructor.
     *
     * @param \Amasty\Rules\Model\DiscountRegistry $discountRegistry
     */
    public function __construct(
        \Amasty\Rules\Model\DiscountRegistry $discountRegistry
    ) {
        $this->discountRegistry = $discountRegistry;
    }

    public function beforeSetAppliedRuleIds(
        \Magento\SalesRule\Model\RulesApplier $subject,
        \Magento\Quote\Model\Quote\Item\AbstractItem $item,
        array $appliedRuleIds
    ) {
        if (!($item instanceof \Magento\Quote\Model\Quote\Item)) {
            /** @var \Magento\Quote\Model\Quote\Item $quoteItem */
            if ($quoteItem = $item->getQuoteItem()) {
                try {
                    $quoteItem->setAppliedRuleIds(join(',', $appliedRuleIds))->save();
                } catch (\Exception $e) {
                }
            }
        }

        return [$item, $appliedRuleIds];
    }

    public function beforeMaintainAddressCouponCode(
        \Magento\SalesRule\Model\RulesApplier $subject,
        \Magento\Quote\Model\Quote\Address $address,
        \Magento\SalesRule\Model\Rule $rule
    ) {
        $id = ($address->getId()) ? $address->getId() : $address->getFakeAddressId();
        if (!$address->getFreeShipping() &&
            $address->getShippingDiscountAmount() > 0 &&
            $rule->getApplyToShipping()
        ) {
            $this->discountRegistry->setShippingDiscount(
                $rule->getRuleId(),
                $address->getShippingDiscountAmount(),
                $id
            );
        }
    }
}
