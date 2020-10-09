<?php
/**
 * @category Trans
 * @package  Trans_Sprint
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Imam Kusuma <imam.kusuma@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */
namespace Trans\Sprint\Setup;

use \Magento\Framework\Setup\SchemaSetupInterface;
use \Magento\Framework\Db\Ddl\Table;
use \Magento\Framework\Setup\ModuleContextInterface;
use \Magento\Framework\Setup\InstallSchemaInterface;
use \Trans\Sprint\Api\Data\SprintResponseInterface;

/**
 * {@inheritdoc}
 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
 */
class InstallSchema implements InstallSchemaInterface
{

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {

        $installer = $setup;
        $installer->startSetup();

        $this->createSprintResponse($installer);
        
        $installer->endSetup();
    }

    /**
     * Create table doku_orders
     *
     * @param $installer
     */
    private function createSprintResponse($installer)
    {
        $table = $installer->getConnection()
            ->newTable($installer->getTable(SprintResponseInterface::TABLE_NAME))
            ->addColumn(
                SprintResponseInterface::ID,
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true],
                'Id'
            )
            ->addColumn(
                SprintResponseInterface::STORE_ID,
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Store Id'
            )
            ->addColumn(
                SprintResponseInterface::QUOTE_ID,
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Quote Id'
            )
            ->addColumn(
                SprintResponseInterface::TRANSACTION_NO,
                Table::TYPE_TEXT,
                50,
                ['nullable' => false],
                'Order Increment Id'
            )
            ->addColumn(
                SprintResponseInterface::CURRENCY,
                Table::TYPE_TEXT,
                5,
                ['nullable' => true],
                'Currency'
            )
            ->addColumn(
                SprintResponseInterface::INSERT_STATUS,
                Table::TYPE_TEXT,
                2,
                ['nullable' => true],
                'response status'
            )->addColumn(
                SprintResponseInterface::INSERT_MESSAGE,
                Table::TYPE_TEXT,
                200,
                ['nullable' => true],
                'insert message'
            )->addColumn(
                SprintResponseInterface::INSERT_ID,
                Table::TYPE_TEXT,
                15,
                ['nullable' => true],
                'Insert ID'
            )->addColumn(
                SprintResponseInterface::REDIRECT_URL,
                Table::TYPE_TEXT,
                100,
                ['nullable' => true],
                'Redirect URL'
            )->addColumn(
                SprintResponseInterface::REDIRECT_DATA,
                Table::TYPE_TEXT,
                225,
                ['nullable' => true],
                'Redirect Data'
            )->addColumn(
                SprintResponseInterface::ADDITIONAL_DATA,
                Table::TYPE_TEXT,
                225,
                ['nullable' => true],
                'Additional Data'
            )->addColumn(
                SprintResponseInterface::CREATED_AT,
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Created Time'
            )->addColumn(
                SprintResponseInterface::UPDATED_AT,
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
                'Updated Time'
            )
            ->setComment('Sprint Response Table');

        $installer->getConnection()->createTable($table);
    }
}
