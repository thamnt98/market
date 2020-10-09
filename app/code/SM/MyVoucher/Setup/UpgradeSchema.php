<?php

namespace SM\MyVoucher\Setup;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

class UpgradeSchema implements UpgradeSchemaInterface{

    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        // TODO: Implement upgrade() method.
        $installer = $setup;
        $installer->startSetup();
        $connection = $setup->getConnection();

        if (version_compare($context->getVersion(), '0.0.2', '<')) {

            $saleRuleTable = $setup->getTable('salesrule');

            //add columns
            $connection->addColumn(
                $saleRuleTable,
                'how_to_use',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'default' => '',
                    'comment' => 'How To Use'
                ]
            );

            $connection->addColumn(
                $saleRuleTable,
                'term_condition',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'default' => '',
                    'comment' => 'Term And Condition'
                ]
            );
        }
        if (version_compare($context->getVersion(), '0.0.3', '<')) {

            $saleRuleTable = $setup->getTable('salesrule');

            //add columns
            $connection->addColumn(
                $saleRuleTable,
                'voucher_image',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'default' => '',
                    'comment' => 'Voucher Image'
                ]
            );
        }
        $installer->endSetup();
    }
}