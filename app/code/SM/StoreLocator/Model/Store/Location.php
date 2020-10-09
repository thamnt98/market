<?php

namespace SM\StoreLocator\Model\Store;

use Magento\Framework\Model\AbstractModel;
use SM\StoreLocator\Model\Store\ResourceModel\Location as LocationResourceModel;

/**
 * Class Location
 * @package SM\StoreLocator\Model\Store
 */
class Location extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(LocationResourceModel::class);
    }
}
