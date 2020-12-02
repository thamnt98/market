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
     * @var \SM\Notification\Helper\Generate\Email
     */
    protected $emailHelper;

    /**
     * @var \SM\Notification\Model\EventSetting
     */
    protected $eventSetting;

    /**
     * @var \SM\Notification\Model\NotificationFactory
     */
    protected $notificationFactory;

    /**
     * @var \SM\Notification\Model\ResourceModel\Notification
     */
    protected $notificationResource;

    /**
     * @var \Magento\Framework\Logger\Monolog|null
     */
    protected $logger;

    /**
     * @var \Magento\Store\Model\App\Emulation
     */
    protected $emulation;

    /**
     * @var \Magento\Customer\Model\ResourceModel\CustomerRepository
     */
    protected $customerRepository;

    /**
     * Coupon constructor.
     *
     * @param \Magento\Customer\Model\ResourceModel\CustomerRepository $customerRepository
     * @param \Magento\Store\Model\App\Emulation                       $emulation
     * @param \SM\Notification\Helper\Generate\Email                   $emailHelper
     * @param \SM\Notification\Model\EventSetting                      $eventSetting
     * @param \SM\Notification\Model\NotificationFactory               $notificationFactory
     * @param \SM\Notification\Model\ResourceModel\Notification        $notificationResource
     * @param \Magento\Framework\Logger\Monolog|null                   $logger
     */
    public function __construct(
        \Magento\Customer\Model\ResourceModel\CustomerRepository $customerRepository,
        \Magento\Store\Model\App\Emulation $emulation,
        \SM\Notification\Helper\Generate\Email $emailHelper,
        \SM\Notification\Model\EventSetting $eventSetting,
        \SM\Notification\Model\NotificationFactory $notificationFactory,
        \SM\Notification\Model\ResourceModel\Notification $notificationResource,
        \Magento\Framework\Logger\Monolog $logger = null
    ) {
        $this->emailHelper = $emailHelper;
        $this->eventSetting = $eventSetting;
        $this->notificationFactory = $notificationFactory;
        $this->notificationResource = $notificationResource;
        $this->logger = $logger;
        $this->emulation = $emulation;
        $this->customerRepository = $customerRepository;
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

            $this->createNotification($customer, $object->getData('rule_id'));
        }

        return $result;
    }

    /**
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @param int                                          $ruleId
     */
    public function createNotification($customer, $ruleId)
    {
        $customerName = $customer->getFirstname() . ' ' .
            ($customer->getMiddlename() ? $customer->getMiddlename() . ' ' : '') .
            $customer->getLastname();
        $title = '%1, we have a voucher for you.';
        $content = 'Find it on My Voucher!';
        $params = [
            'title' => [
                $customerName,
            ],
        ];

        /** @var \SM\Notification\Model\Notification $notification */
        $notification = $this->notificationFactory->create();
        $notification->setTitle($title)
            ->setEvent(\SM\Notification\Model\Notification::EVENT_UPDATE)
            ->setSubEvent(\SM\Notification\Model\Notification::EVENT_PROMO_AND_EVENT)
            ->setCustomerIds([$customer->getId()])
            ->setContent($content)
            ->setParams($params)
            ->setRedirectType(\SM\Notification\Model\Source\RedirectType::TYPE_VOUCHER_DETAIL)
            ->setRedirectId($ruleId);

        $this->eventSetting->init($customer->getId(), \SM\Notification\Model\Notification::EVENT_PROMO_AND_EVENT);
        // Emulation store view
        $this->emulation->startEnvironmentEmulation(
            $customer->getStoreId(),
            \Magento\Framework\App\Area::AREA_FRONTEND
        );
        if ($this->eventSetting->isPush()) {
            $notification->setPushTitle(__($title, $params)->__toString())
                ->setPushContent(__($content)->__toString());
        }

        if ($this->eventSetting->isEmail()) {
            // send mail
        }

        if ($this->eventSetting->isSms()) {
            $notification->setSms(
                __('%1, we have a voucher for you.Find it on My Voucher!', $customerName)->__toString()
            );
        }

        $this->emulation->stopEnvironmentEmulation(); // End Emulation

        try {
            $this->notificationResource->save($notification);
        } catch (\Exception $e) {
            if ($this->logger) {
                $this->logger->error($e->getMessage(), $e->getTrace());
            }
        }
    }
}
