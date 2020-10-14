<?php
/**
 * @category Trans
 * @package  Trans_IntegrationBrand
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   J.P <jaka.pondan@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationBrand\Setup;

use \Magento\Framework\Setup\InstallDataInterface;
use \Magento\Framework\Setup\ModuleContextInterface;
use \Magento\Framework\Setup\ModuleDataSetupInterface;

use \Trans\Integration\Api\Data\IntegrationChannelInterface;
use \Trans\Integration\Api\Data\IntegrationChannelMethodInterface;
use \Trans\Integration\Model\IntegrationChannelFactory;
use \Trans\Integration\Model\IntegrationChannelMethodFactory;
use \Trans\Integration\Helper\ConfigChannel;

use \Trans\Integration\Model\ResourceModel\IntegrationChannel as ResourceChannel;
use \Trans\Integration\Model\ResourceModel\IntegrationChannelMethod as ResourceChannelMethod;
/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * @var Config
     */
    protected $_config;

    /**
     * @var IntegrationChannelFactory
     */
    protected $channelFactory;

    /**
     * @var IntegrationChannelMethodFactory
     */
    protected $methodFactory;

     /**
     * @var ResourceModel
     */
    protected $resourceChannel;

     /**
     * @var ResourceChannelMethod
     */
    protected $resourceChannelMethod;

    /**
     *  constructor.
     */
    public function __construct(
        ConfigChannel $_config
        ,IntegrationChannelFactory $channelFactory
        ,IntegrationChannelMethodFactory $methodFactory
        ,ResourceChannel $resourceChannel
        ,ResourceChannelMethod $resourceChannelMethod

    ) {
        $this->_config=$_config;
        $this->channelFactory=$channelFactory;
        $this->methodFactory=$methodFactory;
        $this->resourceChannel          = $resourceChannel;
        $this->resourceChannelMethod    = $resourceChannelMethod;
    }


    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.1.0', '<')) {
            // Get Method PIM Product
            $channelId = $this->getChannelId($setup);
            // Insert PIm method Product Association
            $this->addMethod($setup,$channelId,"pim",9);
        }
        
        $setup->endSetup();
    }

    
    /**
     * Get Channel Id
     * @throws \Exception
     * @return int channelId
     */
    protected function getChannelId($setup){
       
        $tableName = $setup->getTable(IntegrationChannelInterface::TABLE_NAME);
        // Check if the table already exists
        if ($setup->getConnection()->isTableExists($tableName) != true) {
            throw new StateException(__(
                'Table '.IntegrationChannelInterface::TABLE_NAME." is not exist"
            ));
        }
        $collection = $this->channelFactory->create()->getCollection();
        $collection->addFieldToFilter(IntegrationChannelInterface::CODE,IntegrationChannelInterface::CHANNEL_CODE_PIM);
        if(!$collection->getSize()){
            throw new StateException(__(
                'Channel Data is not available!'
            ));
        }
        return $collection->getFirstItem()->getId();
           
    }

     /**
     * Add Custom Method
     * @throws \Exception
     */
    public function addMethod($setup,$channelId,$tag,$seq) {

        if($channelId<1){
            throw new CouldNotSaveException(__(
                'Could not save because channel id 0'
            ));
        }
        // Get table
        $tableName = $setup->getTable(IntegrationChannelMethodInterface::TABLE_NAME);
        // Check if the table already exists
        if ($setup->getConnection()->isTableExists($tableName) == true) {
            $methodtag = $this->_config->getDefaultMethodTag($tag,$seq);

            $data = [
                IntegrationChannelMethodInterface::CHANNEL_ID   => $channelId,
                IntegrationChannelMethodInterface::DESCRIPTION  => $this->_config->getDefaultMethodChannelDesc($tag,$seq),
                IntegrationChannelMethodInterface::TAG          => $methodtag,
                IntegrationChannelMethodInterface::METHOD       => $this->_config->getDefaultMethod($tag,$seq),
                IntegrationChannelMethodInterface::HEADERS      => $this->_config->getDefaultMethodHeaders($tag,$seq),
                IntegrationChannelMethodInterface::QUERY_PARAMS => $this->_config->getDefaultMethodQuery($tag,$seq),
                IntegrationChannelMethodInterface::BODY         => $this->_config->getDefaultMethodBody($tag,$seq),
                IntegrationChannelMethodInterface::PATH         => $this->_config->getDefaultMethodPath($tag,$seq),
                IntegrationChannelMethodInterface::LIMIT        => $this->_config->getDefaultLimit(),
                IntegrationChannelInterface::STATUS             => $this->_config->getDefaultStatus(),
                IntegrationChannelInterface::CREATED_BY         => $this->_config->getDefaultAuthor(),
                IntegrationChannelInterface::UPDATED_BY         => $this->_config->getDefaultAuthor(),
            ];

            $collection = $this->methodFactory->create()->getCollection();
            $collection->addFieldToFilter(IntegrationChannelMethodInterface::TAG,$methodtag);
           
            $query =[];
            if($collection->getSize()){
                $query =$collection->getFirstItem();
                $data[IntegrationChannelInterface::UPDATED_AT]=date("Y-m-d H:i:s");
               
            }else{
                $query = $this->methodFactory->create();
            }
            $query->addData( $data );
            $this->resourceChannelMethod->save($query);
           
        }
        
    }

}