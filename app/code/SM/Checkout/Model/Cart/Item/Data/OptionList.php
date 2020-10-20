<?php


namespace SM\Checkout\Model\Cart\Item\Data;

/**
 * Class OptionList
 * @package SM\Checkout\Model\Cart\Item\Data\OptionList
 */
class OptionList extends \Magento\Framework\Model\AbstractExtensibleModel implements \SM\Checkout\Api\Data\CartItem\OptionListInterface
{
    /**
     * @inheritDoc
     */
    public function getLabel()
    {
        return $this->getData(self::LABEL);
    }

    /**
     * @inheritDoc
     */
    public function setLabel($label)
    {
        return $this->setData(self::LABEL, $label);
    }

    /**
     * @inheritDoc
     */
    public function getValue()
    {
        return $this->getData(self::VALUE);
    }

    /**
     * @inheritDoc
     */
    public function setValue($value)
    {
        return $this->setData(self::VALUE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getOptionId()
    {
        return $this->getData(self::OPTION_ID);
    }

    /**
     * @inheritDoc
     */
    public function setOptionId($optionId)
    {
        return $this->setData(self::OPTION_ID, $optionId);
    }

    /**
     * @inheritDoc
     */
    public function getOptionValue()
    {
        return $this->getData(self::OPTION_VALUE);
    }

    /**
     * @inheritDoc
     */
    public function setOptionValue($optionValue)
    {
        return $this->setData(self::OPTION_VALUE, $optionValue);
    }

    /**
     * @inheritDoc
     */
    public function getOptionColorText(){
        return $this->getData(self::OPTION_COLOR_TEXT);
    }

    /**
     * @inheritDoc
     */
    public function setOptionColorText($colorText){
        return $this->setData(self::OPTION_COLOR_TEXT,$colorText);
    }

    /**
     * @inheritDoc
     */
    public function getProductName(){
        return $this->getData(self::PRODUCT_NAME);
    }

    /**
     * @inheritDoc
     */
    public function setProductName($data){
        return $this->setData(self::PRODUCT_NAME,$data);

    }

}
