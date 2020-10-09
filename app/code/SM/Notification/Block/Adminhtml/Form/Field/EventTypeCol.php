<?php

namespace SM\Notification\Block\Adminhtml\Form\Field;

use Magento\Framework\View\Element\Html\Select;
use SM\Notification\Model\Notification;

class EventTypeCol extends Select
{
    /**
     * @var \SM\Notification\Model\Source\EventType
     */
    protected $eventTypeOptions;

    /**
     * EventTypeCol constructor.
     *
     * @param \SM\Notification\Model\Source\EventType $eventTypeOptions
     * @param \Magento\Framework\View\Element\Context $context
     * @param array                                   $data
     */
    public function __construct(
        \SM\Notification\Model\Source\EventType $eventTypeOptions,
        \Magento\Framework\View\Element\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->eventTypeOptions = $eventTypeOptions;
    }

    /**
     * Set "name" for <select> element
     *
     * @param string $value
     * @return $this
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }

    /**
     * Set "id" for <select> element
     *
     * @param $value
     * @return $this
     */
    public function setInputId($value)
    {
        return $this->setId($value);
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    public function _toHtml(): string
    {
        if (!$this->getOptions()) {
            $this->setOptions($this->getSourceOptions());
        }

        if (!$this->_beforeToHtml()) {
            return '';
        }

        $html = '<input id="hide_' . $this->getId() . '" type="hidden" name="' . $this->getName() . '" />' .
            '<select id="' .
            $this->getId() .
            '" class="' .
            $this->getClass() .
            '" title="' .
            $this->escapeHtml($this->getTitle()) .
            '" ' .
            $this->getExtraParams() .
            ' readonly >';

        $values = $this->getValue();
        if (!is_array($values)) {
            $values = (array)$values;
        }

        $isArrayOption = true;
        foreach ($this->getOptions() as $key => $option) {
            $optgroupName = '';
            if ($isArrayOption && is_array($option)) {
                $value = $option['value'];
                $label = (string)$option['label'];
                $optgroupName = isset($option['optgroup-name']) ? $option['optgroup-name'] : $label;
                $params = !empty($option['params']) ? $option['params'] : [];
            } else {
                $value = (string)$key;
                $label = (string)$option;
                $isArrayOption = false;
                $params = [];
            }

            if (is_array($value)) {
                $html .= '<optgroup label="' . $this->escapeHtml($label)
                    . '" data-optgroup-name="' . $this->escapeHtml($optgroupName) . '">';
                foreach ($value as $keyGroup => $optionGroup) {
                    if (!is_array($optionGroup)) {
                        $optionGroup = ['value' => $keyGroup, 'label' => $optionGroup];
                    }
                    $html .= $this->_optionToHtml($optionGroup, in_array($optionGroup['value'], $values));
                }
                $html .= '</optgroup>';
            } else {
                $html .= $this->_optionToHtml(
                    ['value' => $value, 'label' => $label, 'params' => $params],
                    in_array($value, $values)
                );
            }
        }
        $html .= '</select>';

        return $html;
    }

    private function getSourceOptions(): array
    {
        return $this->eventTypeOptions->toOptionArray();
    }
}