<?php

namespace SM\StoreLocator\Model\Store\ResourceModel\Location;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use SM\StoreLocator\Model\Store\Location;
use SM\StoreLocator\Model\Store\ResourceModel\Location as LocationResourceModel;

/**
 * Class Collection
 * @package SM\StoreLocator\Model\Store\ResourceModel\Location
 */
class Collection extends AbstractCollection
{
    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(Location::class, LocationResourceModel::class);
    }
}
