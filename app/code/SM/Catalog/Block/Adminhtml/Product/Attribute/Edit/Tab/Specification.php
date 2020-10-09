<?php
/**
 * @category  SM
 * @package   SM_Catalog
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 * @author    Logan Ta <phuongtt2@smartosc.com>
 *
 * @copyright 2020 Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

declare(strict_types=1);


namespace SM\Catalog\Block\Adminhtml\Product\Attribute\Edit\Tab;

use Amasty\ShopbyBase\Model\FilterSetting;
use Amasty\ShopbyBase\Model\FilterSettingFactory;
use Magento\Backend\Block\Template\Context;
use Magento\Config\Model\Config\Source\Yesno;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Magento\Catalog\Api\ProductAttributeRepositoryInterface;

/**
 * Class Specification
 * @package SM\Catalog\Block\Adminhtml\Product\Attribute\Edit\Tab
 */
class Specification extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var Yesno
     */
    protected $yesNo;

    /**
     * @var  FilterSetting
     */
    protected $setting;

    /**
     * @var ProductAttributeRepositoryInterface
     */
    protected $attributeRepository;

    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Yesno $yesNo,
        FilterSettingFactory $settingFactory,
        ProductAttributeRepositoryInterface $attributeRepository,
        array $data = []
    ) {
        $this->yesNo = $yesNo;
        $this->attributeRepository = $attributeRepository;
        $this->setting = $settingFactory->create();

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            ['data' => ['id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post']]
        );

        $fieldsetDisplayProperties = $form->addFieldset(
            'sm_specification_fieldset_display_properties',
            ['legend' => __('Configuration Content'), 'collapsable' => $this->getRequest()->has('popup')]
        );

        $attributeId = $this->_request->getParam('attribute_id');
        if ($attributeId) {
            $attributeData = $this->attributeRepository->get($attributeId);
            if (intval($attributeData->getData('show_specification')) > 1) {
                $fieldsetDisplayProperties->addField(
                    'show_specification',
                    'select',
                    [
                        'name' => 'show_specification',
                        'label' => __('Show On Specification PDP'),
                        'title' => __('Show On Specification PDP'),
                        'value' => 2,
                        'values' => [
                             2 => 'Yes',
                             1 => 'No'
                        ]
                    ]
                );
            } else {
                $fieldsetDisplayProperties->addField(
                    'show_specification',
                    'select',
                    [
                        'name' => 'show_specification',
                        'label' => __('Allow Show On Specification PDP'),
                        'title' => __('Allow Show On Specification PDP'),
                        'value' => 1,
                        'values' => [
                            1 => 'No',
                            2 => 'Yes'
                        ]
                    ]
                );
            }
        } else {
            $fieldsetDisplayProperties->addField(
                'show_specification',
                'select',
                [
                    'name' => 'show_specification',
                    'label' => __('Allow Show On Specification PDP'),
                    'title' => __('Allow Show On Specification PDP'),
                    'value' => 1,
                    'values' => [
                        1 => 'No',
                        2 => 'Yes'
                    ]
                ]
            );
        }

        $this->setForm($form);
        $data = $this->setting->getData();

        $form->setValues($data);

        return parent::_prepareForm();
    }
}
