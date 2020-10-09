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

namespace Trans\IntegrationOrder\Model\ResourceModel\Refund;

use Trans\IntegrationOrder\Model\Refund as RefundModel;
use Trans\IntegrationOrder\Model\ResourceModel\Refund as RefundResourceModel;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {

	/**
	 * @var string
	 */
	//@codingStandardsIgnoreLine
	protected $refundId = 'refund_id';

	/**
	 * Init resource model
	 * @return void
	 */
	public function _construct() {
		$this->_init(
			RefundModel::class,
			RefundResourceModel::class
		);
		$this->_map['integration_oms_refund']['refund_id'] = 'main_table.refund_id';
	}

	/**
	 * Add filter by store
	 *
	 * @param int|array|\Magento\Store\Model\Store $store
	 * @param bool $withAdmin
	 * @return $this
	 */
	public function addStoreFilter($store, $withAdmin = true) {
		return $this;
	}
}
