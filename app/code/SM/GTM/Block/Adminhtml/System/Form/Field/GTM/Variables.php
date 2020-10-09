<?php

namespace SM\GTM\Block\Adminhtml\System\Form\Field\GTM;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;

class Variables extends AbstractFieldArray
{
    const FRONTEND_HANDLER_LABEL = 'Frontend Handlers';
    const FRONTEND_HANDLER = 'frontend_handler';
    const FRONTEND_EVENT_TRIGGER = 'event_trigger';
    const FRONTEND_EVENT_TRIGGER_LABEL = 'Event Trigger';
    const GTM_EVENT_CODE = 'gtm_key';
    const GTM_EVENT_CODE_LABEL = 'Data Layer Event Identifier';
    const GTM_OBJECT_TEMPLATE = 'template';
    const GTM_OBJECT_TEMPLATE_LABEL = 'Template';

    /**
     * Grid columns
     *
     * @var array
     */
    protected $_columns = [];

    /**
     * Enable the "Add after" button or not
     *
     * @var bool
     */
    protected $_addAfter = false;

    /**
     * Label of add button
     *
     * @var string
     */
    protected $_addButtonLabel;
    /**
     * Check if columns are defined, set template
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_addButtonLabel = __('Add new variables');
    }

    /**
     * Prepare to render
     *
     * @return void
     */
    protected function _prepareToRender()
    {
        $this->addColumn(self::FRONTEND_HANDLER, ['label' => __(self::FRONTEND_HANDLER_LABEL)]);
        $this->addColumn(self::FRONTEND_EVENT_TRIGGER, ['label' => __(self::FRONTEND_EVENT_TRIGGER_LABEL)]);
        $this->addColumn(self::GTM_EVENT_CODE, ['label' => __(self::GTM_EVENT_CODE_LABEL)]);
        $this->addColumn(self::GTM_OBJECT_TEMPLATE, ['label' => __(self::GTM_OBJECT_TEMPLATE_LABEL)]);
    }
}
