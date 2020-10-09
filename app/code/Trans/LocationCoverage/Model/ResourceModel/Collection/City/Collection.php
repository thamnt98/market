<?php
/**
 * @category Trans
 * @package  Trans_LocationCoverage
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Randy <muhammad.randy@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\LocationCoverage\Model\ResourceModel\Collection\City;

use Trans\LocationCoverage\Model\City as CityModel;
use Trans\LocationCoverage\Model\ResourceModel\City as CityResourceModel;
use Trans\LocationCoverage\Model\ResourceModel\Collection\AbstractCollection as AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    //@codingStandardsIgnoreLine
    protected $_idCity = 'entity_id';

    /**
     * Init resource model
     * @return void
     */
    public function _construct()
    {
        $this->_init(
            CityModel::class,
            CityResourceModel::class
        );
        $this->_map['city']['entity_id'] = 'main_table.entity_id';
    }

    /**
     * Add filter by store
     *
     * @param int|array|\Magento\Store\Model\Store $store
     * @param bool $withAdmin
     * @return $this
     */
    public function addStoreFilter($store, $withAdmin = true)
    {
        return $this;
    }
}