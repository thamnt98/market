<?php
/**
 * @category Trans
 * @package  Trans_DigitalProduct
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Rifai <muhammad.rifai@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\DigitalProduct\Model\ResourceModel\DigitalProductTransactionResponse;

use Magento\Framework\DB\Select;
use Trans\DigitalProduct\Api\Data\DigitalProductTransactionResponseInterface;
use Trans\DigitalProduct\Model\DigitalProductTransactionResponse;
use Trans\DigitalProduct\Model\ResourceModel\DigitalProductTransactionResponse as ResourceModel;

/**
 * Class Collection
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {
	/**
	 * @var string
	 */
	protected $_idFieldName = DigitalProductTransactionResponseInterface::ID;

	/**
	 * Event prefix
	 *
	 * @var string
	 */
	protected $_eventPrefix = DigitalProductTransactionResponseInterface::DEFAULT_EVENT;

	/**
	 * Event object
	 *
	 * @var string
	 */
	protected $_eventObject = DigitalProductTransactionResponseInterface::DEFAULT_EVENT;

	/**
	 * Define resource model
	 *
	 * @return void
	 * @SuppressWarnings(PHPMD)
	 */
	protected function _construct() {
		$this->_init(DigitalProductTransactionResponse::class, ResourceModel::class);
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