<?php

/**
 * @category  SM
 * @package   SM_Label
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 * @author    Logan Ta <phuongtt2@smartosc.com>
 *
 * @copyright 2020 Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

declare(strict_types=1);

namespace SM\Label\Setup;

use Magento\Framework\DB\Ddl\Table as TableDdl;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * Class UpgradeSchema
 * @package SM\Label\Setup
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.2.0', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('am_label'),
                'type_label',
                TableDdl::TYPE_SMALLINT,
                NULL,
                ['nullable' => false, 'default' => 0],
                'Type Label'
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('am_label'),
                'use_for_cat',
                TableDdl::TYPE_SMALLINT,
                NULL,
                ['nullable' => false, 'default' => 0],
                'Use Label For Category'
            );
        }

        if (version_compare($context->getVersion(), '1.2.1', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('am_label'),
                'type_label_stock',
                TableDdl::TYPE_TEXT,
                NULL,
                ['nullable' => false, 'default' => 'other'],
                'Type Label Stock'
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('am_label'),
                'qty_ordered',
                TableDdl::TYPE_SMALLINT,
                NULL,
                ['nullable' => false, 'default' => 0],
                'Qty Ordered within 24 hours'
            );
        }

        $setup->endSetup();
    }
}
