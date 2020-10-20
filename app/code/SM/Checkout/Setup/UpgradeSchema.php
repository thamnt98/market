<?php
declare(strict_types=1);

namespace SM\Checkout\Setup;

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
        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $setup->getConnection()->changeColumn(
                $setup->getTable('quote_address'),
                'store_pick_up_time',
                'date',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    ['default' => null],
                    'comment' => 'date'
                ]
            );
            $setup->getConnection()->changeColumn(
                $setup->getTable('quote_address'),
                'store_pick_up_delivery',
                'time',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'comment' => 'time'
                ]
            );
            $setup->getConnection()->changeColumn(
                $setup->getTable('quote_address'),
                'location',
                'split_store_code',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'comment' => 'Split Store Code'
                ]
            );
            $setup->getConnection()->changeColumn(
                $setup->getTable('quote_address'),
                'item_rate',
                'pre_shipping_method',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'comment' => 'Pre Shipping Method'
                ]
            );
            $setup->getConnection()->changeColumn(
                $setup->getTable('sales_order'),
                'store_pick_up_time',
                'date',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    ['default' => null],
                    'comment' => 'date'
                ]
            );
            $setup->getConnection()->changeColumn(
                $setup->getTable('sales_order'),
                'store_pick_up_delivery',
                'time',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'comment' => 'time'
                ]
            );
            $setup->getConnection()->changeColumn(
                $setup->getTable('sales_order'),
                'location',
                'split_store_code',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'comment' => 'Split Store Code'
                ]
            );
            $setup->getConnection()->changeColumn(
                $setup->getTable('sales_order_grid'),
                'store_pick_up_time',
                'date',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    ['default' => null],
                    'comment' => 'date'
                ]
            );
            $setup->getConnection()->changeColumn(
                $setup->getTable('sales_order_grid'),
                'store_pick_up_delivery',
                'time',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'comment' => 'time'
                ]
            );
            $setup->getConnection()->changeColumn(
                $setup->getTable('sales_order_grid'),
                'location',
                'split_store_code',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'comment' => 'Split Store Code'
                ]
            );
        }
        if (version_compare($context->getVersion(), '1.0.2', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('quote'),
                'payment_failure_time',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'length' => 11,
                    'nullable' => true,
                    'comment' => 'Payment Failure Time'
                ]
            );
        }
        if (version_compare($context->getVersion(), '1.0.3', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('omni_location_mapping'),
                'support_shipping',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                    'nullable' => false,
                    'default' => false,
                    'comment' => 'Support Shipping'
                ]
            );
        }
        if (version_compare($context->getVersion(), '1.0.4', '<')) {
            $tableName = $setup->getTable('omni_shipping_postcode');
            $table = $setup->getConnection()->newTable($tableName)
                ->addColumn(
                    'id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true]
                )
                ->addColumn(
                    'omni_code',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['nullable' => false, 'default' => '']
                )
                ->addColumn(
                    'post_code',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['nullable' => false, 'default' => '']
                )
                ->setOption('charset', 'utf8');
            $setup->getConnection()->createTable($table);
        }
        if (version_compare($context->getVersion(), '1.0.5', '<')) {
            $tableName = $setup->getTable('omni_shipping_postcode');
            $setup->getConnection()->dropColumn($tableName, 'omni_code');
            $setup->getConnection()->addColumn(
                $setup->getTable('omni_shipping_postcode'),
                'sub_district',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => false,
                    'default' => false,
                    'comment' => 'Sub District'
                ]
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('omni_shipping_postcode'),
                'district',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => false,
                    'default' => false,
                    'comment' => 'District'
                ]
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('omni_shipping_postcode'),
                'jenis',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => false,
                    'default' => false,
                    'comment' => 'Jenis'
                ]
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('omni_shipping_postcode'),
                'city',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => false,
                    'default' => false,
                    'comment' => 'city'
                ]
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('omni_shipping_postcode'),
                'regency',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => false,
                    'default' => false,
                    'comment' => 'regency'
                ]
            );
        }
        $setup->endSetup();
    }
}
