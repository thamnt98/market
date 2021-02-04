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
use SM\Notification\Model\Notification;

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
     * @var \SM\Notification\Model\EventSetting[]
     */
    protected $customerSetting = [];

    /**
     * @var \Magento\Customer\Model\ResourceModel\Online\Grid\CollectionFactory
     */
    protected $customerOnlineCollFact;

    /**
     * @var \SM\Notification\Model\EventSettingFactory
     */
    protected $eventSettingFactory;

    /**
     * AbstractConsumer constructor.
     *
     * @param \SM\Notification\Model\EventSettingFactory                          $eventSettingFactory
     * @param \Magento\Customer\Model\ResourceModel\Online\Grid\CollectionFactory $customerOnlineCollFact
     * @param \Magento\Framework\App\ResourceConnection                           $resourceConnection
     * @param \Magento\Customer\Api\CustomerRepositoryInterface                   $customerRepository
     * @param \Trans\IntegrationNotification\Api\IntegrationNotificationInterface $integrationNotification
     * @param \Magento\Framework\Logger\Monolog                                   $logger
     */
    public function __construct(
        \SM\Notification\Model\EventSettingFactory $eventSettingFactory,
        \Magento\Customer\Model\ResourceModel\Online\Grid\CollectionFactory $customerOnlineCollFact,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Trans\IntegrationNotification\Api\IntegrationNotificationInterface $integrationNotification,
        \Magento\Framework\Logger\Monolog $logger
    ) {
        $this->connection = $resourceConnection->getConnection();
        $this->integration = $integrationNotification;
        $this->customerRepository = $customerRepository;
        $this->logger = $logger;
        $this->customerOnlineCollFact = $customerOnlineCollFact;
        $this->eventSettingFactory = $eventSettingFactory;
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
     * @param string    $event
     *
     * @return \SM\Notification\Model\EventSetting
     */
    protected function getCustomerSetting($customerId, $event)
    {
        if (empty($this->customerSetting[$customerId][$event])) {
            $this->customerSetting
            [$customerId]
            [$event] = $this->eventSettingFactory->create()->init($customerId, $event);
        }

        return $this->customerSetting[$customerId][$event];
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
        $setting = $this->getCustomerSetting($customerId, $event);

        if ($event === Notification::EVENT_ORDER_STATUS) {
            if (!$this->checkCustomerOnline($customerId)) {
                $this->logger->info('Customer offline');
                $setting = $this->getCustomerSetting($customerId, Notification::EVENT_ORDER_STATUS_SIGN_OUT);
            }
        }

        switch ($type) {
            case \SM\Notification\Model\Email::NOTIFICATION_TYPE:
                return $setting->isEmail();
            case \SM\Notification\Model\Push::NOTIFICATION_TYPE:
                return $setting->isPush();
            case \SM\Notification\Model\Sms::NOTIFICATION_TYPE:
                return $setting->isSms();
            default:
                return false;
        }
    }

    /**
     * @param $customerId
     *
     * @return int
     */
    protected function checkCustomerOnline($customerId)
    {
        /** @var \Magento\Customer\Model\ResourceModel\Online\Grid\Collection $coll */
        $coll = $this->customerOnlineCollFact->create();
        $coll->getSelect()->where('main_table.customer_id = ?', $customerId);

        return $coll->count();
    }
}
