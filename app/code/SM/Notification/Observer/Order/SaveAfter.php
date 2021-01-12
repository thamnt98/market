<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Notification
 *
 * Date: July, 07 2020
 * Time: 10:42 AM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Notification\Observer\Order;

use SM\DigitalProduct\Api\Data\Order\DigitalProductInterface as Digital;
use SM\DigitalProduct\Model\Cart\Data\Digital as DigitalProduct;
use SM\Notification\Helper\Data as Helper;

class SaveAfter implements \Magento\Framework\Event\ObserverInterface
{
    const TRIGGER_EVENT_ORDER_READY_TO_PICKUP = 'order_ready_to_pickup';
    const TRIGGER_EVENT_ORDER_COMPLETE        = 'order_complete';

    /**
     * @var \SM\Notification\Model\NotificationFactory
     */
    protected $notifyFactory;

    /**
     * @var \Magento\Framework\Logger\Monolog|null
     */
    protected $logger;

    /**
     * @var \SM\Notification\Model\TriggerEventFactory
     */
    protected $triggerEventFactory;

    /**
     * @var \SM\Sales\Helper\Data
     */
    protected $orderHelper;

    /**
     * @var \SM\Notification\Model\ResourceModel\Notification
     */
    protected $notificationResource;

    /**
     * @var Helper
     */
    protected $helper;

    /**
     * @var \SM\Notification\Model\EventSetting
     */
    protected $setting;

    /**
     * @var \Magento\Store\Model\App\Emulation
     */
    protected $emulation;

    /**
     * @var \SM\Notification\Helper\Generate\Email
     */
    protected $emailHelper;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \SM\Sales\Model\OrderInstallation
     */
    protected $orderInstallation;

    /**
     * SaveAfter constructor.
     * @param Helper $helper
     * @param \Magento\Store\Model\App\Emulation $emulation
     * @param \SM\Sales\Helper\Data $orderHelper
     * @param \SM\Notification\Model\EventSetting $setting
     * @param \SM\Notification\Model\TriggerEventFactory $triggerEventFactory
     * @param \SM\Notification\Model\NotificationFactory $notifyFactory
     * @param \SM\Notification\Model\ResourceModel\Notification $notificationResource
     * @param \SM\Notification\Helper\Generate\Email $emailHelper
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \SM\Sales\Model\OrderInstallation $orderInstallation
     * @param \Magento\Framework\Logger\Monolog|null $logger
     */
    public function __construct(
        Helper $helper,
        \Magento\Store\Model\App\Emulation $emulation,
        \SM\Sales\Helper\Data $orderHelper,
        \SM\Notification\Model\EventSetting $setting,
        \SM\Notification\Model\TriggerEventFactory $triggerEventFactory,
        \SM\Notification\Model\NotificationFactory $notifyFactory,
        \SM\Notification\Model\ResourceModel\Notification $notificationResource,
        \SM\Notification\Helper\Generate\Email $emailHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \SM\Sales\Model\OrderInstallation $orderInstallation,
        \Magento\Framework\Logger\Monolog $logger = null
    ) {
        $this->notifyFactory = $notifyFactory;
        $this->logger = $logger;
        $this->triggerEventFactory = $triggerEventFactory;
        $this->orderHelper = $orderHelper;
        $this->notificationResource = $notificationResource;
        $this->helper = $helper;
        $this->setting = $setting;
        $this->emailHelper = $emailHelper;
        $this->storeManager = $storeManager;
        $this->orderInstallation = $orderInstallation;
        $this->emulation = $emulation;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return int
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getEvent()->getData('order');
        $status = $order->getStatus();

        try {
            if ($order->getIsVirtual()) {
                return $this->createNotifyForDigital($order);
            } elseif ($order->getData('is_parent') ||
                !$order->getCustomerId() ||
                $status === $order->getOrigData(\Magento\Sales\Model\Order::STATUS)
            ) {
                return 0;
            }

            $event = '';
            $this->setting->init($order->getCustomerId(), \SM\Notification\Model\Notification::EVENT_ORDER_STATUS);
            switch ($status) {
                case \Magento\Sales\Model\Order::STATE_COMPLETE:
                    $event = self::TRIGGER_EVENT_ORDER_COMPLETE;
                    $notifyId = $this->generateCompletedData($order);
                    break;
                case $this->orderHelper->getReadyToPickupStatus():
                    $event = self::TRIGGER_EVENT_ORDER_READY_TO_PICKUP;
                    $notifyId = $this->generateReadyToPickupData($order);
                    break;
                case $this->orderHelper->getDeliveredStatus():
                    $notifyId = $this->generateDeliveredData($order);
                    break;
                case $this->orderHelper->getInDeliveryStatus():
                    $notifyId = $this->generateInDeliveryData($order);
                    break;
                case $this->orderHelper->getFailedDeliveryStatus():
                    $notifyId = $this->generateFailedDeliveredData($order);
                    break;
                case $this->orderHelper->getInProcessWaitingForPickUpStatus():
                    $notifyId = $this->generateInProcessWaitingForPickUpData($order);
                    break;
                default:
                    $notifyId = 0;
            }

            if ($notifyId && $event) {
                $this->createTriggerEvent($order->getId(), $event);
            }
        } catch (\Exception $e) {
            if ($this->logger) {
                $this->logger->error(
                    "Cannot save notification : order status - {$status}\n" . $e->getMessage(),
                    $e->getTrace()
                );
            }
        }
    }

