<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Promotion
 *
 * Date: August, 12 2020
 * Time: 11:32 AM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Promotion\Model\Rule\Validator;

class CustomerUses
{
    const CUSTOMER_RULE_REGISTRY_KEY = 'customer_rule_time_used';

    const UNLIMITED_VALUE = 'unlimited';

    /**
     * @var \Magento\SalesRule\Model\Rule\CustomerFactory
     */
    protected $customerRuleFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var array
     */
    protected $addressUsedRule = [];

    /**
     * @var array
     */
    protected $timeUses = [];

    /**
     * CustomerUses constructor.
     *
     * @param \Magento\Customer\Model\Session               $customerSession
     * @param \Magento\SalesRule\Model\Rule\CustomerFactory $customerRuleFactory
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Magento\SalesRule\Model\Rule\CustomerFactory $customerRuleFactory
    ) {
        $this->customerRuleFactory = $customerRuleFactory;
        $this->customerSession = $customerSession;
    }

    /**
     * @param \Magento\SalesRule\Model\Rule $rule
     *
     * @return int|string
     */
    public function getCustomerUseLeft($rule)
    {
        try {
            if (!isset($this->timeUses[$rule->getId()])) {
                $this->timeUses[$rule->getId()] = $this->getRuleTimeLeft($rule);
            }

            $result = $this->timeUses[$rule->getId()];
            if ($result === self::UNLIMITED_VALUE) {
                return self::UNLIMITED_VALUE;
            } elseif (isset($this->addressUsedRule[$rule->getId()])) {
                $result -= count($this->addressUsedRule[$rule->getId()]);
            }

            return $result;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * @param \Magento\SalesRule\Model\Rule      $rule
     * @param \Magento\Quote\Model\Quote\Address $address
     *
     * @return $this
     */
    public function setAddressRule($rule, $address)
    {
        if (!isset($this->addressUsedRule[$rule->getId()]) ||
            !in_array($address->getId(), $this->addressUsedRule[$rule->getId()])
        ) {
            $this->addressUsedRule[$rule->getId()][] = $address->getId();
        }

        return $this;
    }

    /**
     * @param \Magento\SalesRule\Model\Rule      $rule
     * @param \Magento\Quote\Model\Quote\Address $address
     *
     * @return $this
     */
    public function unsetAddressRule($rule, $address)
    {
        if (isset($this->addressUsedRule[$rule->getId()])) {
            $index = array_search($address->getId(), $this->addressUsedRule[$rule->getId()]);
            if ($index !== false && $index !== null) {
                unset($this->addressUsedRule[$rule->getId()][$index]);
            }
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function reset()
    {
        $this->addressUsedRule = [];

        return $this;
    }

    /**
     * @param \Magento\SalesRule\Model\Rule $rule
     *
     * @return int|string
     */
    protected function getRuleTimeLeft($rule)
    {
        $perCustomer = (int)$rule->getUsesPerCustomer();
        $perVoucher = (int)$rule->getUsesPerCoupon();
        if ($rule->getCouponType() == \Magento\SalesRule\Model\Rule::COUPON_TYPE_NO_COUPON) {
            $perVoucher = 0;
        }

        if ($perCustomer === 0 && $perVoucher === 0) {
            return self::UNLIMITED_VALUE;
        } elseif ($perCustomer !== 0 && $perVoucher !== 0) {
            $result = min(
                $perVoucher - $this->getRuleUsed($rule->getId()),
                $perCustomer - $this->getCustomerUsed($rule->getId())
            );
        } elseif ($perCustomer == 0) {
            $result = $perVoucher - $this->getRuleUsed($rule->getId());
        } else {
            $result = $perCustomer - $this->getCustomerUsed($rule->getId());
        }

        return max(0, $result);
    }

    /**
     * @param $ruleId
     *
     * @return int
     */
    protected function getCustomerUsed($ruleId)
    {
        /** @var \Magento\SalesRule\Model\Rule\Customer $ruleCustomer */
        $ruleCustomer = $this->customerRuleFactory->create();
        $ruleCustomer->loadByCustomerRule(
            $this->customerSession->getCustomerId(),
            $ruleId
        );

        return $ruleCustomer->getTimesUsed();
    }

    /**
     * @param $ruleId
     *
     * @return int
     */
    protected function getRuleUsed($ruleId)
    {
        /** @var \Magento\SalesRule\Model\ResourceModel\Rule\Customer $resource */
        $resource = $this->customerRuleFactory->create()->getResource();
        $connection = $resource->getConnection();
        $select = $connection->select()->from(
            'salesrule_customer',
            'SUM(times_used) as used'
        )->where(
            'rule_id = :rule_id'
        )->group('rule_id');

        return (int)$connection->fetchOne($select, [':rule_id' => $ruleId]);
    }
}
