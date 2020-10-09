<?php
/**
 * @category Trans
 * @package  Trans_MasterPayment
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Rifai <muhammad.rifai@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\MasterPayment\Model\ResourceModel\MasterPaymentMatrixAdjustment;

use Magento\Framework\DB\Select;
use Trans\MasterPayment\Api\Data\MasterPaymentMatrixAdjustmentInterface;
use Trans\MasterPayment\Model\MasterPaymentMatrixAdjustment;
use Trans\MasterPayment\Model\ResourceModel\MasterPaymentMatrixAdjustment as ResourceModel;

/**
 * Class Collection
 * @SuppressWarnings(PHPMD)
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {
	/**
	 * @var string
	 */
	protected $_idFieldName = MasterPaymentMatrixAdjustmentInterface::ID;

	/**
	 * Event prefix
	 *
	 * @var string
	 */
	protected $_eventPrefix = MasterPaymentMatrixAdjustmentInterface::DEFAULT_PREFIX;

	/**
	 * Event object
	 *
	 * @var string
	 */
	protected $_eventObject = MasterPaymentMatrixAdjustmentInterface::DEFAULT_EVENT;

	/**
	 * Define resource model
	 *
	 * @return void
	 * @SuppressWarnings(PHPMD)
	 */
	protected function _construct() {
		$this->_init(MasterPayment::class, ResourceModel::class);
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
