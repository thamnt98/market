<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Notification
 *
 * Date: November, 23 2020
 * Time: 2:52 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Notification\Observer;

class DeviceSaveAfter implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \SM\Notification\Helper\Data
     */
    protected $helper;

    /**
     * @var \SM\Notification\Model\EventSetting
     */
    protected $eventSetting;

    /**
     * @var \SM\Notification\Model\NotificationFactory
     */
    protected $modelFactory;

    /**
     * @var \SM\Notification\Model\ResourceModel\Notification
     */
    protected $resource;

    /**
     * @var \Magento\Store\Model\App\Emulation
     */
    protected $emulation;

    /**
     * @var \Magento\Framework\Logger\Monolog
     */
    protected $logger;

    /**
     * DeviceSaveAfter constructor.
     *
     * @param \SM\Notification\Helper\Data                      $helper
     * @param \Magento\Framework\Logger\Monolog                 $logger
     * @param \Magento\Store\Model\App\Emulation                $emulation
     * @param \SM\Notification\Model\EventSetting               $eventSetting
     * @param \SM\Notification\Model\NotificationFactory        $modelFactory
     * @param \SM\Notification\Model\ResourceModel\Notification $resource
     */
    public function __construct(
        \SM\Notification\Helper\Data $helper,
        \Magento\Framework\Logger\Monolog $logger,
        \Magento\Store\Model\App\Emulation $emulation,
        \SM\Notification\Model\EventSetting $eventSetting,
        \SM\Notification\Model\NotificationFactory $modelFactory,
        \SM\Notification\Model\ResourceModel\Notification $resource
    ) {
        $this->helper = $helper;
        $this->modelFactory = $modelFactory;
        $this->resource = $resource;
        $this->eventSetting = $eventSetting;
        $this->emulation = $emulation;
        $this->logger = $logger;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \SM\Customer\Model\CustomerDevice $device */
        $device = $observer->getEvent()->getData('device');

        $this->logger->info('Notification - After Save Device', $device->getData());
        if ($device->getData(\SM\Customer\Model\CustomerDevice::NEW_DEVICE_KEY)) {
            $this->createNotify($device->getData('customer_id'));
        } else {
            $this->logger->info('Old device');
        }
    }

    /**
     * @param int $customerId
     */
    protected function createNotify($customerId)
    {
        /** @var \SM\Notification\Model\Notification $notify */
        $notify = $this->modelFactory->create();
        $title = 'Someone just signed in to your account on other device.';
        $message = 'Not you? Consider changing your password.';

        $notify->setTitle($title)
            ->setContent($message)
            ->setEvent(\SM\Notification\Model\Notification::EVENT_SERVICE)
            ->setSubEvent(\SM\Notification\Model\Notification::EVENT_UNKNOWN_DEVICE)
            ->setCustomerIds([$customerId])
            ->setRedirectType(\SM\Notification\Model\Source\RedirectType::TYPE_HOME);

        $this->eventSetting->init($customerId, \SM\Notification\Model\Notification::EVENT_UNKNOWN_DEVICE);
        $this->emulation->startEnvironmentEmulation(
            $this->helper->getStoreId(),
            \Magento\Framework\App\Area::AREA_FRONTEND,
            true
        );

        if ($this->eventSetting->isPush()) {
            $notify->setPushTitle(__($title)->__toString())
                ->setPushContent(__($message)->__toString());
        }

        if ($this->eventSetting->isEmail()) {
            // Set email
        }

        if ($this->eventSetting->isSms()) {
            // Set sms
        }

        $this->emulation->stopEnvironmentEmulation();

        try {
            $this->resource->save($notify);
            $this->logger->info('Create notify for new device success.');
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage(), $e->getTrace());
        }
    }
}
