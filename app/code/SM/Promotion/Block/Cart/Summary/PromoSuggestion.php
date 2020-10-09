<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Promotion
 *
 * Date: August, 21 2020
 * Time: 11:39 AM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Promotion\Block\Cart\Summary;

class PromoSuggestion extends \Magento\Framework\View\Element\Template
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

    /**
     * PromoSuggestion constructor.
     *
     * @param \Magento\Checkout\Model\Session                               $checkoutSession
     * @param \SM\Promotion\Helper\Validation                               $validationHelper
     * @param \Magento\Customer\Model\Session                               $customerSession
     * @param \Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory $ruleCollFact
     * @param \Magento\Framework\View\Element\Template\Context              $context
     * @param array                                                         $data
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \SM\Promotion\Helper\Validation $validationHelper,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory $ruleCollFact,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->ruleCollFact = $ruleCollFact;
        $this->customerSession = $customerSession;
        $this->validationHelper = $validationHelper;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @return \Magento\SalesRule\Model\Rule|null
     */
    public function getRule()
    {
        if (is_null($this->rule)) {
            foreach ($this->getCollection() as $rule) {
                if ($this->validationHelper->validateCustomer($rule)) {
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
    public function getCollection()
    {
        /** @var \Magento\SalesRule\Model\ResourceModel\Rule\Collection $coll */
        $coll = $this->ruleCollFact->create();

        try {
            $customerGroup = $this->customerSession->getCustomerGroupId();
        } catch (\Exception $e) {
            $customerGroup = \Magento\Customer\Model\Group::NOT_LOGGED_IN_ID;
        }

        $coll->addIsActiveFilter()
            ->addCustomerGroupFilter($customerGroup)
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

    protected function getAppliedRuleIds()
    {
        try {
            $quote = $this->checkoutSession->getQuote();
            $ruleIds = $quote->getAppliedRuleIds();
            if (!is_array($ruleIds)) {
                $ruleIds = explode(',', $ruleIds);
            }

            return $ruleIds;
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * @override
     * @return string
     */
    public function toHtml()
    {
        if (!$this->getRule() || !$this->getRule()->getId()) {
            return '';
        }

        return parent::toHtml();
    }
}
