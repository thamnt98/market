<?php
declare(strict_types=1);

namespace SM\DigitalProduct\Block\Adminhtml\Category\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Ui\Component\Control\Container;

class SaveButton extends GenericButton implements ButtonProviderInterface
{

    /**
     * {@inheritdoc}
     */
    public function getButtonData()
    {
        return [
            'label' => __('Save'),
            'class' => 'save primary',
            'data_attribute' => [
                'mage-init' => [
                    'buttonAdapter' => [
                        'actions' => [
                            [
                                'targetName' => 'sm_digitalproduct_category_form.sm_digitalproduct_category_form',
                                'actionName' => 'save',
                                'params' => [
                                    true,
                                    [
                                        'store_id' => $this->context->getRequestParam('store', 0),
                                        'back' => 'close'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'class_name' => Container::SPLIT_BUTTON,
            'options' => $this->getOptions(),
        ];
    }

    /**
     * Retrieve options
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            [
                'id_hard' => 'save_and_close',
                'label' => __('Save & Continue'),
                'data_attribute' => [
                    'mage-init' => [
                        'buttonAdapter' => [
                            'actions' => [
                                [
                                    'targetName' => 'sm_digitalproduct_category_form.sm_digitalproduct_category_form',
                                    'actionName' => 'save',
                                    'params' => [
                                        true,
                                        [
                                            'store_id' => $this->context->getRequestParam('store', 0),
                                            'back' => 'continue',
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }
}

