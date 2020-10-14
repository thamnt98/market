<?php
/**
 * @category Trans
 * @package  Trans_IntegrationCatalog
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Rifai <muhammad.rifai@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationCatalog\Model\ResourceModel\IntegrationProduct;

use Magento\Framework\DB\Select;
use Trans\IntegrationCatalog\Api\Data\IntegrationProductInterface;
use Trans\IntegrationCatalog\Model\IntegrationProduct;
use Trans\IntegrationCatalog\Model\ResourceModel\IntegrationProduct as ResourceModel;

/**
 * Class Collection
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {
	/**
	 * @var string
	 */
	protected $_idFieldName = IntegrationProductInterface::ID;

	/**
	 * Event prefix
	 *
	 * @var string
	 */
	protected $_eventPrefix = IntegrationProductInterface::DEFAULT_EVENT;

	/**
	 * Event object
	 *
	 * @var string
	 */
	protected $_eventObject = IntegrationProductInterface::DEFAULT_EVENT;

	/**
	 * Define resource model
	 *
	 * @return void
	 * @SuppressWarnings(PHPMD)
	 */
	protected function _construct() {
		$this->_init(IntegrationProduct::class, ResourceModel::class);
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