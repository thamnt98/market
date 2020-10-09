<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\InspireMe\Ui\Category\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Status
 * @package SM\InspireMe\Ui\Category\Source
 */
class Status implements OptionSourceInterface
{
    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        return [
            [
                'label' => __('Disabled'),
                'value' => 0,
            ],
            [
                'label' => __('Enabled'),
                'value' => 1,
            ],
        ];
    }
}
