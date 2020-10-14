<?php
/**
 * @category Trans
 * @package  Trans_IntegrationCategory
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Rifai <muhammad.rifai@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationCategory\Model\ResourceModel\IntegrationCategory;

use Magento\Framework\DB\Select;
use Trans\IntegrationCategory\Api\Data\IntegrationCategoryInterface;
use Trans\IntegrationCategory\Model\IntegrationCategory;
use Trans\IntegrationCategory\Model\ResourceModel\IntegrationCategory as ResourceModel;

/**
 * Class Collection
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {
	/**
	 * @var string
	 */
	protected $_idFieldName = IntegrationCategoryInterface::ID;

	/**
	 * Event prefix
	 *
	 * @var string
	 */
	protected $_eventPrefix = IntegrationCategoryInterface::DEFAULT_EVENT;

	/**
	 * Event object
	 *
	 * @var string
	 */
	protected $_eventObject = IntegrationCategoryInterface::DEFAULT_EVENT;

	/**
	 * Define resource model
	 *
	 * @return void
	 * @SuppressWarnings(PHPMD)
	 */
	protected function _construct() {
		$this->_init(IntegrationCategory::class, ResourceModel::class);
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