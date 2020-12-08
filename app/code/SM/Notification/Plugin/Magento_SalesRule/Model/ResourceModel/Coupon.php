<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Notification
 *
 * Date: November, 25 2020
 * Time: 10:52 AM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Notification\Plugin\Magento_SalesRule\Model\ResourceModel;

class Coupon
{
    /**
     * @var \SM\Notification\Model\ResourceModel\Notification
     */
    protected $resource;

    /**
     * @var \Magento\Framework\Logger\Monolog|null
     */
    protected $logger;

    /**
     * @var \Magento\Customer\Model\ResourceModel\CustomerRepository
     */
    protected $customerRepository;

    /**
     * @var \SM\Notification\Model\Notification\Generate
     */
    protected $generate;

    /**
     * Coupon constructor.
     *
     * @param \SM\Notification\Model\Notification\Generate             $generate
     * @param \Magento\Customer\Model\ResourceModel\CustomerRepository $customerRepository
     * @param \SM\Notification\Model\ResourceModel\Notification        $notificationResource
     * @param \Magento\Framework\Logger\Monolog|null                   $logger
     */
    public function __construct(
        \SM\Notification\Model\Notification\Generate $generate,
        \Magento\Customer\Model\ResourceModel\CustomerRepository $customerRepository,
        \SM\Notification\Model\ResourceModel\Notification $notificationResource,
        \Magento\Framework\Logger\Monolog $logger = null
    ) {
        $this->resource = $notificationResource;
        $this->logger = $logger;
        $this->customerRepository = $customerRepository;
        $this->generate = $generate;
    }

    /**
     * @param \Magento\SalesRule\Model\ResourceModel\Coupon $subject
     * @param \Magento\SalesRule\Model\ResourceModel\Coupon $result
     * @param \Magento\Framework\Model\AbstractModel        $object
     *
     * @return \Magento\SalesRule\Model\ResourceModel\Coupon
     */
    public function afterSave(
        \Magento\SalesRule\Model\ResourceModel\Coupon $subject,
        \Magento\SalesRule\Model\ResourceModel\Coupon $result,
        \Magento\Framework\Model\AbstractModel $object
    ) {
        if ($object->isObjectNew() && $object->getData('customer_id')) {
            try {
                $customer = $this->customerRepository->getById($object->getData('customer_id'));
            } catch (\Exception $e) {
                return $result;
            }

            try {
                $this->resource->save($this->generate->haveCoupon($customer));
            } catch (\Exception $e) {
                if ($this->logger) {
                    $this->logger->error("Have Coupon: \n\t" . $e->getMessage(), $e->getTrace());
                }
            }
        }

        return $result;
    }
}
