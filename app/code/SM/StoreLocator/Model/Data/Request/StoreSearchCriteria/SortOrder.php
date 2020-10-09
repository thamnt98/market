<?php

//declare(strict_types=1);

namespace SM\StoreLocator\Model\Data\Request\StoreSearchCriteria;

use Magento\Framework\Api\SortOrder as BaseSortOrder;
use SM\StoreLocator\Api\Data\Request\SortOrderInterface;

class SortOrder extends BaseSortOrder implements SortOrderInterface
{
    /**
     * @return float
     * @codeCoverageIgnore
     */
    public function getLat(): float
    {
        return (float) $this->_get(self::LAT);
    }

    /**
     * @param float $lat
     * @return self
     * @codeCoverageIgnore
     */
    public function setLat(float $lat): self
    {
        return $this->setData(SortOrder::LAT, $lat);
    }

    /**
     * @return float
     * @codeCoverageIgnore
     */
    public function getLong(): float
    {
        return (float) $this->_get(self::LONG);
    }

    /**
     * @param float $long
     * @return self
     * @codeCoverageIgnore
     */
    public function setLong(float $long): self
    {
        return $this->setData(SortOrder::LONG, $long);
    }

    /**
     * Get sorting field.
     *
     * @return string
     */
    public function getField()
    {
        return parent::getField();
    }

    /**
     * Get sorting direction.
     *
     * @return string
     */
    public function getDirection()
    {
        return parent::getDirection();
    }
}
