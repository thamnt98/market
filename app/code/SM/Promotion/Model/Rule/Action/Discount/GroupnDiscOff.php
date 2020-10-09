<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rules
 */


namespace SM\Promotion\Model\Rule\Action\Discount;

use Magento\Quote\Model\Quote\Item\AbstractItem;
use Magento\SalesRule\Model\Rule;
use Magento\SalesRule\Model\Rule\Action\Discount;
use Magento\SalesRule\Model\Rule\Action\Discount\Data;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Amasty Rules calculation by action.
 *
 * @see \Amasty\Rules\Helper\Data::TYPE_GROUP_N
 */
class GroupnDiscOff extends \Amasty\Rules\Model\Rule\Action\Discount\AbstractRule
{
    const RULE_VERSION = '1.0.0';

    const DEFAULT_SORT_ORDER = 'asc';

    public static $cachedDiscount = [];
    /**
     * @var \Magento\SalesRule\Model\DeltaPriceRound
     */
    private $deltaPriceRound;

    /**
     * GroupnDiscOff constructor.
     * @param \Magento\SalesRule\Model\Validator $validator
     * @param Discount\DataFactory $discountDataFactory
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param StoreManagerInterface $storeManager
     * @param \Amasty\Rules\Helper\Product $rulesProductHelper
     * @param \Amasty\Rules\Helper\Data $rulesDataHelper
     * @param \Amasty\Rules\Helper\Discount $rulesDiscountHelper
     * @param \Magento\Framework\Session\SessionManager $customerSession
     * @param \Amasty\Rules\Model\ConfigModel $configModel
     * @param \Amasty\Rules\Model\RuleResolver $ruleResolver
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoriesCollection
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollections
     * @param \Magento\SalesRule\Model\DeltaPriceRound $deltaPriceRound
     */
    public function __construct(
        \Magento\SalesRule\Model\Validator $validator,
        Discount\DataFactory $discountDataFactory,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        StoreManagerInterface $storeManager,
        \Amasty\Rules\Helper\Product $rulesProductHelper,
        \Amasty\Rules\Helper\Data $rulesDataHelper,
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
     * @param Rule $rule
     * @param AbstractItem $item
     * @param float $qty
     *
     * @return Data
     *
     * @throws \Exception
     */
    public function calculate($rule, $item, $qty)
    {
        $this->beforeCalculate($rule);
        $discountData = $this->calculateDiscount($rule, $item);
        $this->afterCalculate($discountData, $rule, $item);

        return $discountData;
    }

    /**
     * @param Rule $rule
     * @param AbstractItem $item
     *
     * @return Data
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function calculateDiscount($rule, $item)
    {
        $ruleId = $this->getRuleId($rule);

        if (!array_key_exists($ruleId, self::$cachedDiscount)) {
            $this->calculateDiscountForRule($item, $rule);
        }

        $discountData = isset(self::$cachedDiscount[$ruleId][$item->getId()])
            ? self::$cachedDiscount[$ruleId][$item->getId()]
            : $this->discountFactory->create();

        return $discountData;
    }

    /**
     * @param AbstractItem $item
     * @param Rule $rule
     *
     * @return $this
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function calculateDiscountForRule($item, $rule)
    {
        $allItems = $this->getSortedItems(
            $item->getAddress(),
            $rule,
            $this->getSortOrder($rule, self::DEFAULT_SORT_ORDER)
        );

        $totalPrice = $this->getItemsPrice($allItems);

        if ($totalPrice < $rule->getDiscountAmount()) {
            return $this;
        }

        $this->calculateDiscountForEachGroup($rule, $allItems);

        return $this;
    }

    /**
     * @param Rule  $rule
     * @param array $allItems
     *
     * @throws \Exception
     */
    protected function calculateDiscountForEachGroup($rule, $allItems)
    {
        $step = (int)$rule->getDiscountStep() == 0 ? 1 : (int)$rule->getDiscountStep();
        $qty = $this->ruleQuantity(count($allItems), $rule);

        if (!$this->hasDiscountItems($allItems, $qty)) {
            return;
        }

        while (count($allItems) >= $step && $qty > 0) {
            $groupItems = array_slice($allItems, 0, $step);
            $groupItemsPrice = $this->getItemsPrice($groupItems);

            if ($groupItemsPrice < $rule->getDiscountAmount()) {
                $firstItem = array_shift($allItems);
                unset($firstItem);
            } else {
                $this->calculateDiscountForItems($groupItemsPrice, $rule, $groupItems, $rule->getDiscountAmount());
                $count = 0;

                foreach ($allItems as $i => $item) {
                    if ($count >= $step) {
                        break;
                    }

                    unset($allItems[$i]);
                    $count++;
                }
            }

            $qty--;
        }
    }

    /**
     * @param float $totalPrice
     * @param Rule $rule
     * @param AbstractItem[] $itemsForSet
     *
     * @param float $discountAmount
     *
     * @throws \Exception
     */
    protected function calculateDiscountForItems($totalPrice, $rule, $itemsForSet, $discountAmount)
    {
        $ruleId = $this->getRuleId($rule);

        $discountType = \SM\Promotion\Model\Data\Rule::TYPE_EACH_GROUP_N_DISC_OFF . $ruleId;
        foreach ($itemsForSet as $item) {
            $discountData = $this->discountFactory->create();

            $baseItemPrice = $this->rulesProductHelper->getItemBasePrice($item);
            $baseItemOriginalPrice = $this->rulesProductHelper->getItemBaseOriginalPrice($item);

            $percentage = $baseItemPrice / $totalPrice;
            $baseDiscount = $this->deltaPriceRound->round($discountAmount * $percentage, $discountType);
            $itemDiscount = $this->priceCurrency->convert($baseDiscount, $item->getQuote()->getStore());
            $baseOriginalDiscount = $baseItemOriginalPrice - $discountAmount * $percentage;
            $originalDiscount = ($baseItemOriginalPrice / $baseItemPrice) *
                $this->priceCurrency->convert($baseOriginalDiscount, $item->getQuote()->getStore());

            if (!isset(self::$cachedDiscount[$ruleId][$item->getId()])) {
                $discountData->setAmount($itemDiscount);
                $discountData->setBaseAmount($baseDiscount);
                $discountData->setOriginalAmount($originalDiscount);
                $discountData->setBaseOriginalAmount($baseOriginalDiscount);
            } else {
                $cachedItem = self::$cachedDiscount[$ruleId][$item->getId()];
                $discountData->setAmount($itemDiscount + $cachedItem->getAmount());
                $discountData->setBaseAmount($baseDiscount + $cachedItem->getBaseAmount());
                $discountData->setOriginalAmount($originalDiscount + $cachedItem->getOriginalAmount());
                $discountData->setBaseOriginalAmount($baseOriginalDiscount + $cachedItem->getBaseOriginalAmount());
            }

            self::$cachedDiscount[$ruleId][$item->getId()] = $discountData;
        }
    }

    /**
     * @param $items
     *
     * @return float|int
     */
    protected function getItemsPrice($items)
    {
        $totalPrice = 0;
        foreach ($items as $item) {
            $totalPrice += $this->validator->getItemBasePrice($item);
        }

        return $totalPrice;
    }
}
