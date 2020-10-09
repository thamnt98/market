<?php

namespace SM\StoreLocator\Ui;

use Magento\Framework\Api\Filter;
use Magento\Ui\DataProvider\AbstractDataProvider;

class DataProvider extends AbstractDataProvider
{
    /**
     * @param Filter $filter
     * @return null
     */
    public function addFilter(Filter $filter)
    {
        return $this;
    }

    /**
     * @return $this
     */
    public function getCollection()
    {
        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [];
    }
}