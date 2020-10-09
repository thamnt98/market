<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\FlashSale\Block\Adminhtml\Event\Edit;

/**
 * Class Form
 * @package SM\FlashSale\Block\Adminhtml\Event\Edit
 */
class Form extends \Magento\CatalogEvent\Block\Adminhtml\Event\Edit\Form
{
    /**
     * @return Form|void
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();
        $form = $this->getForm();

        $fieldset = $form->addFieldset(
            'flash_sale_fieldset',
            ['legend' => __('Flash Sale Information'), 'class' => 'fieldset-wide']
        );
        $this->_addElementTypes($fieldset);

        $fieldset->addField(
            'mb_title',
            'text',
            ['label' => __('Title'), 'name' => 'mb_title', 'scope' => 'store']
        );

        $fieldset->addField(
            'mb_short_title',
            'text',
            ['label' => __('Short Title'), 'name' => 'mb_short_title', 'scope' => 'store']
        );

        $fieldset->addField(
            'mb_short_description',
            'text',
            ['label' => __('Short Description'), 'name' => 'mb_short_description', 'scope' => 'store']
        );

        $fieldset->addField(
            'terms_conditions',
            'textarea',
            [
                'label' => __('Terms and Conditions'),
                'name' => 'terms_conditions',
                'class' => 'textarea',
                'scope' => 'store'
            ]
        );

        $fieldset->addField(
            'flash_sale_image',
            'image',
            ['label' => __('Background Image'), 'name' => 'flash_sale_image']
        );

        $form->setValues($this->getEvent()->getData());
    }

    /**
     * Retrieve Additional Element Types
     *
     * @return array
     * @codeCoverageIgnore
     */
    protected function _getAdditionalElementTypes()
    {
        return [
            'flash_sale_image' => \SM\FlashSale\Block\Adminhtml\Event\Helper\Image::class
        ];
    }
}
