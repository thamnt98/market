<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Notification
 *
 * Date: December, 02 2020
 * Time: 4:47 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Notification\Controller\Index;

class CreateLoginNotify extends \Magento\Framework\App\Action\Action implements
    \Magento\Framework\App\Action\HttpPostActionInterface
{
    /**
     * @var \SM\Customer\Model\CustomerDeviceFactory
     */
    protected $deviceFactory;

    /**
     * @var \SM\Customer\Model\ResourceModel\CustomerDevice
     */
    protected $deviceResource;

    /**
     * @var \SM\Notification\Model\Notification\Generate
     */
    protected $generate;

    /**
     * @var \SM\Notification\Model\ResourceModel\Notification
     */
    protected $resource;

    /**
     * @var \Magento\Framework\Logger\Monolog
     */
    protected $logger;

    /**
     * CreateLoginNotify constructor.
     *
     * @param \Magento\Framework\Logger\Monolog                 $logger
     * @param \SM\Customer\Model\CustomerDeviceFactory          $deviceFactory
     * @param \SM\Customer\Model\ResourceModel\CustomerDevice   $deviceResource
     * @param \SM\Notification\Model\Notification\Generate      $generate
     * @param \SM\Notification\Model\ResourceModel\Notification $resource
     * @param \Magento\Framework\App\Action\Context             $context
     */
    public function __construct(
        \Magento\Framework\Logger\Monolog $logger,
        \SM\Customer\Model\CustomerDeviceFactory $deviceFactory,
        \SM\Customer\Model\ResourceModel\CustomerDevice $deviceResource,
        \SM\Notification\Model\Notification\Generate $generate,
        \SM\Notification\Model\ResourceModel\Notification $resource,
        \Magento\Framework\App\Action\Context $context
    ) {
        parent::__construct($context);
        $this->deviceFactory = $deviceFactory;
        $this->deviceResource = $deviceResource;
        $this->generate = $generate;
        $this->resource = $resource;
        $this->logger = $logger;
    }

    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $customerId = (int)$data['customer_id'] ?? 0;
        $resultJson = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_JSON);
        $responseData = [
            'status'     => 1,
            'customerId' => $customerId,
        ];

        if ($customerId) {
            if ($this->deviceResource->isFirstDevice($customerId)) {
                $this->initDevice($customerId);
            } else {
                try {
                    $this->resource->save($this->generate->loginOtherDevice($customerId));
                } catch (\Exception $e) {
                    $this->logger->error($e->getMessage(), $e->getTrace());
                }
            }
        } else {
            $responseData['status'] = 0;
            $responseData['error-message'] = 'Customer is required.';
            $resultJson->setHttpResponseCode(\Magento\Framework\Webapi\Exception::HTTP_BAD_REQUEST);
        }

        return $resultJson->setData($responseData);
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
