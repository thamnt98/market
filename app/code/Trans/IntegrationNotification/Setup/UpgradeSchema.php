<?php
/**
 * @category Trans
 * @package  Trans_IntegrationNotification
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpmagento.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationNotification\Setup;
 
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Trans\IntegrationNotification\Api\Data\IntegrationNotificationLogInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * @SuppressWarnings(PHPMD)
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * upgrade schema
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        
        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $this->installTableIntegrationNotificationLog($setup);
        }

        $setup->endSetup();
    }

    /**
     * install table IntegrationNotification log
     * @param SchemaSetupInterface $setup
     */
    protected function installTableIntegrationNotificationLog($setup)
    {
        $table = $setup->getConnection()
            ->newTable($setup->getTable(IntegrationNotificationLogInterface::TABLE_NAME))
            ->addColumn(
                IntegrationNotificationLogInterface::ID,
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true],
                'Id'
            )
            ->addColumn(
                IntegrationNotificationLogInterface::CHANNEL,
                Table::TYPE_TEXT,
                10,
                ['nullable' => false],
                'Channel'
            )
            ->addColumn(
                IntegrationNotificationLogInterface::PARAM,
                Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'param'
            )->addColumn(
                IntegrationNotificationLogInterface::PARAM_ENCRYPT,
                Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'param encrypted'
            )
            ->addColumn(
                IntegrationNotificationLogInterface::RESPONSE,
                Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'message'
            )
            ->addColumn(
                IntegrationNotificationLogInterface::CREATED_AT,
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Created Time'
            )->addColumn(
                IntegrationNotificationLogInterface::UPDATED_AT,
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
                'Updated Time'
            )
            ->setComment('IntegrationNotification log table');

        $setup->getConnection()->createTable($table);
    }
}
