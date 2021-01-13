<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Notification
 *
 * Date: January, 08 2021
 * Time: 2:20 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Notification\Cron\Order;

class PickupExpired extends RemindPickup
{
    const EVENT_NAME = 'pickup_expired';

    protected function process()
    {
        foreach ($this->getOrderList() as $item) {
            try {
                if ($this->createNotification($item)) {
                    $this->connection->insert(
                        \SM\Notification\Model\ResourceModel\TriggerEvent::TABLE_NAME,
                        [
                            'event_id'   => $item['entity_id'],
                            'event_type' => 'order',
                            'event_name' => self::EVENT_NAME,
                        ]
                    );
                }
            } catch (\Exception $e) {
                $this->logger->error(
                    "Notification Pickup Expired create failed: \n\t" . $e->getMessage(),
                    $e->getTrace()
                );
            }
        }
    }

    /**
     * @override
     *
     * @param array $data
     *
     * @return int|void|null
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    protected function createNotification($data)
    {
        $order = $this->orderHelper->getOrderById($data['entity_id'] ?? 0);

        if (!$order || empty($data['customer_id'])) {
            return null;
        }

        $title = "%1, order %2 has been cancelled.";
        $message = "Sorry, you've gone past the pick up time limit.";
        $params = [
            'title'   => [
                $data['customer_name'] ?? '',
                $data['reference_order_id'] ?? $data['reference_number'] ?? $data['increment_id'] ?? '',
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

        $this->eventSetting->init($data['customer_id'], \SM\Notification\Model\Notification::EVENT_ORDER_STATUS);
        if ($this->eventSetting->isPush()) {
            // Emulation store view
            $this->emulation->startEnvironmentEmulation(
                $order->getStoreId()
            );

            $notification->setPushTitle(__($title, $params['title'])->__toString())
                ->setPushContent(__($message)->__toString());

            $this->emulation->stopEnvironmentEmulation(); // End Emulation
        }

        $this->notificationResource->save($notification);

        return $notification->getId();
    }

    /**
     * @return array
     */
    protected function getOrderList()
    {
        $expiredTime = $this->configHelper->getPickupLimitDay();

        if (!$expiredTime) {
            return [];
        }

        $select = $this->generateSelect(self::EVENT_NAME, $expiredTime);

        return $this->connection->fetchAssoc($select);
    }

    /**
     * @return string
     */
    protected function getLockFileName()
    {
        return 'sm_notification_expired.lock';
    }
}
