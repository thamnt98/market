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

namespace Trans\IntegrationCatalog\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\StateException;

use \Magento\Catalog\Model\Product;

use \Trans\Integration\Api\Data\IntegrationChannelInterface;
use \Trans\Integration\Api\Data\IntegrationChannelMethodInterface;
use \Trans\Integration\Model\IntegrationChannelFactory;
use \Trans\Integration\Model\IntegrationChannelMethodFactory;
use \Trans\Integration\Helper\ConfigChannel;
use \Trans\Integration\Model\ResourceModel\IntegrationChannel as ResourceChannel;
use \Trans\Integration\Model\ResourceModel\IntegrationChannelMethod as ResourceChannelMethod;

use \Trans\IntegrationCatalog\Api\Data\IntegrationProductInterface;


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
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @var \Magento\Eav\Model\Config
     */
    private $eavConfig;

    /**
     *  constructor.
     */
    public function __construct(
        ConfigChannel $_config,
        IntegrationChannelFactory $channelFactory,
        IntegrationChannelMethodFactory $methodFactory,
        ResourceChannel $resourceChannel,
        ResourceChannelMethod $resourceChannelMethod,
        EavSetupFactory $eavSetupFactory,
        \Magento\Eav\Model\Config $eavConfig
    ) {
        $this->_config = $_config;
        $this->channelFactory = $channelFactory;
        $this->methodFactory = $methodFactory;
        $this->resourceChannel = $resourceChannel;
        $this->resourceChannelMethod = $resourceChannelMethod;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->eavConfig = $eavConfig;
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
            // Get Method PIM Product
            $channelId = $this->getChannelId($setup);
            // Insert PIm method
            $this->addMethod($setup,$channelId,"pim",2);
        }
        if (version_compare($context->getVersion(), '1.2.0', '<')) {
            // Get Method PIM Product
            $channelId = $this->getChannelId($setup);
            // Insert PIm method Product Association
            $this->addMethod($setup,$channelId,"pim",6);
        }
        if (version_compare($context->getVersion(), '1.3.0', '<')) {
            // Get Method PIM Product
            $channelId = $this->getChannelId($setup);
            // Insert PIm method Product Association
            $this->addMethod($setup,$channelId,"pim",8);
        }
        if (version_compare($context->getVersion(), '1.4.0', '<')) {
            // Get Method PIM Product
            $channelId = $this->getChannelId($setup);
            // Insert PIm method Add query Param ProductTYpe
            $this->addMethod($setup,$channelId,"pim",2);
            $this->addMethod($setup,$channelId,"pim",10);
            $this->addAttributeProductType($setup);
        }
        if (version_compare($context->getVersion(), '1.5.0', '<')) {
            $this->addAttributeProductType($setup);
        }
        
        if (version_compare($context->getVersion(), '1.7.3', '<')) {
            $sellingUnit = 'selling_unit';
            if(!$this->isProductAttributeExists($sellingUnit)) {
                $this->createSellingUnitAttr($setup, $sellingUnit);
            }
        }

    }

    /**
     * Returns true if attribute exists and false if it doesn't exist
     *
     * @param string $code
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function isProductAttributeExists($code)
    {
        $attr = $this->eavConfig->getAttribute(Product::ENTITY, $code);
 
        return ($attr && $attr->getId()) ? true : false;
    }

    /**
     * create selling unit product attribute
     * 
     * @param Magento\Framework\Setup\ModuleDataSetupInterface $setup
     * @param string $code
     * @return void
     */
    protected function createSellingUnitAttr($setup, $code)
    {
        try {
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                $code, 
                [
                    'group' => 'General',
                    'type' => 'int',
                    'backend' => '',
                    'frontend' => '',
                    'label' => 'Selling Unit',
                    'default_label' => 'Selling Unit',
                    'input' => 'select',
                    'class' => '',
                    'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Table',
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => true,
                    'default' => 0,
                    'searchable' => true,
                    'filterable' => true,
                    'comparable' => true,
                    'visible_on_front' => true,
                    'used_in_product_listing' => true,
                    'unique' => false,
                    'apply_to' => '',
                    'apply_to' => 'simple,virtual,configurable',
                    'option' => [
                        'values' => [
                                '1'
                            ],
                    ],
                ]
            );
        } catch (\Exception $e) {
            
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
    protected function addMethod($setup,$channelId,$tag,$seq) {

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
     * Add Attribute Product TYPE "Simple" or "Digital"
     */
    protected function addAttributeProductType($setup){
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, IntegrationProductInterface::PRODUCT_TYPE);
        $eavSetup->addAttribute(\Magento\Catalog\Model\Product::ENTITY, IntegrationProductInterface::PRODUCT_TYPE, [
            'group' => 'Product Details',
            'type' => 'int',
            'backend' => '',
            'frontend' => '',
            'sort_order' => 1,
            'label' => 'Catalog Type',
            'input' => 'select',
            'class' => '',
            'source' => 'Trans\IntegrationCatalog\Model\Source\ProductTypeSelection',
            'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
            'visible' => true,
            'required' => false,
            'user_defined' => false,
            'default' => '',
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' => false,
            'used_in_product_listing' => true,
            'apply_to' => ''
        ]);
    }

}
