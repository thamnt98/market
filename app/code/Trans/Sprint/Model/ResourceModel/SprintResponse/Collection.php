<?php
/**
 * @category Trans
 * @package  Trans_Sprint
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Imam Kusuma <imam.kusuma@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */
namespace Trans\Sprint\Model\ResourceModel\SprintResponse;

use Magento\Framework\DB\Select;
use Trans\Sprint\Model\ResourceModel\SprintResponse as SprintResponseResource;
use Trans\Sprint\Model\SprintResponse;

/**
 * Class Collection
 * @SuppressWarnings(PHPMD)
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {
	/**
	 * @var string
	 */
	protected $_idFieldName = 'id';

	/**
	 * Event prefix
	 *
	 * @var string
	 */
	protected $_eventPrefix = 'trans_sprint_response_collection';

	/**
	 * Event object
	 *
	 * @var string
	 */
	protected $_eventObject = 'sprint_response_collection';

	/**
	 * Define resource model
	 *
	 * @return void
	 * @SuppressWarnings(PHPMD)
	 */
	protected function _construct() {
		$this->_init(SprintResponse::class, SprintResponseResource::class);
	}

	/**
	 * Get SQL for get record count
	 *
	 * Extra GROUP BY strip added.
	 *
	 * @return \Magento\Framework\DB\Select
	 */
	public function getSelectCountSql() {
		$countSelect = parent::getSelectCountSql();
		$countSelect->reset(Select::GROUP);

		return $countSelect;
	}

	public function loadByTransactionNo($transNo) {
		if ($transNo) {
			$this->addFieldToFilter('transaction_no', $transNo);
		}

		return $this;
	}
}
