<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Promotion
 *
 * Date: August, 06 2020
 * Time: 2:59 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Promotion\Model\Rule\Validator;

class AmastyPromo implements \Amasty\Rules\Api\ExtendedValidatorInterface
{
    /**
     * @var \Amasty\Promo\Model\RuleResolver
     */
    protected $ruleResolver;

    /**
     * @var \Amasty\Promo\Helper\Item
     */
    protected $amHelper;

    /**
     * BuyXGetY constructor.
     *
     * @param \Amasty\Promo\Model\RuleResolver $ruleResolver
     * @param \Amasty\Promo\Helper\Item        $amHelper
     */
    public function __construct(
        \Amasty\Promo\Model\RuleResolver $ruleResolver,
        \Amasty\Promo\Helper\Item $amHelper
    ) {
        $this->ruleResolver = $ruleResolver;
        $this->amHelper = $amHelper;
    }

    /**
     * @param $combineCondition
     * @param $type
     *
     * @return bool|null
     */
    public function validate($combineCondition, $type)
    {
        if ($type instanceof \Magento\Quote\Model\Quote\Item) {
            $discountItem = $this->checkActionItem($combineCondition->getRule(), $type);
            if ($discountItem) {
                return true;
            }
        }

        return null;
    }

    /**
     * @param \Magento\SalesRule\Model\Rule   $rule
     * @param \Magento\Quote\Model\Quote\Item $item
     *
     * @return bool
     */
    protected function checkActionItem($rule, $item)
    {
        $action = $rule->getSimpleAction();

        if (strpos($action, "ampromo_") !== false) {
            $amastyRule = $this->ruleResolver->getFreeGiftRule($rule);
            $isPromoItem = $this->amHelper->isPromoItem($item);
            $promoSku = $amastyRule->getSku();
            $itemSku = $item->getSku();

            if ($isPromoItem && strpos($promoSku, $itemSku) !== false) {
                return true;
            }
        }

        return false;
    }
}
