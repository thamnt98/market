<?php

namespace SM\MobileApi\Model\Data\Catalog;

use SM\MobileApi\Api\Data\Catalog\ProductFilterInterface;

class ProductFilter extends \Magento\Framework\Model\AbstractExtensibleModel implements \SM\MobileApi\Api\Data\Catalog\ProductFilterInterface
{
    /**
     * @inheritDoc
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * @inheritDoc
     */
    public function setName($data)
    {
        return $this->setData(self::NAME, $data);
    }

    /**
     * @inheritDoc
     */
    public function getCode()
    {
        return $this->getData(self::CODE);
    }

    /**
     * @inheritDoc
     */
    public function setCode($data)
    {
        return $this->setData(self::CODE, $data);
    }

    /**
     * @inheritDoc
     */
    public function getItems()
    {
        return $this->getData(self::ITEMS);
    }

    /**
     * @inheritDoc
     */
    public function setItems($data)
    {
        return $this->setData(self::ITEMS, $data);
    }

    /**
     * @inheritDoc
     */
    public function getIsMultiselect()
    {
        return $this->getData(self::IS_MULTISELECT);
    }

    /**
     * @inheritDoc
     */
    public function setIsMultiselect($data)
    {
        return $this->setData(self::IS_MULTISELECT, $data);
    }

    /**
     * @return float
     */
    public function getMin()
    {
        return $this->getData(self::MIN);
    }

    /**
     * @param float $data
     *
     * @return $this
     */
    public function setMin($data)
    {
        return $this->setData(self::MIN, $data);
    }

    /**
     * @return float
     */
    public function getMax()
    {
        return $this->getData(self::MAX);
    }

    /**
     * @param float $data
     *
     * @return $this
     */
    public function setMax($data)
    {
        return $this->setData(self::MAX, $data);
    }

    /**
     * @return float
     */
    public function getFrom()
    {
        return $this->getData(self::FROM);
    }

    /**
     * @param float $data
     *
     * @return $this
     */
    public function setFrom($data)
    {
        return $this->setData(self::FROM, $data);
    }

    /**
     * @return float
     */
    public function getTo()
    {
        return $this->getData(self::TO);
    }

    /**
     * @param float $data
     *
     * @return $this
     */
    public function setTo($data)
    {
        return $this->setData(self::TO, $data);
    }
}
