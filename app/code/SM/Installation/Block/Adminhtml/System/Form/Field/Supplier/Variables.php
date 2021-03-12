<?php

namespace SM\Installation\Block\Adminhtml\System\Form\Field\Supplier;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;

class Variables extends AbstractFieldArray
{
    const SKU_LABEL = 'SKU';
    const SKU = 'sku';
    const EMAIL_LABEL = 'Email';
    const EMAIL = 'email';

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
        $this->_addButtonLabel = __('Add new mapping');
    }

    /**
     * Prepare to render
     *
     * @return void
     */
    protected function _prepareToRender()
    {
        $this->addColumn(self::SKU, ['label' => __(self::SKU_LABEL)]);
        $this->addColumn(self::EMAIL, ['label' => __(self::EMAIL_LABEL)]);
    }
}
