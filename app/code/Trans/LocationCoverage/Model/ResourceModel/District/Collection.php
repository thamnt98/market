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

namespace Trans\LocationCoverage\Model\ResourceModel\District;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'district_id';
    
    /**
     * Define resource model.
     */
    protected function _construct()
    {
        $this->_init('Trans\LocationCoverage\Model\District', 'Trans\LocationCoverage\Model\ResourceModel\District');
    }

    /**
     * Filter by district name
     *
     * @param string|array $districtId
     * @return $this
     */
    public function addDistrictIdFilter($districtId)
    {
        if (!empty($districtId)) {
            if (is_array($districtId)) {
                $this->addFieldToFilter('main_table.entity_id', ['in' => $districtId]);
            } else {
                $this->addFieldToFilter('main_table.entity_id', $districtId);
            }
        }
        return $this;
    }
}