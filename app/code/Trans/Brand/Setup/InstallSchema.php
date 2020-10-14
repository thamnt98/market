<?php
/**
 * Class InstallSchema
 *
 * PHP version 7
 *
 * @category Trans
 * @package  Trans_Brand
 * @author   J.P <jaka.pondan@transdigital.co.id>
 */
namespace Trans\Brand\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\DB\Adapter\AdapterInterface;

/**
 * Class InstallSchema
 *
 * PHP version 7
 *
 * @category Trans
 * @package  Trans_Brand
 * @author   J.P <jaka.pondan@transdigital.co.id>
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * Installs DB schema for a module
     *
     * @param SchemaSetupInterface   $setup   setup
     * @param ModuleContextInterface $context context
     *
     * @return void
     */
    public function install(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $installer = $setup;

        $installer->startSetup();

        $table = $installer->getConnection()
            ->newTable($installer->getTable('trans_brand'))
            ->addColumn(
                'brand_id',
                Table::TYPE_SMALLINT,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true],
                'Brand ID'
            )
            ->addColumn(
                'title',
                Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Brand Title'
            )
            ->addColumn(
                'description',
                Table::TYPE_TEXT,
                '64k',
                ['nullable' => true],
                'Brand Description'
            )
            ->addColumn(
                'meta_title',
                Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Brand SEO Meta Title'
            )
            ->addColumn(
                'meta_keywords',
                Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Brand SEO Meta Keyword'
            )
            ->addColumn(
                'meta_description',
                Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Brand SEO Meta Description'
            )
            ->addColumn(
                'url_key',
                Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Brand URL'
            )
            ->addColumn(
                'image',
                Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Brand Image'
            )
            ->addColumn(
                'banner_image',
                Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Brand Banner Image'
            )
            ->addColumn(
                'position',
                Table::TYPE_SMALLINT,
                null,
                ['nullable' => true],
                'Brand Position'
            )
            ->addColumn(
                'status',
                Table::TYPE_SMALLINT,
                null,
                ['nullable' => false,'default' => '1'],
                'Is Brand Active?'
            )
            ->addColumn(
                'creation_time',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                'Creation Time'
            )->addColumn(
                'update_time',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
                'Modification Time'
            )

            // use for fulltext search
            ->addIndex(
                $installer->getIdxName(
                    'trans_brand',
                    ['title'],
                    AdapterInterface::INDEX_TYPE_FULLTEXT
                ),
                ['title'],
                ['type' => AdapterInterface::INDEX_TYPE_FULLTEXT]
            )
            ->setComment('Trans Brand Table');

        $installer->getConnection()->createTable($table);

        $brandProducts = $setup->getConnection()->newTable(
            $setup->getTable('trans_brand_products')
        )->addColumn(
            'brand_product_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Brand Product Id'
        )->addColumn(
            'brand_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false],
            'Brand Id'
        )->addColumn(
            'product_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false,'unsigned' => true],
            'Product Id'
        )->addForeignKey(
            $setup->getFkName(
                'brand_foreign_key',
                'brand_id',
                'trans_brand',
                'brand_id'
            ),
            'brand_id',
            $setup->getTable('trans_brand'),
            'brand_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $setup->getFkName(
                'brand_product_foreign_key',
                'product_id',
                'catalog_product_entity',
                'entity_id'
            ),
            'product_id',
            $setup->getTable('catalog_product_entity'),
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Trans Brand Products Table'
        );
        $setup->getConnection()->createTable($brandProducts);

        $installer->endSetup();
    }
}
