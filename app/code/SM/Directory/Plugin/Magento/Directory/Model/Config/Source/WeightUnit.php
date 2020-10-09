<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\Directory\Plugin\Magento\Directory\Model\Config\Source;

/**
 * Class WeightUnit
 * @package SM\Directory\Plugin\Magento\Directory\Model\Config\Source
 */
class WeightUnit extends \Magento\Directory\Model\Config\Source\WeightUnit
{
    /**
     * @param \Magento\Directory\Model\Config\Source\WeightUnit $subject
     * @param $result
     * @return mixed
     */
    public function afterToOptionArray(
        \Magento\Directory\Model\Config\Source\WeightUnit $subject,
        $result
    ) {
        $result[] = [
            'value' => 'gram',
            'label' => __('grams')
        ];
        return $result;
    }
}
