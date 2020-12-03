<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Customer
 *
 * Date: November, 23 2020
 * Time: 3:53 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Customer\Observer;

class CustomerLogin extends \SM\Notification\Observer\DeviceSaveAfter
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \SM\Customer\Model\CustomerDeviceFactory
     */
    protected $deviceFactory;

    /**
     * @var \SM\Customer\Model\ResourceModel\CustomerDevice
     */
    protected $deviceResource;

    /**
     * CustomerLogin constructor.
     *
     * @param \SM\Notification\Helper\Data                      $helper
     * @param \Magento\Framework\Logger\Monolog                 $logger
     * @param \Magento\Store\Model\App\Emulation                $emulation
     * @param \Magento\Framework\App\RequestInterface           $request
     * @param \SM\Notification\Model\EventSetting               $eventSetting
     * @param \SM\Notification\Model\NotificationFactory        $modelFactory
     * @param \SM\Notification\Model\ResourceModel\Notification $resource
     * @param \SM\Customer\Model\CustomerDeviceFactory          $deviceFactory
     * @param \SM\Customer\Model\ResourceModel\CustomerDevice   $deviceResource
     */
    public function __construct(
        \SM\Notification\Helper\Data $helper,
        \Magento\Framework\Logger\Monolog $logger,
        \Magento\Store\Model\App\Emulation $emulation,
        \Magento\Framework\App\RequestInterface $request,
        \SM\Notification\Model\EventSetting $eventSetting,
        \SM\Notification\Model\NotificationFactory $modelFactory,
        \SM\Notification\Model\ResourceModel\Notification $resource,
        \SM\Customer\Model\CustomerDeviceFactory $deviceFactory,
        \SM\Customer\Model\ResourceModel\CustomerDevice $deviceResource
    ) {
        parent::__construct($helper, $logger, $emulation, $eventSetting, $modelFactory, $resource);
        $this->request = $request;
        $this->deviceFactory = $deviceFactory;
        $this->deviceResource = $deviceResource;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Customer\Model\Customer $customer */
        $customer = $observer->getEvent()->getData('customer');
        if ($this->deviceResource->isFirstDevice($customer->getId())) {
            $this->initDevice($customer->getId());

            return;
        }

        $params = json_decode($this->request->getContent(), true);
        $browserCustomer = $params['device_customers'] ?? [];
        if (!empty($browserCustomer) && in_array($customer->getId(), $browserCustomer)) {
            return;
        }

        $this->createNotify($customer->getId());
    }

    /**
     * @param int $customerId
     */
    protected function initDevice($customerId)
    {
        /** @var \SM\Customer\Model\CustomerDevice $device */
        $device = $this->deviceFactory->create();
        $device->setData('customer_id', $customerId)
            ->setData('device_id', \SM\Customer\Model\CustomerDevice::DESKTOP_TYPE)
            ->setData('type', \SM\Customer\Model\CustomerDevice::DESKTOP_TYPE);

        try {
            $this->deviceResource->save($device);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage(), $e->getTrace());
        }
    }
}
