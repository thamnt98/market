<?php
/**
 * @category Trans
 * @package  Trans_IntegrationCatalogPrice
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author  J.P <jaka.pondan@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationCatalogPrice\Setup;

use Magento\Framework\Db\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Exception\StateException;

use \Trans\IntegrationCatalogPrice\Api\Data\IntegrationJobInterface;
use \Trans\IntegrationCatalogPrice\Api\Data\StorePriceInterface;
use \Trans\IntegrationCatalogPrice\Api\Data\PromotionPriceInterface;
use \Trans\IntegrationCatalogPrice\Api\Data\OnlinePriceInterface;

use \Trans\IntegrationCatalogPrice\Api\Data\IntegrationDataValueInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.1.0', '<')) {
           $this->updateTableCatalogPrice($setup);
        }
        if (version_compare($context->getVersion(), '1.3.0', '<')) {
           $this->updateTableCatalogPricePromotion($setup);
        }
        if (version_compare($context->getVersion(), '1.4.0', '<')) {
           $this->updateTableCatalogPricePromotionColumn($setup);
        }
        if (version_compare($context->getVersion(), '1.5.0', '<')) {
            $this->updateIntegrationJobTable($setup);
        }
        if (version_compare($context->getVersion(), '1.6.0', '<')) {
            $this->deletedTableCatalogPricePromotionColumn($setup);
        }
        if (version_compare($context->getVersion(), '1.7.0', '<')) {
            $this->addOnlinePriceTable($setup);
        }
        if (version_compare($context->getVersion(), '1.8.0', '<')) {
            $this->updateTableCatalogPricePromotionColumnTwo($setup);
        }
        if (version_compare($context->getVersion(), '1.8.1', '<')) {
            $this->updateTableIntegrationCatalogPriceAddConstraint($setup);
        }
    }

    /**
     * Add unique constraint on integration_catalog_store_price
     */
    protected function updateTableIntegrationCatalogPriceAddConstraint($setup)
    {
        $setup->getConnection()->modifyColumn(
            $setup->getTable(StorePriceInterface::TABLE_NAME),
            'sku',
            [
                'type' => Table::TYPE_TEXT,
                'nullable' => true,
                'length' => 20,
                'comment' =>  ucfirst(str_replace('_',' ',StorePriceInterface::PIM_ID))
            ]
        );
        $setup->getConnection()->modifyColumn(
            $setup->getTable(StorePriceInterface::TABLE_NAME),
            'store_code',
            [
                'type' => Table::TYPE_TEXT,
                'nullable' => true,
                'length' => 10,
                'comment' =>  ucfirst(str_replace('_',' ',StorePriceInterface::PIM_ID))
            ]
        );
        $setup->getConnection()->addIndex(
            $setup->getTable(StorePriceInterface::TABLE_NAME),
            'INTEGRATION_CATALOG_STORE_PRICE_SKU_STORE',
            ['sku','store_code'],
            \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
        );
    }

    /**
     * Update Catalog Price Table
     */
    protected function updateTableCatalogPrice($setup){
        $tableName = $setup->getTable(StorePriceInterface::TABLE_NAME);
        // Check if the table already exists
        if ($setup->getConnection()->isTableExists($tableName) != true) {
            throw new StateException(__(
                'Table '. $tableName." is not exist!"
            ));
        }
        // Remove Column
        $setup->getConnection()->dropColumn($setup->getTable($tableName), StorePriceInterface::DROP_ONLINE_SELLING_PRICE);
        $setup->getConnection()->dropColumn($setup->getTable($tableName), StorePriceInterface::DELETED);
        $setup->getConnection()->dropColumn($setup->getTable($tableName), StorePriceInterface::STORE_ATTR_CODE);
        

        // Add Column
        $setup->getConnection()->addColumn(
            $setup->getTable($tableName),
            StorePriceInterface::PIM_ID,
            [
                'type' => Table::TYPE_TEXT,
                'nullable' => true,
                'comment' =>  ucfirst(str_replace('_',' ',StorePriceInterface::PIM_ID)),
                'after' => StorePriceInterface::ID,
            ]
        );

        $setup->getConnection()->addColumn(
            $setup->getTable($tableName),
            StorePriceInterface::PIM_PRODUCT_ID,
            [
                'type' => Table::TYPE_TEXT,
                'nullable' => true,
                'comment' =>  ucfirst(str_replace('_',' ',StorePriceInterface::PIM_PRODUCT_ID)),
                'after' => StorePriceInterface::PIM_ID,
            ]
        );

        $setup->getConnection()->addColumn(
            $setup->getTable($tableName),
            StorePriceInterface::ONLINE_SELLING_PRICE,
            [
                'type' => Table::TYPE_DECIMAL,
                'nullable' => true,
                'comment' =>  ucfirst(str_replace('_',' ',StorePriceInterface::ONLINE_SELLING_PRICE)),
                'after' => StorePriceInterface::PROMO_SELLING_PRICE,
            ]
        );

        $setup->getConnection()->addColumn(
            $setup->getTable($tableName),
            StorePriceInterface::NORMAL_PURCHASE_PRICE,
            [
                'type' => Table::TYPE_DECIMAL,
                'nullable' => true,
                'comment' =>  ucfirst(str_replace('_',' ',StorePriceInterface::NORMAL_PURCHASE_PRICE)),
                'after' => StorePriceInterface::ONLINE_SELLING_PRICE,
            ]
        );

        $setup->getConnection()->addColumn(
            $setup->getTable($tableName),
            StorePriceInterface::PROMO_PURCHASE_PRICE,
            [
                'type' => Table::TYPE_DECIMAL,
                'nullable' => true,
                'comment' =>  ucfirst(str_replace('_',' ',StorePriceInterface::PROMO_PURCHASE_PRICE)),
                'after' => StorePriceInterface::NORMAL_PURCHASE_PRICE,
            ]
        );
        $setup->getConnection()->addColumn(
            $setup->getTable($tableName),
            StorePriceInterface::PIM_CODE,
            [
                'type' => Table::TYPE_TEXT,
                'nullable' => true,
                'comment' =>  ucfirst(str_replace('_',' ',StorePriceInterface::PIM_CODE)),
                'after' => StorePriceInterface::PROMO_PURCHASE_PRICE,
            ]
        );


        $setup->getConnection()->addColumn(
            $setup->getTable($tableName),
            StorePriceInterface::PIM_COMPANY_CODE,
            [
                'type' => Table::TYPE_TEXT,
                'nullable' => true,
                'comment' =>  ucfirst(str_replace('_',' ',StorePriceInterface::PIM_COMPANY_CODE)),
                'after' => StorePriceInterface::PIM_CODE,
            ]
        );

        $setup->getConnection()->addColumn(
            $setup->getTable($tableName),
            StorePriceInterface::CREATED_AT,
            [
                'type' => Table::TYPE_TIMESTAMP,
                'nullable' => false,
                'comment' =>  ucfirst(str_replace('_',' ',StorePriceInterface::CREATED_AT)),
                'after' => StorePriceInterface::PIM_COMPANY_CODE,
            ]
        );

        $setup->getConnection()->addColumn(
            $setup->getTable($tableName),
            StorePriceInterface::UPDATED_AT,
            [
                'type' => Table::TYPE_TIMESTAMP,
                'nullable' => false,
                'comment' =>  ucfirst(str_replace('_',' ',StorePriceInterface::UPDATED_AT)),
                'after' => StorePriceInterface::CREATED_AT,
            ]
        );

       
    }

    /**
     * Update Catalog Price promotion Table
     */
    protected function updateTableCatalogPricePromotion($setup){
        // Get tutorial_simplenews table
        $tableName = $setup->getTable(PromotionPriceInterface::TABLE_NAME);
        // Check if the table already exists
        if ($setup->getConnection()->isTableExists($tableName) != true) {
            // Create tutorial_simplenews table
            $table = $setup->getConnection()
                ->newTable($tableName)
                ->addColumn(
                    PromotionPriceInterface::ID,
                    Table::TYPE_BIGINT,
                    null,
                    [
                        'identity' => true,
                        'unsigned' => true,
                        'nullable' => false,
                        'primary'  => true,
                    ],
                    ucfirst(PromotionPriceInterface::ID)
                )
                ->addColumn(
                    PromotionPriceInterface::PIM_ID,
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => true, 'default' => ""],
                    ucfirst(PromotionPriceInterface::PIM_ID)
                )
                ->addColumn(
                    PromotionPriceInterface::PIM_PRODUCT_ID,
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => true, 'default' => ""],
                    ucfirst(PromotionPriceInterface::PIM_PRODUCT_ID)
                )
                ->addColumn(
                    PromotionPriceInterface::PIM_PROMOTION_ID,
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => true, 'default' => ""],
                    ucfirst(PromotionPriceInterface::PIM_PROMOTION_ID)
                )
                ->addColumn(
                    PromotionPriceInterface::PIM_NAME,
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => true, 'default' => ""],
                    ucfirst(PromotionPriceInterface::PIM_NAME)
                )
                ->addColumn(
                    PromotionPriceInterface::PIM_COMPANY_CODE,
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => true, 'default' => ""],
                    ucfirst(PromotionPriceInterface::PIM_COMPANY_CODE)
                )
                ->addColumn(
                    PromotionPriceInterface::PIM_PROMOTION_TYPE,
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => true, 'default' => ""],
                    ucfirst(PromotionPriceInterface::PIM_PROMOTION_TYPE)
                )
                ->addColumn(
                    PromotionPriceInterface::PIM_DISCOUNT_TYPE,
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => true, 'default' => ""],
                    ucfirst(PromotionPriceInterface::PIM_DISCOUNT_TYPE)
                )
                ->addColumn(
                    PromotionPriceInterface::PIM_ITEM_TYPE,
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => true, 'default' => ""],
                    ucfirst(PromotionPriceInterface::PIM_ITEM_TYPE)
                )
                ->addColumn(
                    PromotionPriceInterface::PIM_MIX_MATCH_CODE,
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => true, 'default' => ""],
                    ucfirst(PromotionPriceInterface::PIM_MIX_MATCH_CODE)
                )
                ->addColumn(
                    PromotionPriceInterface::PIM_SLIDING_DISC_TYPE,
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => true, 'default' => ""],
                    ucfirst(PromotionPriceInterface::PIM_SLIDING_DISC_TYPE)
                )
                ->addColumn(
                    PromotionPriceInterface::PIM_SKU,
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => true, 'default' => ""],
                    ucfirst(PromotionPriceInterface::PIM_SKU)
                )
                ->addColumn(
                    PromotionPriceInterface::PIM_STORECODE,
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => true, 'default' => ""],
                    ucfirst(PromotionPriceInterface::PIM_STORECODE)
                )
                ->addColumn(
                    PromotionPriceInterface::PIM_SALESRULE_ID,
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => true, 'default' => 0],
                    ucfirst(PromotionPriceInterface::PIM_SALESRULE_ID)
                )
                ->addColumn(
                    PromotionPriceInterface::CREATED_AT,
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => 0],
                    ucfirst(PromotionPriceInterface::CREATED_AT)
                )
                ->addColumn(
                    PromotionPriceInterface::UPDATED_AT,
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => 0],
                    ucfirst(PromotionPriceInterface::UPDATED_AT)
                )
                ->setComment('Catalog Promotion Price')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');
            $setup->getConnection()->createTable($table);
        }
    }

    /**
     * Update Catalog Price Table
     */
    protected function updateTableCatalogPricePromotionColumn($setup){
        $tableName = $setup->getTable(PromotionPriceInterface::TABLE_NAME);
        // Check if the table already exists
        if ($setup->getConnection()->isTableExists($tableName) != true) {
            throw new StateException(__(
                'Table '. $tableName." is not exist!"
            ));
        }  

        // Add Column
        $setup->getConnection()->addColumn(
            $setup->getTable($tableName),
            PromotionPriceInterface::PIM_ROW_ID,
            [
                'type' => Table::TYPE_INTEGER,
                'nullable' => true,
                'default' => 0,
                'comment' =>  ucfirst(str_replace('_',' ',PromotionPriceInterface::PIM_ROW_ID)),
                'after' => PromotionPriceInterface::PIM_SALESRULE_ID,
            ]
        );

        $setup->getConnection()->addColumn(
            $setup->getTable($tableName),
            PromotionPriceInterface::PIM_REQUIRED_POINT,
            [
                'type' => Table::TYPE_TEXT,
                'nullable' => true,
                'comment' =>  ucfirst(str_replace('_',' ',PromotionPriceInterface::PIM_REQUIRED_POINT)),
                'after' => PromotionPriceInterface::PIM_ROW_ID,
            ]
        );

        $setup->getConnection()->addColumn(
            $setup->getTable($tableName),
            PromotionPriceInterface::PIM_PROMO_SELLING_PRICE,
            [
                'type' => Table::TYPE_TEXT,
                'nullable' => true,
                'comment' =>  ucfirst(str_replace('_',' ',PromotionPriceInterface::PIM_PROMO_SELLING_PRICE)),
                'after' => PromotionPriceInterface::PIM_REQUIRED_POINT,
            ]
        );

        $setup->getConnection()->addColumn(
            $setup->getTable($tableName),
            PromotionPriceInterface::PIM_PERCENT_DISC,
            [
                'type' => Table::TYPE_TEXT,
                'nullable' => true,
                'comment' =>  ucfirst(str_replace('_',' ',PromotionPriceInterface::PIM_PERCENT_DISC)),
                'after' => PromotionPriceInterface::PIM_PROMO_SELLING_PRICE,
            ]
        );

        $setup->getConnection()->addColumn(
            $setup->getTable($tableName),
            PromotionPriceInterface::PIM_AMOUNT_OFF,
            [
                'type' => Table::TYPE_TEXT,
                'nullable' => true,
                'comment' =>  ucfirst(str_replace('_',' ',PromotionPriceInterface::PIM_AMOUNT_OFF)),
                'after' => PromotionPriceInterface::PIM_PERCENT_DISC,
            ]
        );

    }

    /**
     * LAST_UPDATED table Integration Job Table add new field 'hit'
     * @param $installer
     */
    public function updateIntegrationJobTable($setup){
        // add field hit
        $tableName= IntegrationJobInterface::TABLE_NAME;
        if ($setup->getConnection()->isTableExists($tableName) == true) {

            $setup->getConnection()->addColumn(
                $setup->getTable($tableName),
                IntegrationJobInterface::HIT,
                [
                    'type' => Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' =>  ucfirst(str_replace('_',' ',IntegrationJobInterface::HIT)),
                    'after' => IntegrationJobInterface::STATUS,
                ]
            );
        }
    }

    /**
     * Update Catalog Price Table
     */
    protected function deletedTableCatalogPricePromotionColumn($setup){
        $tableName = $setup->getTable(PromotionPriceInterface::TABLE_NAME);
        // Check if the table already exists
        if ($setup->getConnection()->isTableExists($tableName) != true) {
            throw new StateException(__(
                'Table '. $tableName." is not exist!"
            ));
        }  

        // remove column
        $setup->getConnection()->dropColumn($setup->getTable($tableName), PromotionPriceInterface::PIM_ID);
        $setup->getConnection()->dropColumn($setup->getTable($tableName), PromotionPriceInterface::PIM_PRODUCT_ID);
    }


    /**
     * Create table online store
     * @param $installer
     */
    protected function addOnlinePriceTable($setup) {

        // Get tutorial_simplenews table
        $tableName = $setup->getTable(OnlinePriceInterface::TABLE_NAME);
        // Check if the table already exists
        if ($setup->getConnection()->isTableExists($tableName) != true) {
            // Create tutorial_simplenews table
            $table = $setup->getConnection()
                ->newTable($tableName)
                ->addColumn(
                    OnlinePriceInterface::ID,
                    Table::TYPE_BIGINT,
                    null,
                    [
                        'identity' => true,
                        'unsigned' => true,
                        'nullable' => false,
                        'primary'  => true,
                    ],
                    ucfirst(OnlinePriceInterface::ID)
                )
                ->addColumn(
                    OnlinePriceInterface::SKU,
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => true, 'default' => ""],
                    ucfirst(OnlinePriceInterface::SKU)
                )
                ->addColumn(
                    OnlinePriceInterface::ONLINE_SELLING_PRICE,
                    Table::TYPE_DECIMAL,
                    null,
                    ['nullable' => true, 'default' => 0],
                    ucfirst(OnlinePriceInterface::ONLINE_SELLING_PRICE)
                )
                ->addColumn(
                    OnlinePriceInterface::IS_EXCLUSIVE,
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => true, 'default' => 1],
                    ucfirst(OnlinePriceInterface::IS_EXCLUSIVE)
                )
                ->addColumn(
                    OnlinePriceInterface::START_DATE,
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => true, 'default' => ""],
                    ucfirst(OnlinePriceInterface::START_DATE)
                )
                ->addColumn(
                    OnlinePriceInterface::END_DATE,
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => true, 'default' => ""],
                    ucfirst(OnlinePriceInterface::END_DATE)
                )
                ->addColumn(
                    OnlinePriceInterface::MODIFIED_AT,
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => true, 'default' => ""],
                    ucfirst(OnlinePriceInterface::MODIFIED_AT)
                )
                ->addColumn(
                    OnlinePriceInterface::STAGING_ID,
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => true],
                    ucfirst(OnlinePriceInterface::STAGING_ID)
                )
                ->addColumn(
                    OnlinePriceInterface::CREATED_AT,
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                    ucfirst(OnlinePriceInterface::CREATED_AT)
                )
                ->addColumn(
                    OnlinePriceInterface::UPDATED_AT,
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
                    ucfirst(OnlinePriceInterface::UPDATED_AT)
                )
                ->setComment('Catalog Online Price')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');
            $setup->getConnection()->createTable($table);
        }
    }

    /**
     * Update integration catalog promotion price point_per_unit
     */
    protected function updateTableCatalogPricePromotionColumnTwo($setup){
        $tableName = $setup->getTable(PromotionPriceInterface::TABLE_NAME);
        // Check if the table already exists
        if ($setup->getConnection()->isTableExists($tableName) != true) {
            throw new StateException(__(
                'Table '. $tableName." is not exist!"
            ));
        }  

        // Add Column
        $setup->getConnection()->addColumn(
            $setup->getTable($tableName),
            PromotionPriceInterface::PIM_POINT_PER_UNIT,
            [
                'type' => Table::TYPE_TEXT,
                'nullable' => true,
                'comment' =>  ucfirst(str_replace('_',' ',PromotionPriceInterface::PIM_POINT_PER_UNIT)),
                'after' => PromotionPriceInterface::PIM_AMOUNT_OFF,
            ]
        );
    }
    
}
