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
        return array_values($this->getTreeEvent());
    }

    /**
     * @return array[]
     */
    public function getTreeEvent()
    {
        return [
            Notification::EVENT_UPDATE => [
                'label' => __('METRO Updates'),
                'value' => [
                    [
                        'label' => __('Promo & Event'),
                        'value' => Notification::EVENT_PROMO_AND_EVENT,
                    ],
                    [
                        'label' => __('Information'),
                        'value' => Notification::EVENT_INFO,
                    ],
                ],
            ],
            Notification::EVENT_ORDER_STATUS => [
                'label' => __('Order'),
                'value' => [
                    [
                        'label' => __('Order Status'),
                        'value' => Notification::EVENT_ORDER_STATUS,
                    ]
                ],
            ],
            Notification::EVENT_SERVICE => [
                'label' => __('Services'),
                'value' => [
                    [
                        'label' => __('Subscription'),
                        'value' => Notification::EVENT_SUBSCRIPTION,
                    ],
                    [
                        'label' => __('Reorder Quickly'),
                        'value' => Notification::EVENT_REORDER,
                    ],
                    [
                        'label' => __('Chat Recap'),
                        'value' => Notification::EVENT_CHAT_RECAP,
                    ],
                    [
                        'label' => __('Unknown Device'),
                        'value' => Notification::EVENT_UNKNOWN_DEVICE,
                    ],
                ]
            ]
        ];
    }
}
