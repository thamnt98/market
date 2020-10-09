<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\InspireMe\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class HomepagePosition
 * @package SM\InspireMe\Model\Config\Source
 */
class HomepagePosition implements OptionSourceInterface
{
    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'selected', 'label' => __('Selected Article')],
            ['value' => 'most_view', 'label' => __('Most Viewed')],
            ['value' => 'recent_upload', 'label' => __('Recently Uploaded')],
        ];
    }
}
