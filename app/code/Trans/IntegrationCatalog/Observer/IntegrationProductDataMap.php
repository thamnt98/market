<?php
/**
 * @category Trans
 * @package  Trans_IntegrationCatalog
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   imamkusuma <imam.kusuma@ctcorpdigital.com>
 *
 * Copyright © 2020 CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationCatalog\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Trans\IntegrationCatalog\Api\Data\IntegrationProductInterface;

/**
 * Class IntegrationProductDataMap
 *
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class IntegrationProductDataMap implements ObserverInterface
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product
     */
    protected $productResource;

    /**
     * @var \Trans\IntegrationCatalog\Model\IntegrationProductLogic
     */
    protected $productLogic;

    /**
     * @var \Trans\IntegrationCatalog\Api\IntegrationDataValueRepositoryInterface
     */
    protected $dataValueRepository;

    /**
     * @var \Trans\IntegrationCatalog\Model\IntegrationProductRepository
     */
    protected $integrationProdRepository;

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Product $productResource
     * @param \Trans\IntegrationCatalog\Model\IntegrationProductLogic $productLogic
     * @param \Trans\IntegrationCatalog\Api\IntegrationDataValueRepositoryInterface $dataValueRepository
     * @param \Trans\IntegrationCatalog\Model\IntegrationProductRepository $integrationProdRepository
     * @throws RuntimeException
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product $productResource,
        \Trans\IntegrationCatalog\Model\IntegrationProductLogic $productLogic,
        \Trans\IntegrationCatalog\Api\IntegrationDataValueRepositoryInterface $dataValueRepository,
        \Trans\IntegrationCatalog\Model\IntegrationProductRepository $integrationProdRepository
    ) {
        $this->productLogic = $productLogic;
        $this->productResource = $productResource;
        $this->dataValueRepository = $dataValueRepository;
        $this->integrationProdRepository = $integrationProdRepository;

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/integration_product.log');
        $logger = new \Zend\Log\Logger();
        $this->logger = $logger->addWriter($writer);
    }

    /**
     * Action after data import. Save data mapping.
     *
     * @param Observer $observer
     * @return void
     * @throws LocalizedException
     * @throws UrlAlreadyExistsException
     */
    public function execute(Observer $observer)
    {
        $this->logger->info('Start Mapping ' . date('H:i:s'));
        if ($products = $observer->getEvent()->getBunch()) {

            $simple = [];
            $config = [];
            foreach ($products as $rowData) {
                $dataValue = $this->dataValueRepository->getById($rowData['data_value_id']);
                $sku = $rowData['sku'];

                $product = $this->getCatalogProduct($sku);

                if($product) {
                    $map = array();
                    $map['integration_data_id'] = $rowData['data_value_id'];
                    $map['magento_entity_id'] = $product['entity_id'];

                    if(isset($rowData['category_id'])) {
                        $categoryIds = $this->productLogic->getCategoryId($rowData['category_id']);
                        
                        if(!empty($categoryIds) && is_array($categoryIds)) {
                            $map['magento_category_ids'] = json_encode($categoryIds);
                        }

                        $map['pim_category_id'] = json_encode($rowData['category_id']);
                    }

                    $map['pim_id'] = $rowData['id'];
                    $map['item_id'] = $this->productLogic->getItemId($sku);
                    $map['pim_sku'] = $rowData['sku'];

                    $productType = IntegrationProductInterface::PRODUCT_TYPE_SIMPLE_VALUE;
                    if($rowData[IntegrationProductInterface::PRODUCT_TYPE] == IntegrationProductInterface::PRODUCT_TYPE_DIGITAL_LABEL){
                        $productType = IntegrationProductInterface::PRODUCT_TYPE_DIGITAL_VALUE;
                    }

                    $map['product_type'] = $productType;
                    $rowData['data_attributes'] = $rowData['list_attributes'];
                    $map['attribute_list'] = json_encode($rowData['list_attributes']);

                    unset($rowData['list_attributes']);
                    unset($rowData['id']);

                    $mapData = array_merge($rowData, $map);
                    
                    $simple = $map;

                    // $this->productLogic->saveDataToIntegrationProduct($product['entity_id'], $rowData);

                    if(!empty($simple)) {
                        $checkMap = $this->checkMapData($simple);

                        if(!$checkMap) {
                            $connection = $this->productResource->getConnection();
                            $tableName = $connection->getTableName('integration_catalog_product');
                            $connection->insertOnDuplicate($tableName, $simple, ['pim_sku']);
                        }
                    }

                    if($this->integrationProdRepository->checkPosibilityConfigurable($sku, false)) {
                        $config[] = ['rowData' => $mapData, 'dataValue' => $dataValue];
                        $this->logger->info($sku . ' is configurable');
                    }else{
                        $this->logger->info($sku . ' is not configurable');
                    }
                }
            }

            // if(!empty($simple)) {
            //     $connection = $this->productResource->getConnection();
            //     $tableName = $connection->getTableName('integration_catalog_product');
            //     $connection->insertOnDuplicate($tableName, $simple, ['pim_sku']);
            // }

            if(!empty($config)) {
                foreach ($config as $value) {
                    $this->productLogic->createIntegrationProductMapConfigurable($value['rowData'], $value['dataValue']);
                }
            }

        }
        $this->logger->info('End Mapping ' . date('H:i:s'));
    }

    /**
     * check and update map data
     *
     * @param array $data
     * @return bool
     */
    protected function checkMapData(array $data)
    {
        if(!empty($data)) {
            $connection = $this->productResource->getConnection();
            $tableName = $connection->getTableName('integration_catalog_product');
            $query = $connection->select();
            $query->from(
                $tableName,
                ['*']
            );
            $query->where('pim_sku = ?', $data['pim_sku']);

            $collection = $connection->fetchRow($query);

            if($collection) {
                $where = ['pim_sku = ?' => $data['pim_sku']];

                $connection->update($tableName, $data, $where);

                return true;
            }
        }

        return false;
    }

    /**
     * get catalog product entity by SKU
     *
     * @param string $sku
     */
    protected function getCatalogProduct($sku)
    {
        $connection = $this->productResource->getConnection();
        $table = $connection->getTableName('catalog_product_entity');

        $query = $connection->select();
        $query->from(
            $table,
            ['*']
        )->where('sku = ?', $sku);

        $data = $connection->fetchRow($query);

        if($data) {
            return $data;
        }

        return false;
    }
}
