<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Promotion
 *
 * Date: August, 13 2020
 * Time: 6:04 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */
declare(strict_types = 1);

namespace SM\Promotion\Override\MagentoSalesRule\Model\Coupon;

use  Magento\Sales\Api\Data\OrderInterface;
use Magento\SalesRule\Model\Coupon\Usage\Processor as CouponUsageProcessor;
use Magento\SalesRule\Model\Coupon\Usage\UpdateInfoFactory;

class UpdateCouponUsages extends \Magento\SalesRule\Model\Coupon\UpdateCouponUsages
{
    const DISCOUNT_BREAKDOWN_ITEM          = 'item';
    const DISCOUNT_BREAKDOWN_SHIPPING      = 'shipping';
    const DISCOUNT_BREAKDOWN_FREE_SHIPPING = 'free_shipping';

    /**
     * @var \Magento\SalesRule\Model\RuleFactory
     */
    protected $ruleFactory;

    /**
     * @var \Magento\SalesRule\Model\Rule\CustomerFactory
     */
    protected $ruleCustomerFactory;

    /**
     * @var \Magento\SalesRule\Model\Coupon
     */
    protected $coupon;

    /**
     * @var \Magento\SalesRule\Model\ResourceModel\Coupon\Usage
     */
    protected $couponUsage;

    /**
     * @var \Amasty\RulesPro\Api\RuleUsageRepositoryInterface
     */
    protected $amastyRuleUsageRepository;

    /**
     * @var array
     */
    protected $discountBreakdown = [
        self::DISCOUNT_BREAKDOWN_ITEM          => [],
        self::DISCOUNT_BREAKDOWN_SHIPPING      => [],
        self::DISCOUNT_BREAKDOWN_FREE_SHIPPING => [],
    ];
    /**
     * @var \Magento\SalesRule\Model\ResourceModel\Coupon\CollectionFactory
     */
    protected $couponCollectionFactory;
    /**
     * @var \Amasty\RulesPro\Model\ResourceModel\RuleUsageCounter
     */
    protected $usageCounter;

    /**
     * @param \Amasty\RulesPro\Api\RuleUsageRepositoryInterface $amastyRuleUsageRepository
     * @param \Amasty\Rules\Model\DiscountRegistry $discountRegistry
     * @param \Magento\SalesRule\Model\RuleFactory $ruleFactory
     * @param \Magento\SalesRule\Model\Rule\CustomerFactory $ruleCustomerFactory
     * @param \Magento\SalesRule\Model\Coupon $coupon
     * @param \Magento\SalesRule\Model\ResourceModel\Coupon\Usage $couponUsage
     * @param \Magento\SalesRule\Model\ResourceModel\Coupon\CollectionFactory $couponCollectionFactory
     * @param \Amasty\RulesPro\Model\ResourceModel\RuleUsageCounter $usageCounter
     */
    public function __construct(
        CouponUsageProcessor $couponUsageProcessor,
        UpdateInfoFactory $updateInfoFactory,
        \Amasty\RulesPro\Api\RuleUsageRepositoryInterface $amastyRuleUsageRepository,
        \Amasty\Rules\Model\DiscountRegistry $discountRegistry,
        \Magento\SalesRule\Model\RuleFactory $ruleFactory,
        \Magento\SalesRule\Model\Rule\CustomerFactory $ruleCustomerFactory,
        \Magento\SalesRule\Model\Coupon $coupon,
        \Magento\SalesRule\Model\ResourceModel\Coupon\Usage $couponUsage,
        \Magento\SalesRule\Model\ResourceModel\Coupon\CollectionFactory $couponCollectionFactory,
        \Amasty\RulesPro\Model\ResourceModel\RuleUsageCounter $usageCounter
    ) {
        parent::__construct($couponUsageProcessor, $updateInfoFactory);

        $this->ruleFactory = $ruleFactory;
        $this->ruleCustomerFactory = $ruleCustomerFactory;
        $this->coupon = $coupon;
        $this->couponUsage = $couponUsage;
        $this->initDiscount($discountRegistry);
        $this->amastyRuleUsageRepository = $amastyRuleUsageRepository;
        $this->couponCollectionFactory = $couponCollectionFactory;
        $this->usageCounter = $usageCounter;
    }

    /**
     * @param \SM\MyVoucher\Model\AmastyRules\DiscountRegistry $discountRegistry
     */
    protected function initDiscount($discountRegistry)
    : void {
        if (!$discountRegistry->restoreDataForBreakdown()) {
            return;
        }

        foreach ($discountRegistry->getDiscount() as $ruleId => $amountData) {
            if ($amount = array_sum($amountData)) {
                $this->discountBreakdown[self::DISCOUNT_BREAKDOWN_ITEM][$ruleId] = $amount;
            }
        }

        $this->discountBreakdown[self::DISCOUNT_BREAKDOWN_FREE_SHIPPING] = $discountRegistry->getFreeShipRules();
        $this->discountBreakdown[self::DISCOUNT_BREAKDOWN_SHIPPING] =
            $discountRegistry->getShippingDiscountDataForBreakdown();
    }

