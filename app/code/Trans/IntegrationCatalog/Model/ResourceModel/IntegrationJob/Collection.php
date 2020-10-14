<?php
/**
 * @category Trans
 * @package  Trans_IntegrationCatalog
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   J.P <jaka.pondan@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationCatalog\Model\ResourceModel\IntegrationJob;

use Magento\Framework\DB\Select;
use Trans\IntegrationCatalog\Api\Data\IntegrationJobInterface;
use Trans\IntegrationCatalog\Model\IntegrationJob;
use Trans\IntegrationCatalog\Model\ResourceModel\IntegrationJob as ResourceModel;
use Trans\Integration\Api\Data\IntegrationChannelInterface;

/**
 * Class Collection
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {
	/**
	 * @var string
	 */
	protected $_idFieldName = IntegrationJobInterface::ID;

	/**
	 * Event prefix
	 *
	 * @var string
	 */
	protected $_eventPrefix = IntegrationChannelInterface::DEFAULT_EVENT;

	/**
	 * Event object
	 *
	 * @var string
	 */
	protected $_eventObject = IntegrationChannelInterface::DEFAULT_EVENT;

	/**
	 * Define resource model
	 *
	 * @return void
	 * @SuppressWarnings(PHPMD)
	 */
	protected function _construct() {
		$this->_init(IntegrationJob::class, ResourceModel::class);
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

	/**
	 * Return Data Join
	 *
	 * @param int $id
	 *
	 * @return void
	 */
	public function joinMdJbId($id) {
		$this->integrationCatalogJob  = \Trans\IntegrationCatalog\Model\ResourceModel\IntegrationJob::TABLE_JOB; // main table
		$this->integrationCatalogData = $this->getTable(\Trans\IntegrationCatalog\Model\ResourceModel\IntegrationDataValue::TABLE_DATA_VALUE); // second table

		$this->getSelect()
			->joinLeft(
				['catalog_job' => $this->integrationCatalogData],
				'main_table.id = catalog_job.jb_id'
			);
		$this->getSelect()->where("main_table.md_id=" . $id);
	}
}