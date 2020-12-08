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
     * @var \SM\Notification\Model\Notification\Generate
     */
    protected $generate;

    /**
     * @var \SM\Notification\Model\ResourceModel\Notification
     */
    protected $resource;

    /**
     * @var \Magento\Framework\Logger\Monolog|null
     */
    protected $logger;

    /**
     * @var array
     */
    protected $customers = [
        'email' => [],
        'id'    => [],
        'phone' => [],
    ];

    /**
     * RuleSaveAfter constructor.
     *
     * @param \SM\Notification\Model\Notification\Generate      $generate
     * @param \SM\Notification\Model\ResourceModel\Notification $resource
     * @param \SM\Promotion\Helper\Validation                   $validation
     * @param \SM\Customer\Model\ResourceModel\Customer         $customerResource
     * @param \Magento\Customer\Model\CustomerFactory           $customerFactory
     * @param \Magento\Framework\Logger\Monolog|null            $logger
     */
    public function __construct(
        \SM\Notification\Model\Notification\Generate $generate,
        \SM\Notification\Model\ResourceModel\Notification $resource,
        \SM\Promotion\Helper\Validation $validation,
        \SM\Customer\Model\ResourceModel\Customer $customerResource,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Framework\Logger\Monolog $logger = null
    ) {
        $this->validation = $validation;
        $this->customerFactory = $customerFactory;
        $this->customerResource = $customerResource;
        $this->generate = $generate;
        $this->resource = $resource;
        $this->logger = $logger;
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
                $customer = $this->getCustomer($condition->getAttribute(), $condition->getValue());
                if ($customer === false) {
                    continue;
                } elseif (is_null($customer) || !$customer->getId()) {
                    return;
                } else {
                    $customer->setData('id', $customer->getId())
                        ->setData('entity_id', $customer->getId());
                    if ($this->validation->validateCustomer($rule, $customer)) {
                        try {
                            $this->resource->save($this->generate->haveCoupon($customer->getDataModel()));
                        } catch (\Exception $e) {
                            $this->logger->error("Have Coupon: \n\t" . $e->getMessage(), $e->getTrace());
                        }
                    }

                    return;
                }
            }
        }
    }

    /**
     * @param string $attribute
     * @param string $value
     *
     * @return \Magento\Customer\Model\Customer|null
     */
    protected function getCustomer($attribute, $value)
    {
        try {
            /** @var \Magento\Customer\Model\Customer $customer */
            switch ($attribute) {
                case 'email':
                    if (!isset($this->customers['email'][$value])) {
                        $this->customers['email'][$value] = $this->customerFactory->create()
                            ->setData('website_id', 1)
                            ->loadByEmail($value);
                    }

                    $customer = $this->customers['email'][$value];
                    $this->customers['phone'][$customer->getData('telephone')] = $customer;
                    $this->customers['id'][$customer->getId()] = $customer;
                    break;
                case 'telephone':
                    $phone = '628' . preg_replace("/^(^\+628|^628|^08|^8)/", '', $value);
                    if (!isset($this->customers['phone'][$phone])) {
                        $customerId = $this->customerResource->getCustomerIdByPhoneNumber($phone);
                        $this->customers['phone'][$phone] = $this->customerFactory->create()->load($customerId);
                    }

                    $customer = $this->customers['phone'][$phone];
                    $this->customers['email'][$customer->getEmail()] = $customer;
                    $this->customers['id'][$customer->getId()] = $customer;

                    break;
                case 'id':
                    if (!isset($this->customers['id'][$value])) {
                        $this->customers['id'][$value] = $this->customerFactory->create()->load($value);
                    }

                    $customer = $this->customers['id'][$value];
                    $this->customers['email'][$customer->getEmail()] = $customer;
                    $this->customers['phone'][$customer->getData('telephone')] = $customer;
                    break;
                default:
                    return false;
            }

            return $customer;
        } catch (\Exception $e) {
            return null;
        }
    }
}
