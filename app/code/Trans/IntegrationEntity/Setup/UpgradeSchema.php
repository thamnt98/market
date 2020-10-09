<?php
/**
 * @category Trans
 * @package  Trans_IntegrationEntity
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author  J.P <jaka.pondan@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationEntity\Setup;

use Magento\Framework\Db\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Exception\StateException;

use \Trans\Integration\Api\Data\IntegrationChannelInterface;
use \Trans\IntegrationEntity\Api\Data\IntegrationProductAttributeTypeInterface;
use \Trans\IntegrationEntity\Api\Data\IntegrationProductAttributeSetInterface;
use \Trans\IntegrationEntity\Api\Data\IntegrationProductAttributeSetChildInterface;
use \Trans\IntegrationEntity\Api\Data\IntegrationJobInterface;


class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.1.0', '<')) {
           $this->insertTableCatalogProductAttributeEntityType($setup);
		}
		
		if (version_compare($context->getVersion(), '1.2.0', '<')) {
			$this->updateIntegrationJobTable($setup);
		}

		if (version_compare($context->getVersion(), '1.2.2', '<')) {
			$this->addAttributeSet($setup);
		}

        if (version_compare($context->getVersion(), '1.2.5', '<')) {
            $this->addAttributeSetChild($setup);
            $this->removeAttributeSet($setup);
        }

        if (version_compare($context->getVersion(), '1.2.6', '<')) {
            $this->changeTypeAttributeSet($setup);
        }
        
    }

    /**
     * Update Catalog Price Table
     */
    protected function insertTableCatalogProductAttributeEntityType($setup){
        // Get tutorial_simplenews table
		$tableName = $setup->getTable(\Trans\IntegrationEntity\Api\Data\IntegrationProductAttributeTypeInterface::TABLE_NAME);
		// Check if the table already exists
		if ($setup->getConnection()->isTableExists($tableName) != true) {
			// Create tutorial_simplenews table
			$table = $setup->getConnection()
				->newTable($tableName)
				->addColumn(
					IntegrationProductAttributeTypeInterface::ID,
					Table::TYPE_BIGINT,
					null,
					[
						'identity' => true,
						'unsigned' => true,
						'nullable' => false,
						'primary'  => true,
					],
					ucfirst(IntegrationProductAttributeTypeInterface::ID)
				)
				->addColumn(
					IntegrationProductAttributeTypeInterface::PIM_TYPE_ID,
					Table::TYPE_TEXT,
					null,
					['nullable' => false, 'default' => ""],
					ucfirst(IntegrationProductAttributeTypeInterface::PIM_TYPE_ID)
                )
                ->addColumn(
					IntegrationProductAttributeTypeInterface::PIM_TYPE_CODE,
					Table::TYPE_TEXT,
					null,
					['nullable' => null, 'default' => null],
					ucfirst(IntegrationProductAttributeTypeInterface::PIM_TYPE_CODE)
                )
                ->addColumn(
					IntegrationProductAttributeTypeInterface::PIM_TYPE_NAME,
					Table::TYPE_TEXT,
					null,
					['nullable' => null, 'default' => null],
					ucfirst(IntegrationProductAttributeTypeInterface::PIM_TYPE_NAME)
                )
                ->addColumn(
					IntegrationProductAttributeTypeInterface::BACKEND_CODE,
					Table::TYPE_TEXT,
					null,
					['nullable' => null, 'default' => null],
					ucfirst(IntegrationProductAttributeTypeInterface::BACKEND_CODE)
                )
                ->addColumn(
					IntegrationProductAttributeTypeInterface::FRONTEND_CODE,
					Table::TYPE_TEXT,
					null,
					['nullable' => null, 'default' => null],
					ucfirst(IntegrationProductAttributeTypeInterface::FRONTEND_CODE)
				)
				->addColumn(
					IntegrationProductAttributeTypeInterface::IS_SWATCH,
					Table::TYPE_SMALLINT,
					null,
					['nullable' => false, 'default' => 0],
					ucfirst(IntegrationProductAttributeTypeInterface::IS_SWATCH)
                )
				->addColumn(
					IntegrationProductAttributeTypeInterface::STATUS,
					Table::TYPE_SMALLINT,
					null,
					['nullable' => false, 'default' => 1, 'comment' => '1 = Active, 10 = Not Active'],
					ucfirst(IntegrationProductAttributeTypeInterface::STATUS)
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
				->setComment('Integration Product Attribute Type')
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
		// Get Product Mapping Table
        $tableName = $setup->getTable(IntegrationJobInterface::TABLE_NAME);
        // Check if the table already exists
        if ($setup->getConnection()->isTableExists($tableName) == true) {
			$setup->getConnection()->dropColumn($setup->getTable($tableName), IntegrationJobInterface::HIT);
			// add field hit
			$setup->getConnection()->addColumn(
				$tableName,
				IntegrationJobInterface::HIT,
				[
					'type' => Table::TYPE_INTEGER,
					'nullable' => false, 
					'default' => 0,
					'comment' => IntegrationJobInterface::HIT,
					'after'=>IntegrationJobInterface::MESSAGE
				]
			);
		}
    }

    /**
     * Update product attribute set Table
     */
    protected function addAttributeSet($setup){
        // Get tutorial_simplenews table
        $tableName = $setup->getTable(IntegrationProductAttributeSetInterface::TABLE_NAME);
        // Check if the table already exists
        if ($setup->getConnection()->isTableExists($tableName) != true) {
            // Create tutorial_simplenews table
            $table = $setup->getConnection()
                ->newTable($tableName)
                ->addColumn(
                    IntegrationProductAttributeSetInterface::ID,
                    Table::TYPE_BIGINT,
                    null,
                    [
                        'identity' => true,
                        'unsigned' => true,
                        'nullable' => false,
                        'primary'  => true,
                    ],
                    ucfirst(IntegrationProductAttributeSetInterface::ID)
                )
                ->addColumn(
                    IntegrationProductAttributeSetInterface::PIM_ID,
                    Table::TYPE_TEXT,
                    50,
                    [
                        'nullable' => true,
                    ],
                    ucfirst(IntegrationProductAttributeSetInterface::PIM_ID)
                )
                ->addColumn(
                    IntegrationProductAttributeSetInterface::NAME,
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => true, 'default' => ""],
                    ucfirst(IntegrationProductAttributeSetInterface::NAME)
                )
                ->addColumn(
                    IntegrationProductAttributeSetInterface::CODE,
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => true, 'default' => ""],
                    ucfirst(IntegrationProductAttributeSetInterface::CODE)
                )
                ->addColumn(
                    IntegrationProductAttributeSetInterface::ATTRIBUTE_SET_ID,
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => true, 'default' => ""],
                    ucfirst(IntegrationProductAttributeSetInterface::ATTRIBUTE_SET_ID)
                )
                ->addColumn(
                    IntegrationProductAttributeSetInterface::DELETED,
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => true],
                    ucfirst(IntegrationProductAttributeSetInterface::DELETED)
                )
                ->addColumn(
                    IntegrationProductAttributeSetInterface::DELETED_ATTRIBUTE_LIST,
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => true],
                    ucfirst(IntegrationProductAttributeSetInterface::DELETED_ATTRIBUTE_LIST)
                )
                ->addColumn(
                    IntegrationProductAttributeSetInterface::STATUS,
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => true],
                    ucfirst(IntegrationProductAttributeSetInterface::STATUS)
                )
                ->addColumn(
                    IntegrationProductAttributeSetInterface::CREATED_AT,
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => 0],
                    ucfirst(IntegrationProductAttributeSetInterface::CREATED_AT)
                )
                ->addColumn(
                    IntegrationProductAttributeSetInterface::UPDATED_AT,
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => 0],
                    ucfirst(IntegrationProductAttributeSetInterface::UPDATED_AT)
                )
                ->setComment('Product attribute set')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');
            $setup->getConnection()->createTable($table);
        }
    }

    /**
     * Update product attribute set Table
     */
    protected function addAttributeSetChild($setup){
        // Get tutorial_simplenews table
        $tableName = $setup->getTable(IntegrationProductAttributeSetChildInterface::TABLE_NAME);
        // Check if the table already exists
        if ($setup->getConnection()->isTableExists($tableName) != true) {
            // Create tutorial_simplenews table
            $table = $setup->getConnection()
                ->newTable($tableName)
                ->addColumn(
                    IntegrationProductAttributeSetChildInterface::ID,
                    Table::TYPE_BIGINT,
                    null,
                    [
                        'identity' => true,
                        'unsigned' => true,
                        'nullable' => false,
                        'primary'  => true,
                    ],
                    ucfirst(IntegrationProductAttributeSetChildInterface::ID)
                )
                ->addColumn(
                    IntegrationProductAttributeSetChildInterface::PIM_ID,
                    Table::TYPE_TEXT,
                    50,
                    [
                        'nullable' => true,
                    ],
                    ucfirst(IntegrationProductAttributeSetChildInterface::PIM_ID)
                )
                ->addColumn(
                    IntegrationProductAttributeSetChildInterface::CODE,
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => true, 'default' => ""],
                    ucfirst(IntegrationProductAttributeSetChildInterface::CODE)
                )
                ->addColumn(
                    IntegrationProductAttributeSetChildInterface::DELETED_ATTRIBUTE_LIST,
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => true],
                    ucfirst(IntegrationProductAttributeSetChildInterface::DELETED_ATTRIBUTE_LIST)
                )
                ->addColumn(
                    IntegrationProductAttributeSetChildInterface::STATUS,
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => true],
                    ucfirst(IntegrationProductAttributeSetChildInterface::STATUS)
                )
                ->addColumn(
                    IntegrationProductAttributeSetChildInterface::CREATED_AT,
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => 0],
                    ucfirst(IntegrationProductAttributeSetChildInterface::CREATED_AT)
                )
                ->addColumn(
                    IntegrationProductAttributeSetChildInterface::UPDATED_AT,
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => 0],
                    ucfirst(IntegrationProductAttributeSetChildInterface::UPDATED_AT)
                )
                ->setComment('Product attribute set Child')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');
            $setup->getConnection()->createTable($table);
        }
    }

    /**
     * remove product attribute set Table
     */
    protected function removeAttributeSet($setup){
        $tableName = $setup->getTable(IntegrationProductAttributeSetInterface::TABLE_NAME);
        // Check if the table already exists
        if ($setup->getConnection()->isTableExists($tableName) != true) {
            throw new StateException(__(
                'Table '. $tableName." is not exist!"
            ));
        }
        // Remove Column
        $setup->getConnection()->dropColumn($setup->getTable($tableName), IntegrationProductAttributeSetInterface::CODE);
        $setup->getConnection()->dropColumn($setup->getTable($tableName), IntegrationProductAttributeSetInterface::DELETED_ATTRIBUTE_LIST);
    }

    /**
     * change type pim id product attribute set Table
     */
    protected function changeTypeAttributeSet($setup){
        $setup->getConnection()->changeColumn(
            $setup->getTable(IntegrationProductAttributeSetInterface::TABLE_NAME),
            IntegrationProductAttributeSetInterface::PIM_ID,
            IntegrationProductAttributeSetInterface::PIM_ID,
            [
                'type' => Table::TYPE_TEXT,
                'length' => 50
            ]
        );
    }
    
}
