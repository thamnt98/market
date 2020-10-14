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

namespace Trans\IntegrationCatalog\Setup;

use \Magento\Framework\Db\Ddl\Table;
use \Magento\Framework\Setup\InstallSchemaInterface;
use \Magento\Framework\Setup\ModuleContextInterface;
use \Magento\Framework\Setup\SchemaSetupInterface;
use \Trans\IntegrationCatalog\Api\Data\IntegrationDataValueInterface;
use \Trans\IntegrationCatalog\Api\Data\IntegrationJobInterface;
use \Trans\IntegrationCatalog\Api\Data\IntegrationProductInterface;
use \Trans\IntegrationCategory\Api\Data\IntegrationCategoryInterface;
use \Trans\Integration\Api\Data\IntegrationChannelInterface;

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

		$this->integrationJobTable($setup);
		$this->integrationDataValueTable($setup);
		$this->integrationProduct($setup);

		$this->updateIntegrationJobTable($setup);
		$this->updateIntegrationJobTableWithLastJbId($setup);
		$this->updateIntegrationCatalogProductSKU($setup);

		$setup->endSetup();
	}

	/**
	 * Create table Job
	 * @param $installer
	 */
	public function integrationJobTable($setup) {

		// Get tutorial_simplenews table
		$tableName = $setup->getTable(\Trans\IntegrationCatalog\Api\Data\IntegrationJobInterface::TABLE_NAME);
		// Check if the table already exists
		if ($setup->getConnection()->isTableExists($tableName) != true) {
			// Create tutorial_simplenews table
			$table = $setup->getConnection()
				->newTable($tableName)
				->addColumn(
					IntegrationJobInterface::ID,
					Table::TYPE_BIGINT,
					null,
					[
						'identity' => true,
						'unsigned' => true,
						'nullable' => false,
						'primary'  => true,
					],
					ucfirst(IntegrationJobInterface::ID)
				)
				->addColumn(
					IntegrationJobInterface::METHOD_ID,
					Table::TYPE_INTEGER,
					null,
					['nullable' => false, 'default' => 0],
					ucfirst(IntegrationJobInterface::METHOD_ID)
				)
				->addColumn(
					IntegrationJobInterface::BATCH_ID,
					Table::TYPE_TEXT,
					null,
					['nullable' => true, 'default' => ""],
					ucfirst(IntegrationJobInterface::BATCH_ID)
				)
				->addColumn(
					IntegrationJobInterface::LAST_UPDATED,
					Table::TYPE_DATETIME,
					null,
					['nullable' => true, 'default' => null],
					ucfirst(IntegrationJobInterface::LAST_UPDATED)
				)
				->addColumn(
					IntegrationJobInterface::TOTAL_DATA,
					Table::TYPE_INTEGER,
					null,
					['nullable' => true, 'default' => NULL],
					ucfirst(IntegrationJobInterface::TOTAL_DATA)
				)
				->addColumn(
					IntegrationJobInterface::LIMIT,
					Table::TYPE_INTEGER,
					null,
					['nullable' => true, 'default' => NULL],
					ucfirst(IntegrationJobInterface::LIMIT)
				)
				->addColumn(
					IntegrationJobInterface::OFFSET,
					Table::TYPE_INTEGER,
					null,
					['nullable' => true, 'default' => NULL],
					ucfirst(IntegrationJobInterface::OFFSET)
				)
				->addColumn(
					IntegrationJobInterface::START_JOB,
					Table::TYPE_DATETIME,
					null,
					['nullable' => true, 'default' => null],
					ucfirst(IntegrationJobInterface::START_JOB)
				)
				->addColumn(
					IntegrationJobInterface::END_JOB,
					Table::TYPE_DATETIME,
					null,
					['nullable' => true, 'default' => null],
					ucfirst(IntegrationJobInterface::END_JOB)
				)
				->addColumn(
					IntegrationJobInterface::MESSAGE,
					Table::TYPE_TEXT,
					null,
					['nullable' => true, 'default' => NULL],
					ucfirst(IntegrationJobInterface::MESSAGE)
				)
				->addColumn(
					IntegrationJobInterface::STATUS,
					Table::TYPE_SMALLINT,
					null,
					['nullable' => false, 'default' => 1, 'comment' => '1 = Waiting, 10 = Process , 20 = Cancel , 30 = Complete'],
					ucfirst(IntegrationJobInterface::STATUS)
				)
				->addColumn(
					IntegrationChannelInterface::CREATED_AT,
					Table::TYPE_TIMESTAMP,
					null,
					['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
					'Created Time'
				)
				->addColumn(
					IntegrationChannelInterface::UPDATED_AT,
					Table::TYPE_TIMESTAMP,
					null,
					['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
					'Updated Time'
				)
				->addColumn(
					IntegrationChannelInterface::CREATED_BY,
					Table::TYPE_INTEGER,
					null,
					['nullable' => true, 'default' => null],
					ucfirst(IntegrationChannelInterface::CREATED_BY)
				)
				->addColumn(
					IntegrationChannelInterface::UPDATED_BY,
					Table::TYPE_INTEGER,
					null,
					['nullable' => true, 'default' => null],
					ucfirst(IntegrationChannelInterface::UPDATED_BY)
				)
				->setComment('Integration Job')
				->setOption('type', 'InnoDB')
				->setOption('charset', 'utf8');
			$setup->getConnection()->createTable($table);
		}
	}

	/**
	 * Create table Data Value
	 * @param $installer
	 */
	public function integrationDataValueTable($setup) {

		// Get tutorial_simplenews table
		$tableName = $setup->getTable(\Trans\IntegrationCatalog\Api\Data\IntegrationDataValueInterface::TABLE_NAME);
		// Check if the table already exists
		if ($setup->getConnection()->isTableExists($tableName) != true) {
			// Create tutorial_simplenews table
			$table = $setup->getConnection()
				->newTable($tableName)
				->addColumn(
					IntegrationDataValueInterface::ID,
					Table::TYPE_BIGINT,
					null,
					[
						'identity' => true,
						'unsigned' => true,
						'nullable' => false,
						'primary'  => true,
					],
					ucfirst(IntegrationDataValueInterface::ID)
				)
				->addColumn(
					IntegrationDataValueInterface::JOB_ID,
					Table::TYPE_BIGINT,
					null,
					['nullable' => false, 'default' => 0],
					ucfirst(IntegrationDataValueInterface::JOB_ID)
				)
				->addColumn(
					IntegrationDataValueInterface::DATA_VALUE,
					Table::TYPE_TEXT,
					null,
					['nullable' => true, 'default' => NULL],
					ucfirst(IntegrationDataValueInterface::DATA_VALUE)
				)
				->addColumn(
					IntegrationDataValueInterface::MESSAGE,
					Table::TYPE_TEXT,
					null,
					['nullable' => true, 'default' => NULL],
					ucfirst(IntegrationDataValueInterface::MESSAGE)
				)
				->addColumn(
					IntegrationDataValueInterface::STATUS,
					Table::TYPE_SMALLINT,
					null,
					['nullable' => false, 'default' => 1],
					ucfirst(IntegrationDataValueInterface::STATUS)
				)
				->addColumn(
					IntegrationChannelInterface::CREATED_AT,
					Table::TYPE_TIMESTAMP,
					null,
					['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
					'Created Time'
				)
				->addColumn(
					IntegrationChannelInterface::UPDATED_AT,
					Table::TYPE_TIMESTAMP,
					null,
					['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
					'Updated Time'
				)
				->setComment('Integration Job')
				->setOption('type', 'InnoDB')
				->setOption('charset', 'utf8');
			$setup->getConnection()->createTable($table);
		}
	}

	/**
	 * Create Table Product Mapping
	 * @param $installer
	 */
	public function integrationProduct($setup) {

		// Get tutorial_simplenews table
		$tableName = $setup->getTable(IntegrationProductInterface::TABLE_NAME);
		// Check if the table already exists
		if ($setup->getConnection()->isTableExists($tableName) != true) {
			// Create tutorial_simplenews table
			$table = $setup->getConnection()
				->newTable($tableName)
				->addColumn(
					IntegrationProductInterface::ID,
					Table::TYPE_INTEGER,
					null,
					[
						'identity' => true,
						'unsigned' => true,
						'nullable' => false,
						'primary'  => true,
					],
					ucfirst(IntegrationProductInterface::ID)
				)
				->addColumn(
					IntegrationProductInterface::INTEGRATION_DATA_ID,
					Table::TYPE_INTEGER,
					null,
					['nullable' => true, 'default' => null],
					ucfirst(IntegrationProductInterface::INTEGRATION_DATA_ID)
				)
				->addColumn(
					IntegrationProductInterface::MAGENTO_ENTITY_ID,
					Table::TYPE_INTEGER,
					null,
					['nullable' => true, 'default' => null],
					ucfirst(IntegrationProductInterface::MAGENTO_ENTITY_ID)
				)
				->addColumn(
					IntegrationProductInterface::PIM_ID,
					Table::TYPE_TEXT,
					null,
					['nullable' => true, 'default' => null],
					ucfirst(IntegrationProductInterface::PIM_ID)
				)
				->addColumn(
					IntegrationProductInterface::ITEM_ID,
					Table::TYPE_TEXT,
					null,
					['nullable' => true, 'default' => null],
					ucfirst(IntegrationProductInterface::ITEM_ID)
				)
				->addColumn(
					IntegrationProductInterface::MAGENTO_PARENT_ID,
					Table::TYPE_INTEGER,
					null,
					['nullable' => true, 'default' => null],
					ucfirst(IntegrationProductInterface::MAGENTO_PARENT_ID)
				)
				->addColumn(
					IntegrationProductInterface::PIM_CATGORY_ID,
					Table::TYPE_TEXT,
					null,
					['nullable' => true, 'default' => ''],
					ucfirst(IntegrationProductInterface::PIM_CATGORY_ID)
				)
				->addColumn(
					IntegrationCategoryInterface::CREATED_AT,
					Table::TYPE_TIMESTAMP,
					null,
					['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
					'Created Time'
				)
				->addColumn(
					IntegrationCategoryInterface::UPDATED_AT,
					Table::TYPE_TIMESTAMP,
					null,
					['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
					'Updated Time'
				)
				->setComment('Integration Product')
				->setOption('type', 'InnoDB')
				->setOption('charset', 'utf8');
			$setup->getConnection()->createTable($table);
		}
	}

	/**
     * LAST_UPDATED table Integration Job Table add new field 'hit'
     * @param $installer
     */
    public function updateIntegrationJobTable($setup){
        // add field hit
        $setup->getConnection()->addColumn(
            \Trans\IntegrationCatalog\Api\Data\IntegrationJobInterface::TABLE_NAME,
            IntegrationJobInterface::HIT,
            [
                'type' => Table::TYPE_TEXT,
                'nullable' => true, 
                'default' => NULL,
                'comment' => IntegrationJobInterface::HIT
            ]
        );
    }
    

    /**
     * LAST_UPDATED table Integration Job Table add new field 'hit'
     * @param $installer
     */
    public function updateIntegrationJobTableWithLastJbId($setup)
    {

        // Get Job Table
        $tableName = $setup->getTable(\Trans\IntegrationCatalog\Api\Data\IntegrationJobInterface::TABLE_NAME);
        // Check if the table already exists
        if ($setup->getConnection()->isTableExists($tableName) == true) {
            // add field hit
            $setup->getConnection()->addColumn(
                $tableName,
                IntegrationJobInterface::LAST_JB_ID,
                [
                    'type' => Table::TYPE_BIGINT,
                    'nullable' => true, 
                    'default' => NULL,
                    'comment' =>  IntegrationJobInterface::LAST_JB_ID,
                ]
            );
        }
    }

	/**
	 * Integration Table Update SKU
	 */
    public function updateIntegrationCatalogProductSKU($setup){
        // Get Product Mapping Table
        $tableName = $setup->getTable(\Trans\IntegrationCatalog\Api\Data\IntegrationProductInterface::TABLE_NAME);
        // Check if the table already exists
        if ($setup->getConnection()->isTableExists($tableName) == true) {
            // add field hit
            $setup->getConnection()->addColumn(
                $tableName,
                IntegrationProductInterface::PIM_SKU,
                [
                    'type' => Table::TYPE_TEXT,
                    'nullable' => true, 
                    'default' => NULL,
                    'comment' =>  IntegrationProductInterface::PIM_SKU,
                ]
            );
        }
    }
}