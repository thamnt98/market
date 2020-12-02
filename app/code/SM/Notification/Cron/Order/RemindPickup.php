<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Notification
 *
 * Date: August, 24 2020
 * Time: 10:36 AM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Notification\Cron\Order;

use SM\Notification\Cron\AbstractGenerate;

class RemindPickup extends AbstractGenerate
{
    const EVENT_NAME_AFTER_READY  = 'remind_pickup_after_ready';
    const EVENT_NAME_BEFORE_LIMIT = 'remind_pickup_before_limit';

    /**
     * @var \SM\Sales\Helper\Data
     */
    protected $orderHelper;

    /**
     * RemindPickup constructor.
     *
     * @param \Magento\Framework\Filesystem                     $filesystem
     * @param \SM\Sales\Helper\Data                             $orderHelper
     * @param \Magento\Store\Model\App\Emulation                $emulation
     * @param \SM\Notification\Model\EventSetting               $eventSetting
     * @param \SM\Notification\Helper\Config                    $configHelper
     * @param \SM\Notification\Model\NotificationFactory        $notificationFactory
     * @param \SM\Notification\Model\ResourceModel\Notification $notificationResource
     * @param \Magento\Framework\App\ResourceConnection         $resourceConnection
     * @param \Magento\Framework\Logger\Monolog|null            $logger
     *
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function __construct(
        \Magento\Framework\Filesystem $filesystem,
        \SM\Sales\Helper\Data $orderHelper,
        \Magento\Store\Model\App\Emulation $emulation,
        \SM\Notification\Model\EventSetting $eventSetting,
        \SM\Notification\Helper\Config $configHelper,
        \SM\Notification\Model\NotificationFactory $notificationFactory,
        \SM\Notification\Model\ResourceModel\Notification $notificationResource,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Framework\Logger\Monolog $logger
    ) {
        parent::__construct(
            $filesystem,
            $emulation,
            $eventSetting,
            $configHelper,
            $notificationFactory,
            $notificationResource,
            $resourceConnection,
            $logger
        );

        $this->orderHelper = $orderHelper;
    }

    public function process()
    {
        $this->create(self::EVENT_NAME_AFTER_READY);
        $this->create(self::EVENT_NAME_BEFORE_LIMIT);
    }

    /**
     * @param string $event
     */
    protected function create($event)
    {
        switch ($event) {
            case self::EVENT_NAME_BEFORE_LIMIT:
                $orders = $this->getOrderBeforeLimit();
                break;
            case self::EVENT_NAME_AFTER_READY:
                $orders = $this->getOrderAfterReady();
                break;
            default:
                return;
        }

        foreach ($orders as $item) {
            try {
                if ($this->createNotification($item)) {
                    $this->connection->insert(
                        \SM\Notification\Model\ResourceModel\TriggerEvent::TABLE_NAME,
                        [
                            'event_id'   => $item['entity_id'],
                            'event_type' => 'order',
                            'event_name' => $event,
                        ]
                    );
                }
            } catch (\Exception $e) {
                $this->logger->error(
                    "Notification Reminder Pickup create failed: \n\t" . $e->getMessage(),
                    $e->getTrace()
                );
            }
        }
    }

    /**
     * @param array $data
     *
     * @return int|null
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    protected function createNotification($data)
    {
        $order = $this->orderHelper->getOrderById($data['entity_id'] ?? 0);

        if (!$order || empty($data['customer_id'])) {
            return null;
        }

        $title = "%1, you haven't picked your order up.";
        $message = 'Please visit %1 to collect order %2 before %3.';
        $params = [
            'title'   => [
                $data['customer_name'] ?? '',
            ],
            'content' => [
                $data['store_name'] ?? '',
                $data['reference_order_id'] ?? $data['reference_number'] ?? $data['increment_id'] ?? '',
                $data['expired'] ?? '',
            ],
        ];
        /** @var \SM\Notification\Model\Notification $notification */
        $notification = $this->notificationFactory->create();
        $notification->setTitle($title)
            ->setContent($message)
            ->setEvent(\SM\Notification\Model\Notification::EVENT_ORDER_STATUS)
            ->setSubEvent(\SM\Notification\Model\Notification::EVENT_ORDER_STATUS)
            ->setCustomerIds([$data['customer_id']])
            ->setRedirectType(\SM\Notification\Model\Source\RedirectType::TYPE_ORDER_DETAIL)
            ->setRedirectId($order->getData('parent_order') ? $order->getData('parent_order') : $order->getEntityId())
            ->setParams($params);