    /**
     * Executes the current command.
     *
     * @param OrderInterface $subject
     * @param bool $increment
     *
     * @return OrderInterface
     * @throws \Exception
     */
    public function execute(OrderInterface $subject, bool $increment)
    : OrderInterface {
        if (!$subject || !$subject->getAppliedRuleIds()) {
            return $subject;
        }
        // lookup rule ids
        $ruleIds = explode(',', $subject->getAppliedRuleIds());
        $ruleIds = array_unique($ruleIds);
        $customerId = (int)$subject->getCustomerId();
        // use each rule (and apply to customer, if applicable)
        foreach ($ruleIds as $ruleId) {
            if (!$ruleId) {
                continue;
            }

            $this->updateRuleUsages($increment, (int)$ruleId, $customerId, $subject);
        }

        $this->updateCouponUsages($subject, $increment, $customerId);

        return $subject;
    }

    /**
     * Update the number of rule usages.
     *
     * @param bool $increment
     * @param int $ruleId
     * @param int $customerId
     * @param OrderInterface $order
     *
     * @throws \Exception
     */
    protected function updateRuleUsages(bool $increment, int $ruleId, int $customerId, OrderInterface $order)
    {
        /** @var \Magento\SalesRule\Model\Rule $rule */
        $rule = $this->ruleFactory->create();
        $rule->load($ruleId);

        if (!$rule->getId() || !$this->checkMainSubOrder($order, $ruleId)) {
            return;
        }

        $rule->loadCouponCode();

        if ($increment || $rule->getTimesUsed() > 0) {
            $rule->setTimesUsed($rule->getTimesUsed() + ($increment ? 1 : -1));
            $rule->save();
            if($increment) {
                $this->amastyRuleUsageRepository->incrementUsageCountByRuleIds([$ruleId]);
            } else {
                $this->decrementAmastyUsageCount($ruleId);
            }
        }

        if ($customerId) {
            $this->updateCustomerRuleUsages($increment, $ruleId, $customerId);
        }
    }

    /**
     * Update the number of rule usages per customer.
     *
     * @param bool $increment
     * @param int  $ruleId
     * @param int  $customerId
     *
     * @throws \Exception
     */
    protected function updateCustomerRuleUsages(bool $increment, int $ruleId, int $customerId)
    : void {
        /** @var \Magento\SalesRule\Model\Rule\Customer $ruleCustomer */
        $ruleCustomer = $this->ruleCustomerFactory->create();
        $ruleCustomer->loadByCustomerRule($customerId, $ruleId);

        if ($ruleCustomer->getId()) {
            if ($increment || $ruleCustomer->getTimesUsed() > 0) {
                $ruleCustomer->setTimesUsed($ruleCustomer->getTimesUsed() + ($increment ? 1 : -1));
            }
        } elseif ($increment) {
            $ruleCustomer->setCustomerId($customerId)->setRuleId($ruleId)->setTimesUsed(1);
        }

        $ruleCustomer->save();
    }

    /**
     * Update the number of coupon usages.
     *
     * @param OrderInterface $subject
     * @param bool $increment
     * @param int $customerId
     *
     */
    protected function updateCouponUsages(OrderInterface $subject, bool $increment, int $customerId)
    : void {
        if (empty($subject->getCouponCode())) {
            return;
        }
        $coupons = explode(',', $subject->getCouponCode());
        $collection = $this->couponCollectionFactory->create();
        $collection->addFieldToFilter('code', ['in' => $coupons]);

        foreach ($collection as $coupon) {
            if (!$coupon->getId() ||
                !$coupon->getRuleId() ||
                !$this->checkMainSubOrder($subject, (int) $coupon->getRuleId())
            ) {
                return;
            }

            if ($increment || $coupon->getTimesUsed() > 0) {
                $coupon->setTimesUsed($coupon->getTimesUsed() + ($increment ? 1 : -1));
                $coupon->save();
            }

            if ($customerId) {
                $this->couponUsage->updateCustomerCouponTimesUsed($customerId, $coupon->getId(), $increment);
            }
        }
    }

    /**
     * @param OrderInterface $order
     * @param int $ruleId
     *
     * @return bool
     */
    protected function checkMainSubOrder(OrderInterface $order, int $ruleId)
    : bool {
        $shippingRuleIds = $order->getData(\SM\Promotion\Model\Data\Rule::SHIPPING_RULE_IDS_FIELD);

        if (!is_array($shippingRuleIds)) {
            $shippingRuleIds = explode(',', is_null($shippingRuleIds) ? '' : $shippingRuleIds);
        }

        if (($order->getData('parent_order') && in_array($ruleId, $shippingRuleIds)) ||
            (!$order->getData('parent_order') && !in_array($ruleId, $shippingRuleIds))
        ) {
            return true;
        }

        return false;
    }

    protected function decrementAmastyUsageCount($ruleId) {
        $where = ['salesrule_id = (?)' => $ruleId];
        $this->usageCounter->getConnection()->update(
            $this->usageCounter->getTable(\Amasty\RulesPro\Model\ResourceModel\RuleUsageCounter::TABLE_NAME),
            ['count' => new \Zend_Db_Expr('count-1')],
            $where
        );
    }
}
