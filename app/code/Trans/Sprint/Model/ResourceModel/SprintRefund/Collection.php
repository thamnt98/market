<?php
/**
 * @category Trans
 * @package  Trans_Sprint
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Rifai <muhammad.rifai@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Sprint\Model\ResourceModel\SprintRefund;

use Magento\Framework\DB\Select;
use Trans\Sprint\Api\Data\SprintRefundInterface;
use Trans\Sprint\Model\ResourceModel\SprintRefund as ResourceModel;
use Trans\Sprint\Model\SprintRefund;

/**
 * Class Collection
 * @SuppressWarnings(PHPMD)
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {
	/**
	 * @var string
	 */
	protected $_idFieldName = SprintRefundInterface::ID;

	/**
	 * Event prefix
	 *
	 * @var string
	 */
	protected $_eventPrefix = SprintRefundInterface::DEFAULT_PREFIX;

	/**
	 * Event object
	 *
	 * @var string
	 */
	protected $_eventObject = SprintRefundInterface::DEFAULT_EVENT;

	/**
	 * Define resource model
	 *
	 * @return void
	 * @SuppressWarnings(PHPMD)
	 */
	protected function _construct() {
		$this->_init(SprintRefund::class, ResourceModel::class);
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
}
