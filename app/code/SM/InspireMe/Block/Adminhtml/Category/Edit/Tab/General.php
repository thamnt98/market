<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\InspireMe\Block\Adminhtml\Category\Edit\Tab;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Mirasvit\Blog\Model\Category;
use Mirasvit\Blog\Model\ResourceModel\Category\CollectionFactory;

/**
 * Class General
 * @package SM\InspireMe\Block\Adminhtml\Category\Edit\Tab
 */
class General extends \Magento\Backend\Block\Widget\Form
{
    /**
     * @var FormFactory
     */
    protected $formFactory;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var CollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * General constructor.
     * @param CollectionFactory $postCollectionFactory
     * @param FormFactory $formFactory
     * @param Registry $registry
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        CollectionFactory $postCollectionFactory,
        FormFactory $formFactory,
        Registry $registry,
        Context $context,
        array $data = []
    ) {
        $this->categoryCollectionFactory = $postCollectionFactory;
        $this->formFactory               = $formFactory;
        $this->registry                  = $registry;

        parent::__construct($context, $data);
    }

    /**
     * @return \Magento\Backend\Block\Widget\Form
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        $form = $this->formFactory->create();
        $this->setForm($form);

        /** @var Category $category */
        $category = $this->registry->registry('current_model');

        $fieldset = $form->addFieldset('edit_fieldset', [
            'legend' => __('General Information'),
        ]);

        if ($category->getId()) {
            $fieldset->addField('entity_id', 'hidden', [
                'name'  => 'entity_id',
                'value' => $category->getId(),
            ]);
        }

        $fieldset->addField('name', 'text', [
            'label'    => __('Title'),
            'name'     => 'name',
            'value'    => $category->getName(),
            'required' => true,
        ]);

        $fieldset->addField('status', 'select', [
            'label'  => __('Status'),
            'name'   => 'status',
            'value'  => $category->getStatus(),
            'values' => ['0' => __('Disabled'), '1' => __('Enabled')],
        ]);

        return parent::_prepareForm();
    }

}
