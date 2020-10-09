<?php
/**
 * @category Trans
 * @package  Trans_IntegrationOrder
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Randy <muhammad.randy@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationOrder\Model\ResourceModel\IntegrationOrderAllocationRule;

use Trans\IntegrationOrder\Model\IntegrationOrderAllocationRule as AllocationRuleModel;
use Trans\IntegrationOrder\Model\ResourceModel\IntegrationOrderAllocationRule as AllocationRuleResourceModel;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {

	/**
	 * @var string
	 */
	//@codingStandardsIgnoreLine
	protected $_oarId = 'oar_id';

	/**
	 * Init resource model
	 * @return void
	 */
	public function _construct() {
		$this->_init(
			AllocationRuleModel::class,
			AllocationRuleResourceModel::class
		);
		$this->_map['integration_oms_oar']['oar_id'] = 'main_table.oar_id';
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

	/**
	 * Quote Id get Collection
	 *
	 * @param int $quoteId
	 * @return $this
	 */
	public function getDataByQuoteId($quoteId) {
		if (!empty($quoteId)) {
			if (is_array($quoteId)) {
				$this->addFieldToFilter('main_table.quote_id', ['in' => $quoteId]);
			} else {
				$this->addFieldToFilter('main_table.quote_id', $quoteId);
			}
		}
		return $this;
	}
}