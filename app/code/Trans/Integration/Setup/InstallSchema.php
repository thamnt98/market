<?php
/**
 * @category Trans
 * @package  Trans_Integration
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   J.P <jaka.pondan@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Integration\Setup;

use \Magento\Framework\Setup\InstallSchemaInterface;
use \Magento\Framework\Setup\ModuleContextInterface;
use \Magento\Framework\Setup\SchemaSetupInterface;

use \Magento\Framework\Db\Ddl\Table;

use \Trans\Integration\Api\Data\IntegrationChannelInterface;
use \Trans\Integration\Api\Data\IntegrationChannelMethodInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        
        $setup->startSetup();

        $this->integrationChannelTable($setup);
        $this->integrationChannelMethodTable($setup);
        
        $setup->endSetup();
    }

    /**
     * Create table Channel
     * @param $installer
     */
    public function integrationChannelTable($setup)
    {

       // Get tutorial_simplenews table
       $tableName = $setup->getTable(IntegrationChannelInterface::TABLE_NAME);
       // Check if the table already exists
       if ($setup->getConnection()->isTableExists($tableName) != true) {
           // Create tutorial_simplenews table
           $table = $setup->getConnection()
               ->newTable($tableName)
               ->addColumn(
                IntegrationChannelInterface::ID,
                   Table::TYPE_INTEGER,
                   null,
                   [
                       'identity' => true,
                       'unsigned' => true,
                       'nullable' => false,
                       'primary' => true
                   ],
                   ucfirst(IntegrationChannelInterface::ID)
               )
               ->addColumn(
                IntegrationChannelInterface::NAME,
                Table::TYPE_TEXT,
                   null,
                   ['nullable' => false, 'default' => ''],
                   ucfirst(IntegrationChannelInterface::NAME)
               )
               ->addColumn(
                IntegrationChannelInterface::CODE,
                Table::TYPE_TEXT,
                   null,
                   ['nullable' => false, 'default' => ''],
                   ucfirst(IntegrationChannelInterface::CODE)
               )
               ->addColumn(
                IntegrationChannelInterface::URL,
                   Table::TYPE_TEXT,
                   null,
                   ['nullable' => true, 'default' => ''],
                   ucfirst(IntegrationChannelInterface::URL)
               )
               ->addColumn(
                IntegrationChannelInterface::ENV,
                  Table::TYPE_TEXT,
                   null,
                   ['nullable' => true, 'default' => ''],
                   ucfirst(IntegrationChannelInterface::ENV)
               )
               ->addColumn(
                IntegrationChannelInterface::STATUS,
                Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'default' => 1],
                ucfirst(IntegrationChannelInterface::STATUS)
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
               ->setComment('Integration Channel')
               ->setOption('type', 'InnoDB')
               ->setOption('charset', 'utf8');
           $setup->getConnection()->createTable($table);
       }
    }

    /**
     * Create table Channel Method
     * @param $installer
     */
    public function integrationChannelMethodTable($setup)
    {

       // Get tutorial_simplenews table
       $tableName = $setup->getTable(IntegrationChannelMethodInterface::TABLE_NAME);
       // Check if the table already exists
       if ($setup->getConnection()->isTableExists($tableName) != true) {
           // Create tutorial_simplenews table
           $table = $setup->getConnection()
               ->newTable($tableName)
               ->addColumn(
                IntegrationChannelMethodInterface::ID,
                   Table::TYPE_INTEGER,
                   null,
                   [
                       'identity' => true,
                       'unsigned' => true,
                       'nullable' => false,
                       'primary' => true
                   ],
                   ucfirst(IntegrationChannelMethodInterface::ID)
               )
               ->addColumn(
                IntegrationChannelMethodInterface::CHANNEL_ID,
                Table::TYPE_INTEGER,
                   null,
                   ['nullable' => false, 'default' => 0],
                   ucfirst(IntegrationChannelMethodInterface::CHANNEL_ID)
               )
               ->addColumn(
                IntegrationChannelMethodInterface::TAG,
                   Table::TYPE_TEXT,
                   null,
                   ['nullable' => true, 'default' => ''],
                   ucfirst(IntegrationChannelMethodInterface::TAG). " entity"
               )
               ->addColumn(
                IntegrationChannelMethodInterface::DESCRIPTION,
                   Table::TYPE_TEXT,
                   null,
                   ['nullable' => true, 'default' => ''],
                   ucfirst(IntegrationChannelMethodInterface::DESCRIPTION)
               )
               ->addColumn(
                IntegrationChannelMethodInterface::METHOD,
                Table::TYPE_TEXT,
                   null,
                   ['nullable' => true, 'default' => ''],
                   ucfirst(IntegrationChannelMethodInterface::METHOD)
               )
               ->addColumn(
                IntegrationChannelMethodInterface::HEADERS,
                Table::TYPE_TEXT,
                   null,
                   ['nullable' => true, 'default' => NULL],
                   ucfirst(IntegrationChannelMethodInterface::HEADERS)
               )
               ->addColumn(
                IntegrationChannelMethodInterface::QUERY_PARAMS,
                Table::TYPE_TEXT,
                   null,
                   ['nullable' => true, 'default' => NULL],
                   ucfirst(IntegrationChannelMethodInterface::QUERY_PARAMS)
               )
               ->addColumn(
                IntegrationChannelMethodInterface::BODY,
                Table::TYPE_TEXT,
                   null,
                   ['nullable' => true, 'default' => NULL],
                   ucfirst(IntegrationChannelMethodInterface::BODY)
               )
               ->addColumn(
                IntegrationChannelMethodInterface::PATH,
                Table::TYPE_TEXT,
                   null,
                   ['nullable' => true, 'default' => NULL],
                   ucfirst(IntegrationChannelMethodInterface::PATH)
               )
               ->addColumn(
                   IntegrationChannelMethodInterface::LIMIT,
                   Table::TYPE_INTEGER,
                   null,
                   ['nullable' => false, 'default' => 1],
                   ucfirst(IntegrationChannelMethodInterface::LIMIT)
               )
               ->addColumn(
                IntegrationChannelMethodInterface::STATUS,
                Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'default' => 1],
                ucfirst(IntegrationChannelMethodInterface::STATUS)
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
               ->setComment('Integration Channel Method')
               ->setOption('type', 'InnoDB')
               ->setOption('charset', 'utf8');
           $setup->getConnection()->createTable($table);
       }
    }

   
}