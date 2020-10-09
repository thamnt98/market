<?php


namespace SM\CustomPrice\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        if (!$installer->tableExists('omni_location_mapping')) {
            $table = $installer->getConnection()->newTable(
                $installer->getTable('omni_location_mapping')
            )
                               ->addColumn(
                                   'id',
                                   \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                                   null,
                                   [
                                       'identity' => true,
                                       'nullable' => false,
                                       'primary'  => true,
                                       'unsigned' => true,
                                   ],
                                   'ID'
                               )
                               ->addColumn(
                                   'omni_code',
                                   \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                                   25,
                                   ['nullable => false'],
                                   'Omni Store Code'
                               )
                               ->addColumn(
                                   'district_id',
                                   \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                                   25,
                                   [],
                                   'District Id'
                               )
                               ->setComment('Post Table');
            $installer->getConnection()->createTable($table);
        }
        if (version_compare($context->getVersion(), '1.0.2', '<')) {
            $installer->getConnection()->addColumn(
                $installer->getTable('quote'),
                'omni_store_id',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 20,
                    'nullable' => true,
                    'comment' => 'Omni Store Id'
                ]
            );
        }
        $installer->endSetup();


    }
}
