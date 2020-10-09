<?php
/**
 * @category  SM
 * @package   SM_Label
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 * @author    Logan Ta <phuongtt2@smartosc.com>
 *
 * @copyright 2020 Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

declare(strict_types=1);

namespace SM\Label\Block\Adminhtml\Labels\Edit\Tab;

use Amasty\Label\Block\Adminhtml\Labels\Edit\Tab\AbstractImage;

/**
 * Class Category
 * @package SM\Label\Block\Adminhtml\Labels\Edit\Tab
 */
class Category extends AbstractImage
{
    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Category');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Category');
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('current_amasty_label');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('labels_');

        $fldCat = $form->addFieldset('category_page', ['legend'=> __('Category Page')]);
        $fldCat->addType('color', \Amasty\Label\Block\Adminhtml\Data\Form\Element\Color::class);
        $fldCat->addType('custom_file', \Amasty\Label\Block\Adminhtml\Data\Form\Element\File::class);
        $fldCat->addType('preview', \Amasty\Label\Block\Adminhtml\Data\Form\Element\Preview::class);

        $fldCat->addField(
            'cat_img',
            'custom_file',
            [
                'label' => __('Label Type'),
                'name' => 'cat_img',
                'after_element_html' => $this->getImageHtml('cat_img', $model->getCatImg()),
            ]
        );

        $fldCat->addField(
            'cat_label_color',
            'color',
            [
                'label' => __('Label Color'),
                'name' => 'cat_label_color'
            ]
        );

//        $fldCat->addField(
//            'cat_pos',
//            'select',
//            [
//                'label' => __('Label Position'),
//                'name' => 'cat_pos',
//                'values' => $model->getAvailablePositions(),
//                'after_element_html' => $this->getPositionHtml('cat_pos')
//            ]
//        );

        $fldCat->addField(
            'cat_image_size',
            'text',
            [
                'label' => __('Label Size'),
                'name' => 'cat_image_size',
                'note' => __('Percent of the product image.'),
            ]
        );

        $activeOnCatPage = $fldCat->addField('use_for_cat', 'select', [
            'label'     => __('Show On Category Page'),
            'title'     => __('Show On Category Page'),
            'name'      => 'use_for_cat',
            'options'   => [
                '0' => __('No'),
                '1' => __('Yes'),
            ],
        ]);

        $catTxt = $fldCat->addField(
            'cat_txt',
            'text',
            [
                'label' => __('Label Text'),
                'name' => 'cat_txt',
                'note' => __('Max chars for a label is 30 chars.'),
                'required' => true,
                'class' => 'input-text required-entry validate-length maximum-length-30 minimum-length-1'
            ]
        );

        $fldCat->addField(
            'cat_color',
            'color',
            [
                'label' => __('Text Color'),
                'name' => 'cat_color'
            ]
        );

        $fldCat->addField(
            'cat_size',
            'text',
            [
                'label' => __('Text Size'),
                'name' => 'cat_size',
                'note' => __('Example: 12px;'),
            ]
        );

        $fldCat->addField(
            'cat_style',
            'textarea',
            [
                'label' => __('Advanced Settings/CSS'),
                'name'  => 'cat_style',
                'note'  => __(
                    'Customize label and text styles with CSS parameters. ' .
                    'For more information click <a href="%1" target="_blank">here</a>.' .
                    '<br> Ex.: text-align: center; line-height: 20px; transform: rotate(-90deg);',
                    'https://www.w3schools.com/cssref/default.asp'
                )
            ]
        );
        if ($model && $model->getId()) {
            $fldCat->addField(
                'cat_preview',
                'preview',
                [
                    'label' => '',
                    'name'  => 'cat_preview'
                ]
            );
        }

        // define field dependencies
        /**
         * @var \Magento\Backend\Block\Widget\Form\Element\Dependence
         */
        $dependence = $this->getLayout()->createBlock(\Magento\Backend\Block\Widget\Form\Element\Dependence::class)
            // active cat text label on category page
            ->addFieldMap($activeOnCatPage->getHtmlId(), $activeOnCatPage->getName())
            ->addFieldMap($catTxt->getHtmlId(), $catTxt->getName())
            ->addFieldDependence(
                $catTxt->getName(),
                $activeOnCatPage->getName(),
                '1'
            );
        $this->setChild('form_after', $dependence);

        $data = $model->getData();
        $data = $this->_restoreSizeColor($data);
        $form->setValues($data);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
