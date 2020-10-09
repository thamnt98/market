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

class Duplicate extends Back
{
    public function getButtonData()
    {
        $id = (int)$this->context->getRequestParam('id');

        return [
            'label'      => __('Duplicate'),
            'on_click'   => sprintf(
                "location.href = '%s';",
                $this->context->getUrl(
                    '*/*/new',
                    [
                        'id' => $this->context->getRequestParam('id'),
                    ]
                )
            ),
            'class'      => 'primary',
            'sort_order' => 30,
        ];
    }
}
