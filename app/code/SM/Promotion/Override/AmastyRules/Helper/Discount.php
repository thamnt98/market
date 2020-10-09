<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Promotion
 *
 * Date: September, 17 2020
 * Time: 3:35 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Promotion\Override\AmastyRules\Helper;

class Discount extends \Amasty\Rules\Helper\Discount
{
    /**
     * @var array
     */
    public static $maxDiscount = [];

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var array
     */
    protected $processedData = [];

    /**
     * Discount constructor.
     *
     * @param \Magento\Framework\App\Helper\Context             $context
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
    ) {
        parent::__construct($context, $priceCurrency);

        $this->priceCurrency = $priceCurrency;
    }

    /**
     * @param \Magento\SalesRule\Model\Rule                      $rule
     * @param \Magento\SalesRule\Model\Rule\Action\Discount\Data $discountData
     * @param \Magento\Store\Model\Store                         $store
     *
     * @param int                                                $itemId
     *
     * @return \Magento\SalesRule\Model\Rule\Action\Discount\Data
     */
    public function setDiscount(
        \Magento\SalesRule\Model\Rule $rule,
        \Magento\SalesRule\Model\Rule\Action\Discount\Data $discountData,
        \Magento\Store\Model\Store $store,
        $itemId
    ) {
        if ($rule->getAmrulesRule()->getMaxDiscount() == 0) {
            return $discountData;
        }

        if (!empty($this->processedData[$rule->getId()][$itemId])) {
            $cache = $this->processedData[$rule->getId()][$itemId];
            $discountData->setBaseAmount($cache->getBaseAmount());
            $discountData->setAmount($cache->getAmount());
            $discountData->setBaseOriginalAmount($cache->getBaseOriginalAmount());
            $discountData->setOriginalAmount($cache->getOriginalAmount());

            return $discountData;
        }

        if (!isset(self::$maxDiscount[$rule->getId()]) || isset($this->processedData[$rule->getId()][$itemId])) {
            self::$maxDiscount[$rule->getId()] = $rule->getAmrulesRule()->getMaxDiscount();
            $this->processedData[$rule->getId()] = null;
        }

        if (self::$maxDiscount[$rule->getId()] - $discountData->getBaseAmount() < 0) {
            $convertedPrice = $this->priceCurrency->convert(self::$maxDiscount[$rule->getId()], $store);
            $discountData->setBaseAmount((float)self::$maxDiscount[$rule->getId()]);
            $discountData->setAmount($this->priceCurrency->round($convertedPrice));
            $discountData->setBaseOriginalAmount((float)self::$maxDiscount[$rule->getId()]);
            $discountData->setOriginalAmount($this->priceCurrency->round($convertedPrice));
            self::$maxDiscount[$rule->getId()] = 0;
        } else {
            self::$maxDiscount[$rule->getId()] =
                self::$maxDiscount[$rule->getId()] - $discountData->getBaseAmount();
        }

        $this->processedData[$rule->getId()][$itemId] = clone $discountData;

        return $discountData;
    }
}
