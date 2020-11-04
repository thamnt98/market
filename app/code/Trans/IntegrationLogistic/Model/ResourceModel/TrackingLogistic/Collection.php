<?php
/**
 * @category Trans
 * @package  Trans_IntegrationLogistic
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Randy <muhammad.randy@transdigital.co.id>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationLogistic\Model\ResourceModel\TrackingLogistic;

use Trans\IntegrationLogistic\Model\ResourceModel\TrackingLogistic as TrackingLogisticResourceModel;
use Trans\IntegrationLogistic\Model\TrackingLogistic as TrackingLogisticModel;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
	//@codingStandardsIgnoreLine
	protected $_idTracking = 'tracking_id';

    /**
     * Init resource model
     * @return void
     */
    public function _construct()
    {
        $this->_init(
            TrackingLogisticModel::class,
            TrackingLogisticResourceModel::class
        );
        $this->_map['integration_tpl_tracking']['tracking_id'] = 'main_table.tracking_id';
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
