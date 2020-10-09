<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\Help\Block\Adminhtml\Topic\Edit;

use Magento\Ui\Component\Control\Container;

/**
 * Class SaveButton
 * @package SM\Help\Block\Adminhtml\Topic\Edit
 */
class SaveButton extends \SM\Help\Block\Adminhtml\Topic\Edit\GenericButton
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
                                'targetName' => 'sm_help_topic_form.sm_help_topic_form',
                                'actionName' => 'save',
                                'params' => [
                                    true,
                                    [
                                        'store_id' => $this->context->getRequestParam('store', 0),
                                        'back' => 'continue'
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
                'label' => __('Save & Duplicate'),
                'id_hard' => 'save_and_duplicate',
                'data_attribute' => [
                    'mage-init' => [
                        'buttonAdapter' => [
                            'actions' => [
                                [
                                    'targetName' => 'sm_help_topic_form.sm_help_topic_form',
                                    'actionName' => 'save',
                                    'params' => [
                                        true,
                                        [
                                            'store_id' => $this->context->getRequestParam('store', 0),
                                            'back' => 'duplicate',
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            [
                'id_hard' => 'save_and_close',
                'label' => __('Save & Close'),
                'data_attribute' => [
                    'mage-init' => [
                        'buttonAdapter' => [
                            'actions' => [
                                [
                                    'targetName' => 'sm_help_topic_form.sm_help_topic_form',
                                    'actionName' => 'save',
                                    'params' => [
                                        true,
                                        [
                                            'store_id' => $this->context->getRequestParam('store', 0),
                                            'back' => 'close',
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
