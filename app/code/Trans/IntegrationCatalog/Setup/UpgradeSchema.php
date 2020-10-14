<?php
/**
 * @category Trans
 * @package  Trans_IntegrationCatalog
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author  J.P <jaka.pondan@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationCatalog\Setup;

use Magento\Framework\Db\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Exception\StateException;

use \Trans\Integration\Api\Data\IntegrationChannelInterface;
use \Trans\IntegrationCatalog\Api\Data\IntegrationProductInterface;
use \Trans\IntegrationCatalog\Api\Data\ProductAssociationInterface;

use \Trans\IntegrationCatalog\Api\Data\ConfigurableProductCronSynchInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.1.0', '<')) {
           $this->updateTableCatalogProduct($setup);
        }

        if (version_compare($context->getVersion(), '1.5.0', '<')) {
            $this->updateTableCatalogProductDigital($setup);
        }

        if (version_compare($context->getVersion(), '1.6.0', '<')) {
           $this->updateTableProductAssociation($setup);
        }

        if (version_compare($context->getVersion(), '1.7.0', '<')) {
           $this->updateTableProductAssociationColumn($setup);
        }

        if (version_compare($context->getVersion(), '1.7.2', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable(IntegrationProductInterface::TABLE_NAME),
                IntegrationProductInterface::ATTRIBUTE_LIST,
                [
                    'type' => Table::TYPE_TEXT,
                    'nullable' => TRUE,
                    'comment' => 'PRODUCT ATTRIBUTE LIST'
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.7.4', '<')) {
          $this->addConfigurableProductCronSynchTable($setup);
        }

    }

    /**
     * Synch Configurable Product data
     * @param SchemaSetupInterface $setup
     */
    public function addConfigurableProductCronSynchTable($setup)
    {
      if ($setup->getConnection()->isTableExists(ConfigurableProductCronSynchInterface::TABLE_NAME) != true) {

            $table = $setup->getConnection()
                ->newTable(ConfigurableProductCronSynchInterface::TABLE_NAME)
                ->addColumn(
                    'row_id',
                    Table::TYPE_BIGINT,
                    null,
                    ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary'  => true]
                )
                ->addColumn(
                    'cron_name',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false]
                )
                ->addColumn(
                    'cron_offset',
                    Table::TYPE_BIGINT,
                    null,
                    ['unsigned' => true, 'nullable' => false]
                )
                ->addColumn(
                    'cron_length',
                    Table::TYPE_BIGINT,
                    null,
                    ['unsigned' => true, 'nullable' => false]
                )
                ->addColumn(
                    'last_updated',
                    Table::TYPE_DATETIME,
                    null,
                    ['unsigned' => true, 'nullable' => false, 'primary'  => true]
                )
                ->setComment('Cron configurable Product Watch Attribute Cron')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');
            $setup->getConnection()->createTable($table);
        }
    }

    /**
     * Fix Table Catalog Product
     */
    protected function updateTableCatalogProduct($setup){
        $tableName = $setup->getTable(IntegrationProductInterface::TABLE_NAME);
        // Check if the table already exists
        if ($setup->getConnection()->isTableExists($tableName) != true) {
            throw new StateException(__(
                'Table '. $tableName." is not exist!"
            ));
        }
        // Remove Column
        $setup->getConnection()->dropColumn($setup->getTable($tableName), IntegrationProductInterface::PIM_CATGORY_ID);
        // $setup->getConnection()->dropColumn($setup->getTable($tableName), IntegrationProductInterface::INTEGRATION_DATA_ID);
        // Add Column
        $setup->getConnection()->addColumn(
            $setup->getTable($tableName),
            IntegrationProductInterface::PIM_CATEGORY_ID,
            [
                'type' => Table::TYPE_TEXT,
                'nullable' => true,
                'comment' =>  ucfirst(str_replace('_',' ',IntegrationProductInterface::PIM_CATEGORY_ID)),
                'after' => IntegrationProductInterface::MAGENTO_PARENT_ID,
            ]
        );

        $setup->getConnection()->addColumn(
            $setup->getTable($tableName),
            IntegrationProductInterface::MAGENTO_CATEGORY_IDS,
            [
                'type' => Table::TYPE_TEXT,
                'nullable' => true,
                'comment' =>  ucfirst(str_replace('_',' ',IntegrationProductInterface::MAGENTO_CATEGORY_IDS)),
                'after' => IntegrationProductInterface::MAGENTO_PARENT_ID,
            ]
        );

        $setup->getConnection()->addColumn(
            $setup->getTable($tableName),
            IntegrationProductInterface::STATUS_CONFIGURABLE,
            [
                'type' => Table::TYPE_SMALLINT,
                'nullable' => false,
                'default'=>0,
                'comment' => '0 = not configurable ,1 = configurable not save , 5 = configurable need update , 10 = configurable updated',

                'after' => IntegrationProductInterface::PIM_SKU,
            ]
        );

    }

    /**
     * Update Catalog Product Association Table
     */
    protected function updateTableProductAssociation($setup){
        // Get tutorial_simplenews table
        $tableName = $setup->getTable(ProductAssociationInterface::TABLE_NAME);
        // Check if the table already exists
        if ($setup->getConnection()->isTableExists($tableName) != true) {
            // Create tutorial_simplenews table
            $table = $setup->getConnection()
                ->newTable($tableName)
                ->addColumn(
                   ProductAssociationInterface::ID,
                    Table::TYPE_BIGINT,
                    null,
                    [
                        'identity' => true,
                        'unsigned' => true,
                        'nullable' => false,
                        'primary'  => true,
                    ],
                    ucfirst(ProductAssociationInterface::ID)
                )
                ->addColumn(
                    ProductAssociationInterface::ASSOCIATION_RULE_ID,
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => true, 'default' => ""],
                    ucfirst(ProductAssociationInterface::ASSOCIATION_RULE_ID)
                )
                ->addColumn(
                    ProductAssociationInterface::ASSOCIATION_NAME,
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => true, 'default' => ""],
                    ucfirst(ProductAssociationInterface::ASSOCIATION_STATUS_JOB_DATA)
                )
                ->addColumn(
                    ProductAssociationInterface::ASSOCIATION_DISPLAY_RULE,
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => true, 'default' => ""],
                    ucfirst(ProductAssociationInterface::ASSOCIATION_PRODUCT_DISPLAY_BY)
                )
                ->addColumn(
                    ProductAssociationInterface::ASSOCIATION_PRODUCT_DISPLAY,
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => true, 'default' => ""],
                    ucfirst(ProductAssociationInterface::ASSOCIATION_PRODUCT_DISPLAY)
                )
                ->addColumn(
                    ProductAssociationInterface::ASSOCIATION_EXCEPT_DISPLAY,
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => true, 'default' => ""],
                    ucfirst(ProductAssociationInterface::ASSOCIATION_EXCEPT_DISPLAY)
                )
                ->addColumn(
                    ProductAssociationInterface::DELETED,
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => true, 'default' => ""],
                    ucfirst(ProductAssociationInterface::DELETED)
                )
                ->addColumn(
                    ProductAssociationInterface::PIM_ID,
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => true, 'default' => ""],
                    ucfirst(ProductAssociationInterface::PIM_ID)
                )
                ->addColumn(
                    ProductAssociationInterface::PIM_NAME,
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => true, 'default' => ""],
                    ucfirst(ProductAssociationInterface::PIM_NAME)
                )
                ->addColumn(
                    ProductAssociationInterface::CREATED_AT,
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => 0],
                    ucfirst(ProductAssociationInterface::CREATED_AT)
                )
                ->addColumn(
                    ProductAssociationInterface::UPDATED_AT,
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => 0],
                    ucfirst(ProductAssociationInterface::UPDATED_AT)
                )
                ->setComment('Product Promo Association')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');
            $setup->getConnection()->createTable($table);
        }
    }

     /**
     * Fix Table Catalog Product
     */
    protected function updateTableCatalogProductDigital($setup){
        $tableName = $setup->getTable(IntegrationProductInterface::TABLE_NAME);
        // Check if the table already exists
        if ($setup->getConnection()->isTableExists($tableName) != true) {
            throw new StateException(__(
                'Table '. $tableName." is not exist!"
            ));
        }
        // Remove First
        $setup->getConnection()->dropColumn($setup->getTable($tableName), IntegrationProductInterface::PRODUCT_TYPE);
        // Add Column
        $setup->getConnection()->addColumn(
            $setup->getTable($tableName),
            IntegrationProductInterface::PRODUCT_TYPE,
            [
                'type' => Table::TYPE_INTEGER,
                'nullable' => false,
                'default'  =>1, // 1 is simple // 3 is digital
                'comment' =>  ucfirst(str_replace('_',' ',IntegrationProductInterface::PRODUCT_TYPE))." 1= SImple , 3 =Digital",
                'after' => IntegrationProductInterface::PIM_SKU,
            ]
        );

    }

    /**
     * Update product association Table
     */
    protected function updateTableProductAssociationColumn($setup){
        $tableName = $setup->getTable(ProductAssociationInterface::TABLE_NAME);
        // Check if the table already exists
        if ($setup->getConnection()->isTableExists($tableName) != true) {
            throw new StateException(__(
                'Table '. $tableName." is not exist!"
            ));
        }

        // Add Column
        $setup->getConnection()->addColumn(
            $setup->getTable($tableName),
            ProductAssociationInterface::ASSOCIATION_DISPLAY_RULE_BY,
            [
                'type' => Table::TYPE_INTEGER,
                'nullable' => true,
                'comment' =>  ucfirst(str_replace('_',' ',ProductAssociationInterface::ASSOCIATION_DISPLAY_RULE_BY)),
                'after' => ProductAssociationInterface::ASSOCIATION_DISPLAY_RULE,
            ]
        );

        $setup->getConnection()->addColumn(
            $setup->getTable($tableName),
            ProductAssociationInterface::ASSOCIATION_PRODUCT_DISPLAY_BY,
            [
                'type' => Table::TYPE_INTEGER,
                'nullable' => true,
                'comment' =>  ucfirst(str_replace('_',' ',ProductAssociationInterface::ASSOCIATION_PRODUCT_DISPLAY_BY)),
                'after' => ProductAssociationInterface::ASSOCIATION_PRODUCT_DISPLAY,
            ]
        );

        $setup->getConnection()->addColumn(
            $setup->getTable($tableName),
            ProductAssociationInterface::ASSOCIATION_DISPLAY_SEQUENCE,
            [
                'type' => Table::TYPE_TEXT,
                'nullable' => true,
                'comment' =>  ucfirst(str_replace('_',' ',ProductAssociationInterface::ASSOCIATION_DISPLAY_SEQUENCE)),
                'after' => ProductAssociationInterface::ASSOCIATION_EXCEPT_DISPLAY,
            ]
        );

        $setup->getConnection()->addColumn(
            $setup->getTable($tableName),
            ProductAssociationInterface::ASSOCIATION_STATUS_JOB_DATA,
            [
                'type' => Table::TYPE_INTEGER,
                'nullable' => true,
                'comment' =>  ucfirst(str_replace('_',' ',ProductAssociationInterface::ASSOCIATION_STATUS_JOB_DATA)),
                'after' => ProductAssociationInterface::PIM_NAME,
            ]
        );

        $setup->getConnection()->addColumn(
            $setup->getTable($tableName),
            ProductAssociationInterface::STATUS,
            [
                'type' => Table::TYPE_INTEGER,
                'nullable' => true,
                'comment' =>  ucfirst(str_replace('_',' ',ProductAssociationInterface::STATUS)),
                'after' => ProductAssociationInterface::ASSOCIATION_STATUS_JOB_DATA,
            ]
        );

        $setup->getConnection()->addColumn(
            $setup->getTable($tableName),
            ProductAssociationInterface::ASSOCIATION_LINK_ID,
            [
                'type' => Table::TYPE_TEXT,
                'nullable' => true,
                'comment' =>  ucfirst(str_replace('_',' ',ProductAssociationInterface::ASSOCIATION_LINK_ID)),
                'after' => ProductAssociationInterface::ASSOCIATION_RULE_ID,
            ]
        );

        $setup->getConnection()->addColumn(
            $setup->getTable($tableName),
            ProductAssociationInterface::MODIFIED_AT,
            [
                'type' => Table::TYPE_TIMESTAMP,
                'nullable' => true,
                'comment' =>  ucfirst(str_replace('_',' ',ProductAssociationInterface::MODIFIED_AT)),
                'after' => ProductAssociationInterface::UPDATED_AT,
            ]
        );

        $setup->getConnection()->changeColumn(
            $setup->getTable($tableName),
            ProductAssociationInterface::DELETED,
            ProductAssociationInterface::DELETED,
            [
                'type' => Table::TYPE_INTEGER
            ]
        );

        // Remove Column
        $setup->getConnection()->dropColumn($setup->getTable($tableName), ProductAssociationInterface::PIM_NAME);
    }

}
