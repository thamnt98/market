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
     * @var \SM\Notification\Model\ResourceModel\Notification
     */
    protected $resource;

    /**
     * @var \Magento\Framework\Logger\Monolog
     */
    protected $logger;

    /**
     * @var \SM\Notification\Model\Notification\Generate
     */
    protected $generate;

    /**
     * DeviceSaveAfter constructor.
     *
     * @param \SM\Notification\Helper\Data                      $helper
     * @param \Magento\Framework\Logger\Monolog                 $logger
     * @param \SM\Notification\Model\EventSetting               $eventSetting
     * @param \SM\Notification\Model\Notification\Generate      $generate
     * @param \SM\Notification\Model\ResourceModel\Notification $resource
     */
    public function __construct(
        \SM\Notification\Helper\Data $helper,
        \Magento\Framework\Logger\Monolog $logger,
        \SM\Notification\Model\EventSetting $eventSetting,
        \SM\Notification\Model\Notification\Generate $generate,
        \SM\Notification\Model\ResourceModel\Notification $resource
    ) {
        $this->helper = $helper;
        $this->resource = $resource;
        $this->eventSetting = $eventSetting;
        $this->logger = $logger;
        $this->generate = $generate;
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
        try {
            $this->resource->save($this->generate->loginOtherDevice($customerId));
            $this->logger->info('Create notify for new device success.');
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage(), $e->getTrace());
        }
    }
}
