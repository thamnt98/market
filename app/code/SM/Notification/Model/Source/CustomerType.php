<?php

namespace SM\Notification\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

class CustomerType implements OptionSourceInterface
{
    const TYPE_ALL              = 1;
    const TYPE_CUSTOMER         = 2;
    const TYPE_CUSTOMER_SEGMENT = 3;

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'label' => 'All',
                'value' => self::TYPE_ALL,
            ],
            [
                'label' => 'Specific Customer',
                'value' => self::TYPE_CUSTOMER,
            ],
            [
                'label' => 'Specific Customer Segment',
                'value' => self::TYPE_CUSTOMER_SEGMENT,
            ],
        ];
    }
}
