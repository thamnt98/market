<?php


namespace SM\Review\Block\Adminhtml\Form\Button;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class Reject
 * @package SM\Review\Block\Adminhtml\Form\Button
 */
class Reject extends GenericButton implements ButtonProviderInterface
{
    /**
     * @return array
     */
    public function getButtonData()
    {
        $id = $this->context->getRequest()->getParam('id');
        if ($id) {
            return [
                'label' => __('Reject'),
                'on_click' => 'deleteConfirm(\'' . __('Are you sure you want to reject this edit ?') . '\', \'' . $this->getRejectUrl() . '\')',
                'class' => 'delete',
                'sort_order' => 20
            ];
        }

        return [];
    }

    /**
     * @return string
     */
    public function getRejectUrl()
    {
        $id = $this->context->getRequest()->getParam('id');
        return $this->getUrl('*/edit/reject', ['id' => $id]);
    }
}
