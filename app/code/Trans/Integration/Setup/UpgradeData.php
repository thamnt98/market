<?php
/**
 * @category Trans
 * @package  Trans_Sprint
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Imam Kusuma <imam.kusuma@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Integration\Setup;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Exception\CouldNotSaveException;

use \Trans\Integration\Api\Data\IntegrationChannelInterface;
use \Trans\Integration\Model\IntegrationChannelFactory;
use \Trans\Integration\Helper\ConfigChannel;
use \Trans\Integration\Model\ResourceModel\IntegrationChannel as ResourceChannel;


class UpgradeData implements UpgradeDataInterface
{
  
    /**
     * @var Config
     */
    protected $_config;

    /**
     * @var ResourceModel
     */
    protected $resourceChannel;

    /**
     * @var IntegrationChannelFactory
     */
    protected $channelFactory;

    /**
     *  constructor.
     */
    public function __construct(
        ConfigChannel $_config
        ,IntegrationChannelFactory $channelFactory
        ,ResourceChannel $resourceChannel
    ) {
        $this->_config              = $_config;
        $this->channelFactory       = $channelFactory;
        $this->resourceChannel      = $resourceChannel;
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
            $channelId =$this->addChannel($setup,"pim");
        }

    }

    /**
     * Table Data Integration Channel
     * @param $setup
     */
    public function addChannel($setup,$channel = "")
    {  // Get emqube_mymodule table
        $id = 0;
        $tableName = $setup->getTable(IntegrationChannelInterface::TABLE_NAME);
        Print 1;
        // Check if the table already exists
        if ($setup->getConnection()->isTableExists($tableName) == true) {
            Print 2;
            $collection = $this->channelFactory->create()->getCollection();
            $collection->addFieldToFilter(IntegrationChannelInterface::CODE,$channel);
           
            if($collection->getSize()){
                Print 3;
                // Delete old Channel data
                $i = 0 ;
                foreach($collection as $row){
                    $this->resourceChannel->delete($row);
                    $i++;
                }
            }
            // Insert New Channel
            $data = [
                IntegrationChannelInterface::NAME       => $this->_config->getDefaultChannelName($channel),
                IntegrationChannelInterface::CODE       => $this->_config->getDefaultCode($channel),
                IntegrationChannelInterface::URL        => $this->_config->getDefaultBaseUrl($channel),
                IntegrationChannelInterface::ENV        => $this->_config->getChannelEnv(),
                IntegrationChannelInterface::STATUS     => $this->_config->getDefaultStatus(),
                IntegrationChannelInterface::CREATED_BY => $this->_config->getDefaultAuthor(),
                IntegrationChannelInterface::UPDATED_BY => $this->_config->getDefaultAuthor(),
            ];
            Print 4;
            $factory = $this->channelFactory->create();
            $factory->addData($data)->save();
            
            $id = $factory->getId();
            Print 5;
            
        }
        return $id ;
    }

    

}
