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
     * @param \SM\Notification\Helper\CustomerSetting                    $settingHelper
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
        \SM\Notification\Helper\CustomerSetting $settingHelper,
        \SM\Notification\Model\NotificationFactory $notificationFactory,
        \SM\Notification\Model\ResourceModel\Notification $notificationResource,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Framework\Logger\Monolog $logger
    ) {
        parent::__construct(
            $filesystem,
            $emulation,
            $settingHelper,
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
                $this->logger->error("Notification Payment Expired create failed: \n\t" . $e->getMessage());
            }
        }
    }

    /**
     * @return \Magento\Sales\Api\Data\OrderInterface[]
     */
    protected function getOrders()
    {
        $allowMethods = $this->settingHelper->getConfigValue('sm_notification/generate/va_payment');

        if (empty($allowMethods)) {
            return [];
        }

        $allowMethods = explode(',', $allowMethods);
        $date = $this->timezone->date(new \DateTime());
        $current = $date->format('Y-m-d H:i:s');
        $allowStatus = [
            'canceled',
            'closed',
            $this->settingHelper->getConfigValue(
                \Trans\Sprint\Helper\Config::GENERAL_NEW_ORDER_STATUS,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            ) ?: 'pending',
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
//            ->where('main_table.created_at > ?', '2020-11-20') // not send old order
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
        $content = 'Order ID/%1 has passed the payment due time.';
        $params = [
            'content' => [
                $order->getIncrementId(),
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

        $setting = $this->settingHelper->getCustomerSetting($order->getCustomerId());
        $isSendMail = in_array(
            $this->settingHelper->generateSettingCode(
                \SM\Notification\Model\Notification::EVENT_ORDER_STATUS,
                'email'
            ),
            $setting
        );
        $isPush = in_array(
            $this->settingHelper->generateSettingCode(
                \SM\Notification\Model\Notification::EVENT_ORDER_STATUS,
                'push'
            ),
            $setting
        );
        $isSms = in_array(
            $this->settingHelper->generateSettingCode(
                \SM\Notification\Model\Notification::EVENT_ORDER_STATUS,
                'sms'
            ),
            $setting
        );

        if ($isPush) {
            $notification->setPushTitle(__($title))
                ->setPushContent(__($content, $params['content']));
        }

        if ($isSendMail) {
            // todo email
//            $notification->setEmailTemplate(
//                $this->emailHelper->getExpiredTemplateId($order->getStoreId())
//            )->setEmailParams([
//                \SM\Notification\Model\Notification\Consumer\Email::EMAIL_PARAM_ORDER_KEY => $order->getEntityId(),
//            ]);
        }

        if ($isSms) {
            // todo sms content
//            $notification->setSms('');
        }

        $this->notificationResource->save($notification);

        return $notification->getId();
    }

    protected function getLockFileName()
    {
        return 'sm_notification_va_expired.lock';
    }
}
