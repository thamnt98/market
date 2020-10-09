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
     * @var \SM\Notification\Helper\CustomerSetting
     */
    protected $settingHelper;

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
     * SaveAfter constructor.
     *
     * @param Helper                                            $helper
     * @param \SM\Sales\Helper\Data                             $orderHelper
     * @param \SM\Notification\Model\TriggerEventFactory        $triggerEventFactory
     * @param \SM\Notification\Helper\CustomerSetting           $settingHelper
     * @param \SM\Notification\Model\NotificationFactory        $notifyFactory
     * @param \SM\Notification\Model\ResourceModel\Notification $notificationResource
     * @param \Magento\Framework\Logger\Monolog|null            $logger
     */
    public function __construct(
        Helper $helper,
        \SM\Sales\Helper\Data $orderHelper,
        \SM\Notification\Model\TriggerEventFactory $triggerEventFactory,
        \SM\Notification\Helper\CustomerSetting $settingHelper,
        \SM\Notification\Model\NotificationFactory $notifyFactory,
        \SM\Notification\Model\ResourceModel\Notification $notificationResource,
        \Magento\Framework\Logger\Monolog $logger = null
    ) {
        $this->settingHelper = $settingHelper;
        $this->notifyFactory = $notifyFactory;
        $this->logger = $logger;
        $this->triggerEventFactory = $triggerEventFactory;
        $this->orderHelper = $orderHelper;
        $this->notificationResource = $notificationResource;
        $this->helper = $helper;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
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
                $status === $order->getOrigData(\Magento\Sales\Model\Order::STATUS)
            ) {
                return;
            }

            $event = '';
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
//            case $this->orderHelper->getInProcessStatus():
//                $notifyId = this->generateInProcessData($order);
//                break;
                default:
                    $notifyId = 0;
            }

            if ($notifyId && $event) {
                $this->createTriggerEvent($order->getId(), $event);
            }
        } catch (\Exception $e) {
            if ($this->logger) {
                $this->logger->error("Cannot save notification : order status - {$status}\n" . $e->getMessage());
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
                $this->logger->error("Cannot save trigger event : {$event}\n" . $e->getMessage());
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

        /** @var \SM\Notification\Model\Notification $notify */
        $notify = $this->notifyFactory->create();
        $notify->setTitle('Order ID/%1 has been completed!')
            ->setContent('Hope you love your shopping experience with Transmart.')
            ->setImage($this->helper->getMediaPathImage(Helper::XML_IMAGE_ORDER_STATUS_COMPLETED, $order->getStoreId()))
            ->setEvent(\SM\Notification\Model\Notification::EVENT_ORDER_STATUS)
            ->setPushTitle(__('Order ID/%1 has been completed!', [$order->getIncrementId()]))
            ->setPushContent(__('Hope you love your shopping experience with Transmart.'))
            ->setCustomerIds([$order->getCustomerId()])
            ->setRedirectType(\SM\Notification\Model\Source\RedirectType::TYPE_ORDER_DETAIL)
            ->setRedirectId($order->getData('parent_order') ? $order->getData('parent_order') : $order->getId())
            ->setParams([
                'title' => [
                    $order->getIncrementId(),
                ],
            ]);

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

        /** @var \SM\Notification\Model\Notification $notify */
        $notify = $this->notifyFactory->create();
        $notify->setTitle('Order ID/%1 is on its way!')
            ->setContent('Check the delivery status in My Order page.')
            ->setEvent(\SM\Notification\Model\Notification::EVENT_ORDER_STATUS)
            ->setPushTitle(__('Order ID/%1 is on its way!', [$order->getIncrementId()]))
            ->setPushContent(__('Check the delivery status in My Order page.'))
            ->setCustomerIds([$order->getCustomerId()])
            ->setRedirectType(\SM\Notification\Model\Source\RedirectType::TYPE_ORDER_DETAIL)
            ->setRedirectId($order->getData('parent_order') ? $order->getData('parent_order') : $order->getId())
            ->setImage(
                $this->helper->getMediaPathImage(
                    Helper::XML_IMAGE_ORDER_STATUS_IN_DELIVERY,
                    $order->getStoreId()
                )
            )->setParams([
                'title' => [
                    $order->getIncrementId(),
                ],
            ]);

        $this->notificationResource->save($notify);
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
            $title = '%1, order ID/%2 has been delivered to your address';
            $content = 'Tap here to confirm your order delivery.';
            $params = [
                'title'   => [
                    $order->getCustomerName(),
                    $order->getIncrementId()
                ],
            ];
        } else {
            $source = $this->orderHelper->getOrderStorePickup($order);
            $title = '%1, thank you for picking up order ID/%2 at %3 Enjoy your products!';
            $content = '';
            $params = [
                'title' => [
                    $order->getCustomerName(),
                    $order->getIncrementId(),
                    $source ? $source->getName() : '',
                ],
            ];
        }

        /** @var \SM\Notification\Model\Notification $notify */
        $notify = $this->notifyFactory->create();
        $notify->setTitle($title)
            ->setEvent(\SM\Notification\Model\Notification::EVENT_ORDER_STATUS)
            ->setPushTitle(__($title, $params['title']))
            ->setContent($content)
            ->setImage($this->helper->getMediaPathImage(Helper::XML_IMAGE_ORDER_STATUS_DELIVERED, $order->getStoreId()))
            ->setPushContent(__($content, $params['content'] ?? []))
            ->setCustomerIds([$order->getCustomerId()])
            ->setRedirectType(\SM\Notification\Model\Source\RedirectType::TYPE_ORDER_DETAIL)
            ->setRedirectId($order->getData('parent_order') ? $order->getData('parent_order') : $order->getId())
            ->setParams($params);

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

        /** @var \SM\Notification\Model\Notification $notify */
        $notify = $this->notifyFactory->create();
        $source = $this->orderHelper->getOrderStorePickup($order);
        $notify->setTitle('%1, your order is ready to be picked up.')
            ->setContent('Please visit %1 to collect order ID/%2.')
            ->setEvent(\SM\Notification\Model\Notification::EVENT_ORDER_STATUS)
            ->setPushTitle(__('%1, your order is ready to be picked up.', [$order->getCustomerName()]))
            ->setPushContent(__(
                'Please visit %1 to collect order ID/%2.',
                [
                    $source ? $source->getName() : '',
                    $order->getIncrementId(),
                ]
            ))
            ->setCustomerIds([$order->getCustomerId()])
            ->setRedirectType(\SM\Notification\Model\Source\RedirectType::TYPE_ORDER_DETAIL)
            ->setRedirectId($order->getData('parent_order') ? $order->getData('parent_order') : $order->getId())
            ->setImage(
                $this->helper->getMediaPathImage(
                    Helper::XML_IMAGE_ORDER_STATUS_READY_TO_PICKUP,
                    $order->getStoreId()
                )
            )->setParams([
                'title'   => [
                    $order->getCustomerName(),
                ],
                'content' => [
                    $source ? $source->getName() : '',
                    $order->getIncrementId(),
                ],
            ]);

        $this->notificationResource->save($notify);

        return $notify->getId();
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     *
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function createNotifyForDigital($order)
    {
        if (!$order->getData('digital_transaction_fail') ||
            $order->getData('digital_transaction_fail') == $order->getOrigData('digital_transaction_fail')
        ) {
            return;
        }

        /** @var \Magento\Sales\Model\Order\Item $item */
        $item = $order->getItemsCollection()->getFirstItem();
        $buyRequest = $item->getProductOptionByCode('info_buyRequest') ?? [];
        if (empty($buyRequest[Digital::DIGITAL])) {
            return;
        }

        /** @var \SM\Notification\Model\Notification $notify */
        $notify = $this->notifyFactory->create();
        $notify->setTitle('Sorry, your %1 transaction has been cancelled.')
            ->setContent(
                "Your bill payment ID/%1 failed due to system problem." .
                " We will refund your payment in nx24 hours"
            )
            ->setEvent(\SM\Notification\Model\Notification::EVENT_ORDER_STATUS)
            ->setPushTitle(__(
                'Sorry, your %1 transaction has been cancelled.',
                [$buyRequest[Digital::DIGITAL][DigitalProduct::SERVICE_TYPE] ?? '']
            ))
            ->setPushContent(__(
                "Your bill payment ID/%1 failed due to system problem." .
                " We will refund your payment in nx24 hours",
                [
                    $order->getIncrementId(),
                ]
            ))
            ->setCustomerIds([$order->getCustomerId()])
            ->setRedirectType(\SM\Notification\Model\Source\RedirectType::TYPE_ORDER_DETAIL)
            ->setRedirectId($order->getData('parent_order') ? $order->getData('parent_order') : $order->getId())
            ->setParams([
                'title'   => [
                    $buyRequest[Digital::DIGITAL][DigitalProduct::SERVICE_TYPE] ?? '',
                ],
                'content' => [
                    $order->getIncrementId(),
                ],
            ]);

        $this->notificationResource->save($notify);
    }
}
