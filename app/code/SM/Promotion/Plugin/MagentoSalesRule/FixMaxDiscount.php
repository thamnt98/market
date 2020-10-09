<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Promotion
 *
 * Date: September, 17 2020
 * Time: 2:33 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Promotion\Plugin\MagentoSalesRule;

class FixMaxDiscount
{
    /**
     * @var \Amasty\Rules\Helper\Discount
     */
    protected $rulesDiscountHelper;

    /**
     * @var \Amasty\Rules\Model\RuleResolver
     */
    protected $ruleResolver;

    /**
     * FixMaxDiscount constructor.
     *
     * @param \Amasty\Rules\Model\RuleResolver $ruleResolver
     * @param \Amasty\Rules\Helper\Discount    $rulesDiscountHelper
     */
    public function __construct(
        \Amasty\Rules\Model\RuleResolver $ruleResolver,
        \Amasty\Rules\Helper\Discount $rulesDiscountHelper
    ) {
        $this->rulesDiscountHelper = $rulesDiscountHelper;
        $this->ruleResolver = $ruleResolver;
    }

    /**
     * @param \Magento\SalesRule\Model\Rule\Action\Discount\DiscountInterface $subject
     * @param \Magento\SalesRule\Model\Rule\Action\Discount\Data              $result
     * @param \Magento\SalesRule\Model\Rule                                   $rule
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem                    $item
     * @param float                                                           $qty
     *
     * @return \Magento\SalesRule\Model\Rule\Action\Discount\Data
     */
    public function afterCalculate(
        $subject,
        $result,
        $rule,
        $item,
        $qty
    ) {
        try {
            if (!$rule->getData('amrules_rule')) {
                $rule->setData('amrules_rule', $this->ruleResolver->getSpecialPromotions($rule));
            }

            if ($item instanceof \Magento\Quote\Model\Quote\Item) {
                $itemId = $item->getId();
            } else {
                $itemId = $item->getQuoteItemId();
            }

            if ($rule->getData('amrules_rule')) {
                $this->rulesDiscountHelper->setDiscount($rule, $result, $item->getStore(), $itemId);
            }
        } catch (\Exception $e) {
        }

        return $result;
    }

}
