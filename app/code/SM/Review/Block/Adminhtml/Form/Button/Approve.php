<?php


namespace SM\Review\Block\Adminhtml\Form\Button;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class Approve
 * @package SM\Review\Block\Adminhtml\Form\Button
 */
class Approve extends GenericButton implements ButtonProviderInterface
{
    public function getButtonData()
    {
        $id = $this->context->getRequest()->getParam('id');
        if ($id) {
            return [
                'label' => __('Approve'),
                'on_click' => 'deleteConfirm(\'' . __('Are you sure you want to approve this edit ?') . '\', \'' . $this->getApproveUrl() . '\')',
                'class' => 'save primary',
                'sort_order' => 20
            ];
        }

        return [];
    }

    /**
     * @return string
     */
    public function getApproveUrl()
    {
        $id = $this->context->getRequest()->getParam('id');
        return $this->getUrl('*/edit/approve', ['id' => $id]);
    }
}
