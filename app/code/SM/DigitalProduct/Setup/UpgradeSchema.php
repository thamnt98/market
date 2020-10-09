<?php
declare(strict_types=1);

namespace SM\DigitalProduct\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/**
 * Upgrade the Sales_Order Table to remove extra field
 */
class UpgradeSchema implements UpgradeSchemaInterface
{

    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if (!$context->getVersion() || $context->getVersion() == '' || version_compare($context->getVersion(), '1.0.1', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('sales_order'),
                'digital_transaction_fail',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'default' => 0,
                    'comment' => 'Digital Transaction Fail'
                ]
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('sales_order_grid'),
                'digital_transaction_fail',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'default' => 0,
                    'comment' => 'Digital Transaction Fail'
                ]
            );
        }
        $setup->endSetup();
    }
}

