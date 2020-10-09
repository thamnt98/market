<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Promotion
 *
 * Date: June, 25 2020
 * Time: 1:30 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Promotion\Model\MagentoSalesRule\Rule\Action\Discount;

class CartFixed extends \Magento\SalesRule\Model\Rule\Action\Discount\CartFixed
{
    /**
     * @var string
     */
    protected static $discountType = 'CartFixed';

    /**
     * @var \Magento\SalesRule\Model\DeltaPriceRound
     */
    protected $deltaPriceRound;

    /**
     * CartFixed constructor.
     *
     * @param \Magento\SalesRule\Model\Validator                        $validator
     * @param \Magento\SalesRule\Model\Rule\Action\Discount\DataFactory $discountDataFactory
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface         $priceCurrency
     * @param \Magento\SalesRule\Model\DeltaPriceRound                  $deltaPriceRound
     */
    public function __construct(
        \Magento\SalesRule\Model\Validator $validator,
        \Magento\SalesRule\Model\Rule\Action\Discount\DataFactory $discountDataFactory,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\SalesRule\Model\DeltaPriceRound $deltaPriceRound
    ) {
        parent::__construct($validator, $discountDataFactory, $priceCurrency, $deltaPriceRound);
        $this->deltaPriceRound = $deltaPriceRound;
    }

    /**
     * @param \Magento\SalesRule\Model\Rule                $rule
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem $item
     * @param float                                        $qty
     *
     * @return \Magento\SalesRule\Model\Rule\Action\Discount\Data
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function calculate($rule, $item, $qty)
    {
        /** @var \Magento\SalesRule\Model\Rule\Action\Discount\Data $discountData */
        $discountData = $this->discountFactory->create();

        $ruleTotals = $this->validator->getRuleItemTotalsInfo($rule->getId());

        $quote = $item->getQuote();
        $address = $item->getAddress();

        $itemPrice = $this->validator->getItemPrice($item);
        $baseItemPrice = $this->validator->getItemBasePrice($item);
        $itemOriginalPrice = $this->validator->getItemOriginalPrice($item);
        $baseItemOriginalPrice = $this->validator->getItemBaseOriginalPrice($item);

        /**
         * prevent applying whole cart discount for every shipping order, but only for first order
         */
        if ($quote->getIsMultiShipping()) {
            $usedForAddressId = $this->getCartFixedRuleUsedForAddress($rule->getId());
            if ($usedForAddressId && $usedForAddressId != $address->getId()) {
                return $discountData;
            } else {
                $this->setCartFixedRuleUsedForAddress($rule->getId(), $address->getId());
            }
        }
        $cartRules = $address->getCartFixedRules();
        if (empty($cartRules[$rule->getId()])) {
            $cartRules[$rule->getId()] = $rule->getDiscountAmount();
        }

        $availableDiscountAmount = (float)$cartRules[$rule->getId()];
        $discountType = self::$discountType . $rule->getId();

        if ($availableDiscountAmount > 0) {
            $store = $quote->getStore();
            if ($ruleTotals['items_count'] <= 1) {
                $quoteAmount = $this->priceCurrency->convert($availableDiscountAmount, $store);
                $baseDiscountAmount = min($baseItemPrice * $qty, $availableDiscountAmount);
                $this->deltaPriceRound->reset($discountType);
            } else {
                $ratio = $baseItemPrice * $qty / $ruleTotals['base_items_price'];
                $maximumItemDiscount = $this->deltaPriceRound->round(
                    $rule->getDiscountAmount() * $ratio,
                    $discountType
                );

                $quoteAmount = $this->priceCurrency->convert($maximumItemDiscount, $store);

                $baseDiscountAmount = min($baseItemPrice * $qty, $maximumItemDiscount);
                $this->validator->decrementRuleItemTotalsCount($rule->getId());
            }

            $baseDiscountAmount = $this->priceCurrency->round($baseDiscountAmount);

            $availableDiscountAmount -= $baseDiscountAmount;
            $cartRules[$rule->getId()] = $availableDiscountAmount;
            if ($availableDiscountAmount <= 0) {
                $this->deltaPriceRound->reset($discountType);
            }

            $discountData->setAmount($this->priceCurrency->round(min($itemPrice * $qty, $quoteAmount)));
            $discountData->setBaseAmount($baseDiscountAmount);
            $discountData->setOriginalAmount(min($itemOriginalPrice * $qty, $quoteAmount));
            $discountData->setBaseOriginalAmount($this->priceCurrency->round($baseItemOriginalPrice));
        }

        $address->setCartFixedRules($cartRules);

        return $discountData;
    }
}
