<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rules
 */


namespace SM\Promotion\Model\Rule\Action\Discount;

use Amasty\Rules\Helper\Data;
use Magento\SalesRule\Model\Rule as RuleModel;
use Magento\SalesRule\Model\Rule\Action\Discount;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Amasty Rules calculation by action.
 * @see \SM\Promotion\Model\Data\Rule::TYPE_SETOF_FIXED_DISCOUNT
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class SetofFixedPrice extends \Amasty\Rules\Model\Rule\Action\Discount\AbstractSetof
{
    /**
     * @var Magento\SalesRule\Model\DeltaPriceRound
     */
    private $deltaPriceRound;

    /**
     * SetofFixedPrice constructor.
     * @param \Magento\SalesRule\Model\Validator $validator
     * @param Discount\DataFactory $discountDataFactory
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param StoreManagerInterface $storeManager
     * @param \Amasty\Rules\Helper\Product $rulesProductHelper
     * @param Data $rulesDataHelper
     * @param \Amasty\Rules\Helper\Discount $rulesDiscountHelper
     * @param \Magento\Framework\Session\SessionManager $customerSession
     * @param \Amasty\Rules\Model\ConfigModel $configModel
     * @param \Amasty\Rules\Model\RuleResolver $ruleResolver
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoriesCollection
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollections
     * @param Magento\SalesRule\Model\DeltaPriceRound $deltaPriceRound
     */
    public function __construct(
        \Magento\SalesRule\Model\Validator $validator,
        Discount\DataFactory $discountDataFactory,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        StoreManagerInterface $storeManager,
        \Amasty\Rules\Helper\Product $rulesProductHelper,
        Data $rulesDataHelper,
        \Amasty\Rules\Helper\Discount $rulesDiscountHelper,
        \Magento\Framework\Session\SessionManager $customerSession,
        \Amasty\Rules\Model\ConfigModel $configModel,
        \Amasty\Rules\Model\RuleResolver $ruleResolver,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoriesCollection,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollections,
        \Magento\SalesRule\Model\DeltaPriceRound $deltaPriceRound
    ) {
        parent::__construct($validator, $discountDataFactory, $priceCurrency, $storeManager, $rulesProductHelper,
            $rulesDataHelper, $rulesDiscountHelper, $customerSession, $configModel, $ruleResolver,
            $categoriesCollection, $productCollections);
        $this->deltaPriceRound = $deltaPriceRound;
    }

    /**
     * @param RuleModel $rule
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem $item
     *
     * @return $this
     */
    protected function calculateDiscountForRule($rule, $item)
    {
        list($setQty, $itemsForSet) = $this->prepareDataForCalculation($rule);

        if (!$itemsForSet) {
            return $this;
        }

        $this->calculateDiscountForItems($rule, $itemsForSet, $setQty);

        foreach ($itemsForSet as $i => $item) {
            unset(self::$allItems[$i]);
        }

        return $this;
    }

    /**
     * @param RuleModel $rule
     * @param array $itemsForSet
     * @param $setQty
     * @return void
     *
     * @throws \Exception
     */
    private function calculateDiscountForItems($rule, $itemsForSet, $setQty)
    {
        $ruleId = $this->getRuleId($rule);

        $total = $this->getSetTotal($itemsForSet);
        $discountType = \SM\Promotion\Model\Data\Rule::TYPE_SETOF_FIXED_DISCOUNT . $ruleId;
        foreach ($itemsForSet as $item) {
            /** @var \Magento\SalesRule\Model\Rule\Action\Discount\Data $discountData */
            $discountData = $this->discountFactory->create();
            $baseItemPrice = $this->rulesProductHelper->getItemBasePrice($item);
            $baseItemOriginalPrice = $this->rulesProductHelper->getItemBaseOriginalPrice($item);
            $ratio = $baseItemPrice * $item->getQty() / $total;
            $discountAmount = $rule->getDiscountAmount();
            if ($setQty > 1) {
                $discountAmount = $discountAmount * $setQty;
            }

            $maximumItemDiscount = $this->deltaPriceRound->round(
                $discountAmount * $ratio,
                $discountType);

            $baseDiscount = min($baseItemPrice, $maximumItemDiscount);
            $itemDiscount = $this->priceCurrency->convert($baseDiscount, $item->getQuote()->getStore());
            $baseOriginalDiscount = min($baseItemOriginalPrice, $maximumItemDiscount);
            $originalDiscount = ($baseItemOriginalPrice / $baseItemPrice) *
                $this->priceCurrency->convert($baseOriginalDiscount, $item->getQuote()->getStore());

//            $this->validator->decrementRuleItemTotalsCount($rule->getId());
            if (!isset(self::$cachedDiscount[$ruleId][$item->getProductId()])) {
                $discountData->setAmount($itemDiscount);
                $discountData->setBaseAmount($baseDiscount);
                $discountData->setOriginalAmount($originalDiscount);
                $discountData->setBaseOriginalAmount($baseOriginalDiscount);
            } else {
                /** @var \Magento\SalesRule\Model\Rule\Action\Discount\Data $cachedItem */
                $cachedItem = self::$cachedDiscount[$ruleId][$item->getProductId()];
                $discountData->setAmount($itemDiscount + $cachedItem->getAmount());
                $discountData->setBaseAmount($baseDiscount + $cachedItem->getBaseAmount());
                $discountData->setOriginalAmount($originalDiscount + $cachedItem->getOriginalAmount());
                $discountData->setBaseOriginalAmount($baseOriginalDiscount + $cachedItem->getBaseOriginalAmount());
            }

            self::$cachedDiscount[$ruleId][$item->getProductId()] = $discountData;
        }
    }

    private function getSetTotal($itemsForSet)
    {
        $total = 0;
        foreach ($itemsForSet as $item) {
            $total += $item->getQty() * $baseItemPrice = $this->rulesProductHelper->getItemBasePrice($item);
        }
        return $total;
    }

    /**
     * Calculate discount for rule once. Cached values is returned the next times
     *
     * @param \Magento\SalesRule\Model\Rule $rule
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem $item
     *
     * @return \Magento\SalesRule\Model\Rule\Action\Discount\Data
     *
     * @throws \Exception
     */
    protected function calculateDiscount($rule, $item)
    {
        $ruleId = $this->getRuleId($rule);

        if (!array_key_exists($ruleId, self::$cachedDiscount)) {
            $this->calculateDiscountForRule($rule, $item);
        }

        return isset(self::$cachedDiscount[$ruleId][$item->getProductId()])
            ? clone self::$cachedDiscount[$ruleId][$item->getProductId()]
            : $this->discountFactory->create();
    }
}
