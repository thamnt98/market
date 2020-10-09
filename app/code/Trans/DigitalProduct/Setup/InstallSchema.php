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

namespace Trans\DigitalProduct\Setup;

use \Magento\Framework\Db\Ddl\Table;
use \Magento\Framework\Setup\InstallSchemaInterface;
use \Magento\Framework\Setup\ModuleContextInterface;
use \Magento\Framework\Setup\SchemaSetupInterface;
use \Trans\DigitalProduct\Api\Data\DigitalProductOperatorListInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface {
	/**
	 * {@inheritdoc}
	 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
	 */
	public function install(SchemaSetupInterface $setup, ModuleContextInterface $context) {

		$setup->startSetup();

		$this->digitalProductOperatorList($setup);

		$setup->endSetup();
	}

	/**
	 * Create table Job
	 * @param $installer
	 */
	public function digitalProductOperatorList($setup) {

		// Get tutorial_simplenews table
		$tableName = $setup->getTable(DigitalProductOperatorListInterface::TABLE_NAME);
		// Check if the table already exists
		if ($setup->getConnection()->isTableExists($tableName) != true) {
			// Create tutorial_simplenews table
			$table = $setup->getConnection()
				->newTable($tableName)
				->addColumn(
					DigitalProductOperatorListInterface::ID,
					Table::TYPE_BIGINT,
					null,
					[
						'identity' => true,
						'unsigned' => true,
						'nullable' => false,
						'primary'  => true,
					],
					ucfirst(DigitalProductOperatorListInterface::ID)
				)
				->addColumn(
					DigitalProductOperatorListInterface::BRAND_ID,
					Table::TYPE_INTEGER,
					null,
					['nullable' => false, 'default' => 0],
					ucfirst(DigitalProductOperatorListInterface::BRAND_ID)
				)
				->addColumn(
					DigitalProductOperatorListInterface::OPERATOR_NAME,
					Table::TYPE_TEXT,
					null,
					['nullable' => true, 'default' => ""],
					ucfirst(DigitalProductOperatorListInterface::OPERATOR_NAME)
				)
				->addColumn(
					DigitalProductOperatorListInterface::SERVICE_NAME,
					Table::TYPE_TEXT,
					null,
					['nullable' => true, 'default' => ""],
					ucfirst(DigitalProductOperatorListInterface::SERVICE_NAME)
				)
				->addColumn(
					DigitalProductOperatorListInterface::PREFIX_NUMBER,
					Table::TYPE_TEXT,
					null,
					['nullable' => true, 'default' => ""],
					ucfirst(DigitalProductOperatorListInterface::PREFIX_NUMBER)
				)
				->addColumn(
					DigitalProductOperatorListInterface::CREATED_AT,
					Table::TYPE_TIMESTAMP,
					null,
					['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
					'Created Time'
				)
				->addColumn(
					DigitalProductOperatorListInterface::UPDATED_AT,
					Table::TYPE_TIMESTAMP,
					null,
					['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
					'Updated Time'
				)
				->setComment('Digital Product Operator List')
				->setOption('type', 'InnoDB')
				->setOption('charset', 'utf8');
			$setup->getConnection()->createTable($table);
		}
	}
}