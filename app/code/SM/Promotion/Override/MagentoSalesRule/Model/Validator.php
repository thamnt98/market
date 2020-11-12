<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Promotion
 *
 * Date: August, 12 2020
 * Time: 11:18 AM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Promotion\Override\MagentoSalesRule\Model;

class Validator extends \Magento\SalesRule\Model\Validator
{
    const CUSTOMER_RULE_PREFIX = 'customer_rule_';

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \SM\Promotion\Model\Rule\Validator\CustomerUses
     */
    protected $customerUses;

    /**
     * @var \Amasty\Rules\Model\RuleResolver
     */
    protected $amastyRuleResolver;

    /**
     * Validator constructor.
     *
     * @param \Amasty\Rules\Model\RuleResolver                              $amastyRuleResolver
     * @param \SM\Promotion\Model\Rule\Validator\CustomerUses               $customerUses
     * @param \Magento\Checkout\Model\Session                               $checkoutSession
     * @param \Magento\Framework\Model\Context                              $context
     * @param \Magento\Framework\Registry                                   $registry
     * @param \Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory $collectionFactory
     * @param \Magento\Catalog\Helper\Data                                  $catalogData
     * @param \Magento\SalesRule\Model\Utility                              $utility
     * @param \Magento\SalesRule\Model\RulesApplier                         $rulesApplier
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface             $priceCurrency
     * @param \Magento\SalesRule\Model\Validator\Pool                       $validators
     * @param \Magento\Framework\Message\ManagerInterface                   $messageManager
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null  $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null            $resourceCollection
     * @param array                                                         $data
     */
    public function __construct(
        \Amasty\Rules\Model\RuleResolver $amastyRuleResolver,
        \SM\Promotion\Model\Rule\Validator\CustomerUses $customerUses,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory $collectionFactory,
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\SalesRule\Model\Utility $utility,
        \Magento\SalesRule\Model\RulesApplier $rulesApplier,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\SalesRule\Model\Validator\Pool $validators,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $collectionFactory,
            $catalogData,
            $utility,
            $rulesApplier,
            $priceCurrency,
            $validators,
            $messageManager,
            $resource,
            $resourceCollection,
            $data
        );
        $this->checkoutSession = $checkoutSession;
        $this->customerUses = $customerUses;
        $this->amastyRuleResolver = $amastyRuleResolver;
    }

    public function processShippingAmount(\Magento\Quote\Model\Quote\Address $address)
    {
        if ($isMain = $this->checkoutSession->getMainOrder()) {
            $this->checkoutSession->unsMainOrder();
        }

        $shippingAmount = $address->getShippingAmount();
        $baseShippingAmount = $address->getBaseShippingAmount();
        $quote = $address->getQuote();
        $appliedRuleIds = [];
        /** @var \Magento\SalesRule\Model\Rule[] $rules */
        $rules = $this->_getRules($address)->getItems();

        if ($this->addressIsFreeShip($address) || empty($rules)) {
            $address->setShippingDiscountAmount(0);
            $address->setBaseShippingDiscountAmount(0);
            if ($isMain) {
                $this->checkoutSession->setMainOrder(true);
            }

            return $this;
        }

        foreach ($rules as $rule) {
            $this->customerUses->unsetAddressRule($rule, $address);
            $customerUses = $this->customerUses->getCustomerUseLeft($rule);
            /* @var \Magento\SalesRule\Model\Rule $rule */
            if (!$rule->getApplyToShipping() ||
                !$this->validatorUtility->canProcessRule($rule, $address) ||
                !$customerUses
            ) {
                continue;
            }

            $discountAmount = 0;
            $baseDiscountAmount = 0;
            $rulePercent = min(100, $rule->getDiscountAmount());
            switch ($rule->getSimpleAction()) {
                case \Magento\SalesRule\Model\Rule::TO_PERCENT_ACTION:
                    $rulePercent = max(0, 100 - $rule->getDiscountAmount());
                // break is intentionally omitted
                case \Magento\SalesRule\Model\Rule::BY_PERCENT_ACTION:
                    $discountAmount = ($shippingAmount - $address->getShippingDiscountAmount()) * $rulePercent / 100;
                    $baseDiscountAmount = ($baseShippingAmount -
                            $address->getBaseShippingDiscountAmount()) * $rulePercent / 100;
                    $discountPercent = min(100, $address->getShippingDiscountPercent() + $rulePercent);
                    $address->setShippingDiscountPercent($discountPercent);
                    break;
                case \Magento\SalesRule\Model\Rule::TO_FIXED_ACTION:
                    $quoteAmount = $this->priceCurrency->convert($rule->getDiscountAmount(), $quote->getStore());
                    $discountAmount = $shippingAmount - $quoteAmount;
                    $baseDiscountAmount = $baseShippingAmount - $rule->getDiscountAmount();
                    break;
                case \Magento\SalesRule\Model\Rule::BY_FIXED_ACTION:
                    $quoteAmount = $this->priceCurrency->convert($rule->getDiscountAmount(), $quote->getStore());
                    $discountAmount = $quoteAmount;
                    $baseDiscountAmount = $rule->getDiscountAmount();
                    break;
            }

            try {
                $rule->setData('amrules_rule', $this->amastyRuleResolver->getSpecialPromotions($rule));
                if ($rule->getAmrulesRule() && $rule->getAmrulesRule()->getMaxDiscount() != 0) {
                    $basePercent = $baseDiscountAmount / $discountAmount;
                    $discountAmount = min($discountAmount, $rule->getAmrulesRule()->getMaxDiscount());
                    $baseDiscountAmount = $discountAmount * $basePercent;
                }
            } catch (\Exception $e) {
            }

            $discountAmount = min($address->getShippingDiscountAmount() + $discountAmount, $shippingAmount);
            $baseDiscountAmount = min(
                $address->getBaseShippingDiscountAmount() + $baseDiscountAmount,
                $baseShippingAmount
            );
            $address->setShippingDiscountAmount($discountAmount);
            $address->setBaseShippingDiscountAmount($baseDiscountAmount);
            $appliedRuleIds[$rule->getRuleId()] = $rule->getRuleId();
            $this->customerUses->setAddressRule($rule, $address);

            $this->rulesApplier->maintainAddressCouponCode($address, $rule, $this->getCouponCode());
            $this->rulesApplier->addDiscountDescription($address, $rule);
            if ($rule->getStopRulesProcessing()) {
                break;
            }
        }

        $address->setAppliedRuleIds($this->validatorUtility->mergeIds($address->getAppliedRuleIds(), $appliedRuleIds));
        $address->setData(
            \SM\Promotion\Model\Data\Rule::SHIPPING_RULE_IDS_FIELD,
            $this->validatorUtility->mergeIds(
                $address->getData(\SM\Promotion\Model\Data\Rule::SHIPPING_RULE_IDS_FIELD),
                $appliedRuleIds
            )
        );
        $quote->setData(
            \SM\Promotion\Model\Data\Rule::SHIPPING_RULE_IDS_FIELD,
            $this->validatorUtility->mergeIds(
                $quote->getData(\SM\Promotion\Model\Data\Rule::SHIPPING_RULE_IDS_FIELD),
                $appliedRuleIds
            )
        );
        $quote->setAppliedRuleIds($this->validatorUtility->mergeIds($quote->getAppliedRuleIds(), $appliedRuleIds));
        if ($isMain) {
            $this->checkoutSession->setMainOrder(true);
        }

        return $this;
    }

    /**
     * @override
     * Reset quote and address applied rules
     *
     * @param \Magento\Quote\Model\Quote\Address $address
     *
     * @return \Magento\SalesRule\Model\Validator
     */
    public function reset(\Magento\Quote\Model\Quote\Address $address)
    {
        $this->validatorUtility->resetRoundingDeltas();
        $address->setBaseSubtotalWithDiscount($address->getBaseSubtotal());
        $address->setSubtotalWithDiscount($address->getSubtotal());
        if ($this->_isFirstTimeResetRun) {
            $address->setAppliedRuleIds('');
            $address->setData(\SM\Promotion\Model\Data\Rule::SHIPPING_RULE_IDS_FIELD, '');
            $address->getQuote()->setAppliedRuleIds('');
            $address->getQuote()->setData(\SM\Promotion\Model\Data\Rule::SHIPPING_RULE_IDS_FIELD, '');
            $this->_isFirstTimeResetRun = false;
        }

        return $this;
    }

    /**
     * @override
     * Get rules collection for current object state
     *
     * @param \Magento\Quote\Model\Quote\Address|null $address
     *
     * @return \Magento\SalesRule\Model\ResourceModel\Rule\Collection
     */
    protected function _getRules(\Magento\Quote\Model\Quote\Address $address = null)
    {
        $addressId = $this->getAddressId($address);
        $key = $this->getWebsiteId() . '_'
            . $this->getCustomerGroupId() . '_'
            . $this->getCouponCode() . '_'
            . $addressId . '_'
            . (int)$this->checkoutSession->getMainOrder();
        if (!isset($this->_rules[$key])) {
            $this->_rules[$key] = $this->_collectionFactory->create()
                ->setValidationFilter(
                    $this->getWebsiteId(),
                    $this->getCustomerGroupId(),
                    $this->getCouponCode(),
                    null,
                    $address
                )
                ->addFieldToFilter('is_active', 1)
                ->load();
        }

        return $this->_rules[$key];
    }

    /**
     * @param \Magento\Quote\Model\Quote\Address $address
     *
     * @return bool
     */
    protected function addressIsFreeShip($address)
    {
        /** @var \Magento\Quote\Model\Quote\Address\Item $item */
        foreach ($address->getAllVisibleItems() as $item) {
            if ($item->getFreeShipping()) {
                return true;
            }
        }

        return false;
    }

    public function process(\Magento\Quote\Model\Quote\Item\AbstractItem $item)
    {
        if (!($item instanceof \Magento\Quote\Model\Quote\Item) && $item->getQuoteItem()) {
            $item->getQuoteItem()
                ->setDiscountAmount(0)
                ->setBaseDiscountAmount(0)
                ->setDiscountPercent(0);
        }

        return parent::process($item);
    }
}
