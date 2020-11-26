<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Notification
 *
 * Date: June, 17 2020
 * Time: 3:22 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Notification\Cron;

class VaPaymentExpiringSoon extends AbstractGenerate
{
    const EVENT_NAME = 'va_payment_expiring';

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;

    /**
     * @var \Trans\Sprint\Model\ResourceModel\SprintResponse\CollectionFactory
     */
    protected $sprintResponseCollFact;

    /**
     * @var \SM\Sales\Helper\Data
     */
    protected $orderHelper;

    /**
     * VaPaymentExpiringSoon constructor.
     *
     * @param \Magento\Framework\Filesystem                                      $filesystem
     * @param \SM\Sales\Helper\Data                                              $orderHelper
     * @param \Trans\Sprint\Model\ResourceModel\SprintResponse\CollectionFactory $sprintResponseCollFact
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface               $timezone
     * @param \Magento\Store\Model\App\Emulation                                 $emulation
     * @param \SM\Notification\Model\EventSetting                                $eventSetting
     * @param \SM\Notification\Helper\Config                                     $configHelper
     * @param \SM\Notification\Model\NotificationFactory                         $notificationFactory
     * @param \SM\Notification\Model\ResourceModel\Notification                  $notificationResource
     * @param \Magento\Framework\App\ResourceConnection                          $resourceConnection
     * @param \Magento\Framework\Logger\Monolog|null                             $logger
     *
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function __construct(
        \Magento\Framework\Filesystem $filesystem,
        \SM\Sales\Helper\Data $orderHelper,
        \Trans\Sprint\Model\ResourceModel\SprintResponse\CollectionFactory $sprintResponseCollFact,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
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
        $this->timezone = $timezone;
        $this->sprintResponseCollFact = $sprintResponseCollFact;
        $this->orderHelper = $orderHelper;
    }

    public function process()
    {
        /** @var \Trans\Sprint\Model\SprintResponse $item */
        foreach ($this->getRecords() as $item) {
            try {
                if ($this->createNotification($item)) {
                    $event = self::EVENT_NAME;
                    $this->connection->delete(
                        \SM\Notification\Model\ResourceModel\TriggerEvent::TABLE_NAME,
                        "event_id = '{$item->getData('order_id')}' AND event_type = 'order' AND event_name = '{$event}'"
                    );
                    $this->connection->insert(
                        \SM\Notification\Model\ResourceModel\TriggerEvent::TABLE_NAME,
                        [
                            'event_id'   => $item->getData('order_id'),
                            'event_type' => 'order',
                            'event_name' => $event
                        ]
                    );
                } else {
                    $this->logger->error(
                        "Notification Payment Expiring Soon create failed: \n\t Order Not Found",
                        $item->getData()
                    );
                }
            } catch (\Exception $e) {
                $this->logger->error(
                    "Notification Payment Expiring Soon create failed: \n\t" . $e->getMessage(),
                    $e->getTrace()
                );
            }
        }
    }

    /**
     * @param \Trans\Sprint\Model\SprintResponse $sprintItem
     *
     * @return array
     * @throws \Exception
     */
    protected function createNotification($sprintItem)
    {
        $order = $this->orderHelper->getOrderById($sprintItem->getData('order_id'));

        if (!$order) {
            return null;
        }

        $expireDate = new \DateTime($sprintItem->getExpireDate());
        if (strtolower($sprintItem->getData('store_code')) === 'id_id') {
            $expireDate = $expireDate->format('d/m/Y H.i') . ' WIB';
        } else {
            $expireDate = $expireDate->format('d/m/Y h.i A');
        }

        $title = "Hurry, It's time to make your payment.";
        $message = 'Order %1 is waiting for your payment. The time is due by %2.';
        $params = [
            'content' => [
                $sprintItem->getData('increment_id'),
                $expireDate
            ]
        ];

        /** @var \SM\Notification\Model\Notification $notification */
        $notification = $this->notificationFactory->create();
        $notification->setTitle($title)
            ->setContent($message)
            ->setEvent(\SM\Notification\Model\Notification::EVENT_ORDER_STATUS)
            ->setSubEvent(\SM\Notification\Model\Notification::EVENT_ORDER_STATUS)
            ->setCustomerIds([$order->getCustomerId()])
            ->setRedirectType(\SM\Notification\Model\Source\RedirectType::TYPE_ORDER_DETAIL)
            ->setRedirectId($order->getData('parent_order') ? $order->getData('parent_order') : $order->getEntityId())
            ->setParams($params);

        // Emulation store view
        $this->emulation->startEnvironmentEmulation(
            $order->getStoreId(),
            \Magento\Framework\App\Area::AREA_FRONTEND
        );

        $notification->setPushTitle(__($title)->__toString())
            ->setPushContent(__($message, $params['content'])->__toString());

        $this->emulation->stopEnvironmentEmulation(); // End Emulation

        $this->notificationResource->save($notification);

        return $notification->getId();
    }

    /**
     * @return \Trans\Sprint\Model\ResourceModel\SprintResponse\Collection|array
     */
    protected function getRecords()
    {
        $allowMethods = $this->configHelper->getVaPaymentList();

        if (empty($allowMethods)) {
            return [];
        }

        $stepTime = $this->configHelper->getVaPaymentExpiringMinute();
        if ($stepTime < 1) {
            return [];
        }

        $date = $this->timezone->date(new \DateTime());
        $current = $date->format('Y-m-d H:i:s');
        $date->modify("+{$stepTime} minute");
        $date = $this->timezone->date($date);
        $expired = $date->format('Y-m-d H:i:s');

        $sprintIdsSelect = $this->connection->select()
            ->from('sprint_response', ['MAX(id)'])
            ->group('quote_id');

        $coll = $this->sprintResponseCollFact->create();
        $coll->addFieldToFilter('expire_date', ['lteq' => $expired])
            ->addFieldToFilter('expire_date', ['gteq' => $current])
            ->addFieldToFilter('insert_status', '00')
            ->addFieldToFilter('flag', 'pending')
            ->addFieldToFilter('payment_method', ['in' => $allowMethods]);
        $coll->getSelect()
            ->joinInner(
                ['s' => 'store'],
                'main_table.store_id = s.store_id',
                ['store_code' => 's.code']
            )->joinInner(
                ['o' => 'sales_order'],
                "o.reference_number = main_table.transaction_no AND o.status = 'pending_payment'",
                [
                    'increment_id' => 'o.increment_id',
                    'order_id'     => 'o.entity_id',
                    'customer_id'  => 'o.customer_id',
                ]
            )->joinLeft(
                ['n' => \SM\Notification\Model\ResourceModel\TriggerEvent::TABLE_NAME],
                "o.entity_id = n.event_id AND n.event_name = '" . self::EVENT_NAME . "'",
                []
            )->where(
                'n.event_id IS NULL'
            )->where(
                'o.is_parent = ?',
                1
            )->where(
                'main_table.id IN (' . $sprintIdsSelect->__toString() . ')'
            );

        try {
            return $coll->getItems();
        } catch (\Exception $e) {
            $this->logger->error("Notification Payment Expiring Soon create failed: \n\t" . $e->getMessage());

            return [];
        }
    }

    /**
     * @return string
     */
    protected function getLockFileName()
    {
        return 'sm_notification_va_expiring.lock';
    }
}
