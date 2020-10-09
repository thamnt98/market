<?php
/**
 * @category Trans
 * @package  Trans_IntegrationBrand
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author  J.P <jaka.pondan@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationBrand\Setup;

use Magento\Framework\Db\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Exception\StateException;

use \Trans\IntegrationBrand\Api\Data\IntegrationBrandInterface;
use \Trans\IntegrationBrand\Api\Data\IntegrationJobInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.2.0', '<')) {
           $this->updateIntegrationBrand($setup);
        }

        if (version_compare($context->getVersion(), '1.3.0', '<')) {
            $this->updateJobTable($setup);
         }
    }

    /**
     * Update Integration Brand
     */
    protected function updateIntegrationBrand($setup){
        $tableName = $setup->getTable('integration_brand');
        // Check if the table already exists
        if ($setup->getConnection()->isTableExists($tableName) == true) {
            $setup->getConnection()->dropTable($tableName);
            
        }
    }

    /**
     * Update Integration Brand
     */
    protected function updateJobTable($setup){
        $tableName = $setup->getTable(\Trans\IntegrationBrand\Api\Data\IntegrationJobInterface::TABLE_NAME);
        // Check if the table already exists
        if ($setup->getConnection()->isTableExists($tableName) == true) {
            $setup->getConnection()->dropColumn($setup->getTable($tableName), IntegrationJobInterface::HIT);
            $setup->getConnection()->addColumn(
                $setup->getTable($tableName),
                IntegrationJobInterface::HIT,
                [
                    'type' => Table::TYPE_INTEGER,
                    'nullable' => false,
                    'default' => 0,
                    'comment' =>  ucfirst(str_replace('_',' ',IntegrationJobInterface::HIT)),
                    'after' => IntegrationJobInterface::MESSAGE,
                ]
            );
            
        }
    }

   
    
}
