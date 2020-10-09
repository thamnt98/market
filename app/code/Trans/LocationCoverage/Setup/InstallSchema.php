<?php
/**
 * @category Trans
 * @package  Trans_LocationCoverage
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Randy <muhammad.randy@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\LocationCoverage\Setup;

use Trans\LocationCoverage\Api\Data\CityInterface;
use Trans\LocationCoverage\Api\Data\DistrictInterface;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

//@codingStandardsIgnoreFile

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    const TABLE = 'regency';
    const TABLES = 'districts';

    /**
     * {@inheritdoc}
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        // Install City
        $cityTable = $setup->getTable(self::TABLE);

        if (!$setup->tableExists($cityTable)) {
            $table = $setup->getConnection()->newTable(
                $setup->getTable(self::TABLE)
            )->addColumn(
                CityInterface::ENTITY_ID,
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true],
                'City Id'
            )->addColumn(
                CityInterface::REGION_ID,
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Region Id'
            )->addColumn(
                CityInterface::CITY_NAME,
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'City Name'
            );
            $setup->getConnection()->createTable($table);
        }

        // Install District
        $cityTables = $setup->getTable(self::TABLES);

        if (!$setup->tableExists($cityTables)) {
            $tables = $setup->getConnection()->newTable(
                $setup->getTable(self::TABLES)
            )->addColumn(
                DistrictInterface::DISTRICT_ID,
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true, 'auto_increment' => true],
                'District Id'
            )->addColumn(
                DistrictInterface::ENTITY_ID,
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'City Id'
            )->addColumn(
                DistrictInterface::DISTRICT_NAME,
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'District Name'
            )->addColumn(
                DistrictInterface::DISTRICT_KEY,
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'District Key'
            );
            $setup->getConnection()->createTable($tables);
        }

        $setup->endSetup();
    }
}