        // Emulation store view
        $this->emulation->startEnvironmentEmulation(
            $order->getStoreId(),
            \Magento\Framework\App\Area::AREA_FRONTEND
        );

        $notification->setPushTitle(__($title, $params['title'])->__toString())
            ->setPushContent(__($message, $params['content'])->__toString());

        $this->emulation->stopEnvironmentEmulation(); // End Emulation

        $this->notificationResource->save($notification);

        return $notification->getId();
    }

    /**
     * @return array
     */
    protected function getOrderAfterReady()
    {
        $afterReady = $this->configHelper->getRemindPickupDay();
        $select = $this->generateSelect(self::EVENT_NAME_AFTER_READY, $afterReady);

        return $this->connection->fetchAssoc($select);
    }

    /**
     * @return array
     */
    protected function getOrderBeforeLimit()
    {
        $beforeLimit = $this->configHelper->getRemindPickupExpiringSoonDay();
        $time = $this->configHelper->getPickupLimitDay() - $beforeLimit;

        if (!$beforeLimit || $time < 0) {
            return [];
        }

        $select = $this->generateSelect(self::EVENT_NAME_BEFORE_LIMIT, $time);

        return $this->connection->fetchAssoc($select);
    }

    /**
     * @param string $event
     * @param int    $time
     *
     * @return \Magento\Framework\DB\Select
     */
    protected function generateSelect($event, $time)
    {
        $pickupEvent = \SM\Notification\Observer\Order\SaveAfter::TRIGGER_EVENT_ORDER_READY_TO_PICKUP;
        $limit = $this->configHelper->getPickupLimitDay();

        $select = $this->connection->select();
        $select->from(
            ['o' => 'sales_order'],
            []
        )->joinInner(
            ['customer' => 'customer_entity'],
            'o.customer_id = customer.entity_id',
            []
        )->joinInner(
            ['t' => \SM\Notification\Model\ResourceModel\TriggerEvent::TABLE_NAME],
            'o.entity_id = t.event_id',
            []
        )->joinInner(
            ['source' => 'inventory_source'],
            'o.store_pick_up = source.source_code',
            []
        )->where(
            'o.is_parent <> ?',
            1
        )->where(
            'o.status = ?',
            $this->orderHelper->getReadyToPickupStatus()
        )->where(
            'o.store_pick_up IS NOT NULL'
        )->where(
            'o.store_pick_up <> \'\''
        )->where(
            't.event_name = ?',
            $pickupEvent
        )->where(
            '(SELECT count(id) FROM sm_notification_trigger_event ' .
            'WHERE event_id = o.entity_id AND event_name = \'' . $event . '\') < 1'
        )->where(
            'current_timestamp() >= DATE_ADD(' .
            '(SELECT created_at FROM sm_notification_trigger_event ' .
            'WHERE event_id = o.entity_id AND event_name = \'' . $pickupEvent . '\' ' .
            'ORDER BY created_at DESC LIMIT 1
            ), INTERVAL ' . $time . ' day)'
        )->limit(50);

        $select->columns([
            'o.entity_id',
            'o.increment_id',
            'o.customer_id',
            'o.reference_number',
            'o.reference_order_id',
            "CONCAT(o.customer_firstname,' ', o.customer_lastname) as customer_name",
            'source.name as store_name',
            'DATE_ADD(' .
            '(SELECT created_at FROM sm_notification_trigger_event ' .
            'WHERE event_id = o.entity_id AND event_name = \'' . $pickupEvent . '\' ' .
            'ORDER BY created_at DESC LIMIT 1
            ), INTERVAL ' . $limit . ' day) as expired',
        ]);

        return $select;
    }

    /**
     * @return string
     */
    protected function getLockFileName()
    {
        return 'sm_notification_remind_pickup.lock';
    }
}
