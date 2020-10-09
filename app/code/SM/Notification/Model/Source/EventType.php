<?php

namespace SM\Notification\Model\Source;

use SM\Notification\Model\Notification;

class EventType implements \Magento\Framework\Data\OptionSourceInterface
{

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'label' => __('Order Status'),
                'value' => Notification::EVENT_ORDER_STATUS,
            ],
            [
                'label' => __('Transmart Updates'),
                'value' => Notification::EVENT_UPDATE,
            ],
            [
                'label' => __('Services'),
                'value' => Notification::EVENT_SERVICE,
            ],
            [
                'label' => __('Chat Recap'),
                'value' => Notification::EVENT_CHAT_RECAP,
            ],
            [
                'label' => __('My Appointment'),
                'value' => Notification::EVENT_MY_APPOINTMENT,
            ],
            [
                'label' => __('Promotion'),
                'value' => Notification::EVENT_PROMO,
            ],
            [
                'label' => __('Subscription'),
                'value' => Notification::EVENT_SUBSCRIPTION,
            ],
            [
                'label' => __('Unknown Device'),
                'value' => Notification::EVENT_UNKNOWN_DEVICE,
            ],
        ];
    }
}
