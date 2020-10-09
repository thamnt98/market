<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Notification
 *
 * Date: June, 30 2020
 * Time: 6:33 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Notification\Ui\Component\Form\Button;

class Save extends Back
{
    public function getButtonData()
    {
        return [
            'label'      => __('Save'),
            'data_attribute' => [
                'mage-init' => ['button' => ['event' => 'save', 'target' => '#edit_form']],
                'form-role' => 'save',
            ],
            'class'      => 'save primary',
            'sort_order' => 30,
        ];
    }
}
