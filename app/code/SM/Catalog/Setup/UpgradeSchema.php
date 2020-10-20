<?php

/**
 * @category  SM
 * @package   SM_Catalog
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 * @author    Logan Ta <phuongtt2@smartosc.com>
 *
 * @copyright 2020 Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

declare(strict_types=1);

namespace SM\Catalog\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/**
 * Class UpgradeSchema
 * @package SM\Catalog\Setup
 */
class UpgradeSchema implements UpgradeSchemaInterface
{

    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        $connection = $setup->getConnection();

        if (version_compare($context->getVersion(), '1.2.0', '<')) {

            $saleRuleTable = $setup->getTable('catalog_eav_attribute');

            $connection->addColumn(
                $saleRuleTable,
                'show_specification',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'nullable' => true,
                    'default' => 1,
                    'comment' => 'Show Specification'
                ]
            );
        }

        $installer->endSetup();
    }
}