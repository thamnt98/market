<?php
namespace SM\Bundle\Model\Data;
use Magento\Framework\Model\AbstractExtensibleModel;

class CustomOption extends AbstractExtensibleModel implements \SM\Bundle\Api\Data\CustomOptionInterface{

    /**
     * @return int
     */
    public function getOptionId()
    {
        // TODO: Implement getOptionId() method.
        return $this->getData(self::OPTION_ID);
    }

    /**
     * @param int $data
     * @return $this
     */
    public function setOptionId($data)
    {
        // TODO: Implement setOptionId() method.
        $this->setData(self::OPTION_ID,$data);
        return $this;
    }

    /**
     * @return int
     */
    public function getOptionValue()
    {
        // TODO: Implement getOptionValue() method.
        return $this->getData(self::OPTION_VALUE);
    }

    /**
     * @param int $data
     * @return $this
     */
    public function setOptionValue($data)
    {
        // TODO: Implement setOptionValue() method.
        $this->setData(self::OPTION_VALUE,$data);
        return $this;
    }

}