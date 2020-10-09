<?php
/**
 * @category SM
 * @package SM_Theme
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 * @author      Son Nguyen <sonnn@smartosc.com>
 *
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

declare(strict_types=1);

namespace SM\Theme\Model\Config\Source;

class ShowCartOptions implements \Magento\Framework\Data\OptionSourceInterface
{

    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => '2',
                'label' => __('Default')
            ],
            [
                'value' => '1',
                'label' => __('Show')
            ],
            [
                'value' => '0',
                'label' => __('Hide')
            ]
        ];
    }
}
