<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Notification
 *
 * Date: November, 25 2020
 * Time: 2:18 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Notification\Observer;

class RuleSaveAfter implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \SM\Promotion\Helper\Validation
     */
    protected $validation;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var \SM\Customer\Model\ResourceModel\Customer
     */
    protected $customerResource;

    /**
     * @var \SM\Notification\Plugin\Magento_SalesRule\Model\ResourceModel\Coupon
     */
    protected $couponPlugin;

    /**
     * RuleSaveAfter constructor.
     *
     * @param \SM\Promotion\Helper\Validation                                      $validation
     * @param \SM\Customer\Model\ResourceModel\Customer                            $customerResource
     * @param \SM\Notification\Plugin\Magento_SalesRule\Model\ResourceModel\Coupon $couponPlugin
     * @param \Magento\Customer\Model\CustomerFactory                              $customerFactory
     */
    public function __construct(
        \SM\Promotion\Helper\Validation $validation,
        \SM\Customer\Model\ResourceModel\Customer $customerResource,
        \SM\Notification\Plugin\Magento_SalesRule\Model\ResourceModel\Coupon $couponPlugin,
        \Magento\Customer\Model\CustomerFactory $customerFactory
    ) {
        $this->validation = $validation;
        $this->customerFactory = $customerFactory;
        $this->customerResource = $customerResource;
        $this->couponPlugin = $couponPlugin;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\SalesRule\Model\Rule $rule */
        $rule = $observer->getEvent()->getData('rule');

        if (!$rule->isObjectNew() || $rule->getCouponType() != \Magento\SalesRule\Model\Rule::COUPON_TYPE_SPECIFIC) {
            return;
        }

        $conditions = $rule->getConditions()->getConditions();

        foreach ($conditions as $condition) {
            if ($condition instanceof \Amasty\Conditions\Model\Rule\Condition\CustomerAttributes &&
                $condition->getOperator() === '==' &&
                !is_array($condition->getValue())
            ) {
                try {
                    /** @var \Magento\Customer\Model\Customer $customer */
                    switch ($condition->getAttribute()) {
                        case 'email':
                            $customer = $this->customerFactory->create()
                                ->setData('website_id', 1)
                                ->loadByEmail($condition->getValue());
                            break;
                        case 'telephone':
                            $phone = '628' . preg_replace("/^(^\+628|^628|^08|^8)/", '', $condition->getValue());
                            $customerId = $this->customerResource->getCustomerIdByPhoneNumber($phone);
                            $customer = $this->customerFactory->create()->load($customerId);
                            break;
                        case 'id':
                            $customer = $this->customerFactory->create()->load($condition->getValue());
                            break;
                    }
                } catch (\Exception $e) {
                    return;
                }

                if (isset($customer)) {
                    if (!$customer || !$customer->getId()) {
                        return;
                    } else {
                        $customer->setData('id', $customer->getId())
                            ->setData('entity_id', $customer->getId());
                        if ($this->validation->validateCustomer($rule, $customer)) {
                            $this->couponPlugin->createNotification($customer->getDataModel(), $rule->getRuleId());
                        }

                        return;
                    }
                }
            }
        }
    }
}
