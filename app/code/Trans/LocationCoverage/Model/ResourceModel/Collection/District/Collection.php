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

namespace Trans\LocationCoverage\Model\ResourceModel\Collection\District;

use Trans\LocationCoverage\Model\District as DistrictModel;
use Trans\LocationCoverage\Model\ResourceModel\District as DistrictResourceModel;
use Trans\LocationCoverage\Model\ResourceModel\Collection\AbstractCollection as AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    //@codingStandardsIgnoreLine
    protected $_idDistrict = 'district_id';

    /**
     * Init resource model
     * @return void
     */
    public function _construct()
    {
        $this->_init(
            DistrictModel::class,
            DistrictResourceModel::class
        );
        $this->_map['district']['district_id'] = 'main_table.district_id';
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