<?php
/**
 * @category Trans
 * @package  Trans_Brand
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author  J.P <jaka.pondan@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Brand\Setup;

use Magento\Framework\Db\Ddl\Table;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use \Trans\Brand\Api\Data\BrandInterface;

class UpgradeSchema implements UpgradeSchemaInterface {
	public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context) {
		$setup->startSetup();

		if (version_compare($context->getVersion(), '1.1.0', '<')) {
			$this->updateTableBrand($setup);
		}

		if (version_compare($context->getVersion(), '1.1.1', '<')) {
			$setup->getConnection()->addColumn(
				$setup->getTable('amasty_amshopby_option_setting'),
				'pim_code',
				[
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'nullable' => false,
					'comment' => 'pim_code',
					'before' => 'title',
				]
			);
			$setup->getConnection()->addColumn(
				$setup->getTable('amasty_amshopby_option_setting'),
				'pim_id',
				[
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'nullable' => false,
					'comment' => 'pim_id',
					'after' => 'pim_code',
				]
			);
		}
	}

	/**
	 * Update Catalog Price Table
	 */
	protected function updateTableBrand($setup) {
		$tableName = $setup->getTable(BrandInterface::TABLE_NAME);
		// Check if the table already exists
		if ($setup->getConnection()->isTableExists($tableName) != true) {
			throw new StateException(__(
				'Table ' . $tableName . " is not exist!"
			));
		}

		// Add Column
		$setup->getConnection()->addColumn(
			$setup->getTable($tableName),
			BrandInterface::PIM_ID,
			[
				'type' => Table::TYPE_TEXT,
				'nullable' => true,
				'comment' => ucfirst(str_replace('_', ' ', BrandInterface::PIM_ID)),
				'after' => BrandInterface::BRAND_ID,
			]
		);

		$setup->getConnection()->addColumn(
			$setup->getTable($tableName),
			BrandInterface::PIM_CODE,
			[
				'type' => Table::TYPE_TEXT,
				'nullable' => true,
				'comment' => ucfirst(str_replace('_', ' ', BrandInterface::PIM_CODE)),
				'after' => BrandInterface::PIM_ID,
			]
		);

		$setup->getConnection()->addColumn(
			$setup->getTable($tableName),
			BrandInterface::COMPANY_CODE,
			[
				'type' => Table::TYPE_TEXT,
				'nullable' => true,
				'comment' => ucfirst(str_replace('_', ' ', BrandInterface::COMPANY_CODE)),
				'after' => BrandInterface::PIM_CODE,
			]
		);

	}

}
