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

class Delete extends Back
{
    public function getButtonData()
    {
        $id = (int)$this->context->getRequestParam('id');
        $isDuplicate = (int)$this->context->getRequestParam('is_duplicate');
        if (!$id || ($id && $isDuplicate)) {
            return [];
        }

        return [
            'label'      => __('Delete'),
            'on_click'   => sprintf(
                "location.href = '%s';",
                $this->context->getUrl('notification/index/delete', ['id' => $this->context->getRequestParam('id')])
            ),
            'class'      => 'delete',
            'sort_order' => 20,
        ];
    }
}
