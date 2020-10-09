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

namespace Trans\LocationCoverage\Model\ResourceModel\City;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'entity_id';
    
    /**
     * Define resource model.
     */
    protected function _construct()
    {
        $this->_init('Trans\LocationCoverage\Model\City', 'Trans\LocationCoverage\Model\ResourceModel\City');
    }

    /**
     * Filter by city name
     *
     * @param string|array $cityId
     * @return $this
     */
    public function addCityIdFilter($cityId)
    {
        if (!empty($cityId)) {
            if (is_array($cityId)) {
                $this->addFieldToFilter('main_table.region_id', ['in' => $cityId]);
            } else {
                $this->addFieldToFilter('main_table.region_id', $cityId);
            }
        }
        return $this;
    }
}