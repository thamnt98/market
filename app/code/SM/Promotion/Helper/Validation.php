<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Promotion
 *
 * Date: July, 15 2020
 * Time: 2:51 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Promotion\Helper;

class Validation extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \SM\Catalog\Helper\Data
     */
    protected $catalogHelper;

    /**
     * @var \Amasty\Rules\Model\RuleResolver
     */
    protected $ruleResolver;

    /**
     * @var \Magento\Customer\Model\Customer
     */
    protected $customer;

    /**
     * @var \SM\FlashSale\Model\Customer\Calculation
     */
    protected $flashSaleCalculation;

    /**
     * Validation constructor.
     *
     * @param \SM\FlashSale\Model\Customer\Calculation   $flashSaleCalculation
     * @param \Amasty\Rules\Model\RuleResolver           $ruleResolver
     * @param \SM\Catalog\Helper\Data                    $catalogHelper
     * @param \Magento\Checkout\Model\Session            $checkoutSession
     * @param \Magento\Customer\Model\Session            $customerSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Helper\Context      $context
     * @param \Magento\Customer\Model\Customer           $customer
     */
    public function __construct(
        \SM\FlashSale\Model\Customer\Calculation $flashSaleCalculation,
        \Amasty\Rules\Model\RuleResolver $ruleResolver,
        \SM\Catalog\Helper\Data $catalogHelper,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Customer\Model\Customer $customer
    ) {
        parent::__construct($context);
        $this->customerSession = $customerSession;
        $this->storeManager = $storeManager;
        $this->checkoutSession = $checkoutSession;
        $this->catalogHelper = $catalogHelper;
        $this->ruleResolver = $ruleResolver;
        $this->customer = $customer;
        $this->flashSaleCalculation = $flashSaleCalculation;
    }

    /**
     * @param \Magento\SalesRule\Model\Rule         $rule
     *
     * @param \Magento\Customer\Model\Customer|null $customer
     *
     * @return bool
     */
    public function validateCustomer($rule, $customer = null)
    {
        $conditions = $rule->getConditions()->getConditions() ?? [];

        $ruleWebsites = $rule->getWebsiteIds();
        if (is_string($ruleWebsites)) {
            $ruleWebsites = explode(',', $ruleWebsites);
        }

        try {
            if (!in_array($this->customerSession->getCustomerGroupId(), $rule->getCustomerGroupIds()) ||
                !in_array($this->storeManager->getWebsite()->getId(), $ruleWebsites)
            ) {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }

        if (!$customer) {
            $customer = $this->customerSession->getCustomer();
            $customer->setData('id', $this->customerSession->getCustomerId())
                ->setData('entity_id', $this->customerSession->getCustomerId());
        }

        foreach ($conditions as $condition) {
            try {
                if ($condition instanceof \Amasty\Conditions\Model\Rule\Condition\CustomerAttributes &&
                    !$condition->validate($customer)
                ) {
                    return false;
                }
            } catch (\Exception $e) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param $rule
     * @param $customerId
     *
     * @return bool
     */
    public function validateApiCustomer($rule, $customerId)
    {
        $conditions = $rule->getConditions()->getConditions() ?? [];

        $customer = $this->customer->load($customerId);

        $ruleWebsites = $rule->getWebsiteIds();
        if (is_string($ruleWebsites)) {
            $ruleWebsites = explode(',', $ruleWebsites);
        }

        try {
            if (!in_array($customer->getGroupId(), $rule->getCustomerGroupIds()) ||
                !in_array($this->storeManager->getWebsite()->getId(), $ruleWebsites)
            ) {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }

        $customer->setData('id', $customer->getId())
            ->setData('entity_id', $customer->getId());
        foreach ($conditions as $condition) {
            try {
                if ($condition instanceof \Amasty\Conditions\Model\Rule\Condition\CustomerAttributes &&
                    !$condition->validate($customer)
                ) {
                    return false;
                }
            } catch (\Exception $e) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param \Magento\SalesRule\Model\Rule  $rule
     *
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return bool
     */
    public function validateProduct($rule, $product)
    {
        $conditions = $rule->getConditions()->getConditions();
        $actions = $rule->getActions()->getData('actions') ?? [];
        $productSetTypeRules = [
            'setof_fixed_discount',
            \Amasty\Rules\Helper\Data::TYPE_SETOF_FIXED,
            \Amasty\Rules\Helper\Data::TYPE_SETOF_PERCENT,
        ];
        if (empty($conditions) && empty($actions) && !in_array($rule->getSimpleAction(), $productSetTypeRules)) {
            return false;
        }

        if (!$this->validateProductSetByItem($rule, $product)) {
            return false;
        }

        if (!$this->validateCustomer($rule)) {
            return false;
        }

        $product->setData('product', $product)->setData('product_id', $product);
        foreach ($actions as $action) {
            try {
                if ($action instanceof \Magento\AdvancedSalesRule\Model\Rule\Condition\Product &&
                    !$action->validate($product)
                ) {
                    return false;
                }
            } catch (\Exception $e) {
                return false;
            }
        }

        return true;
    }

    /**
     * Validate product when call by api
     *
     * @param $rule
     * @param $product
     * @param $customerId
     *
     * @return bool
     */
    public function validateApiProduct($rule, $product, $customerId)
    {
        $conditions = $rule->getConditions()->getConditions();
        $actions = $rule->getActions()->getData('actions') ?? [];
        $productSetTypeRules = [
            'setof_fixed_discount',
            \Amasty\Rules\Helper\Data::TYPE_SETOF_FIXED,
            \Amasty\Rules\Helper\Data::TYPE_SETOF_PERCENT,
        ];
        if (empty($conditions) && empty($actions) && !in_array($rule->getSimpleAction(), $productSetTypeRules)) {
            return false;
        }

        if (in_array($rule->getSimpleAction(), $productSetTypeRules)) {
            try {
                $amastyRule = $this->ruleResolver->getSpecialPromotions($rule);
            } catch (\Exception $e) {
                return false;
            }

            if (!$amastyRule || !$amastyRule->getData('promo_skus')) {
                return false;
            }

            $skus = explode(',', $amastyRule->getData('promo_skus'));
            if (!in_array($product->getSku(), $skus)) {
                return false;
            }
        }

        if (!$this->validateApiCustomer($rule, $customerId)) {
            return false;
        }

        $product->setData('product', $product)->setData('product_id', $product);
        foreach ($actions as $action) {
            try {
                if ($action instanceof \Magento\AdvancedSalesRule\Model\Rule\Condition\Product &&
                    !$action->validate($product)
                ) {
                    return false;
                }
            } catch (\Exception $e) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param \Magento\SalesRule\Model\Rule                $rule
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem $item
     *
     * @return bool
     */
    public function validateSpecial($rule, $item)
    {
        if (!$rule->getData('skip_special')) {
            return true;
        }

        if ($item->getParentItem()) {
            return false;
        }

        if ($this->flashSaleCalculation->getFlashSaleEvent($item->getProduct()) &&
            $this->flashSaleCalculation->getFlashSalePrice($item) >= $item->getProduct()->getPrice()
        ) {
            return true;
        }

        return !$this->catalogHelper->getDiscountPercent($item->getProduct());
    }

    /**
     * @param \Magento\SalesRule\Model\Rule  $rule
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return false
     */
    public function validateProductSetByItem($rule, $product)
    {
        $productSetTypeRules = [
            'setof_fixed_discount',
            \Amasty\Rules\Helper\Data::TYPE_SETOF_FIXED,
            \Amasty\Rules\Helper\Data::TYPE_SETOF_PERCENT,
        ];

        if (!in_array($rule->getSimpleAction(), $productSetTypeRules)) {
            return true;
        }

        if (in_array($product->getSku(), $this->getAmastyPromoSkus($rule))) {
            return true;
        }

        return false;
    }

    /**
     * @param \Magento\SalesRule\Model\Rule $rule
     * @param \Magento\Quote\Model\Quote    $quote
     *
     * @return false
     */
    public function validateProductSetByCart($rule, $quote)
    {
        $productSetTypeRules = [
            'setof_fixed_discount',
            \Amasty\Rules\Helper\Data::TYPE_SETOF_FIXED,
            \Amasty\Rules\Helper\Data::TYPE_SETOF_PERCENT,
        ];

        if (!in_array($rule->getSimpleAction(), $productSetTypeRules)) {
            return true;
        }

        $availableSkus = $this->getAmastyPromoSkus($rule);
        if (empty($availableSkus)) {
            return false;
        }

        $quoteSkus = [];
        foreach ($quote->getAllVisibleItems() as $item) {
            $quoteSkus[] = $item->getProduct()->getSku();
        }

        if (count(array_diff($availableSkus, $quoteSkus))) {
            return false;
        }

        return true;
    }

    /**
     * @param \Magento\SalesRule\Model\Rule $rule
     * @param \Magento\Quote\Model\Quote    $quote
     *
     * @return false
     */
    public function validateBuyXY($rule, $quote)
    {
        if (!in_array($rule->getSimpleAction(), \Amasty\Rules\Helper\Data::BUY_X_GET_Y)) {
            return true;
        }

        $availableSkus = $this->getAmastyPromoSkus($rule);
        if (empty($availableSkus)) {
            return false;
        }

        foreach ($quote->getAllVisibleItems() as $item) {
            if (in_array($item->getProduct()->getSku(), $availableSkus)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param \Magento\SalesRule\Model\Rule $rule
     *
     * @return string[]
     */
    public function getAmastyPromoSkus($rule)
    {
        try {
            $amastyRule = $this->ruleResolver->getSpecialPromotions($rule);
        } catch (\Exception $e) {
            return [];
        }

        if (!$amastyRule || !$amastyRule->getData('promo_skus')) {
            return [];
        }

        return explode(',', $amastyRule->getData('promo_skus') ?? '');
    }
}
