<?php
/**
 * @category Trans
 * @package  Trans_IntegrationLogistic
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Randy <muhammad.randy@ctcorpdigital.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationLogistic\Setup;

use \Magento\Framework\Db\Ddl\Table;
use \Magento\Framework\Setup\InstallSchemaInterface;
use \Magento\Framework\Setup\ModuleContextInterface;
use \Magento\Framework\Setup\SchemaSetupInterface;
use \Trans\IntegrationLogistic\Api\Data\TrackingLogisticInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {

        $setup->startSetup();
        $this->trackingLogistic($setup);
        $setup->endSetup();
    }

    /**
     * Create Table TPL Tracking Logistic
     * @param $installer
     */
    public function trackingLogistic($setup)
    {

        // Get table
        $tableName = $setup->getTable(TrackingLogisticInterface::TABLE_NAME);
        // Check if the table already exists
        if ($setup->getConnection()->isTableExists($tableName) != true) {
            // Create table
            $table = $setup->getConnection()
                ->newTable($tableName)
                ->addColumn(
                    TrackingLogisticInterface::TRACKING_ID,
                    Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'unsigned' => true,
                        'nullable' => false,
                        'primary' => true,
                    ],
                    ucfirst(TrackingLogisticInterface::TRACKING_ID)
                )
                ->addColumn(
                    TrackingLogisticInterface::COURIER_ID,
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'default' => 0],
                    ucfirst(TrackingLogisticInterface::COURIER_ID)
                )
                ->addColumn(
                    TrackingLogisticInterface::COURIER_NAME,
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false, 'default' => ''],
                    ucfirst(TrackingLogisticInterface::COURIER_NAME)
                )
                ->addColumn(
                    TrackingLogisticInterface::ORDER_NUMBER,
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false, 'default' => ''],
                    ucfirst(TrackingLogisticInterface::ORDER_NUMBER)
                )
                ->addColumn(
                    TrackingLogisticInterface::AWB_NUMBER,
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false, 'default' => ''],
                    ucfirst(TrackingLogisticInterface::AWB_NUMBER)
                )
                ->addColumn(
                    TrackingLogisticInterface::TPL_STATUS_ID,
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'default' => 0],
                    ucfirst(TrackingLogisticInterface::TPL_STATUS_ID)
                )
                ->addColumn(
                    TrackingLogisticInterface::TPL_STATUS_NAME,
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false, 'default' => ''],
                    ucfirst(TrackingLogisticInterface::TPL_STATUS_NAME)
                )
                ->addColumn(
                    TrackingLogisticInterface::STATUS_COURIER_ID,
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'default' => 0],
                    ucfirst(TrackingLogisticInterface::STATUS_COURIER_ID)
                )
                ->addColumn(
                    TrackingLogisticInterface::STATUS_COURIER_NAME,
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false, 'default' => ''],
                    ucfirst(TrackingLogisticInterface::STATUS_COURIER_NAME)
                )
                ->addColumn(
                    TrackingLogisticInterface::DRIVER_NAME,
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false, 'default' => ''],
                    ucfirst(TrackingLogisticInterface::DRIVER_NAME)
                )
                ->addColumn(
                    TrackingLogisticInterface::DRIVER_PHONE,
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false, 'default' => null],
                    ucfirst(TrackingLogisticInterface::DRIVER_PHONE)
                )
                ->addColumn(
                    TrackingLogisticInterface::DRIVER_PLATE,
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false, 'default' => ''],
                    ucfirst(TrackingLogisticInterface::DRIVER_PLATE)
                )
                ->addColumn(
                    TrackingLogisticInterface::URL_TRACKING,
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false, 'default' => null],
                    ucfirst(TrackingLogisticInterface::URL_TRACKING)
                )
                ->addColumn(
                    TrackingLogisticInterface::TRACKING_NOTES,
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false, 'default' => null],
                    ucfirst(TrackingLogisticInterface::TRACKING_NOTES)
                )
                ->addColumn(
                    TrackingLogisticInterface::SERVICE_NAME,
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false, 'default' => null],
                    ucfirst(TrackingLogisticInterface::SERVICE_NAME)
                )
                ->addColumn(
                    TrackingLogisticInterface::TIMESTAMP_DATE,
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false, 'default' => ''],
                    ucfirst(TrackingLogisticInterface::TIMESTAMP_DATE)
                )
                ->setComment('Integration TPL Tracking')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');
            $setup->getConnection()->createTable($table);
        }
    }
}
