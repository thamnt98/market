<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Notification
 *
 * Date: June, 04 2020
 * Time: 11:13 AM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Notification\Model\Notification\Consumer;

define('DS', DIRECTORY_SEPARATOR);

use Monolog\Logger;

abstract class AbstractConsumer
{
    /**
     * @var \Trans\IntegrationNotification\Api\IntegrationNotificationInterface
     */
    protected $integration;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $connection;

    /**
     * @var \SM\Notification\Helper\CustomerSetting
     */
    protected $settingHelper;

    /**
     * @var array
     */
    protected $customerSetting = [];

    /**
     * AbstractConsumer constructor.
     *
     * @param \SM\Notification\Helper\CustomerSetting                             $settingHelper
     * @param \Magento\Framework\App\ResourceConnection                           $resourceConnection
     * @param \Magento\Customer\Api\CustomerRepositoryInterface                   $customerRepository
     * @param \Trans\IntegrationNotification\Api\IntegrationNotificationInterface $integrationNotification
     * @param \Magento\Framework\Logger\Monolog                                   $logger
     */
    public function __construct(
        \SM\Notification\Helper\CustomerSetting $settingHelper,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Trans\IntegrationNotification\Api\IntegrationNotificationInterface $integrationNotification,
        \Magento\Framework\Logger\Monolog $logger
    ) {
        $this->connection = $resourceConnection->getConnection();
        $this->integration = $integrationNotification;
        $this->customerRepository = $customerRepository;
        $this->logger = $logger;
        $this->settingHelper = $settingHelper;
    }

    /**
     * @param $customerId
     *
     * @return \Magento\Customer\Api\Data\CustomerInterface|null
     * @throws \Exception
     */
    public function getCustomer($customerId)
    {
        try {
            return $this->customerRepository->getById($customerId);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Log Error.
     *
     * @param       $message
     * @param array $params
     */
    public function logError($message, $params = [])
    {
        if ($this->logger) {
            $this->logger->error($message, $params);
        }
    }

    /**
     * Log Info.
     *
     * @param       $message
     * @param array $params
     */
    public function logInfo($message, $params = [])
    {
        if ($this->logger) {
            $this->logger->info($message, $params);
        }
    }

    /**
     * @param int $customerId
     *
     * @return array
     */
    protected function getCustomerSetting($customerId)
    {
        if (empty($this->customerSetting[$customerId])) {
            $this->customerSetting[$customerId] = $this->settingHelper->getCustomerSetting($customerId);
        }

        return $this->customerSetting[$customerId];
    }

    /**
     * @param $customerId
     * @param $event
     * @param $type
     *
     * @return bool
     */
    protected function validate($customerId, $event, $type)
    {
        $setting = $this->getCustomerSetting($customerId);
        $settingCode = $this->settingHelper->generateSettingCode($event, $type);

        return in_array($settingCode, $setting);
    }
}
