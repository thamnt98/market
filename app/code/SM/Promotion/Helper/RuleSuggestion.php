<?php
/**
 * Class RuleSuggestion
 * @package SM\Promotion\Helper
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\Promotion\Helper;

class RuleSuggestion
{
    /**
     * @var \Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory
     */
    protected $ruleCollFact;

    /**
     * @var \Magento\SalesRule\Model\Rule
     */
    protected $rule = null;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \SM\Promotion\Helper\Validation
     */
    protected $validationHelper;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    private $customerGroupId;
    private $appliedRuleIds;

    public function __construct(
        \SM\Promotion\Helper\Validation $validationHelper,
        \Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory $ruleCollFact,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        $this->ruleCollFact = $ruleCollFact;
        $this->validationHelper = $validationHelper;
    }

    public function getRule($appliedRuleIds, $customerGroupId, $customerId)
    {
        if (is_null($this->rule)) {
            $this->appliedRuleIds = $appliedRuleIds;
            $this->customerGroupId = $customerGroupId;
            foreach ($this->getCollection() as $rule) {
                if ($this->validationHelper->validateApiCustomer($rule, $customerId)) {
                    $this->rule = $rule;
                    break;
                }
            }
        }

        return $this->rule;
    }

    /**
     * @return \Magento\SalesRule\Model\ResourceModel\Rule\Collection
     */
    private function getCollection()
    {
        /** @var \Magento\SalesRule\Model\ResourceModel\Rule\Collection $coll */
        $coll = $this->ruleCollFact->create();

        $coll->addIsActiveFilter()
            ->addCustomerGroupFilter($this->customerGroupId)
            ->addFieldToFilter('is_suggestion', 1)
            ->addFieldToFilter('coupon_type', \Magento\SalesRule\Model\Rule::COUPON_TYPE_NO_COUPON)
            ->addOrder(
                \Magento\SalesRule\Model\Data\Rule::KEY_SORT_ORDER,
                \Magento\SalesRule\Model\ResourceModel\Rule\Collection::SORT_ORDER_ASC
            );

        if ($applied = $this->getAppliedRuleIds()) {
            $coll->addFieldToFilter('rule_id', ['nin' => $applied]);
        }

        return $coll;
    }

    /**
     * @return array|false|string[]
     */
    private function getAppliedRuleIds()
    {
        try {
            $ruleIds = $this->appliedRuleIds;
            if (!is_array($ruleIds)) {
                $ruleIds = explode(',', $ruleIds);
            }

            return $ruleIds;
        } catch (\Exception $e) {
            return [];
        }
    }
}
