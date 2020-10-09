<?php
/**
 * Class Main
 *
 * PHP version 7
 *
 * @category Trans
 * @package  Trans_Brand
 * @author   J.P <jaka.pondan@transdigital.co.id>
 */
namespace Trans\Brand\Block\Adminhtml\Index\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;

/**
 * Class Main
 *
 * @category Trans
 * @package  Trans_Brand
 * @author   J.P <jaka.pondan@transdigital.co.id>
 */
class Main extends Generic implements TabInterface
{
    /**
     * BrandModel
     *
     * @var \Trans\Brand\Model\Brand
     */
    protected $brand;

    /**
     * Main constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context     context
     * @param \Magento\Framework\Registry             $registry    registry
     * @param \Magento\Framework\Data\FormFactory     $formFactory formFactory
     * @param \Trans\Brand\Model\Brand           $brand       brand
     * @param array                                   $data        data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Trans\Brand\Model\Brand $brand,
        array $data = []
    ) {
        $this->brand = $brand;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare Form
     *
     * @return mixed
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('brands_index');

         $form = $this->_formFactory->create(
             [
                'data' => [
                    'id' => 'edit_form',
                    'enctype'=>'multipart/form-data',
                    'action' => $this->getData('action'),
                    'method' => 'post'
                ]
             ]
         );

        $form->setHtmlIdPrefix('post_');

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('Brand Details'), 'class' => 'fieldset-wide']
        );

        if ($model->getId()) {
            $fieldset->addField('brand_id', 'hidden', ['name' => 'brand_id']);
        }

        $fieldset->addField(
            'title',
            'text',
            [
                'name' => 'title',
                'label' => __('Title'),
                'title' => __('Title'),
                'class' => 'required-entry',
                'required' => true
            ]
        );

        $fieldset->addField(
            'description',
            'textarea',
            [
                'name' => 'description',
                'label' => __('Description'),
                'title' => __('Description')
            ]
        );

        $fieldset->addField(
            'meta_title',
            'text',
            [
                'name' => 'meta_title',
                'label' => __('SEO Meta Title'),
                'title' => __('SEO Meta Title')
            ]
        );

        $fieldset->addField(
            'meta_keywords',
            'text',
            [
                'name' => 'meta_keywords',
                'label' => __('SEO Meta Keywords'),
                'title' => __('SEO Meta Keywords')
            ]
        );

        $fieldset->addField(
            'meta_description',
            'text',
            [
                'name' => 'meta_description',
                'label' => __('SEO Meta Description'),
                'title' => __('SEO Meta Description')
            ]
        );

        $fieldset->addField(
            'url_key',
            'text',
            [
                'name' => 'url_key',
                'label' => __('URL Key'),
                'title' => __('URL Key')
            ]
        );

        if ($model->getId()) {
            $isRequired=false;
        } else {
            $isRequired=true;
        }

        $fieldset->addField(
            'image',
            'image',
            [
                'name' => 'image',
                'label' => __('Image'),
                'title' => __('Image'),
                'note' => __(
                    'Note : Please upload image of 210 x 50 (Width x Height) size.'
                ),
                'class' =>'admin__control-image',
                'required' => $isRequired,
            ]
        )->setAfterElementHtml(
            '<script>
                require([
                    "jquery",
                ], function($){
                    $(document).ready(function () {
                        $("#post_image_delete").parent().hide();
                        if($("#post_image_image").attr("src")){
                            $("#post_image").removeClass("required-file");
                        }else{
                            $("#post_image").addClass("required-file");
                        }

                        $( "#post_image" ).attr(
                            "accept", 
                            "image/x-png,image/gif,image/jpeg,image/jpg,image/png"
                        );
                    });
                  });
            </script>'
        );

        $fieldset->addField(
            'banner_image',
            'image',
            [
                'name' => 'banner_image',
                'label' => __('Banner Image'),
                'title' => __('Banner Image'),
                'class' =>'admin__control-image'
            ]
        )->setAfterElementHtml(
            '<script>
                require([
                    "jquery",
                ], function($){
                    $(document).ready(function () {
                        $( "#post_banner_image" ).attr(
                            "accept", 
                            "image/x-png,image/gif,image/jpeg,image/jpg,image/png"
                        );
                    });
                  });
            </script>'
        );

        $fieldset->addField(
            'position',
            'text',
            [
                'name' => 'position',
                'label' => __('Position'),
                'title' => __('Position'),
                'class'     => 'validate-number',
                'required' => true
            ]
        );

        $fieldset->addField(
            'status',
            'select',
            [
                'label' => __('Status'),
                'title' => __('Status'),
                'name' => 'status',
                'required' => true,
                'values' => $this->brand->getAvailableStatuses()
            ]
        );

        if (!$model->getId()) {
            $model->setData('status', '1');
        }

        $form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Brand Details');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Brand Details');
    }

    /**
     * {@inheritdoc}
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId resourceId
     *
     * @return boolean
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
