<?php
/**
 * @category Trans
 * @package  Trans_Sprint
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 *
 * Copyright © 2019 CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Sprint\Model\ResourceModel\CustomerTokenization;

use Magento\Framework\DB\Select;
use Trans\Sprint\Model\CustomerTokenization;
use Trans\Sprint\Api\Data\SprintCustomerTokenizationInterface;
use Trans\Sprint\Model\ResourceModel\CustomerTokenization as ResourceModel;

/**
 * Class Collection
 * @SuppressWarnings(PHPMD)
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {
	/**
	 * @var string
	 */
	protected $_idFieldName = SprintCustomerTokenizationInterface::ID;

	/**
	 * Event prefix
	 *
	 * @var string
	 */
	protected $_eventPrefix = 'sprint_customer_tokenization';

	/**
	 * Event object
	 *
	 * @var string
	 */
	protected $_eventObject = 'sprint_customer_tokenization';

	/**
	 * Define resource model
	 *
	 * @return void
	 * @SuppressWarnings(PHPMD)
	 */
	protected function _construct() {
		$this->_init(CustomerTokenization::class, ResourceModel::class);
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
