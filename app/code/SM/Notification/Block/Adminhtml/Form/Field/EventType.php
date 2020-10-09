<?php

namespace SM\Notification\Block\Adminhtml\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;

class EventType extends AbstractFieldArray
{
    /**
     * @var EventTypeCol
     */
    protected $eventType;
    /**
     * @var EnableCol
     */
    protected $enableCol;

    /**
     * @var string
     */
    protected $_template = 'SM_Notification::system/config/form/field/event_types.phtml';

    /**
     * Prepare rendering the new field by adding all the needed columns
     */
    protected function _prepareToRender()
    {
        $this->addColumn('event_type', [
            'label' => __('Event Type'),
            'renderer' => $this->getEventType()
        ]);
        $this->addColumn('name', ['label' => __('Name'), 'class' => 'required-entry']);
        $this->addColumn('enable', [
            'label' => __('Enable'),
            'renderer' => $this->getEnable()
        ]);
        $this->_addAfter = false;
    }

    /**
     * Prepare existing row data object
     *
     * @param DataObject $row
     * @throws LocalizedException
     */
    protected function _prepareArrayRow(DataObject $row): void
    {
        $options = [];

        $tax = $row->getEventType();
        if ($tax !== null) {
            $options['option_' . $this->getEventType()->calcOptionHash($tax)] = 'selected="selected"';
        }

        $row->setData('option_extra_attrs', $options);
    }

    /**
     * @return EventTypeCol
     * @throws LocalizedException
     */
    protected function getEventType()
    {
        if (!$this->eventType) {
            $this->eventType = $this->getLayout()->createBlock(
                EventTypeCol::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }
        return $this->eventType;
    }

    /**
     * @return EnableCol
     * @throws LocalizedException
     */
    protected function getEnable()
    {
        if (!$this->enableCol) {
            $this->enableCol = $this->getLayout()->createBlock(
                EnableCol::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }
        return $this->enableCol;
    }
}
