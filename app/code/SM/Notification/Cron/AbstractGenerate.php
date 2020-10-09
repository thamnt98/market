<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Notification
 *
 * Date: June, 13 2020
 * Time: 5:26 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Notification\Cron;

abstract class AbstractGenerate
{
    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $connection;

    /**
     * @var \SM\Notification\Helper\CustomerSetting
     */
    protected $settingHelper;

    /**
     * @var \Magento\Framework\Logger\Monolog
     */
    protected $logger;

    /**
     * @var \SM\Notification\Model\NotificationFactory
     */
    protected $notificationFactory;

    /**
     * @var \SM\Notification\Model\ResourceModel\Notification
     */
    protected $notificationResource;

    /**
     * @var \Magento\Store\Model\App\Emulation
     */
    protected $emulation;

    /**
     * constructor.
     *
     * @param \Magento\Store\Model\App\Emulation                $emulation
     * @param \SM\Notification\Helper\CustomerSetting           $settingHelper
     * @param \SM\Notification\Model\NotificationFactory        $notificationFactory
     * @param \SM\Notification\Model\ResourceModel\Notification $notificationResource
     * @param \Magento\Framework\App\ResourceConnection         $resourceConnection
     * @param \Magento\Framework\Logger\Monolog|null            $logger
     */
    public function __construct(
        \Magento\Store\Model\App\Emulation $emulation,
        \SM\Notification\Helper\CustomerSetting $settingHelper,
        \SM\Notification\Model\NotificationFactory $notificationFactory,
        \SM\Notification\Model\ResourceModel\Notification $notificationResource,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Framework\Logger\Monolog $logger
    ) {
        $this->notificationFactory = $notificationFactory;
        $this->notificationResource = $notificationResource;
        $this->connection = $resourceConnection->getConnection();
        $this->settingHelper = $settingHelper;
        $this->logger = $logger;
        $this->construct();
        $this->emulation = $emulation;
    }

    protected function construct()
    {
    }

    abstract public function execute();
}
