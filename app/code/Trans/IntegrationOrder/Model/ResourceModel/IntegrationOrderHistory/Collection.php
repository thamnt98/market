<?php
/**
 * @category Trans
 * @package  Trans_IntegrationOrder
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Randy <muhammad.randy@transdigital.co.id>
 *
 * Copyright © 2020 PT CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationOrder\Model\ResourceModel\IntegrationOrderHistory;

use Trans\IntegrationOrder\Model\IntegrationOrderHistory as IntegrationOrderHistoryModel;
use Trans\IntegrationOrder\Model\ResourceModel\IntegrationOrderHistory as IntegrationOrderHistoryResourceModel;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
	//@codingStandardsIgnoreLine
	protected $_idOrder = 'history_id';

    /**
     * Init resource model
     * @return void
     */
    public function _construct()
    {
        $this->_init(
            IntegrationOrderHistoryModel::class,
            IntegrationOrderHistoryResourceModel::class
        );
        $this->_map['integration_oms_order_history']['history_id'] = 'main_table.history_id';
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
