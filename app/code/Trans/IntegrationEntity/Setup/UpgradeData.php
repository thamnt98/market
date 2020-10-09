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

namespace Trans\IntegrationEntity\Setup;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\StateException;

use \Trans\Integration\Api\Data\IntegrationChannelInterface;
use \Trans\Integration\Api\Data\IntegrationChannelMethodInterface;
use \Trans\Integration\Model\IntegrationChannelFactory;
use \Trans\Integration\Model\IntegrationChannelMethodFactory;
use \Trans\Integration\Helper\ConfigChannel;
use \Trans\Integration\Model\ResourceModel\IntegrationChannel as ResourceChannel;
use \Trans\Integration\Model\ResourceModel\IntegrationChannelMethod as ResourceChannelMethod;

use \Trans\IntegrationEntity\Api\Data\IntegrationProductAttributeTypeInterface;
use \Trans\IntegrationEntity\Model\IntegrationProductAttributeTypeFactory;

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
     * @var ResourceChannelMethod
     */
    protected $resourceChannelMethod;

    /**
     * @var IntegrationChannelFactory
     */
    protected $channelFactory;

    /**
     * @var IntegrationChannelMethodFactory
     */
    protected $methodFactory;

    /**
     * @var IntegrationProductAttributeTypeFactory
     */
    protected $attributeTypeFactory;

    /**
     * @var \Trans\IntegrationEntity\Model\ResourceModel\IntegrationProductAttributeType\CollectionFactory
     */
    protected $attrTypeCollection;

    /**
     * @var \Trans\IntegrationEntity\Model\ResourceModel\IntegrationProductAttributeType
     */
    protected $resourceAttrType;

    /**
     *  constructor.
     *
     *  @param \Trans\IntegrationEntity\Model\ResourceModel\IntegrationProductAttributeType\CollectionFactory $attrTypeCollection
     *  @param \Trans\IntegrationEntity\Model\ResourceModel\IntegrationProductAttributeType $resourceAttrType
     */
    public function __construct(
        ConfigChannel $_config,
        IntegrationChannelFactory $channelFactory,
        IntegrationChannelMethodFactory $methodFactory,
        ResourceChannel $resourceChannel,
        ResourceChannelMethod $resourceChannelMethod,
        IntegrationProductAttributeTypeFactory $attributeTypeFactory,
        \Trans\IntegrationEntity\Model\ResourceModel\IntegrationProductAttributeType\CollectionFactory $attrTypeCollection,
        \Trans\IntegrationEntity\Model\ResourceModel\IntegrationProductAttributeType $resourceAttrType
    ) {
        $this->_config = $_config;
        $this->channelFactory = $channelFactory;
        $this->methodFactory = $methodFactory;
        $this->resourceChannel = $resourceChannel;
        $this->resourceChannelMethod = $resourceChannelMethod;
        $this->attributeTypeFactory = $attributeTypeFactory;
        $this->attrTypeCollection = $attrTypeCollection;
        $this->resourceAttrType = $resourceAttrType;
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
            // Get Method PIM Product Attribute
            $channelId = $this->getChannelId($setup);
            // Insert PIm method Attribute
            $this->addMethod($setup,$channelId,"pim",4);
            // Insert Map Pim data Product Attr
            $this->insertProductAttributeTypeDataMap($setup);
        }

        if (version_compare($context->getVersion(), '1.2.1', '<')) {
            $collection = $this->attrTypeCollection->create();
            $collection->addFieldToFilter(IntegrationProductAttributeTypeInterface::PIM_TYPE_ID, ['in' => ['1','2']]);

            if ($collection->getSize()) {
                foreach($collection as $type) {
                    $connection = $this->resourceAttrType->getConnection();
                    $data = [IntegrationProductAttributeTypeInterface::IS_SWATCH => 0];
                    $where = [IntegrationProductAttributeTypeInterface::PIM_TYPE_ID => ['1', '2']];

                    $tableName = $connection->getTableName(IntegrationProductAttributeTypeInterface::TABLE_NAME);
                    $connection->update($tableName, $data, $where);
                }
            }

            $attributeTypeMapData = [
                [
                    IntegrationProductAttributeTypeInterface::PIM_TYPE_ID => 9,
                    IntegrationProductAttributeTypeInterface::PIM_TYPE_CODE => 'yes_no',
                    IntegrationProductAttributeTypeInterface::PIM_TYPE_NAME => 'Yes No',
                    IntegrationProductAttributeTypeInterface::BACKEND_CODE => 'int',
                    IntegrationProductAttributeTypeInterface::FRONTEND_CODE => 'select',
                    IntegrationProductAttributeTypeInterface::IS_SWATCH => 0
                ],
                [
                    IntegrationProductAttributeTypeInterface::PIM_TYPE_ID => 10,
                    IntegrationProductAttributeTypeInterface::PIM_TYPE_CODE => 'swatch',
                    IntegrationProductAttributeTypeInterface::PIM_TYPE_NAME => 'Visual Swatch',
                    IntegrationProductAttributeTypeInterface::BACKEND_CODE => 'int',
                    IntegrationProductAttributeTypeInterface::FRONTEND_CODE => 'swatch_visual',
                    IntegrationProductAttributeTypeInterface::IS_SWATCH => 1
                ],
                [
                    IntegrationProductAttributeTypeInterface::PIM_TYPE_ID => 11,
                    IntegrationProductAttributeTypeInterface::PIM_TYPE_CODE => 'swatch_text',
                    IntegrationProductAttributeTypeInterface::PIM_TYPE_NAME => 'Text Swatch',
                    IntegrationProductAttributeTypeInterface::BACKEND_CODE => 'int',
                    IntegrationProductAttributeTypeInterface::FRONTEND_CODE => 'swatch_text',
                    IntegrationProductAttributeTypeInterface::IS_SWATCH => 1
                ]
            ];

            $collection=[];
            $factory=[]; 
            $i=0;
            foreach($attributeTypeMapData as $row){
                $collection[$i] = $this->attrTypeCollection->create();
                $collection[$i]->addFieldToFilter(IntegrationProductAttributeTypeInterface::PIM_TYPE_CODE,$row[IntegrationProductAttributeTypeInterface::PIM_TYPE_CODE]);
            
                if(!$collection[$i]->getSize()){
                    $factory[$i] = $this->attributeTypeFactory->create();
                    $factory[$i]->addData($row);
                    $this->resourceAttrType->save($factory[$i]);
                }

                $i++;   
            }
        }

        if (version_compare($context->getVersion(), '1.2.3', '<')) {
            // Insert PIm method Attribute set
            $this->addMethodSet("pim",11);
        }

        if (version_compare($context->getVersion(), '1.2.4', '<')) {
            // Insert PIm method Attribute set
            $this->addMethodSet("pim",11);
        }

        if (version_compare($context->getVersion(), '1.2.7', '<')) {
            //change yes_no's frontend_input attribute type from select to boolean
            $dataType = $this->attributeTypeFactory->create();
            $this->resourceAttrType->load($dataType, 'yes_no', IntegrationProductAttributeTypeInterface::PIM_TYPE_CODE);

            if ($dataType->getId()) {
                $dataType->setFrontendInput('boolean');
                $this->resourceAttrType->save($dataType);
            }            
        }
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

     /**
     * Insert PIM Attr Type Data And Magento Product Attr Type
     */
    protected function insertProductAttributeTypeDataMap($setup){
      
        $attributeTypeMapData= [
            [
                IntegrationProductAttributeTypeInterface::PIM_TYPE_ID           => 1
                ,IntegrationProductAttributeTypeInterface::PIM_TYPE_CODE        => 'multi_select'
                ,IntegrationProductAttributeTypeInterface::PIM_TYPE_NAME        => 'Multi Select'
                ,IntegrationProductAttributeTypeInterface::BACKEND_CODE         => 'text'
                ,IntegrationProductAttributeTypeInterface::FRONTEND_CODE        => 'multiselect'
                ,IntegrationProductAttributeTypeInterface::IS_SWATCH            => 1
            ],
            [
                IntegrationProductAttributeTypeInterface::PIM_TYPE_ID           => 2
                ,IntegrationProductAttributeTypeInterface::PIM_TYPE_CODE        => 'single_select'
                ,IntegrationProductAttributeTypeInterface::PIM_TYPE_NAME        => 'Single Select'
                ,IntegrationProductAttributeTypeInterface::BACKEND_CODE         => 'static'
                ,IntegrationProductAttributeTypeInterface::FRONTEND_CODE        => 'select'
                ,IntegrationProductAttributeTypeInterface::IS_SWATCH            => 1
            ],
            [
                IntegrationProductAttributeTypeInterface::PIM_TYPE_ID           => 3
                ,IntegrationProductAttributeTypeInterface::PIM_TYPE_CODE        => 'number'
                ,IntegrationProductAttributeTypeInterface::PIM_TYPE_NAME        => 'Number'
                ,IntegrationProductAttributeTypeInterface::BACKEND_CODE         => 'int'
                ,IntegrationProductAttributeTypeInterface::FRONTEND_CODE        => 'text'
            ],
            [
                IntegrationProductAttributeTypeInterface::PIM_TYPE_ID           => 4
                ,IntegrationProductAttributeTypeInterface::PIM_TYPE_CODE        => 'decimal'
                ,IntegrationProductAttributeTypeInterface::PIM_TYPE_NAME        => 'Decimal'
                ,IntegrationProductAttributeTypeInterface::BACKEND_CODE         => 'decimal'
                ,IntegrationProductAttributeTypeInterface::FRONTEND_CODE        => 'text'
            ],
            [
                IntegrationProductAttributeTypeInterface::PIM_TYPE_ID           => 5
                ,IntegrationProductAttributeTypeInterface::PIM_TYPE_CODE        => 'date'
                ,IntegrationProductAttributeTypeInterface::PIM_TYPE_NAME        => 'Date'
                ,IntegrationProductAttributeTypeInterface::BACKEND_CODE         => 'datetime'
                ,IntegrationProductAttributeTypeInterface::FRONTEND_CODE        => 'date'
            ],
            [
                IntegrationProductAttributeTypeInterface::PIM_TYPE_ID           => 6
                ,IntegrationProductAttributeTypeInterface::PIM_TYPE_CODE        => 'text'
                ,IntegrationProductAttributeTypeInterface::PIM_TYPE_NAME        => 'text'
                ,IntegrationProductAttributeTypeInterface::BACKEND_CODE         => 'varchar'
                ,IntegrationProductAttributeTypeInterface::FRONTEND_CODE        => 'text'
            ],
            [
                IntegrationProductAttributeTypeInterface::PIM_TYPE_ID           => 7
                ,IntegrationProductAttributeTypeInterface::PIM_TYPE_CODE        => 'text_area'
                ,IntegrationProductAttributeTypeInterface::PIM_TYPE_NAME        => 'Text Area'
                ,IntegrationProductAttributeTypeInterface::BACKEND_CODE         => 'text'
                ,IntegrationProductAttributeTypeInterface::FRONTEND_CODE        => 'textarea'
            ],
            [
                IntegrationProductAttributeTypeInterface::PIM_TYPE_ID           => 8
                ,IntegrationProductAttributeTypeInterface::PIM_TYPE_CODE        => 'price'
                ,IntegrationProductAttributeTypeInterface::PIM_TYPE_NAME        => 'Price'
                ,IntegrationProductAttributeTypeInterface::BACKEND_CODE         => 'decimal'
                ,IntegrationProductAttributeTypeInterface::FRONTEND_CODE        => 'price'
            ]
            
        ];

        // Get table
        $tableName = $setup->getTable(IntegrationChannelMethodInterface::TABLE_NAME);
        // Check if the table already exists
        if ($setup->getConnection()->isTableExists($tableName) == true) {
       
            $dataBank = [];
            $collection=[];
            $factory=[]; 
            $i=0;
            foreach($attributeTypeMapData as $row){
                $collection[$i] = $this->attributeTypeFactory->create()->getCollection();
                $collection[$i]->addFieldToFilter(IntegrationProductAttributeTypeInterface::PIM_TYPE_CODE,$row[IntegrationProductAttributeTypeInterface::PIM_TYPE_CODE]);
            
                if($collection[$i]->getSize()){

                }else{
                    $factory[$i] = $this->attributeTypeFactory->create();
                
                }
                $factory[$i]->addData($row)->save();
                $i++;
                
            }
        }
        
    }

    /**
     * Add Custom Method
     * @throws \Exception
     */
    public function addMethodSet($tag,$seq) {

        $data = [
            IntegrationChannelMethodInterface::CHANNEL_ID   => $this->_config->getDefaultMethodChannelId($tag,$seq),
            IntegrationChannelMethodInterface::DESCRIPTION  => $this->_config->getDefaultMethodChannelDesc($tag,$seq),
            IntegrationChannelMethodInterface::TAG          => $this->_config->getDefaultMethodTag($tag,$seq),
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
        $factory = $this->methodFactory->create();
        $factory->addData($data)->save();
    }


}
