<?php
/**
 * @category SM
 * @package SM_Catalog
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 * @author Logan Ta <phuongtt2@smartosc.com>
 *
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

declare(strict_types=1);

namespace SM\Catalog\Model\Source\Delivery;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Method
 * @package SM\Catalog\Model\Source\Delivery
 */
class Method implements OptionSourceInterface
{
    //Default option
    const REGULAR = 1;
    const SAME_DAY = 2;
    const SCHEDULED = 3;
    const NEXT_DAY = 4;

    /**
     * to option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::SAME_DAY,
                'label' => __('Instant (3 hours)')
            ],
            [
                'value' => self::NEXT_DAY,
                'label' => __('Next day (1 day)')
            ],
            [
                'value' => self::REGULAR,
                'label' => __('Regular (2-7 days)')
            ],
            [
                'value' => self::SCHEDULED,
                'label' => __('Schedule for Later')
            ]
        ];
    }
}
