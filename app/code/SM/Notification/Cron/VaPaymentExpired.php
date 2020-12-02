<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Notification
 *
 * Date: November, 19 2020
 * Time: 5:55 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Notification\Cron;

class VaPaymentExpired extends AbstractGenerate
{
    const EVENT_NAME = 'va_payment_expired';

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;

    /**
     * @var \SM\Notification\Helper\Data
     */
    protected $helper;

    /**
     * @var \SM\Notification\Helper\Generate\Email
     */
    protected $emailHelper;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $orderCollFact;

    /**
     * VaPaymentExpired constructor.
     *
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollFact
     * @param \SM\Notification\Helper\Generate\Email                     $emailHelper
     * @param \SM\Notification\Helper\Data                               $helper
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface       $timezone
     * @param \Magento\Framework\Filesystem                              $filesystem
     * @param \Magento\Store\Model\App\Emulation                         $emulation
     * @param \SM\Notification\Model\EventSetting                        $eventSetting
     * @param \SM\Notification\Helper\Config                             $configHelper
     * @param \SM\Notification\Model\NotificationFactory                 $notificationFactory
     * @param \SM\Notification\Model\ResourceModel\Notification          $notificationResource
     * @param \Magento\Framework\App\ResourceConnection                  $resourceConnection
     * @param \Magento\Framework\Logger\Monolog                          $logger
     *
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function __construct(
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollFact,
        \SM\Notification\Helper\Generate\Email $emailHelper,
        \SM\Notification\Helper\Data $helper,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Framework\Filesystem $filesystem,
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
        $this->helper = $helper;
        $this->emailHelper = $emailHelper;
        $this->orderCollFact = $orderCollFact;
    }

    protected function process()
    {
        foreach ($this->getOrders() as $order) {
            try {
                $this->createNotify($order);
                $this->connection->insert(
                    \SM\Notification\Model\ResourceModel\TriggerEvent::TABLE_NAME,
                    [
                        'event_id'   => $order->getEntityId(),
                        'event_type' => 'order',
                        'event_name' => self::EVENT_NAME
                    ]
                );
            } catch (\Exception $e) {
                $this->logger->error(
                    "Notification Payment Expired create failed: \n\t" . $e->getMessage(),
                    $e->getTrace()
                );
            }
        }
    }

    /**
     * @return \Magento\Sales\Api\Data\OrderInterface[]
     */
    protected function getOrders()
    {
        $allowMethods = $this->configHelper->getVaPaymentList();

        if (empty($allowMethods)) {
            return [];
        }

        $date = $this->timezone->date(new \DateTime());
        $current = $date->format('Y-m-d H:i:s');
        $allowStatus = [
            'canceled',
            'closed',
            $this->configHelper->getOrderPendingStatus() ?: 'pending',
        ];

        $coll = $this->orderCollFact->create();
        $coll->getSelect()
            ->joinInner(['p' => 'sprint_response'], 'main_table.reference_number= p.transaction_no', [])
            ->joinLeft(
                ['n' => \SM\Notification\Model\ResourceModel\TriggerEvent::TABLE_NAME],
                "main_table.entity_id = n.event_id AND n.event_name = '" . self::EVENT_NAME . "'",
                []
            )->where('n.event_id IS NULL')
            ->where("p.payment_method IN ('" . implode("','", $allowMethods) . "')")
            ->where('p.expire_date < ?', $current)
            ->where('main_table.is_parent = ?', 1)
            ->where('main_table.customer_id IS NOT NULL')
            ->where("main_table.status IN ('" . implode("','", $allowStatus) . "')")
            ->limit(100)
            ->group('main_table.entity_id');

        return $coll->getItems();
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderInterface    $order
     *
     * @return int
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    protected function createNotify($order)
    {
        $title = 'Sorry, your order has been cancelled.';
        $content = 'Order %1 has passed the payment due time.';
        $params = [
            'content' => [
                $order->getData('reference_order_id')
                ?? $order->getData('reference_number')
                ?? $order->getIncrementId(),
            ],
        ];
        /** @var \SM\Notification\Model\Notification $notification */
        $notification = $this->notificationFactory->create();
        $notification->setTitle($title)
            ->setEvent(\SM\Notification\Model\Notification::EVENT_ORDER_STATUS)
            ->setSubEvent(\SM\Notification\Model\Notification::EVENT_ORDER_STATUS)
            ->setCustomerIds([$order->getCustomerId()])
            ->setRedirectId($order->getEntityId())
            ->setRedirectType(\SM\Notification\Model\Source\RedirectType::TYPE_ORDER_DETAIL)
            ->setContent($content)
            ->setImage(
                $this->helper->getMediaPathImage(
                    \SM\Notification\Helper\Data::XML_IMAGE_PAYMENT_FAILED,
                    $order->getStoreId()
                )
            )->setParams($params);

        $this->eventSetting->init($order->getCustomerId(), \SM\Notification\Model\Notification::EVENT_ORDER_STATUS);
        if ($this->eventSetting->isPush()) {
            $this->emulation->startEnvironmentEmulation(
                $order->getStoreId(),
                \Magento\Framework\App\Area::AREA_FRONTEND,
                true
            );

            $notification->setPushTitle(__($title)->__toString())
                ->setPushContent(__($content, $params['content'])->__toString());

            $this->emulation->stopEnvironmentEmulation();
        }

        if ($this->eventSetting->isEmail()) {
            // set email
//            $notification->setEmailTemplate(
//                $this->emailHelper->getExpiredTemplateId($order->getStoreId())
//            )->setEmailParams([
//                \SM\Notification\Model\Notification\Consumer\Email::EMAIL_PARAM_ORDER_KEY => $order->getEntityId(),
//            ]);
        }

        if ($this->eventSetting->isSms()) {
            // set sms
        }

        $this->notificationResource->save($notification);

        return $notification->getId();
    }

    protected function getLockFileName()
    {
        return 'sm_notification_va_expired.lock';
    }
}