    public function createTriggerEvent($orderId, $event)
    {
        try {
            /** @var \SM\Notification\Model\TriggerEvent $trigger */
            $trigger = $this->triggerEventFactory->create();
            $trigger->setData([
                'event_id'   => $orderId,
                'event_type' => 'order',
                'event_name' => $event,
            ])->save();
        } catch (\Exception $e) {
            if ($this->logger) {
                $this->logger->error(
                    "Cannot save trigger event : {$event}\n" . $e->getMessage(),
                    $e->getTrace()
                );
            }
        }
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     *
     * @return int
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function generateCompletedData($order)
    {
        /** @var \SM\Notification\Model\ResourceModel\TriggerEvent\Collection $triggerColl */
        $triggerColl = $this->triggerEventFactory->create()->getCollection();
        $triggerColl->addFieldToFilter('event_name', self::TRIGGER_EVENT_ORDER_COMPLETE)
            ->addFieldToFilter('event_type', 'order')
            ->addFieldToFilter('event_id', $order->getId());

        if ($triggerColl->count()) {
            return 0;
        }

        $title = 'Order %1 has been completed!';
        $content = 'Hope you love your shopping experience with Transmart.';
        $params = [
            'title' => [
                $order->getData('reference_order_id'),
            ],
        ];

        $notify = $this->initNotification($order);
        $notify->setTitle($title)
            ->setContent($content)
            ->setImage(
                $this->helper->getMediaPathImage(Helper::XML_IMAGE_ORDER_STATUS_COMPLETED, $order->getStoreId())
            )->setParams($params);
        $this->addNotificationAdditional($notify, $order);

        $this->notificationResource->save($notify);

        return $notify->getId();
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     *
     * @return int
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function generateInDeliveryData($order)
    {
        if ($order->getShippingMethod() === \SM\Checkout\Model\MultiShippingHandle::STORE_PICK_UP) {
            return 0;
        }

        $title = 'Order %1 is on its way!';
        $content = 'Check the delivery status in My Order page.';
        $params = [
            'title' => [
                $order->getData('reference_order_id'),
            ],
        ];

        $notify = $this->initNotification($order);
        $notify->setTitle($title)
            ->setContent($content)
            ->setImage(
                $this->helper->getMediaPathImage(
                    Helper::XML_IMAGE_ORDER_STATUS_IN_DELIVERY,
                    $order->getStoreId()
                )
            )->setParams($params);
        $this->addNotificationAdditional($notify, $order);

        $this->notificationResource->save($notify);

        return $notify->getId();
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     *
     * @return int
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function generateDeliveredData($order)
    {
        if ($order->getShippingMethod() !== \SM\Checkout\Model\MultiShippingHandle::STORE_PICK_UP) {
            $title = '%1, order %2 has been delivered to your address.';
            $content = 'Tap here to confirm your order delivery.';
            $params = [
                'title'   => [
                    $order->getCustomerName(),
                    $order->getData('reference_order_id')
                ],
            ];
        } else {
            $source = $this->orderHelper->getOrderStorePickup($order);
            $title = '%1, thank you for picking up order %2 at %3 Enjoy your products!';
            $content = '';
            $params = [
                'title' => [
                    $order->getCustomerName(),
                    $order->getData('reference_order_id'),
                    $source ? $source->getName() : '',
                ],
            ];
        }

        $notify = $this->initNotification($order);
        $notify->setTitle($title)
            ->setContent($content)
            ->setImage($this->helper->getMediaPathImage(Helper::XML_IMAGE_ORDER_STATUS_DELIVERED, $order->getStoreId()))
            ->setParams($params);
        $this->addNotificationAdditional($notify, $order);

        $this->notificationResource->save($notify);

        return $notify->getId();
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     *
     * @return int
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function generateReadyToPickupData($order)
    {
        /** @var \SM\Notification\Model\ResourceModel\TriggerEvent\Collection $triggerColl */
        $triggerColl = $this->triggerEventFactory->create()->getCollection();
        $triggerColl->addFieldToFilter('event_name', self::TRIGGER_EVENT_ORDER_READY_TO_PICKUP)
            ->addFieldToFilter('event_type', 'order')
            ->addFieldToFilter('event_id', $order->getId());

        if ($order->getShippingMethod() !== \SM\Checkout\Model\MultiShippingHandle::STORE_PICK_UP ||
            $triggerColl->count()
        ) {
            return 0;
        }

        $source = $this->orderHelper->getOrderStorePickup($order);
        $title = '%1, your order is ready to be picked up.';
        $content = 'Please visit %1 to collect order %2.';
        $params = [
            'title'   => [
                $order->getCustomerName(),
            ],
            'content' => [
                $source ? $source->getName() : '',
                $order->getData('reference_order_id'),
            ],
        ];

        $notify = $this->initNotification($order);
        $notify->setTitle($title)
            ->setContent($content)
            ->setImage(
                $this->helper->getMediaPathImage(
                    Helper::XML_IMAGE_ORDER_STATUS_READY_TO_PICKUP,
                    $order->getStoreId()
                )
            )->setParams($params);
        $this->addNotificationAdditional($notify, $order);

        $this->notificationResource->save($notify);

        return $notify->getId();
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     *
     * @return int
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function generateFailedDeliveredData($order)
    {
        $title = 'Sorry, we had to cancel order %1.';
        $content = 'Your order delivery is unsuccessful. Find more info here.';
        $params = [
            'title'   => [
                $order->getData('reference_order_id')
            ]
        ];

        $notify = $this->initNotification($order);
        $notify->setTitle($title)
            ->setContent($content)
            ->setImage($this->helper->getMediaPathImage(Helper::XML_IMAGE_ORDER_STATUS_DELIVERED, $order->getStoreId()))
            ->setParams($params);
        $this->addNotificationAdditional(
            $notify,
            $order,
            $this->emailHelper->getFailedDeliveryTemplateId($order->getStoreId())
        );
        $this->notificationResource->save($notify);

        return $notify->getId();
    }

    /**
     * @param $order
     *
     * @return boolean
     */
    public function generateInProcessWaitingForPickUpData($order)
    {
        try {
            $this->orderInstallation->sendMail($order);
        } catch (\Exception $e) {
        }

        return true;
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     *
     * @return int
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function createNotifyForDigital($order)
    {
        if (!$order->getData('digital_transaction_fail') ||
            $order->getData('digital_transaction_fail') == $order->getOrigData('digital_transaction_fail')
        ) {
            return 0;
        }

        /** @var \Magento\Sales\Model\Order\Item $item */
        $item = $order->getItemsCollection()->getFirstItem();
        $buyRequest = $item->getProductOptionByCode('info_buyRequest') ?? [];
        if (empty($buyRequest[Digital::DIGITAL])) {
            return 0;
        }

        $title = 'Sorry, your %1 transaction has been cancelled.';
        $content = 'Your bill payment %1 failed due to system problem.' .
            ' We will refund your payment in nx24 hours';
        $params = [
            'title'   => [
                $buyRequest[Digital::DIGITAL][DigitalProduct::SERVICE_TYPE] ?? '',
            ],
            'content' => [
                $order->getData('reference_order_id'),
            ],
        ];

        $notify = $this->initNotification($order);
        $notify->setTitle($title)
            ->setContent($content)
            ->setParams($params);
        $this->addNotificationAdditional($notify, $order);

        $this->notificationResource->save($notify);

        return (int) $notify->getId();
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     *
     * @return \SM\Notification\Model\Notification
     */
    protected function initNotification($order)
    {
        /** @var \SM\Notification\Model\Notification $notification */
        $notification = $this->notifyFactory->create();
        $notification->setEvent(\SM\Notification\Model\Notification::EVENT_ORDER_STATUS)
            ->setSubEvent(\SM\Notification\Model\Notification::EVENT_ORDER_STATUS)
            ->setCustomerIds([$order->getCustomerId()])
            ->setRedirectId($order->getData('parent_order') ? $order->getData('parent_order') : $order->getId())
            ->setRedirectType(\SM\Notification\Model\Source\RedirectType::TYPE_ORDER_DETAIL);

        return $notification;
    }

    /**
     * @param \SM\Notification\Model\Notification $notify
     * @param \Magento\Sales\Model\Order          $order
     * @param int|string|null                     $email
     * @param string|null                         $sms
     */
    protected function addNotificationAdditional($notify, $order, $email = null, $sms = null)
    {
        $this->emulation->startEnvironmentEmulation(
            $order->getStoreId(),
            \Magento\Framework\App\Area::AREA_FRONTEND,
            true
        );

        $params = $notify->getParams() ?: [];
        if ($this->setting->isPush()) {
            $notify->setPushTitle(__($notify->getTitle(), $params['title'] ?? [])->__toString())
                ->setPushContent(__($notify->getContent(), $params['content'] ?? [])->__toString());
        }

        if ($this->setting->isEmail() && $email) {
            $notify->setEmailTemplate($email)
                ->setEmailParams([
                    \SM\Notification\Model\Notification\Consumer\Email::EMAIL_PARAM_ORDER_KEY => $order->getId(),
                    \SM\Notification\Model\Notification\Consumer\Email::EMAIL_PARAM_CONTACT_US_LINK => $this->storeManager->getStore()->getUrl('help/contactus')
                ]);
        }

        if ($this->setting->isSms() && $sms) {
            $notify->setSms(__($sms)->__toString());
        }

        $this->emulation->stopEnvironmentEmulation();
    }
}
