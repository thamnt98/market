<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Promotion
 *
 * Date: July, 17 2020
 * Time: 2:20 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Promotion\Override\MagentoSalesRule\Model;

class RulesApplier extends \Magento\SalesRule\Model\RulesApplier
{
    /**
     * @var \Magento\SalesRule\Model\Quote\ChildrenValidationLocator|null
     */
    protected $childrenValidationLocator;

    /**
     * @var array
     */
    protected $discountAggregator;

    /**
     * @var \SM\Promotion\Helper\Validation
     */
    protected $validationHelper;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $priceCurrency;
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * RulesApplier constructor.
     *
     * @param \Magento\Checkout\Model\Session                                 $checkoutSession
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface               $priceCurrency
     * @param \SM\Promotion\Helper\Validation                                 $validationHelper
     * @param \Magento\SalesRule\Model\Rule\Action\Discount\CalculatorFactory $calculatorFactory
     * @param \Magento\Framework\Event\ManagerInterface                       $eventManager
     * @param \Magento\SalesRule\Model\Utility                                $utility
     * @param \Magento\SalesRule\Model\Quote\ChildrenValidationLocator        $childrenValidationLocator
     * @param \Magento\SalesRule\Model\Rule\Action\Discount\DataFactory|null  $discountDataFactory
     * @param \Magento\SalesRule\Api\Data\RuleDiscountInterfaceFactory|null   $discountInterfaceFactory
     * @param \Magento\SalesRule\Api\Data\DiscountDataInterfaceFactory|null   $discountDataInterfaceFactory
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \SM\Promotion\Helper\Validation $validationHelper,
        \Magento\SalesRule\Model\Rule\Action\Discount\CalculatorFactory $calculatorFactory,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\SalesRule\Model\Utility $utility,
        \Magento\SalesRule\Model\Quote\ChildrenValidationLocator $childrenValidationLocator,
        \Magento\SalesRule\Model\Rule\Action\Discount\DataFactory $discountDataFactory = null,
        \Magento\SalesRule\Api\Data\RuleDiscountInterfaceFactory $discountInterfaceFactory = null,
        \Magento\SalesRule\Api\Data\DiscountDataInterfaceFactory $discountDataInterfaceFactory = null
    ) {
        parent::__construct(
            $calculatorFactory,
            $eventManager,
            $utility,
            $childrenValidationLocator,
            $discountDataFactory,
            $discountInterfaceFactory,
            $discountDataInterfaceFactory
        );
        $this->childrenValidationLocator = $childrenValidationLocator;
        $this->validationHelper = $validationHelper;
        $this->priceCurrency = $priceCurrency;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * Apply rules to current order item
     *
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem           $item
     * @param \Magento\SalesRule\Model\ResourceModel\Rule\Collection $rules
     * @param bool                                                   $skipValidation
     * @param mixed                                                  $couponCode
     *
     * @return array
     */
    public function applyRules($item, $rules, $skipValidation, $couponCode)
    {
        $address = $item->getAddress();
        $appliedRuleIds = [];
        $this->discountAggregator = [];
        $skipFlag = false;
        /* @var $rule \Magento\SalesRule\Model\Rule */
        foreach ($rules as $rule) {
            if (!$this->validatorUtility->canProcessRule($rule, $address) ||
                ($skipFlag && $rule->getSimpleAction() !== \Magento\SalesRule\Model\Rule::CART_FIXED_ACTION)
            ) {
                continue;
            }

            if (!$skipValidation &&
                !($this->validationHelper->validateBuyXY($rule, $address->getQuote()) &&
                    $this->validationHelper->validateProductSetByCart($rule, $address->getQuote())
                )
            ) {
                continue;
            }

            if (!$skipValidation && !$rule->getActions()->validate($item)) {
                if (!$this->childrenValidationLocator->isChildrenValidationRequired($item)) {
                    continue;
                }
                $childItems = $item->getChildren();
                $isContinue = true;
                if (!empty($childItems)) {
                    foreach ($childItems as $childItem) {
                        if ($rule->getActions()->validate($childItem)) {
                            $isContinue = false;
                        }
                    }
                }
                if ($isContinue) {
                    continue;
                }
            }

            $this->applyRule($item, $rule, $address, $couponCode);
            $appliedRuleIds[$rule->getRuleId()] = $rule->getRuleId();

            if ($rule->getStopRulesProcessing()) {
                $skipFlag = true;
            }
        }

        $this->distributeDiscount($item);

        return $appliedRuleIds;
    }

    /**
     * @param \Magento\SalesRule\Model\Rule\Action\Discount\Data $discountData
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem       $item
     *
     * @return $this
     */
    protected function setDiscountData($discountData, $item)
    {
        parent::setDiscountData($discountData, $item);
        if (!($item instanceof \Magento\Quote\Model\Quote\Item) && $item->getQuoteItem()) {
            parent::setDiscountData($discountData, $item->getQuoteItem());
        }

        return $this;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem $item
     */
    protected function distributeDiscount(\Magento\Quote\Model\Quote\Item\AbstractItem $item)
    {
        if ($item instanceof \Magento\Quote\Model\Quote\Item ||
            !$item->getQuoteItem() ||
            !$item->getChildren() ||
            !$item->isChildrenCalculated()
        ) {
            return;
        }

        $qItem = $item->getQuoteItem();
        $parentBaseRowTotal = $qItem->getBaseRowTotal();
        $keys = [
            'discount_amount',
            'base_discount_amount',
            'original_discount_amount',
            'base_original_discount_amount',
        ];
        $roundingDelta = [];
        foreach ($keys as $key) {
            //Initialize the rounding delta to a tiny number to avoid floating point precision problem
            $roundingDelta[$key] = 0.0000001;
        }
        foreach ($qItem->getChildren() as $child) {
            $ratio = $parentBaseRowTotal != 0 ? $child->getBaseRowTotal() / $parentBaseRowTotal : 0;
            foreach ($keys as $key) {
                if (!$qItem->hasData($key)) {
                    continue;
                }
                $value = $qItem->getData($key) * $ratio;
                $roundedValue = $this->priceCurrency->round($value + $roundingDelta[$key]);
                $roundingDelta[$key] += $value - $roundedValue;
                $child->setData($key, $roundedValue);
            }
        }

        foreach ($keys as $key) {
            $qItem->setData($key, 0);
        }
    }

    /**
     * @override
     *
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem $item
     * @param \Magento\SalesRule\Model\Rule                $rule
     * @param \Magento\Quote\Model\Quote\Address           $address
     *
     * @return \Magento\SalesRule\Model\Rule\Action\Discount\Data
     */
    protected function getDiscountData($item, $rule, $address)
    {
        if (!$this->checkoutSession->getData('main_order') &&
            $this->checkoutSession->getData('is_multiple_shipping_addresses')
        ) {
            $discountData = $this->discountFactory->create();
            $discountData->setBaseAmount(0)
                ->setAmount(0)
                ->setBaseOriginalAmount(0)
                ->setOriginalAmount(0);

            return $discountData;
        }

        return parent::getDiscountData($item, $rule, $address);
    }
}
