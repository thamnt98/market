<?php
namespace SM\Customer\Model\Data;

class CustomerOption extends \Magento\Framework\DataObject implements \SM\Customer\Api\Data\CustomerOptionInterface{

    /**
     * @inheritdoc
     */
    public function getAttributeCode()
    {
        return $this->getData(self::ATTRIBUTE_CODE);
    }

    /**
     * @inheritdoc
     */
    public function setAttributeCode($data)
    {
        return $this->setData(self::ATTRIBUTE_CODE,$data);
    }

    /**
     * @inheritdoc
     */
    public function getOptionValue()
    {
        return $this->getData(self::OPTION_VALUE);
    }

    /**
     * @inheritdoc
     */
    public function setOptionValue($data)
    {
        return $this->setData(self::OPTION_VALUE,$data);
    }

    /**
     * @inheritdoc
     */
    public function getOptionLabel()
    {
        return $this->getData(self::OPTION_LABEL);
    }

    /**
     * @inheritdoc
     */
    public function setOptionLabel($data)
    {
        return $this->setData(self::OPTION_LABEL,$data);
    }
}