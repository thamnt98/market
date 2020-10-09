<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_MyVoucher
 *
 * Date: June, 20 2020
 * Time: 4:06 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\MyVoucher\Model\AmastyRules;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class DiscountRegistry extends \Amasty\Rules\Model\DiscountRegistry
{
    const DISCOUNT_REGISTRY_FREE_SHIP_RULES = 'discount_registry_free_ship_rules';

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Magento\Framework\App\Request\DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var \Amasty\Rules\Model\DiscountBreakdownLineFactory
     */
    protected $breakdownLineFactory;

    /**
     * @var \Magento\SalesRule\Model\Utility
     */
    protected $validatorUtility;

    /**
     * @var \Magento\SalesRule\Model\RuleFactory
     */
    protected $ruleFactory;

    /**
     * @var \Magento\SalesRule\Model\Rule[]
     */
    protected $rules = [];

    /**
     * @var array
     */
    protected $shippingDiscountDataForBreakdown = [];

    /**
     * @var array
     */
    protected $shippingAddressDiscount = [];

    /**
     * @var array
     */
    protected $ruleFreeShip = [];

    /**
     * DiscountRegistry constructor.
     *
     * @param \Magento\SalesRule\Model\RuleFactory                  $ruleFactory
     * @param \Magento\SalesRule\Model\Utility                      $validatorUtility
     * @param \Magento\Store\Model\StoreManagerInterface            $storeManager
     * @param \Magento\SalesRule\Api\RuleRepositoryInterface        $ruleRepository
     * @param \Psr\Log\LoggerInterface                              $logger
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     * @param \Amasty\Rules\Model\DiscountBreakdownLineFactory      $breakdownLineFactory
     */
    public function __construct(
        \Magento\SalesRule\Model\RuleFactory $ruleFactory,
        \Magento\SalesRule\Model\Utility $validatorUtility,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\SalesRule\Api\RuleRepositoryInterface $ruleRepository,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        \Amasty\Rules\Model\DiscountBreakdownLineFactory $breakdownLineFactory
    ) {
        parent::__construct($storeManager, $ruleRepository, $logger, $dataPersistor, $breakdownLineFactory);
        $this->storeManager = $storeManager;
        $this->logger = $logger;
        $this->dataPersistor = $dataPersistor;
        $this->breakdownLineFactory = $breakdownLineFactory;
        $this->validatorUtility = $validatorUtility;
        $this->ruleFactory = $ruleFactory;
    }

    /**
     * @override
     * @return \Amasty\Rules\Api\Data\DiscountBreakdownLineInterface[]|array
     */
    public function getRulesWithAmount()
    {
        $totalAmount = [];
        $shippingDiscountDataForBreakdown = $this->getShippingDiscountDataForBreakdown();
        $discount = $this->getDiscount();
        foreach ($shippingDiscountDataForBreakdown as $ruleId => $amount) {
            if (!isset($discount[$ruleId])) {
                $discount[$ruleId] = [0];
            }
        }

        try {
            foreach ($discount as $ruleId => $ruleItemsAmount) {
                $rule = $this->getRule($ruleId);
                if (!$rule->getId()) {
                    continue;
                }

                $ruleAmount = array_sum($ruleItemsAmount);
                if (isset($shippingDiscountDataForBreakdown[$rule->getId()])) {
                    $ruleAmount += $shippingDiscountDataForBreakdown[$rule->getId()];
                }

                if ($ruleAmount > 0) {
                    /** @var \SM\Promotion\Api\Data\DiscountBreakdownInterface $breakdownLine */
                    $breakdownLine = $this->breakdownLineFactory->create();

                    $breakdownLine->setRuleName($rule->getStoreLabel() ? $rule->getStoreLabel() : $rule->getName());
                    $breakdownLine->setId($rule->getId());
                    $breakdownLine->setCode($rule->getCouponCode());
                    $breakdownLine->setRuleAmount($ruleAmount * -1);

                    $totalAmount[$ruleId] = $breakdownLine;
                }
            }

            $freeShipRule = array_diff($this->getFreeShipRules(), array_keys($totalAmount));
            foreach ($freeShipRule as $ruleId) {
                $rule = $this->getRule($ruleId);
                if (!$rule->getId() || !$rule->getIsActive()) {
                    continue;
                }

                /** @var \SM\Promotion\Api\Data\DiscountBreakdownInterface $breakdownLine */
                $breakdownLine = $this->breakdownLineFactory->create();
                $breakdownLine->setRuleName($rule->getStoreLabel() ? $rule->getStoreLabel() : $rule->getName());
                $breakdownLine->setId($rule->getId());
                $breakdownLine->setCode($rule->getCouponCode());
                $breakdownLine->setRuleAmount(0);

                $totalAmount[] = $breakdownLine;
            }
        } catch (NoSuchEntityException $entityException) {
            $this->logger->critical($entityException);
        } catch (LocalizedException $e) {
            $this->logger->critical($e);
        }

        return $totalAmount;
    }

    /**
     * @override
     * @param \Magento\SalesRule\Model\Rule\Action\Discount\Data $discountData
     * @param \Magento\SalesRule\Model\Rule                      $rule
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem          $item
     *
     * @return $this
     */
    public function setDiscount($discountData, $rule, $item)
    {
        if ($item instanceof \Magento\Quote\Model\Quote\Address\Item) {
            $item = $item->getQuoteItem();
        }

        if (!$item || !$item->getData('is_active')) {
            return $this;
        }

        /** @var \Magento\Quote\Model\Quote\Item $item */
        $item->setDiscountPercent((float)$item->getDiscountPercent());
        $this->validatorUtility->deltaRoundingFix($discountData, $item);

        return parent::setDiscount($discountData, $rule, $item);
    }

    /**
     * @override
     * @param \Magento\SalesRule\Model\Rule\Action\Discount\Data $discountData
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem       $item
     */
    public function fixDiscount($discountData, $item)
    {
        if ($item instanceof \Magento\Quote\Model\Quote\Address\Item) {
            $item = $item->getQuoteItem();
        }

        parent::fixDiscount($discountData, $item);
    }

    /**
     * @param $id
     *
     * @return \Magento\SalesRule\Model\Rule
     */
    protected function getRule($id)
    {
        if (!isset($this->rules[$id])) {
            $this->rules[$id] = $this->ruleFactory->create()->load($id);
        }

        return $this->rules[$id];
    }

    /**
     * @override
     * Calculate shipping discount amount for each sales rule
     *
     * @param string|int $ruleId
     * @param int|float  $shippingDiscountAmount
     * @param null       $addressId
     */
    public function setShippingDiscount($ruleId, $shippingDiscountAmount, $addressId = null)
    {
        if (is_null($addressId)) {
            return;
        }

        if (isset($this->shippingAddressDiscount[$addressId])) {
            if (isset($this->shippingAddressDiscount[$addressId][$ruleId])) {
                $this->shippingAddressDiscount[$addressId][$ruleId] = abs($shippingDiscountAmount);
            } else {
                $addressDiscount = array_sum($this->shippingAddressDiscount[$addressId]);
                $this->shippingAddressDiscount[$addressId][$ruleId] = abs($shippingDiscountAmount) - $addressDiscount;
            }
        } else {
            $this->shippingAddressDiscount[$addressId][$ruleId] = abs($shippingDiscountAmount);
        }

        $this->shippingDiscountDataForBreakdown = [];
        foreach ($this->shippingAddressDiscount as $addrDiscount) {
            foreach ($addrDiscount as $ruleId => $amount) {
                if (!isset($this->shippingDiscountDataForBreakdown[$ruleId])) {
                    $this->shippingDiscountDataForBreakdown[$ruleId] = 0;
                }

                $this->shippingDiscountDataForBreakdown[$ruleId] += $amount;
            }
        }

        $this->dataPersistor->set(self::DISCOUNT_REGISTRY_SHIPPING_DATA, $this->shippingDiscountDataForBreakdown);
    }

    /**
     * @override
     * @return bool
     */
    public function restoreDataForBreakdown()
    {
        $hasShippingDiscount = !empty($this->dataPersistor->get(self::DISCOUNT_REGISTRY_SHIPPING_DATA) ?: []);
        if (empty($this->getFreeShipRules())) {
            $this->setFreeShipRules($this->dataPersistor->get(self::DISCOUNT_REGISTRY_FREE_SHIP_RULES));
        }

        return parent::restoreDataForBreakdown() || $hasShippingDiscount;
    }

    /**
     * @param array $ruleIds
     *
     * @return $this
     */
    public function setFreeShipRules($ruleIds = [])
    {
        if (!is_array($ruleIds)) {
            $ruleIds = explode(',', $ruleIds);
        }

        $this->ruleFreeShip = array_unique(array_merge($this->ruleFreeShip, $ruleIds));
        $this->ruleFreeShip = array_filter($this->ruleFreeShip, function ($value) {
            return !empty($value);
        });
        $this->dataPersistor->set(self::DISCOUNT_REGISTRY_FREE_SHIP_RULES, $this->ruleFreeShip);

        return $this;
    }

    /**
     * @return array
     */
    public function getFreeShipRules()
    {
        return $this->ruleFreeShip;
    }

    /**
     * @override
     */
    public function unsetDataForBreakdown()
    {
        $this->dataPersistor->clear(self::DISCOUNT_REGISTRY_FREE_SHIP_RULES);
        parent::unsetDataForBreakdown();
    }
}
