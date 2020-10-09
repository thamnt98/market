<?php
namespace SM\Customer\Api\Data;

interface CustomerOptionInterface extends \Magento\Framework\Api\ExtensibleDataInterface{

    const ATTRIBUTE_CODE    = 'attribute_code';
    const OPTION_VALUE      = 'option_value';
    const OPTION_LABEL      = 'option_label';

    /**
     * @return string
     */
    public function getAttributeCode();

    /**
     * @param string $data
     * @return $this
     */
    public function setAttributeCode($data);

    /**
     * @return string
     */
    public function getOptionValue();

    /**
     * @param string $data
     * @return $this
     */
    public function setOptionValue($data);

    /**
     * @return string
     */
    public function getOptionLabel();

    /**
     * @param string $data
     * @return $this
     */
    public function setOptionLabel($data);
}