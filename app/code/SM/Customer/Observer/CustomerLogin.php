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

use SM\Customer\Model\CustomerDevice;

class CustomerLogin implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress
     */
    protected $remoteAddress;

    /**
     * @var \SM\Customer\Model\CustomerDeviceFactory
     */
    protected $deviceFactory;

    /**
     * @var \SM\Customer\Model\ResourceModel\CustomerDevice
     */
    protected $deviceResource;

    /**
     * @var \SM\Notification\Helper\Data
     */
    protected $notificationHelper;

    /**
     * CustomerLogin constructor.
     *
     * @param \SM\Notification\Helper\Data                         $notificationHelper
     * @param \SM\Customer\Model\CustomerDeviceFactory             $deviceFactory
     * @param \SM\Customer\Model\ResourceModel\CustomerDevice      $deviceResource
     * @param \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress
     */
    public function __construct(
        \SM\Notification\Helper\Data $notificationHelper,
        \SM\Customer\Model\CustomerDeviceFactory $deviceFactory,
        \SM\Customer\Model\ResourceModel\CustomerDevice $deviceResource,
        \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress
    ) {
        $this->remoteAddress = $remoteAddress;
        $this->deviceFactory = $deviceFactory;
        $this->deviceResource = $deviceResource;
        $this->notificationHelper = $notificationHelper;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Customer\Model\Customer $customer */
        $customer = $observer->getEvent()->getData('customer');
        $this->initDevice($customer->getId());
    }

    /**
     * @param int $customerId
     */
    public function initDevice($customerId)
    {
        if (!$this->notificationHelper->isApiRequest() && $this->remoteAddress->getRemoteAddress() !== false) {
            /** @var \SM\Customer\Model\CustomerDevice $device */
            $device = $this->deviceFactory->create();
            $coll = $device->getCollection()
                ->addFieldToFilter('customer_id', $customerId)
                ->addFieldToFilter('device_id', $this->remoteAddress->getRemoteAddress());
            if ($coll->getSize()) {
                return;
            }

            $device->setData('customer_id', $customerId)
                ->setData('device_id', $this->remoteAddress->getRemoteAddress())
                ->setData('type', \SM\Customer\Model\CustomerDevice::DESKTOP_TYPE);

            try {
                $this->deviceResource->save($device);
            } catch (\Exception $e) {
            }
        }
    }
}
