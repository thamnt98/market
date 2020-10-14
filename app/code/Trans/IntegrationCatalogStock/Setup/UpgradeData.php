<?php
/**
 * @category Trans
 * @package  Trans_CatalogStock
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Hadi <ashadi.sejati@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationCatalogStock\Setup;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Exception\CouldNotSaveException;

use Trans\Integration\Api\Data\IntegrationChannelMethodInterface;
use Trans\Integration\Model\IntegrationChannelMethodFactory;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var IntegrationChannelMethodFactory
     */
    protected $integrationChannelMethodFactory;

    /**
     * @var IntegrationChannelMethodInterface
     */
    protected $integrationChannelMethodInterface;

    /**
     *  constructor.
     */
    public function __construct(
        IntegrationChannelMethodFactory $integrationChannelMethodFactory,
        IntegrationChannelMethodInterface $integrationChannelMethodInterface
    ) {
        $this->integrationChannelMethodFactory       = $integrationChannelMethodFactory;
        $this->integrationChannelMethodInterface      = $integrationChannelMethodInterface;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.1.0', '<')) {
            $changePath =$this->changePath($setup,"/v1/inventory/recent","/v1/inventory/recent_stock");
        }

    }

    /**
     * Table Data Integration method Channel
     * @param $setup
     */
    public function changePath($setup, $path = "", $newpath = "")
    {  

        $tableName = $setup->getTable(IntegrationChannelMethodInterface::TABLE_NAME);

        // Check if the table already exists
        if ($setup->getConnection()->isTableExists($tableName) == true) {

            $collection = $this->integrationChannelMethodFactory->create()->getCollection();
            $collection->addFieldToFilter(IntegrationChannelMethodInterface::PATH, $path);
           
            if($collection->getSize()) {
                foreach($collection as $row){
                    $row->setData(IntegrationChannelMethodInterface::PATH, $newpath);
                    $row->save();
                }
            }            
        }

        return true ;
    }

    

}
