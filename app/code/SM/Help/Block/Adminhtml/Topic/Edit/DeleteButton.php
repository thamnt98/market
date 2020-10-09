<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\Help\Block\Adminhtml\Topic\Edit;

/**
 * Class DeleteButton
 * @package SM\Help\Block\Adminhtml\Topic\Edit
 */
class DeleteButton extends GenericButton
{
    /**
     * Get Button Data
     *
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Delete'),
            'on_click' => sprintf("location.href = '%s';", $this->getDeleteUrl()),
            'class' => 'delete',
            'sort_order' => 20
        ];
    }
    /**
     * Get URL for delete
     *
     * @return string
     */
    private function getDeleteUrl()
    {
        return $this->getUrl(
            'sm_help/topic/delete',
            ['topic_id' => $this->context->getRequestParam('topic_id')]
        );
    }
}
