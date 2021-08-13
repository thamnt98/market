<?php
namespace SM\NotificationGraphQl\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use SM\Customer\Model\ResourceModel\CustomerDevice as CustomerDeviceResourceModel;
use SM\Customer\Model\CustomerDeviceFactory;
use Magento\Framework\Logger\Monolog;
use SM\Customer\Model\CustomerDevice;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;

/**
 * Class NotificationSaveDevice
 * @package SM\NotificationGraphQl\Model\Resolver
 */
class NotificationSaveDevice implements ResolverInterface
{
    /**
     * @var CustomerDeviceResourceModel
     */
    protected $customerDeviceResource;

    /**
     * @var CustomerDeviceFactory
     */
    protected $customerDeviceFactory;

    /**
     * @var Monolog
     */
    protected $logger;

    /**
     * NotificationSaveDevice constructor.
     * @param CustomerDeviceResourceModel $customerDeviceResource
     * @param CustomerDeviceFactory $customerDeviceFactory
     * @param Monolog $logger
     */
    public function __construct(
        CustomerDeviceResourceModel $customerDeviceResource,
        CustomerDeviceFactory $customerDeviceFactory,
        Monolog $logger
    ) {
        $this->customerDeviceResource = $customerDeviceResource;
        $this->customerDeviceFactory = $customerDeviceFactory;
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $customerId = $context->getUserId();

        if (!$customerId || $customerId == 0) {
            throw new GraphQlAuthorizationException(
                __('Only logged in customer can register device')
            );
        }

        if (empty($args['device'])) {
            throw new GraphQlInputException(__('"device" variable is required'));
        }

        $deviceInput = $args['device'];
        if (empty($deviceInput['device_id'])) {
            throw new GraphQlInputException(__('"device_id" field is required'));
        }
        if (empty($deviceInput['token'])) {
            throw new GraphQlInputException(__('"token" field is required'));
        }
        if (empty($deviceInput['type'])) {
            throw new GraphQlInputException(__('"type" field is required'));
        }

        /** @var CustomerDevice $device */
        $device = $this->customerDeviceFactory->create();
        $device->setData('customer_id', $customerId)
            ->setData('device_id', $deviceInput['device_id'])
            ->setData('token', $deviceInput['token'])
            ->setData('type', $deviceInput['type']);
        try {
            $this->customerDeviceResource->save($device);
        } catch (\Exception $e) {
            $this->logger->error('Error when saving customer device: ' . $e->getMessage(), $e->getTrace());
            throw new GraphQlInputException(__('Could not save the device: %1', $e->getMessage()));
        }

        return $device->getData();
    }
}
