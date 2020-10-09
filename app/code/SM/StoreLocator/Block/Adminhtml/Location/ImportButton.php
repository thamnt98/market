<?php

namespace SM\StoreLocator\Block\Adminhtml\Location;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class ImportButton
 * @package SM\StoreLocator\Block\Adminhtml\Location
 */
class ImportButton implements ButtonProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getButtonData()
    {
        return [
            'label'          => __('Import'),
            'class'          => 'save primary',
            'data_attribute' => [
                'mage-init' => [
                    'button' => ['event' => 'save'],
                ],
                'form-role' => 'save',
            ],
            'sort_order'     => 100,
        ];
    }
}
