<?php
/**
 * @category Trans
 * @package  Trans_IntegrationCatalogPrice
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   hadi <ashadi.sejati@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationCatalogPrice\Model;

use Trans\IntegrationCatalogPrice\Api\PromotionPriceLogicInterface;
use Trans\IntegrationCatalogPrice\Api\Data\PromotionPriceInterface;
use Trans\IntegrationCatalogPrice\Api\Data\IntegrationJobInterface;
use Trans\IntegrationCatalogPrice\Api\Data\IntegrationDataValueInterface;
use Trans\Integration\Helper\Validation;
use Trans\Integration\Helper\AttributeOption;
use Trans\Core\Helper\Data as CoreHelper;
use Trans\IntegrationCatalogPrice\Helper\Data as HelperPrice;
use Trans\IntegrationEntity\Api\IntegrationProductAttributeInterface;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Framework\Exception\StateException;
use Magento\Staging\Api\Data\UpdateInterface;

class PromotionPriceLogic implements PromotionPriceLogicInterface
{

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var CoreHelper
     */
    protected $coreHelper;

    /**
     * @var HelperPrice
     */
    protected $helperPrice;

    /**
     * @var PromotionPriceRepositoryInterface
     */
    protected $promotionPriceRepositoryInterface;
    
    /**
     * @var PromotionPriceInterfaceFactory
     */
    protected $promotionPriceInterfaceFactory;

    /**
     * @var IntegrationJobRepositoryInterface
     */
    protected $integrationJobRepositoryInterface;

    /**
     * @var integrationDataValueRepositoryInterface
     */
    protected $integrationDataValueRepositoryInterface;

    /**
     * @var Validation
     */
    protected $validation;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute
     */
    protected $eavAttribute;

    /**
     * @var AttributeOption
     */
    protected $attributeOptionHelper;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;

    /**
     * @var IntegrationProductRepositoryInterface
     */
    protected $integrationProductRepositoryInterface;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepositoryInterface;

    /**
     * @var IntegrationProductAttributeRepositoryInterface
     */
    protected $integrationAttributeRepository;

    /**
     * @var UpdateRepositoryInterface
     */
    protected $updateRepository;

    /**
     * @var UpdateInterfaceFactory
     */
    protected $updateInterfaceFactory;

    /**
     * @var UpdateFactory
     */
    protected $updateFactory;

    /**
     * @var RuleRepositoryInterface
     */
    protected $ruleRepositoryInterface;

    /**
     * @var RuleInterfaceFactory
     */
    protected $ruleInterfaceFactory;

    /**
     * @var RuleFactory
     */
    protected $ruleFactory;

    /**
     * @var Rule
     */
    protected $ruleResource;

    /**
     * @var \Magento\SalesRule\Model\Rule\Condition\Product\FoundFactory
     */
    protected $foundProductRuleFactory;

    /**
     * @var AmastyRuleInterface
     */
    protected $amastyRuleInterface;

    /**
     * @var AmastyRuleInterfaceFactory
     */
    protected $amastyRuleInterfaceFactory;

    /**
     * @var AmastyPromoInterface
     */
    protected $amastyPromoInterface;

    /**
     * @var AmastyPromoInterfaceFactory
     */
    protected $amastyPromoInterfaceFactory;

    /**
     * @var Magento\Framework\App\ResourceConnection
     */
    protected $resourceConnection;

    /**
     * @var \Magento\Framework\Indexer\IndexerRegistry
     */
    protected $indexerRegistry;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @param \Trans\Integration\Logger\Logger $Logger
     * @param \Trans\IntegrationCatalogPrice\Api\PromotionPriceRepositoryInterface $promotionPriceRepositoryInterface
     * @param \Trans\IntegrationCatalogPrice\Api\Data\PromotionPriceInterfaceFactory $promotionPriceInterfaceFactory
     * @param \Trans\IntegrationCatalogPrice\Api\IntegrationDataValueRepositoryInterface $IntegrationDataValueRepositoryInterface
     * @param Validation $validation
     * @param \Trans\IntegrationCatalogPrice\Api\IntegrationJobRepositoryInterface $integrationJobRepositoryInterface
     * @param \Magento\Catalog\Model\ResourceModel\Eav\Attribute $eavAttribute
     * @param AttributeOption $attributeOptionHelper
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Trans\IntegrationCatalog\Api\IntegrationProductRepositoryInterface $productRepository
     * @param CoreHelper $coreHelper
     * @param HelperPrice $helperPrice
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepositoryInterface
     * @param \Trans\IntegrationEntity\Api\IntegrationProductAttributeRepositoryInterface productRepositoryInterface$integrationAttributeRepository
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     * @param \Magento\Framework\Indexer\IndexerRegistry $indexerRegistry
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     */
    public function __construct(
        \Trans\Integration\Logger\Logger $logger,
        \Trans\IntegrationCatalogPrice\Api\PromotionPriceRepositoryInterface $promotionPriceRepositoryInterface,
        \Trans\IntegrationCatalogPrice\Api\Data\PromotionPriceInterfaceFactory $promotionPriceInterfaceFactory,
        \Trans\IntegrationCatalogPrice\Api\IntegrationDataValueRepositoryInterface $integrationDataValueRepositoryInterface,
        Validation $validation,
        \Trans\IntegrationCatalogPrice\Api\IntegrationJobRepositoryInterface $integrationJobRepositoryInterface,
        \Magento\Catalog\Model\ResourceModel\Eav\Attribute $eavAttribute,
        AttributeOption $attributeOptionHelper,
        EavConfig $eavConfig,
        \Trans\IntegrationCatalog\Api\IntegrationProductRepositoryInterface $integrationProductRepositoryInterface,
        CoreHelper $coreHelper,
        HelperPrice $helperPrice,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepositoryInterface,
        \Trans\IntegrationEntity\Api\IntegrationProductAttributeRepositoryInterface $integrationAttributeRepository,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Framework\Indexer\IndexerRegistry $indexerRegistry,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
    ) {
        $this->logger                                   = $logger;
        $this->promotionPriceRepositoryInterface        = $promotionPriceRepositoryInterface;
        $this->promotionPriceInterfaceFactory           = $promotionPriceInterfaceFactory;
        $this->integrationDataValueRepositoryInterface  = $integrationDataValueRepositoryInterface;
        $this->validation                               = $validation;
        $this->integrationJobRepositoryInterface        = $integrationJobRepositoryInterface;
        $this->eavAttribute                             = $eavAttribute;
        $this->attributeOptionHelper                    = $attributeOptionHelper;
        $this->eavConfig                                = $eavConfig;
        $this->integrationProductRepositoryInterface    = $integrationProductRepositoryInterface;
        $this->coreHelper                               = $coreHelper;
        $this->productRepositoryInterface               = $productRepositoryInterface;
        $this->integrationAttributeRepository           = $integrationAttributeRepository;
        $this->updateRepositoryInterface                = $helperPrice->getUpdateRepositoryInterface();
        $this->updateInterfaceFactory                   = $helperPrice->getUpdateInterfaceFactory();
        $this->updateFactory                            = $helperPrice->getUpdateFactory();
        $this->ruleRepositoryInterface                  = $helperPrice->getRuleRepositoryInterface();
        $this->ruleInterfaceFactory                     = $helperPrice->getRuleInterfaceFactory();
        $this->ruleFactory                              = $helperPrice->getRuleFactory();
        $this->ruleResource                             = $helperPrice->getRule();
        $this->foundProductRuleFactory                  = $helperPrice->getFoundFactory();
        $this->amastyRuleInterface                      = $helperPrice->getAmastyRuleInterface();
        $this->amastyRuleInterfaceFactory               = $helperPrice->getAmastyRuleInterfaceFactory();
        $this->amastyPromoInterface                     = $helperPrice->getAmastyPromoInterface();
        $this->amastyPromoInterfaceFactory              = $helperPrice->getGiftRuleInterfaceFactory();
        $this->productStaging                           = $helperPrice->getProductStagingInterface();
        $this->versionManager                           = $helperPrice->getVersionManagerFactory();
        $this->timezone                                 = $helperPrice->getTimezoneInterface();
        $this->date                                     = $helperPrice->getDateTime();
        $this->resourceConnection = $resourceConnection;
        $this->indexerRegistry = $indexerRegistry;
        $this->productCollectionFactory = $productCollectionFactory;

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/integration_promotion.log');
        $logger = new \Zend\Log\Logger();
        $this->logger = $logger->addWriter($writer);
    }


    /**
     * {@inheritdoc}
    */
    public function prepareData($jobs = [])
    {
        if (empty($jobs)) {
            throw new StateException(__(
                'Parameter Channel are empty !'
            ));
        }
    
        $jobId     = $jobs->getFirstItem()->getId();
        $jobStatus = $jobs->getFirstItem()->getStatus();
        $status    = PromotionPriceInterface::STATUS_JOB_DATA;

        $result = $this->integrationDataValueRepositoryInterface->getByJobIdWithStatus($jobId, $status);
        if (!$result) {
            throw new NoSuchEntityException(__('Requested Data Config doesn\'t exist'));
        }

        return $result;
    }

    /**
     * {@inheritdoc}
    */
    public function remapData($jobs=[], $data=[])
    {
        $i= 0 ;
        $dataValue = [];
        $params=[];

        if (!$jobs->getFirstItem()->getId()) {
            throw new NoSuchEntityException(__('Error Jobs Datas doesn\'t exist'));
        }
        $jobId = $jobs->getFirstItem()->getId();
        $this->updateJobStatus($jobId, IntegrationJobInterface::STATUS_PROGRESS_CATEGORY);
        
        $attributeCode=[];
        $attributeId=[];
        
        $dataProduct = [];
        $storeQuery = [];
        
        try {
            if ($data) {
                foreach ($data->getData() as $n => $row) {
                    $dataValue[$i] = json_decode($row['data_value'], true);
                    $value = $dataValue[$i];
                    
                    if (isset($value['sku'])) {
                        $dataProduct[$value['sku']][$i] = $value;
                        $dataProduct[$value['sku']][$i]['data_id'] = $row['id'];
                    }
                    
                    $i++;
                }
            } else {
                throw new StateException(
                    __("No data found.")
                );
            }
        } catch (\Exception $exception) {
            $this->updateJobStatus($jobId, IntegrationJobInterface::STATUS_PROGRES_UPDATE_FAIL, $exception->getMessage());
            throw new StateException(
                __("Error Validate SKU - ".$exception->getMessage())
            );
        }

        return $dataProduct;
    }

    /**
     * Update Jobs Status
     * @param $jobId int
     * @param $status int
     * @param $msg string
     * @throw new StateException
     */
    protected function updateJobStatus($jobId, $status=0, $msg="")
    {
        if (empty($jobId)) {
            throw new NoSuchEntityException(__('Jobs ID doesn\'t exist'));
        }
        try {
            $jobs = $this->integrationJobRepositoryInterface->getById($jobId);
            $jobs->setStatus($status);
            if (!empty($msg)) {
                $jobs->setMessage($msg);
            }
            $this->integrationJobRepositoryInterface->save($jobs);
        } catch (\Exception $exception) {
            throw new StateException(
                __('Cannot Update Job Status! - '.$exception->getMessage())
            );
        }
    }

    /**
     * Update Data Value Status
     * @param $jobId int
     * @param $status int
     * @param $msg string
     * @throw new StateException
     */
    protected function updateDataValueStatus($dataId, $status=0, $msg="")
    {
        if (empty($dataId)) {
            throw new NoSuchEntityException(__('Jobs ID doesn\'t exist'));
        }
        try {
            $query = $this->integrationDataValueRepositoryInterface->getById($dataId);
            $query->setStatus($status);
            if (!empty($msg)) {
                $query->setMessage($msg);
            }
            $this->integrationDataValueRepositoryInterface->save($query);
        } catch (\Exception $exception) {
            throw new StateException(
                __('Cannot Update Data Value Status! - '.$exception->getMessage())
            );
        }
    }

    /**
     * prepare data promo
     * @param $data mixed
     * @return $dataPass mixed
     */
    protected function prepareDataPromoGlobal($data)
    {
        $dataAttr = $data["attributes"];

        // promo day add comma
        $promo_day_text = (string)$dataAttr['promo_day'];
        $promo_day_arr = str_split($promo_day_text, "1");
        foreach ($promo_day_arr as $promo_day_ar) {
            if ($promo_day_ar == 0) {
                $promo_day_ar = 7;
            }
            $promo_day_arr_n[] = $promo_day_ar;
        }
        sort($promo_day_arr_n);
        $promo_day_new_text = implode(",", $promo_day_arr_n);

        //sliding discount type info
        $sliding_text  = $dataAttr['sliding_disc_type_info'];
        $sliding_arr = explode(";", $sliding_text);
        $sliding_arr_count = count($sliding_arr);

        // parse dataPass array to promotype = 8 & promotype = 2
        $dataPass = [
            'id' => $data['id'],
            'product_id' => $dataAttr['product_id'],
            'sku' => $data['sku'],
            'store_code' => $dataAttr['store_code'],
            'company_code' => $data['company_code'],
            'from_date' => $dataAttr['start_date'],
            'from_time' => $dataAttr['start_time'],
            'to_date' => $dataAttr['end_date'],
            'to_time' => $dataAttr['end_time'],
            'uses_per_customer' => 0,
            'is_active' => 1,
            'stop_rules_processing' => 1,
            'is_advanced' => 1,
            'sort_order' => 0,
            'simple_action' => 'by_percent',
            'discount_amount' => $dataAttr['percent_disc'],
            'discount_qty' => $dataAttr['max_point'],
            'discount_step'  => 1,
            'apply_to_shipping'  => 0,
            'times_used'  => 0,
            'is_rss'  => 1,
            'coupon_type'  => 1,
            'use_auto_generation'  => 0,
            'uses_per_coupon'  => 0,
            'simple_free_shipping'  => 1,
            'created_in'  => 1,
            'customer_group_id' => '1',
            'website_ids' => 1,
            'day_of_week' => $promo_day_new_text,
            'sliding_disc_type' => $dataAttr['sliding_disc_type_info'],
            'sliding_disc_type_info' => $sliding_arr,
            'sliding_disc_type_count' => $sliding_arr_count,
            'promotion_type' => $dataAttr['promotion_type'],
            'discount_type' => $dataAttr['discount_type'],
            'promo_price_qty' => $dataAttr['promo_price_qty'],
            'promotion_id' => $dataAttr['promotion_id'],
            'target_group' => $dataAttr['target_group'],
            'sliding_discount_type' => $dataAttr['sliding_disc_type'],
            'item_type' => $dataAttr['item_type']
        ];

        // for promo type 1 ,7 , 5 ,4 and 2
        if ($dataAttr['promotion_type'] == 1 || $dataAttr['promotion_type'] == 2 || $dataAttr['promotion_type'] == 7 || $dataAttr['promotion_type'] == 5 || $dataAttr['promotion_type'] == 4) {
            $dataPass['promo_selling_price'] = $dataAttr['promo_selling_price'];
            $dataPass['percent_disc'] = $dataAttr['percent_disc'];
            $dataPass['amount_off'] = $dataAttr['amount_off'];
        }
        // for promo type 2
        if ($dataAttr['promotion_type'] == 2) {
            $dataPass['normal_price_qty'] = $dataAttr['normal_price_qty'];
        }
        // for promo type 5 and 7
        if ($dataAttr['promotion_type'] == 5 || $dataAttr['promotion_type'] == 7) {
            $dataPass['mix_and_match_code'] = $dataAttr['mix_and_match_code'];
            $dataPass['point_per_unit'] = $dataAttr['point_per_unit'];
            $dataPass['required_point'] = $dataAttr['required_point'];
        }
        // for promo type 7
        if ($dataAttr['promotion_type'] == 7) {
            $dataPass['required_point'] = $dataAttr['required_point'];
        }

        // for promo type 4
        if ($dataAttr['promotion_type'] == 4) {
            $dataPass['max_promo_price_qty'] = $dataAttr['max_promo_price_qty'];
        }

        return $dataPass;
    }

    /**
     * Save promotion
     * @param $dataProduct mixed
     * @return true
     */
    public function save($jobs = [], $dataProduct = [])
    {
        $jobId = $jobs->getFirstItem()->getId();
        if (!$jobs->getFirstItem()->getId()) {
            $message = 'Error Jobs Datas doesn\'t exist';
            $this->updateJobStatus($jobId, IntegrationJobInterface::STATUS_PROGRES_UPDATE_FAIL, $message);
            throw new NoSuchEntityException(__($message));
        }

        $jobId = $jobs->getFirstItem()->getId();

        $checkDataProduct = array_filter($dataProduct);

        if (empty($checkDataProduct)) {
            $message = "Theres No SKU Key Available";
            $this->updateJobStatus($jobId, IntegrationJobInterface::STATUS_PROGRES_UPDATE_FAIL, $message);
            throw new StateException(
                __($message)
            );
        }
        $query = [];
        $productIds = [];
        $productMapingData = [];
        $index = 0;
        $check = [];
        $productInterface = [];
        $dataMarkdown = [];

        //Get all required data
        $dataList = $this->getDataList($dataProduct);
        $productInterfaces = $this->getProductByMultipleSku($dataList['sku_list']);
        $attributeInterfaces = $this->getAttributeIdList($dataList['store_list']);

        try {
            foreach ($dataProduct as $sku => $data) {
                try {
                    // validate sku exist or not
                    if (in_array($sku, $productInterfaces['sku'])) {
                        $indexO = 0;
                        $defaultPrice = 0;                
                        foreach ($data as $row) {
                            try {
                                $dataPass = $this->prepareDataPromoGlobal($row);
                                switch ($dataPass['promotion_type']) {
                                    // promotype = 1
                                    case 1:
                                            $markdownFunction = $this->markdownFunction($dataPass, $attributeInterfaces);
                                            if ($markdownFunction) {
                                                $dataMarkdown[] = $markdownFunction;
                                            }
                                        break;

                                    // promotype = 2
                                    case 2:
                                            $this->promoType2Function($dataPass);
                                        break;

                                    // promotype = 8
                                    case 8:
                                            $this->promoType8Function($dataPass);
                                        break;

                                    // promotype = 5
                                    case 5:
                                            $this->promoType5Function($dataPass);
                                        break;

                                    // promotype = 7
                                    case 7:
                                            // $this->promoType7Function($dataPass);
                                        break;

                                    // promotype = 4
                                    case 4:
                                            $this->promoType4Function($dataPass);
                                        break;

                                    default:
                                    break;
                                }

                                $indexO++;

                                $this->updateDataValueStatus($row['data_id'], IntegrationDataValueInterface::STATUS_DATA_SUCCESS, null);

                            } catch (\Exception $exception) {
                                $msgP = "Error Save to Magento table ".__FUNCTION__." : ".$exception->getMessage();
                                $this->updateDataValueStatus($row['data_id'], IntegrationDataValueInterface::STATUS_DATA_FAIL_UPDATE, $msgP);
                                $this->logger->info($msgP);
                                continue;
                            }
                        }
                    }

                    
                } catch (\Exception $exception) {
                    foreach ($data as $valueX) {
                        $msgPp = "Error Save to Magento table ".__FUNCTION__." : ".$exception->getMessage();
                        $this->updateDataValueStatus($valueX['data_id'], IntegrationDataValueInterface::STATUS_DATA_FAIL_UPDATE, $msgPp);
                        $this->logger->info($msgPp);
                    }

                    continue;
                }
                        
                // $this->logger->info("promo Updated --->".print_r($sku, true));

                $index++;
            }

            // // save bulk markdown
            try {
                if ($dataMarkdown) {
                    $connectionMarkdown = $this->resourceConnection->getConnection();
                    
                    $connectionMarkdown->insertOnDuplicate("catalog_product_entity_decimal", $dataMarkdown, ['value']);
                    $this->logger->info('insertOnDuplicate success ' . date('d-M-Y H:i:s')); 
                }
            } catch (\Exception $e) {
                $this->logger->info('insertOnDuplicate fail ' . date('d-M-Y H:i:s')); 
            }
            try {
                if(!empty($productInterfaces['id'])) {
                    $this->reindexByProductsIds($productInterfaces['id'], ['catalog_product_attribute', 'catalogsearch_fulltext']);
                    $this->logger->info('reindex success ' . date('d-M-Y H:i:s')); 
                }
            } catch (\Exception $e) {
                $this->logger->info('reindex fail ' . date('d-M-Y H:i:s')); 
            }

        }
        catch (\Exception $exception) {
            $msg = "-".__FUNCTION__." ".$exception->getMessage();    
            $this->updateJobStatus($jobId,IntegrationJobInterface::STATUS_PROGRES_UPDATE_FAIL,$msg);
            throw new StateException(__($msg));
        }
        
        $this->updateJobStatus($jobId, IntegrationJobInterface::STATUS_COMPLETE);
        return $productMapingData;
    }

    /**
     * reindex bu product ids
     *
     * @param array $productIds
     * @param array $indexLists
     * @return void
     */
    protected function reindexByProductsIds($productIds, $indexLists)
    {
        foreach($indexLists as $indexList) {
            $categoryIndexer = $this->indexerRegistry->get($indexList);
            if (!$categoryIndexer->isScheduled()) {
                $categoryIndexer->reindexList(array_unique($productIds));
            }
        }
    }

    /**
     * Save promotion
     * @param $dataProduct mixed
     * @return true
     */
    public function saveTest($dataProduct)
    {
        // $i = 0;
        // $jsonResponse = json_decode($dataProduct, true);
        
        // // validate if data exist
        // if (!isset($jsonResponse['data'])) {
        //     return false;
        // }

        // $datas = $jsonResponse['data'];

        
        // $productInterface = [];
        // $attributeInterfaces = [];


        // //Get all required data
        // $dataList = $this->getDataListxx($datas);
        // $productInterfaces = $this->getProductByMultipleSku($dataList['sku_list']);
        // $attributeInterfaces = $this->getAttributeIdList($dataList['store_list']);

        // foreach ($datas as $row) {
        //     try {
        //         $dataPass = $this->prepareDataPromoGlobal($row);
        //         switch ($dataPass['promotion_type']) {
        //             // promotype = 1
        //             case 1:
        //                     $markdownFunction = $this->markdownFunction($dataPass, $attributeInterfaces);
        //                     if ($markdownFunction) {
        //                         $dataMarkdown[] = $markdownFunction;
        //                     }
        //                 break;

        //             // promotype = 2
        //             case 2:
        //                     $this->promoType2Function($dataPass);
        //                 break;

        //             // promotype = 8
        //             case 8:
        //                     $this->promoType8Function($dataPass);
        //                 break;

        //             // promotype = 5
        //             case 5:
        //                     $this->promoType5Function($dataPass);
        //                 break;

        //             // promotype = 7
        //             case 7:
        //                     // $this->promoType7Function($dataPass);
        //                 break;

        //             // promotype = 4
        //             case 4:
        //                     $this->promoType4Function($dataPass);
        //                 break;

        //             default:
        //             break;
        //         }

        //         $indexO++;

        //         $this->updateDataValueStatus($row['data_id'], IntegrationDataValueInterface::STATUS_DATA_SUCCESS, null);

        //     } catch (\Exception $exception) {
                
        //         continue;
        //     }
        // }

        // // save bulk markdown
        // try {
        //     if ($dataMarkdown) {
        //         $connectionMarkdown = $this->resourceConnection->getConnection();
                
        //         $connectionMarkdown->insertOnDuplicate("catalog_product_entity_decimal", $dataMarkdown, ['value']);
        //         $this->logger->info('insertOnDuplicate success ' . date('d-M-Y H:i:s')); 
        //     }
        // } catch (\Exception $e) {
        //     $this->logger->info('insertOnDuplicate fail ' . date('d-M-Y H:i:s')); 
        // }
        
        return true;
    }

    /**
     * function for markdown
     * @param $dataPass mixed
     * @param $attributeInterfaces mixed
     * @throw new StateException
     */
    protected function markdownFunction($dataPass, $attributeInterfaces)
    {
        try {
            // if promo id not exist
            if ($dataPass['target_group'] == 0) {
                // $checkIntPromoId = $this->checkIntegrationPromoByPromoId($dataPass['promotion_id']);
                $checkIntPromoId = $this->checkIntegrationPromoByPromoIdStoreCode($dataPass);

                // Nested switch discount type
                // data start time
                $startTime = $this->timezone->date(new \DateTime())->format('Y-m-d H:i:s');
                $endTime = $dataPass['to_date'].' '.$dataPass['to_time'];
                $endTime = (new \DateTime($endTime))->format('Y-m-d H:i:s');
                $skuNumber = $dataPass['sku'];
                
                // data campaign and rollback
                $isCampaign = 0;
                $isRollback = 1;

                // for data function validateStorePrice and saveBasePriceStorePrice
                $dataStoreCode = [
                    'sku' => $dataPass['sku'],
                    'discount_type' => $dataPass['discount_type'],
                    'percent_disc' => $dataPass['percent_disc'],
                    'amount_off' => $dataPass['amount_off'],
                    'promo_selling_price' => $dataPass['promo_selling_price'],
                    'disc_type' => $dataPass['discount_type'],
                    'store_code' => $dataPass['store_code']
                ];
                
                // check if end date less than now
                if ($endTime > $startTime) {
                    switch ($dataPass['discount_type']) {
                        // promotype = 1 , disctype = 1
                        case 1:
                            // data name and desc
                            $name = $dataPass['promotion_id'].':'.$dataPass['promotion_type'].' - Markdown Price Rp '.$dataPass['promo_selling_price'].' fixed price ('.$dataPass['sku'].')';
                            $desc = $dataPass['promotion_id'].':'.$dataPass['promotion_type'].' - Markdown Price Rp '.$dataPass['promo_selling_price'].' fixed price ('.$dataPass['sku'].')';

                            $savePromoPriceStoreStatus = $this->savePromoPriceStore($startTime, $endTime, $name, $desc, $isCampaign, $isRollback, $dataStoreCode, $attributeInterfaces);
                            break;

                        // promotype = 1 , disctype = 2
                        case 2:
                            // data name and desc
                            $name = $dataPass['promotion_id'].':'.$dataPass['promotion_type'].' - Markdown Price '.$dataPass['percent_disc'].' % Off ('.$dataPass['sku'].')';
                            $desc = $dataPass['promotion_id'].':'.$dataPass['promotion_type'].' - Markdown Price '.$dataPass['percent_disc'].' % Off ('.$dataPass['sku'].')';
                            
                            $savePromoPriceStoreStatus = $this->savePromoPriceStore($startTime, $endTime, $name, $desc, $isCampaign, $isRollback, $dataStoreCode, $attributeInterfaces);
                            break;

                        // promotype = 1 , disctype = 3
                        case 3:
                            // data name and desc
                            $name = $dataPass['promotion_id'].':'.$dataPass['promotion_type'].' - Markdown Price Rp '.$dataPass['amount_off'].' Off ('.$dataPass['sku'].')';
                            $desc = $dataPass['promotion_id'].':'.$dataPass['promotion_type'].' - Markdown Price Rp '.$dataPass['amount_off'].' Off ('.$dataPass['sku'].')';

                            $savePromoPriceStoreStatus = $this->savePromoPriceStore($startTime, $endTime, $name, $desc, $isCampaign, $isRollback, $dataStoreCode, $attributeInterfaces);
                            break;
                    }

                    // save to integration prom price table
                    $dataPass['name'] = $name;
                    $dataPass['from_date'] = $startTime;
                    $dataPass['to_date'] = $endTime;
                    if ($savePromoPriceStoreStatus) {
                        if (!$checkIntPromoId) {
                            $saveDataIntPromo = $this->saveIntegrationPromo($dataPass);
                        }
                        else {
                            // update end date
                            $checkIntPromoId->setStartDate($startTime);
                            $checkIntPromoId->setEndDate($endTime);
                            if (!empty($dataPass['promo_selling_price'])) {
                                $checkIntPromoId->setPromoSellingPrice($dataPass['promo_selling_price']);
                            }
                            if (!empty($dataPass['percent_disc'])) {
                                $checkIntPromoId->setPercentDisc($dataPass['percent_disc']);
                            }
                            if (!empty($dataPass['amount_off'])) {
                                $checkIntPromoId->setAmountOff($dataPass['amount_off']);
                            }
                            $this->promotionPriceRepositoryInterface->save($checkIntPromoId);
                        }
                    }

                    return $savePromoPriceStoreStatus;
                }
                else {
                    if ($checkIntPromoId) {
                        $savePromoPriceStoreStatus = $this->savePromoPriceStoreEndDate($dataStoreCode, $attributeInterfaces);

                        // update end date
                        $checkIntPromoId->setEndDate($endTime);
                        $this->promotionPriceRepositoryInterface->save($checkIntPromoId);

                        return $savePromoPriceStoreStatus;
                    }
                }
            }
            // --------------
        } catch (\Exception $e) {
            $this->logger->info("error saved markdown --->".print_r($dataPass['sku'], true));
            throw new StateException(
                __($e->getMessage())
            );
        }
    }

    /**
     * function for promo type 4
     * @param $dataPass mixed
     * @throw new StateException
     */
    protected function promoType4Function($dataPass)
    {
        try {
            // if promo id not exist
            // $checkIntPromoId = $this->checkIntegrationPromoByPromoId($dataPass['promotion_id']);
            $checkIntPromoId = $this->checkIntegrationPromoSkuAndPromoType($dataPass);

            if (!$checkIntPromoId) {
                $dataPass['simple_free_shipping'] = 0;
                $dataPass['discount_qty'] = $dataPass['max_promo_price_qty'];
                // Nested switch discount type
                switch ($dataPass['discount_type']) {
                    // promotype = 4 , disctype = 1
                    case 1:
                        // add dataPass array for name and desc
                        $dataPass['rule_name'] = 'Only Rp '.$dataPass['promo_selling_price'].' (Max. '.$dataPass['max_promo_price_qty'].')';
                        $dataPass['name'] = $dataPass['promotion_id'].':'.$dataPass['promotion_type'].' - Rp '.$dataPass['promo_selling_price'].' Fixed price for SKU '.$dataPass['sku'].' with maximum of '.$dataPass['max_promo_price_qty'].' qty per transaction ('.$dataPass['sku'].')';
                        $dataPass['desc'] = $dataPass['promotion_id'].':'.$dataPass['promotion_type'].' - Rp '.$dataPass['promo_selling_price'].' Fixed price for SKU '.$dataPass['sku'].' with maximum of '.$dataPass['max_promo_price_qty'].' qty per transaction ('.$dataPass['sku'].') - ('.$dataPass['from_date'].' - '.$dataPass['to_date'].')';
                        $dataPass['simple_action'] = 'setof_fixed';
                        $dataPass['discount_amount'] = $dataPass['promo_selling_price'];

                        $result = $this->saveSalesRule($dataPass);
                        $dataPass['salesrule_id'] = $result['rule_id'];
                        break;

                    // promotype = 4 , disctype = 2
                    case 2:
                        // add dataPass array for name and desc
                        $dataPass['rule_name'] = 'Disc. '.$dataPass['percent_disc'].'% (Max. '.$dataPass['max_promo_price_qty'].')';
                        $dataPass['name'] = $dataPass['promotion_id'].':'.$dataPass['promotion_type'].' - '.$dataPass['percent_disc'].'% off for SKU '.$dataPass['sku'].' with maximum of '.$dataPass['max_promo_price_qty'].' qty per transaction ('.$dataPass['sku'].')';
                        $dataPass['desc'] = $dataPass['promotion_id'].':'.$dataPass['promotion_type'].' - '.$dataPass['percent_disc'].'% off for SKU '.$dataPass['sku'].' with maximum of '.$dataPass['max_promo_price_qty'].' qty per transaction ('.$dataPass['sku'].') - ('.$dataPass['from_date'].' - '.$dataPass['to_date'].')';
                        $dataPass['simple_action'] = 'by_percent';
                        $dataPass['discount_amount'] = $dataPass['percent_disc'];

                        $result = $this->saveSalesRule($dataPass);
                        $dataPass['salesrule_id'] = $result['rule_id'];
                        break;

                    // promotype = 4 , disctype = 3
                    case 3:
                        // add dataPass array for name and desc
                        $dataPass['rule_name'] = 'Disc. Rp '.$dataPass['amount_off'].' (Max. '.$dataPass['max_promo_price_qty'].')';
                        $dataPass['name'] = $dataPass['promotion_id'].':'.$dataPass['promotion_type'].' - Rp '.$dataPass['amount_off'].' amount off for SKU '.$dataPass['sku'].' with maximum of '.$dataPass['max_promo_price_qty'].' qty per transaction ('.$dataPass['sku'].')';
                        $dataPass['desc'] = $dataPass['promotion_id'].':'.$dataPass['promotion_type'].' - Rp '.$dataPass['amount_off'].' amount off for SKU '.$dataPass['sku'].' with maximum of '.$dataPass['max_promo_price_qty'].' qty per transaction ('.$dataPass['sku'].') - ('.$dataPass['from_date'].' - '.$dataPass['to_date'].')';
                        $dataPass['simple_action'] = 'by_fixed';
                        $dataPass['discount_amount'] = $dataPass['amount_off'];

                        $result = $this->saveSalesRule($dataPass);
                        $dataPass['salesrule_id'] = $result['rule_id'];
                        break;
                }

                // save to integration prom price table
                $saveDataIntPromo = $this->saveIntegrationPromo($dataPass);
            }
            else {
                // if promo id not exist
                $checkIntPromoIdStoreCode = $this->checkIntegrationPromoByPromoIdStoreCode($dataPass);
                if (!$checkIntPromoIdStoreCode) {
                    $dataPass['salesrule_id'] = $checkIntPromoId->getData('salesrule_id');
                    // add dataPass array for name and desc
                    $dataPass['simple_free_shipping'] = 0;
                    $dataPass['discount_qty'] = $dataPass['max_promo_price_qty'];
                    // Nested switch discount type
                    switch ($dataPass['discount_type']) {
                        // promotype = 4 , disctype = 1
                        case 1:
                            // add dataPass array for name and desc
                            $dataPass['rule_name'] = 'Only Rp '.$dataPass['promo_selling_price'].' (Max. '.$dataPass['max_promo_price_qty'].')';
                            $dataPass['name'] = $dataPass['promotion_id'].':'.$dataPass['promotion_type'].' - Rp '.$dataPass['promo_selling_price'].' Fixed price for SKU '.$dataPass['sku'].' with maximum of '.$dataPass['max_promo_price_qty'].' qty per transaction ('.$dataPass['sku'].')';
                            $dataPass['desc'] = $dataPass['promotion_id'].':'.$dataPass['promotion_type'].' - Rp '.$dataPass['promo_selling_price'].' Fixed price for SKU '.$dataPass['sku'].' with maximum of '.$dataPass['max_promo_price_qty'].' qty per transaction ('.$dataPass['sku'].') - ('.$dataPass['from_date'].' - '.$dataPass['to_date'].')';
                            $dataPass['simple_action'] = 'setof_fixed';
                            $dataPass['discount_amount'] = $dataPass['promo_selling_price'];

                            $this->updateSalesRule($dataPass);
                            break;

                        // promotype = 4 , disctype = 2
                        case 2:
                            // add dataPass array for name and desc
                            $dataPass['rule_name'] = 'Disc. '.$dataPass['percent_disc'].'% (Max. '.$dataPass['max_promo_price_qty'].')';
                            $dataPass['name'] = $dataPass['promotion_id'].':'.$dataPass['promotion_type'].' - '.$dataPass['percent_disc'].'% off for SKU '.$dataPass['sku'].' with maximum of '.$dataPass['max_promo_price_qty'].' qty per transaction ('.$dataPass['sku'].')';
                            $dataPass['desc'] = $dataPass['promotion_id'].':'.$dataPass['promotion_type'].' - '.$dataPass['percent_disc'].'% off for SKU '.$dataPass['sku'].' with maximum of '.$dataPass['max_promo_price_qty'].' qty per transaction ('.$dataPass['sku'].') - ('.$dataPass['from_date'].' - '.$dataPass['to_date'].')';
                            $dataPass['simple_action'] = 'by_percent';
                            $dataPass['discount_amount'] = $dataPass['percent_disc'];

                            $this->updateSalesRule($dataPass);
                            break;

                        // promotype = 4 , disctype = 3
                        case 3:
                            // add dataPass array for name and desc
                            $dataPass['rule_name'] = 'Disc. Rp '.$dataPass['amount_off'].' (Max. '.$dataPass['max_promo_price_qty'].')';
                            $dataPass['name'] = $dataPass['promotion_id'].':'.$dataPass['promotion_type'].' - Rp '.$dataPass['amount_off'].' amount off for SKU '.$dataPass['sku'].' with maximum of '.$dataPass['max_promo_price_qty'].' qty per transaction ('.$dataPass['sku'].')';
                            $dataPass['desc'] = $dataPass['promotion_id'].':'.$dataPass['promotion_type'].' - Rp '.$dataPass['amount_off'].' amount off for SKU '.$dataPass['sku'].' with maximum of '.$dataPass['max_promo_price_qty'].' qty per transaction ('.$dataPass['sku'].') - ('.$dataPass['from_date'].' - '.$dataPass['to_date'].')';
                            $dataPass['simple_action'] = 'by_fixed';
                            $dataPass['discount_amount'] = $dataPass['amount_off'];

                            $this->updateSalesRule($dataPass);
                            break;
                    }
                    // save to integration prom price table
                    $saveDataIntPromo = $this->saveIntegrationPromo($dataPass);
                }
                
            }
            // --------------
        } catch (\Exception $e) {
            $this->logger->info("error saved promo type 4 --->".print_r($dataPass['sku'], true));
            throw new StateException(
                __($e->getMessage())
            );
        }
    }

    /**
     * function for promo type 7
     * @param $dataPass mixed
     * @throw new StateException
     */
    protected function promoType7Function($dataPass)
    {
        try {
            // if promo id not exist
            $checkIntPromoId = $this->checkIntegrationPromoByPromoId($dataPass['promotion_id']);

            // if (!$checkIntPromoId) {
                // add dataPass array for name and desc
                $dataPass['name'] = $dataPass['promotion_id'].':'.$dataPass['promotion_type'].' - spend x amount, get item (pick only one item from multiple reward item) ('.$dataPass['sku'].')';
                $dataPass['desc'] = $dataPass['promotion_id'].':'.$dataPass['promotion_type'].' - spend x amount, get item (pick only one item from multiple reward item) ('.$dataPass['sku'].') - ('.$dataPass['from_date'].' - '.$dataPass['to_date'].')';
                $dataPass['simple_action'] = 'ampromo_items';
                $dataPass['discount_amount'] = 1;
                $dataPass['simple_free_shipping'] = 0;

                // check on table integration promo and item type 1
                if ($dataPass['item_type'] == 1) {
                    $checkDataIntPromo = $this->checkIntegrationPromo($dataPass);

                    // if check 0 then save
                    if (!$checkDataIntPromo) {
                        // save to salesrule
                        $result = $this->saveSalesRule($dataPass);
                        // get salesruleid
                        $dataPass['salesrule_id'] = $result['rule_id'];
                        $dataPass['row_id'] = $result['row_id'];
                        // save to integration promo
                        $saveDataIntPromo = $this->saveIntegrationPromo($dataPass);
                    } else {
                        // get salesruleid
                        $dataPass['salesrule_id'] = $checkDataIntPromo->getData('salesrule_id');
                        $dataPass['row_id'] = $checkDataIntPromo->getData('row_id');
                        // save to integration promo
                        $saveDataIntPromo = $this->saveIntegrationPromo($dataPass);
                        // update condition serialize , save to salesrule
                        $this->updateSalesRule($dataPass);
                    }
                }
                // if item type 2
                else {
                    $dataPass['item_type'] = 1;
                    $checkSalesRule = $this->checkIntegrationPromo($dataPass);
                    if ($checkSalesRule) {
                        // get salesruleid
                        $dataPass['salesrule_id'] = $checkSalesRule->getData('salesrule_id');
                        $dataPass['row_id'] = $checkSalesRule->getData('row_id');
                        // save to integration promo
                        $dataPass['item_type'] = 2;
                        $saveDataIntPromo = $this->saveIntegrationPromo($dataPass);
                        // update sku amasty promo
                        $this->updateAmastySalesRule($dataPass);
                        // update required point on condition serialize
                        $this->updateSalesRuleRequiredPoint($dataPass);
                    } else {
                        // save to integration promo for item type 2 , first
                        $dataPass['item_type'] = 2;
                        $saveDataIntPromo = $this->saveIntegrationPromo($dataPass);
                    }
                }
            // }
            // --------------
        } catch (\Exception $e) {
            $this->logger->info("error saved promo type 7 --->".print_r($dataPass['sku'], true));
            throw new StateException(
                __($e->getMessage())
            );
        }
    }

    /**
     * function for promo type 2
     * @param $dataPass mixed
     * @throw new StateException
     */
    protected function promoType2Function($dataPass)
    {
        try {
            // if promo id not exist
            $dataPass['from_date'] = (new \DateTime($dataPass['from_date']))->format('Y-m-d H:i:s');
            $dataPass['to_date'] = (new \DateTime($dataPass['to_date']))->format('Y-m-d H:i:s');
            $dataPass['same_rule'] = 0;

            $checkIntPromoId = $this->checkIntegrationPromoSkuAndPromoType($dataPass);
            
            if (!$checkIntPromoId) {
                // add dataPass array for name and desc
                $dataPass['stop_rules_processing'] = 0;
                $dataPass['discount_step'] = $dataPass['promo_price_qty'];
                $dataPass['simple_free_shipping'] = 0;

                switch ($dataPass['discount_type']) {
                    // promotype = 2 , disctype = 1
                    case 1:
                        // promotype = 2 , disctype = 1, promo selling price == 1
                        if ($dataPass['promo_selling_price'] == 1) {
                            $dataPass['rule_name'] = 'Buy Z, Get Y';
                            $dataPass['name'] = $dataPass['promotion_id'].':'.$dataPass['promotion_type'].' - Buy Y pcs, Pay for Z pcs only (Free) ('.$dataPass['sku'].')';
                            $dataPass['desc'] = $dataPass['promotion_id'].':'.$dataPass['promotion_type'].' - Buy Y pcs, Pay for Z pcs only: same as buy '.$dataPass['normal_price_qty'].' get '.$dataPass['promo_price_qty'].' free (for the same item) ('.$dataPass['sku'].') - ('.$dataPass['from_date'].' - '.$dataPass['to_date'].')';
                            $dataPass['simple_action'] = 'buy_x_get_y';
                            $dataPass['discount_amount'] = $dataPass['promo_selling_price'];
                        }
                        // promotype = 2 , disctype = 1, promo selling price != 1
                        else {
                            $dataPass['rule_name'] = 'Buy Y, Get Rp '.$dataPass['promo_selling_price'].' Off';
                            $dataPass['name'] = $dataPass['promotion_id'].':'.$dataPass['promotion_type'].' - Buy Y pcs, Pay for Z pcs only (Fixed price) ('.$dataPass['sku'].')';
                            $dataPass['desc'] = $dataPass['promotion_id'].':'.$dataPass['promotion_type'].' - Buy Y pcs, Pay for Z pcs only: same as buy '.$dataPass['normal_price_qty'].' get '.$dataPass['promo_price_qty'].' with Fixed price (for the same item) ('.$dataPass['sku'].') - ('.$dataPass['from_date'].' - '.$dataPass['to_date'].')';
                            $dataPass['simple_action'] = 'eachn_fixprice';
                            $dataPass['discount_amount'] = $dataPass['promo_selling_price'];
                        }

                        // check if discount same rule
                        try {
                            $checkIntPromoTwoFixed = $this->promotionPriceRepositoryInterface->loadDataPromoTypeTwoBySameRuleFixed($dataPass);
                        } catch (\Exception $e) {
                            $this->logger->info("<=End " .$e->getMessage());
                            throw new StateException(
                                __(__FUNCTION__." - ".$e->getMessage())
                            );
                        }
                        
                        if (!$checkIntPromoTwoFixed) {
                            $result = $this->saveSalesRule($dataPass);
                            $dataPass['salesrule_id'] = $result['rule_id'];
                        }
                        else {
                            $dataPass['same_rule'] = 1;
                            $dataPass['salesrule_id'] = $checkIntPromoTwoFixed->getData('salesrule_id');
                            $this->updateSalesRule($dataPass);
                        }

                        break;

                    // promotype = 2 , disctype = 2
                    case 2:
                        $dataPass['rule_name'] = 'Buy Y, Get '.$dataPass['percent_disc'].'% Off';
                        $dataPass['name'] = $dataPass['promotion_id'].':'.$dataPass['promotion_type'].' - Buy Y pcs, Pay for Z pcs only (Percentage) ('.$dataPass['sku'].')';
                        $dataPass['desc'] = $dataPass['promotion_id'].':'.$dataPass['promotion_type'].' - Buy Y pcs, Pay for Z pcs only: same as buy '.$dataPass['normal_price_qty'].' get '.$dataPass['promo_price_qty'].' with percentage (for the same item) ('.$dataPass['sku'].') - ('.$dataPass['from_date'].' - '.$dataPass['to_date'].')';
                        $dataPass['simple_action'] = 'eachn_perc';
                        $dataPass['discount_amount'] = $dataPass['percent_disc'];

                        // check if discount same rule
                        try {
                            $checkIntPromoTwoPercentage = $this->promotionPriceRepositoryInterface->loadDataPromoTypeTwoBySameRulePercentage($dataPass);
                        } catch (\Exception $e) {
                            $this->logger->info("<=End " .$e->getMessage());
                            throw new StateException(
                                __(__FUNCTION__." - ".$e->getMessage())
                            );
                        }
                        
                        if (!$checkIntPromoTwoPercentage) {
                            $result = $this->saveSalesRule($dataPass);
                            $dataPass['salesrule_id'] = $result['rule_id'];
                        }
                        else {
                            $dataPass['same_rule'] = 1;
                            $dataPass['salesrule_id'] = $checkIntPromoTwoPercentage->getData('salesrule_id');
                            $this->updateSalesRule($dataPass);
                        }
                        
                        break;

                    // promotype = 2 , disctype = 3
                    case 3:
                        $dataPass['rule_name'] = 'Buy Y, Get Rp '.$dataPass['amount_off'].' Off';
                        $dataPass['name'] = $dataPass['promotion_id'].':'.$dataPass['promotion_type'].' - Buy Y pcs, Pay for Z pcs only (Amount off) ('.$dataPass['sku'].')';
                        $dataPass['desc'] = $dataPass['promotion_id'].':'.$dataPass['promotion_type'].' - Buy Y pcs, Pay for Z pcs only: same as buy '.$dataPass['normal_price_qty'].' get '.$dataPass['promo_price_qty'].' with amount off (for the same item) ('.$dataPass['sku'].') - ('.$dataPass['from_date'].' - '.$dataPass['to_date'].')';
                        $dataPass['simple_action'] = 'eachn_fixdisc';
                        $dataPass['discount_amount'] = $dataPass['amount_off'];
                        
                        // check if discount same rule
                        try {
                            $checkIntPromoTwoFixed = $this->promotionPriceRepositoryInterface->loadDataPromoTypeTwoBySameRuleAmount($dataPass);
                        } catch (\Exception $e) {
                            $this->logger->info("<=End " .$e->getMessage());
                            throw new StateException(
                                __(__FUNCTION__." - ".$e->getMessage())
                            );
                        }
                        
                        if (!$checkIntPromoTwoFixed) {
                            $result = $this->saveSalesRule($dataPass);
                            $dataPass['salesrule_id'] = $result['rule_id'];
                        }
                        else {
                            $dataPass['same_rule'] = 1;
                            $dataPass['salesrule_id'] = $checkIntPromoTwoFixed->getData('salesrule_id');
                            $this->updateSalesRule($dataPass);
                        }
                        
                        break;
                }

                // save to integration prom price table
                $saveDataIntPromo = $this->saveIntegrationPromo($dataPass);
            }
            else {
                // if sku and store not exist
                $checkIntPromoIdStoreCode = $this->checkIntegrationPromoByPromoIdStoreCode($dataPass);

                if (!$checkIntPromoIdStoreCode) {
                    // add dataPass array for name and desc
                    $dataPass['stop_rules_processing'] = 0;
                    $dataPass['discount_step'] = $dataPass['promo_price_qty'];
                    $dataPass['simple_free_shipping'] = 0;
                    $dataPass['salesrule_id'] = $checkIntPromoId->getData('salesrule_id');
                    switch ($dataPass['discount_type']) {
                        // promotype = 2 , disctype = 1
                        case 1:
                            // promotype = 2 , disctype = 1, promo selling price == 1
                            if ($dataPass['promo_selling_price'] == 1) {
                                $dataPass['rule_name'] = 'Buy Z, Get Y';
                                $dataPass['name'] = $dataPass['promotion_id'].':'.$dataPass['promotion_type'].' - Buy Y pcs, Pay for Z pcs only (Free) ('.$dataPass['sku'].')';
                                $dataPass['desc'] = $dataPass['promotion_id'].':'.$dataPass['promotion_type'].' - Buy Y pcs, Pay for Z pcs only: same as buy '.$dataPass['normal_price_qty'].' get '.$dataPass['promo_price_qty'].' free (for the same item) ('.$dataPass['sku'].') - ('.$dataPass['from_date'].' - '.$dataPass['to_date'].')';
                                $dataPass['simple_action'] = 'buy_x_get_y';
                                $dataPass['discount_amount'] = $dataPass['promo_selling_price'];
                            }
                            // promotype = 2 , disctype = 1, promo selling price != 1
                            else {
                                $dataPass['rule_name'] = 'Buy Y, Get Rp '.$dataPass['promo_selling_price'].' Off';
                                $dataPass['name'] = $dataPass['promotion_id'].':'.$dataPass['promotion_type'].' - Buy Y pcs, Pay for Z pcs only (Fixed price) ('.$dataPass['sku'].')';
                                $dataPass['desc'] = $dataPass['promotion_id'].':'.$dataPass['promotion_type'].' - Buy Y pcs, Pay for Z pcs only: same as buy '.$dataPass['normal_price_qty'].' get '.$dataPass['promo_price_qty'].' with Fixed price (for the same item) ('.$dataPass['sku'].') - ('.$dataPass['from_date'].' - '.$dataPass['to_date'].')';
                                $dataPass['simple_action'] = 'eachn_fixprice';
                                $dataPass['discount_amount'] = $dataPass['promo_selling_price'];
                            }
                            $this->updateSalesRule($dataPass);
                            break;

                        // promotype = 2 , disctype = 2
                        case 2:
                            $dataPass['rule_name'] = 'Buy Y, Get '.$dataPass['percent_disc'].'% Off';
                            $dataPass['name'] = $dataPass['promotion_id'].':'.$dataPass['promotion_type'].' - Buy Y pcs, Pay for Z pcs only (Percentage) ('.$dataPass['sku'].')';
                            $dataPass['desc'] = $dataPass['promotion_id'].':'.$dataPass['promotion_type'].' - Buy Y pcs, Pay for Z pcs only: same as buy '.$dataPass['normal_price_qty'].' get '.$dataPass['promo_price_qty'].' with percentage (for the same item) ('.$dataPass['sku'].') - ('.$dataPass['from_date'].' - '.$dataPass['to_date'].')';
                            $dataPass['simple_action'] = 'eachn_perc';
                            $dataPass['discount_amount'] = $dataPass['percent_disc'];
                            $this->updateSalesRule($dataPass);
                            break;

                        // promotype = 2 , disctype = 3
                        case 3:
                            $dataPass['rule_name'] = 'Buy Y, Get Rp '.$dataPass['amount_off'].' Off';
                            $dataPass['name'] = $dataPass['promotion_id'].':'.$dataPass['promotion_type'].' - Buy Y pcs, Pay for Z pcs only (Amount off) ('.$dataPass['sku'].')';
                            $dataPass['desc'] = $dataPass['promotion_id'].':'.$dataPass['promotion_type'].' - Buy Y pcs, Pay for Z pcs only: same as buy '.$dataPass['normal_price_qty'].' get '.$dataPass['promo_price_qty'].' with amount off (for the same item) ('.$dataPass['sku'].') - ('.$dataPass['from_date'].' - '.$dataPass['to_date'].')';
                            $dataPass['simple_action'] = 'eachn_fixdisc';
                            $dataPass['discount_amount'] = $dataPass['amount_off'];
                            $this->updateSalesRule($dataPass);
                            break;
                    }
                    // save to integration prom price table
                    $saveDataIntPromo = $this->saveIntegrationPromo($dataPass);
                }
                
            }
            // --------------
        } catch (\Exception $e) {
            $this->logger->info("error saved promo type 2 --->".print_r($dataPass['sku'], true));
            throw new StateException(
                __($e->getMessage())
            );
        }
    }

    /**
     * function for promo type 8
     * @param $dataPass mixed
     * @throw new StateException
     */
    protected function promoType8Function($dataPass)
    {
        try {
            // if promo id not exist
            // $checkIntPromoId = $this->checkIntegrationPromoByPromoId($dataPass['promotion_id']);
            $dataPass['discount_type'] = $dataPass['sliding_discount_type'];
            $checkIntPromoId = $this->checkIntegrationPromoSkuAndPromoType($dataPass);

            if (!$checkIntPromoId) {
                $dataPass['simple_free_shipping'] = 0;
                // Nested switch discount type
                switch ($dataPass['discount_type']) {
                    // promotype = 8 , disctype = 1
                    case 1:
                        $dataPass['simple_action'] = 'setof_fixed';
                        $arr = 1;
                        // save to sales rule
                        foreach ($dataPass['sliding_disc_type_info'] as $row) {
                            //sliding discount type info child
                            $sliding_child_arr = explode("-", $row);
                            $dataPass['sliding_child'] = $sliding_child_arr;
                            $dataPass['sliding_child_sequence'] = $arr;
                            $dataPass['discount_amount'] = $dataPass['sliding_child'][2];

                            // add dataPass array for name and desc
                            $dataPass['rule_name'] = 'Buy More, Save More';
                            $dataPass['name'] = $dataPass['promotion_id'].':'.$dataPass['promotion_type'].' - Tier Price Rp '.$sliding_child_arr[2].' fixed price ('.$dataPass['sku'].')';
                            $dataPass['desc'] = $dataPass['promotion_id'].':'.$dataPass['promotion_type'].' - Tier Price Rp '.$sliding_child_arr[2].' fixed price ('.$dataPass['sku'].') - ('.$dataPass['from_date'].' - '.$dataPass['to_date'].')';

                            $result = $this->saveSalesRule($dataPass);
                            $salesRuleIdArray = $result;
                            $arr++;
                        }
                        break;

                    // promotype = 8 , disctype = 2
                    case 2:
                        $dataPass['simple_action'] = 'by_percent';
                        $arr = 1;
                        // save to sales rule
                        foreach ($dataPass['sliding_disc_type_info'] as $row) {
                            //sliding discount type info child
                            $sliding_child_arr = explode("-", $row);
                            $dataPass['sliding_child'] = $sliding_child_arr;
                            $dataPass['sliding_child_sequence'] = $arr;
                            $dataPass['discount_amount'] = $dataPass['sliding_child'][2];

                            // add dataPass array for name and desc
                            $dataPass['rule_name'] = 'Buy More, Save More';
                            $dataPass['name'] = $dataPass['promotion_id'].':'.$dataPass['promotion_type'].' - Tier Price '.$sliding_child_arr[2].' % off ('.$dataPass['sku'].')';
                            $dataPass['desc'] = $dataPass['promotion_id'].':'.$dataPass['promotion_type'].' - Tier Price '.$sliding_child_arr[2].' % off ('.$dataPass['sku'].') - ('.$dataPass['from_date'].' - '.$dataPass['to_date'].')';

                            $this->saveSalesRule($dataPass);
                            $arr++;
                        }
                        break;

                    // promotype = 8 , disctype = 3
                    case 3:
                        $dataPass['simple_action'] = 'by_fixed';
                        $arr = 1;
                        // save to sales rule
                        foreach ($dataPass['sliding_disc_type_info'] as $row) {
                            //sliding discount type info child
                            $sliding_child_arr = explode("-", $row);
                            $dataPass['sliding_child'] = $sliding_child_arr;
                            $dataPass['sliding_child_sequence'] = $arr;
                            $dataPass['discount_amount'] = $dataPass['sliding_child'][2];

                            // add dataPass array for name and desc
                            $dataPass['rule_name'] = 'Buy More, Save More';
                            $dataPass['name'] = $dataPass['promotion_id'].':'.$dataPass['promotion_type'].' - Tier Price Rp '.$sliding_child_arr[2].' Off ('.$dataPass['sku'].')';
                            $dataPass['desc'] = $dataPass['promotion_id'].':'.$dataPass['promotion_type'].' - Tier Price Rp '.$sliding_child_arr[2].' Off ('.$dataPass['sku'].') - ('.$dataPass['from_date'].' - '.$dataPass['to_date'].')';

                            $this->saveSalesRule($dataPass);
                            $arr++;
                        }
                        break;
                }
                // save to integration prom price table
                $saveDataIntPromo = $this->saveIntegrationPromo($dataPass);
            }
            else {
                // if promo id not exist
                $checkIntPromoIdStoreCode = $this->checkIntegrationPromoByPromoIdStoreCode($dataPass);
                if (!$checkIntPromoIdStoreCode) {
                    $dataPass['simple_free_shipping'] = 0;
                    // Nested switch discount type
                    switch ($dataPass['discount_type']) {
                        // promotype = 8 , disctype = 1
                        case 1:
                            $dataPass['simple_action'] = 'setof_fixed';
                            $arr = 1;
                            // save to sales rule
                            foreach ($dataPass['sliding_disc_type_info'] as $row) {
                                //sliding discount type info child
                                $sliding_child_arr = explode("-", $row);
                                $dataPass['sliding_child'] = $sliding_child_arr;
                                $dataPass['sliding_child_sequence'] = $arr;
                                $dataPass['discount_amount'] = $dataPass['sliding_child'][2];

                                // add dataPass array for name and desc
                                $dataPass['rule_name'] = 'Buy More, Save More';
                                $dataPass['name'] = $dataPass['promotion_id'].':'.$dataPass['promotion_type'].' - Tier Price Rp '.$sliding_child_arr[2].' fixed price ('.$dataPass['sku'].')';
                                $dataPass['desc'] = $dataPass['promotion_id'].':'.$dataPass['promotion_type'].' - Tier Price Rp '.$sliding_child_arr[2].' fixed price ('.$dataPass['sku'].') - ('.$dataPass['from_date'].' - '.$dataPass['to_date'].')';

                                $result = $this->saveSalesRule($dataPass);
                                $salesRuleIdArray = $result;
                                $arr++;
                            }
                            break;

                        // promotype = 8 , disctype = 2
                        case 2:
                            $dataPass['simple_action'] = 'by_percent';
                            $arr = 1;
                            // save to sales rule
                            foreach ($dataPass['sliding_disc_type_info'] as $row) {
                                //sliding discount type info child
                                $sliding_child_arr = explode("-", $row);
                                $dataPass['sliding_child'] = $sliding_child_arr;
                                $dataPass['sliding_child_sequence'] = $arr;
                                $dataPass['discount_amount'] = $dataPass['sliding_child'][2];

                                // add dataPass array for name and desc
                                $dataPass['rule_name'] = 'Buy More, Save More';
                                $dataPass['name'] = $dataPass['promotion_id'].':'.$dataPass['promotion_type'].' - Tier Price '.$sliding_child_arr[2].' % off ('.$dataPass['sku'].')';
                                $dataPass['desc'] = $dataPass['promotion_id'].':'.$dataPass['promotion_type'].' - Tier Price '.$sliding_child_arr[2].' % off ('.$dataPass['sku'].') - ('.$dataPass['from_date'].' - '.$dataPass['to_date'].')';

                                $this->saveSalesRule($dataPass);
                                $arr++;
                            }
                            break;

                        // promotype = 8 , disctype = 3
                        case 3:
                            $dataPass['simple_action'] = 'by_fixed';
                            $arr = 1;
                            // save to sales rule
                            foreach ($dataPass['sliding_disc_type_info'] as $row) {
                                //sliding discount type info child
                                $sliding_child_arr = explode("-", $row);
                                $dataPass['sliding_child'] = $sliding_child_arr;
                                $dataPass['sliding_child_sequence'] = $arr;
                                $dataPass['discount_amount'] = $dataPass['sliding_child'][2];

                                // add dataPass array for name and desc
                                $dataPass['rule_name'] = 'Buy More, Save More';
                                $dataPass['name'] = $dataPass['promotion_id'].':'.$dataPass['promotion_type'].' - Tier Price Rp '.$sliding_child_arr[2].' Off ('.$dataPass['sku'].')';
                                $dataPass['desc'] = $dataPass['promotion_id'].':'.$dataPass['promotion_type'].' - Tier Price Rp '.$sliding_child_arr[2].' Off ('.$dataPass['sku'].') - ('.$dataPass['from_date'].' - '.$dataPass['to_date'].')';

                                $this->saveSalesRule($dataPass);
                                $arr++;
                            }
                            break;
                    }
                    // save to integration prom price table
                    $saveDataIntPromo = $this->saveIntegrationPromo($dataPass);
                }
            }
            // --------------
        } catch (\Exception $e) {
            $this->logger->info("error saved promo type 8 --->".print_r($dataPass['sku'], true));
            throw new StateException(
                __($e->getMessage())
            );
        }
    }

    /**
     * function for promo type 5
     * @param $dataPass mixed
     * @throw new StateException
     */
    protected function promoType5Function($dataPass)
    {
        try {
            // if promo id not exist
            $checkIntPromoId = $this->checkIntegrationPromoByPromoId($dataPass['promotion_id']);

            // if (!$checkIntPromoId) {
                $dataPass['discount_step'] = 3;
                $dataPass['simple_free_shipping'] = 0;
                $dataPass['item_type'] = $dataPass['item_type'];
                $dataPass['mix_and_match_code'] = $dataPass['mix_and_match_code'];
                $dataPass['point_per_unit'] = ($dataPass['point_per_unit'] == 0) ? 1 : $dataPass['point_per_unit'] ;

                $dataPass['name'] = $dataPass['promotion_id'].':'.$dataPass['promotion_type'].' - Buy X qty of A, Get Y qty of B';
                $dataPass['desc'] = $dataPass['promotion_id'].':'.$dataPass['promotion_type'].' - Buy X qty of A, Get Y qty of B - ' .$dataPass['store_code'].' - ('.$dataPass['from_date'].' - '.$dataPass['to_date'].')';

                if ($dataPass['item_type'] == 2) {
                    // Nested switch discount type
                    switch ($dataPass['discount_type']) {
                        // promotype = 5 , disctype = 1
                        case 1:
                            $dataPass['rule_name'] = 'Buy Y, Get Rp '.$dataPass['promo_selling_price'].' Off';
                            $dataPass['name'] = $dataPass['promotion_id'].':'.$dataPass['promotion_type'].' - Buy X qty of A, Get Y qty of B with only IDR '.$dataPass['promo_selling_price'].'';
                            $dataPass['desc'] = $dataPass['promotion_id'].':'.$dataPass['promotion_type'].' - Buy X qty of A, Get Y qty of B with only IDR '.$dataPass['promo_selling_price'].' - ' .$dataPass['store_code'].' - ('.$dataPass['from_date'].' - '.$dataPass['to_date'].')';
                            $dataPass['simple_action'] = 'buyxgetn_fixprice';
                            $dataPass['discount_amount'] = $dataPass['promo_selling_price'];
                            
                            // check on table integration promo and item type 2
                            $checkDataIntPromo = $this->checkIntegrationPromoTwo($dataPass);
                            // if check 0 then save
                            if (!$checkDataIntPromo) {
                                // save to salesrule
                                $result = $this->saveSalesRule($dataPass);
                                // get salesruleid
                                $dataPass['salesrule_id'] = $result['rule_id'];
                                $dataPass['row_id'] = $result['row_id'];
                                // save to integration promo
                                $saveDataIntPromo = $this->saveIntegrationPromo($dataPass);
                            } else {
                                // get salesruleid
                                $dataPass['salesrule_id'] = $checkDataIntPromo->getData('salesrule_id');
                                $dataPass['row_id'] = $checkDataIntPromo->getData('row_id');

                                // update condition serialize , save to salesrule
                                $this->updateSalesRule($dataPass);

                                // save to integration promo
                                $saveDataIntPromo = $this->saveIntegrationPromo($dataPass);
                                
                            }

                            // for update item type 1 only
                            $dataPass['item_type_one'] = 1;
                            $checkDataIntPromoOne = $this->checkIntegrationPromoOne($dataPass);

                            if ($checkDataIntPromoOne) {
                                foreach ($checkDataIntPromoOne as $checkDataIntPromoOneValue) {
                                    // update condition serialize , save to salesrule
                                    $dataPass['point_per_unit'] = $checkDataIntPromoOneValue->getData('point_per_unit');
                                    $dataPass['sku'] = $checkDataIntPromoOneValue->getData('sku');
                                    $dataPass['item_type'] = 1;
                                    $this->updateSalesRule($dataPass);

                                    $checkDataIntPromoOneValue->setSaleruleId($dataPass['salesrule_id']);
                                    $checkDataIntPromoOneValue->setRowId($dataPass['row_id']);
                                    $result = $this->promotionPriceRepositoryInterface->save($checkDataIntPromoOneValue);
                                }
                            }
                            break;

                        // promotype = 5 , disctype = 2
                        case 2:

                            $dataPass['rule_name'] = 'Buy Y, Get '.$dataPass['percent_disc'].'% Off';
                            $dataPass['name'] = $dataPass['promotion_id'].':'.$dataPass['promotion_type'].' - Buy X qty of A, Get Y qty of B with '.$dataPass['percent_disc'].'% off';
                            $dataPass['desc'] = $dataPass['promotion_id'].':'.$dataPass['promotion_type'].' - Buy X qty of A, Get Y qty of B with '.$dataPass['percent_disc'].'% off - '.$dataPass['store_code'].' - ('.$dataPass['from_date'].' - '.$dataPass['to_date'].')';
                            $dataPass['simple_action'] = 'buyxgetn_perc';
                            $dataPass['discount_amount'] = $dataPass['percent_disc'];
                            
                            // check on table integration promo and item type 2
                            $checkDataIntPromo = $this->checkIntegrationPromoTwo($dataPass);
                            // if check 0 then save
                            if (!$checkDataIntPromo) {
                                // save to salesrule
                                $result = $this->saveSalesRule($dataPass);
                                // get salesruleid
                                $dataPass['salesrule_id'] = $result['rule_id'];
                                $dataPass['row_id'] = $result['row_id'];
                                // save to integration promo
                                $saveDataIntPromo = $this->saveIntegrationPromo($dataPass);
                            } else {
                                // get salesruleid
                                $dataPass['salesrule_id'] = $checkDataIntPromo->getData('salesrule_id');
                                $dataPass['row_id'] = $checkDataIntPromo->getData('row_id');

                                // update condition serialize , save to salesrule
                                $this->updateSalesRule($dataPass);

                                // save to integration promo
                                $saveDataIntPromo = $this->saveIntegrationPromo($dataPass);
                                
                            }

                            // for update item type 1 only
                            $dataPass['item_type_one'] = 1;
                            $checkDataIntPromoOne = $this->checkIntegrationPromoOne($dataPass);

                            if ($checkDataIntPromoOne) {
                                foreach ($checkDataIntPromoOne as $checkDataIntPromoOneValue) {
                                    // update condition serialize , save to salesrule
                                    $dataPass['point_per_unit'] = $checkDataIntPromoOneValue->getData('point_per_unit');
                                    $dataPass['sku'] = $checkDataIntPromoOneValue->getData('sku');
                                    $dataPass['item_type'] = 1;
                                    $this->updateSalesRule($dataPass);

                                    $checkDataIntPromoOneValue->setSaleruleId($dataPass['salesrule_id']);
                                    $checkDataIntPromoOneValue->setRowId($dataPass['row_id']);
                                    $result = $this->promotionPriceRepositoryInterface->save($checkDataIntPromoOneValue);
                                }
                            }
                            break;

                        // promotype = 5 , disctype = 3
                        case 3:
                            $dataPass['rule_name'] = 'Buy Y, Get Rp '.$dataPass['amount_off'].' Off';
                            $dataPass['name'] = $dataPass['promotion_id'].':'.$dataPass['promotion_type'].' - Buy X qty of A, Get Y qty of B with '.$dataPass['amount_off'].' amount off';
                            $dataPass['desc'] = $dataPass['promotion_id'].':'.$dataPass['promotion_type'].' - Buy X qty of A, Get Y qty of B with '.$dataPass['amount_off'].' amount off - '.$dataPass['store_code'].' - ('.$dataPass['from_date'].' - '.$dataPass['to_date'].')';
                            $dataPass['simple_action'] = 'buyxgetn_fixdisc';
                            $dataPass['discount_amount'] = $dataPass['amount_off'];
                           
                            // check on table integration promo and item type 2
                            $checkDataIntPromo = $this->checkIntegrationPromoTwo($dataPass);
                            // if check 0 then save
                            if (!$checkDataIntPromo) {
                                // save to salesrule
                                $result = $this->saveSalesRule($dataPass);
                                // get salesruleid
                                $dataPass['salesrule_id'] = $result['rule_id'];
                                $dataPass['row_id'] = $result['row_id'];
                                // save to integration promo
                                $saveDataIntPromo = $this->saveIntegrationPromo($dataPass);
                            } else {
                                // get salesruleid
                                $dataPass['salesrule_id'] = $checkDataIntPromo->getData('salesrule_id');
                                $dataPass['row_id'] = $checkDataIntPromo->getData('row_id');

                                // update condition serialize , save to salesrule
                                $this->updateSalesRule($dataPass);

                                // save to integration promo
                                $saveDataIntPromo = $this->saveIntegrationPromo($dataPass);
                                
                            }

                            // for update item type 1 only
                            $dataPass['item_type_one'] = 1;
                            $checkDataIntPromoOne = $this->checkIntegrationPromoOne($dataPass);

                            if ($checkDataIntPromoOne) {
                                foreach ($checkDataIntPromoOne as $checkDataIntPromoOneValue) {
                                    // update condition serialize , save to salesrule
                                    $dataPass['point_per_unit'] = $checkDataIntPromoOneValue->getData('point_per_unit');
                                    $dataPass['sku'] = $checkDataIntPromoOneValue->getData('sku');
                                    $dataPass['item_type'] = 1;
                                    $this->updateSalesRule($dataPass);

                                    $checkDataIntPromoOneValue->setSaleruleId($dataPass['salesrule_id']);
                                    $checkDataIntPromoOneValue->setRowId($dataPass['row_id']);
                                    $result = $this->promotionPriceRepositoryInterface->save($checkDataIntPromoOneValue);
                                }
                            }
                            break;
                    }
                }
                else {
                    // for update item type 1 only
                    // check on table integration promo and item type 2
                    $dataPass['item_type'] = 2;
                    $checkDataIntPromo = $this->checkIntegrationPromoTwo($dataPass);
                    // if check 0 then save
                    if (!$checkDataIntPromo) {
                        $dataPass['item_type'] = 1;
                        $saveDataIntPromo = $this->saveIntegrationPromo($dataPass);
                    }
                    else {
                        $dataPass['item_type'] = 1;
                        $dataPass['salesrule_id'] = $checkDataIntPromo->getData('salesrule_id');
                        $dataPass['row_id'] = $checkDataIntPromo->getData('row_id');

                        // update condition serialize , save to salesrule
                        $this->updateSalesRule($dataPass);

                        // save to integration promo
                        $saveDataIntPromo = $this->saveIntegrationPromo($dataPass);
                    }
                }
            // }
            // --------------
        } catch (\Exception $e) {
            $this->logger->info("error saved promo type 5 --->".print_r($dataPass['sku'], true));
            throw new StateException(
                __($e->getMessage())
            );
            
        }
    }

    /**
     * Save to integration catalog promotion price
     * @param $data mixed
     * @return $result mixed
     * @throw logger error
     */
    protected function saveIntegrationPromo($data)
    {
        try {
            $query = $this->promotionPriceInterfaceFactory->create();

            $query->setSku($data['sku']);
            $query->setStoreCode($data['store_code']);
            $query->setCompanyCode($data['company_code']);
            $query->setPimPromotionId($data['promotion_id']);
            $query->setName($data['name']);
            $query->setPromotionType($data['promotion_type']);
            $query->setDiscountType($data['discount_type']);

            if (!empty($data['item_type'])) {
                $query->setItemType($data['item_type']);
            }

            if (!empty($data['mix_and_match_code'])) {
                $query->setMixMatchCode($data['mix_and_match_code']);
            }

            $query->setSlidingDiscType($data['sliding_disc_type']);

            if (!empty($data['salesrule_id'])) {
                $query->setSaleruleId($data['salesrule_id']);
            }

            if (!empty($data['row_id'])) {
                $query->setRowId($data['row_id']);
            }

            if (!empty($data['required_point'])) {
                $query->setRequiredPoint($data['required_point']);
            }

            if (!empty($data['promo_selling_price'])) {
                $query->setPromoSellingPrice($data['promo_selling_price']);
            }

            if (!empty($data['percent_disc'])) {
                $query->setPercentDisc($data['percent_disc']);
            }

            if (!empty($data['amount_off'])) {
                $query->setAmountOff($data['amount_off']);
            }

            if (!empty($data['point_per_unit'])) {
                $query->setPointPerUnit($data['point_per_unit']);
            }

            if (!empty($data['promo_price_qty'])) {
                $query->setPromoPriceQty($data['promo_price_qty']);
            }

            if (!empty($data['normal_price_qty'])) {
                $query->setNormalPriceQty($data['normal_price_qty']);
            }

            if (!empty($data['rule_name'])) {
                $query->setRuleName($data['rule_name']);
            }
            
            if ($data['promotion_type'] == 2) {
                if (!empty($data['from_date'])) {
                    $query->setStartDate($data['from_date']);
                }

                if (!empty($data['to_date'])) {
                    $query->setEndDate($data['to_date']);
                }
            }

            if ($data['promotion_type'] == 1) {
                if (!empty($data['from_date'])) {
                    $query->setStartDate($data['from_date']);
                }

                if (!empty($data['to_date'])) {
                    $query->setEndDate($data['to_date']);
                }
            }
            

            $result = $this->promotionPriceRepositoryInterface->save($query);

            $this->logger->info("saveIntegrationPromo saved [" . $data['name'] ."]");
        } catch (\Exception $e) {
            $this->logger->info("<=End saveIntegrationPromo" .$e->getMessage());
            throw new StateException(
                __(__FUNCTION__." - ".$e->getMessage())
            );
        }

        return $result;
    }

    /**
     * check to integration catalog promotion price by promo id
     * @param $data mixed
     * @return $query mixed
     * @throw logger error
     */
    protected function checkIntegrationPromoByPromoId($data)
    {
        try {
            $query = $this->promotionPriceRepositoryInterface->loadDataPromoByPromoId($data);
        } catch (Exception $e) {
            $this->logger->info("<=End checkIntegrationPromo" .$e->getMessage());
            throw new StateException(
                __(__FUNCTION__." - ".$e->getMessage())
            );
        }

        return $query;
    }

    /**
     * check to integration catalog promotion price by sku , promo type , discount type and store code
     * @param $data mixed
     * @return $query mixed
     * @throw logger error
     */
    protected function checkIntegrationPromoByPromoIdStoreCode($data)
    {
        try {
            $query = $this->promotionPriceRepositoryInterface->loadDataPromoByPromoIdStoreCode($data);
        } catch (Exception $e) {
            $this->logger->info("<=End checkIntegrationPromo" .$e->getMessage());
            throw new StateException(
                __(__FUNCTION__." - ".$e->getMessage())
            );
        }

        return $query;
    }

    /**
     * check to integration catalog promotion price
     * @param $data mixed
     * @return $query mixed
     * @throw logger error
     */
    protected function checkIntegrationPromo($data)
    {
        try {
            $query = $this->promotionPriceRepositoryInterface->loadDataPromoFive($data);
        } catch (\Exception $e) {
            $this->logger->info("<=End checkIntegrationPromo" .$e->getMessage());
            throw new StateException(
                __(__FUNCTION__." - ".$e->getMessage())
            );
        }

        return $query;
    }

    /**
     * check to integration catalog promotion price
     * @param $data mixed
     * @return $query mixed
     * @throw logger error
     */
    protected function checkIntegrationPromoTwo($data)
    {
        try {
            $query = $this->promotionPriceRepositoryInterface->loadDataPromoFiveItemTwo($data);
        } catch (\Exception $e) {
            $this->logger->info("<=End checkIntegrationPromo" .$e->getMessage());
            throw new StateException(
                __(__FUNCTION__." - ".$e->getMessage())
            );
        }

        return $query;
    }

    /**
     * check to integration catalog promotion price
     * @param $data mixed
     * @return $query mixed
     * @throw logger error
     */
    protected function checkIntegrationPromoOne($data)
    {
        try {
            $query = $this->promotionPriceRepositoryInterface->loadDataPromoFiveItemOne($data);
        } catch (\Exception $e) {
            $this->logger->info("<=End checkIntegrationPromo" .$e->getMessage());
            throw new StateException(
                __(__FUNCTION__." - ".$e->getMessage())
            );
        }

        return $query;
    }

    /**
     * check to integration catalog promotion price + sku
     * @param $data mixed
     * @return $query mixed
     * @throw logger error
     */
    protected function checkIntegrationPromoSku($data)
    {
        try {
            $query = $this->promotionPriceRepositoryInterface->loadDataPromoFiveCheck($data);
        } catch (\Exception $e) {
            $this->logger->info("<=End checkIntegrationPromoSku" .$e->getMessage());
            throw new StateException(
                __(__FUNCTION__." - ".$e->getMessage())
            );
        }

        return $query;
    }

    /**
     * check to integration catalog promotion price + sku + promo type + discount type
     * @param $data mixed
     * @return $query mixed
     * @throw logger error
     */
    protected function checkIntegrationPromoSkuAndPromoType($data)
    {
        try {
            $query = $this->promotionPriceRepositoryInterface->loadDataPromoBySkuPromoType($data);
        } catch (\Exception $e) {
            $this->logger->info("<=End checkIntegrationPromoSku" .$e->getMessage());
            throw new StateException(
                __(__FUNCTION__." - ".$e->getMessage())
            );
        }

        return $query;
    }

    /**
     * Save to promo price by store
     * @param $startTime datetime
     * @param $endTime datetime
     * @param $name string
     * @param $desc string
     * @param $isCampaign int
     * @param $isRollback int
     * @param $dataStoreCode mixed
     * @param $attributeIdarray array
     * @return $data mixed
     * @throw logger error
     */
    protected function savePromoPriceStore($startTime, $endTime, $name, $desc, $isCampaign, $isRollback, $dataStoreCode, $attributeIdarray)
    {
        try {
            $dataStoreCode['store_attr_code'] = $dataStoreCode['store_code'];
            // save special price
            $promoPriceRepo = $this->saveBasePriceStorePrice($dataStoreCode, $attributeIdarray);

            $this->logger->info("promo price saved [" . $name ."]");
        } catch (\Exception $e) {
            $this->logger->info("<=End promo price" .$e->getMessage());
            throw new StateException(
                __(__FUNCTION__." - ".$e->getMessage())
            );
        }

        return $promoPriceRepo;
    }

    /**
     * save base price
     * @param mixed $param
     * @return array $result
     */
    protected function saveBasePriceStorePrice($param, $attributeIdarray)
    {
        try {
            // get base price
            $basePriceCode = PromotionPriceLogicInterface::PRODUCT_ATTR_BASE_PRICE.$param['store_attr_code'];

            $product = $this->productRepositoryInterface->get($param['sku']);
            $productPriceBySku = $product->getCustomAttribute($basePriceCode) ? $product->getCustomAttribute($basePriceCode)->getValue() : 0;
            
            $dataBase = [
                'base_price' => $productPriceBySku
            ];

            // calc special price
            $specialPrice = '';
            if ($param['disc_type'] == 1) {
                $specialPrice = $param['promo_selling_price'];
            }
            if ($param['disc_type'] == 2) {
                $specialPrice = $dataBase['base_price'] - ($dataBase['base_price'] * ($param['percent_disc']/100));
                if ($specialPrice < 0) {
                    $specialPrice = 0;
                }
            }
            if ($param['disc_type'] == 3) {
                $specialPrice = $dataBase['base_price'] - $param['amount_off'];
                if ($specialPrice < 0) {
                    $specialPrice = 0;
                }
            }
            
            // save to special price
            $specialPriceCode = PromotionPriceLogicInterface::PRODUCT_ATTR_PROMO_PRICE.$param['store_attr_code'];
            
            // $product->setCustomAttribute($specialPriceCode, $specialPrice);

            // $this->productRepositoryInterface->save($product);

            if ($attributeIdarray[$specialPriceCode]['attribute_id']) {
                $dataMarkdown =
                    [
                        "attribute_id"=>"".$attributeIdarray[$specialPriceCode]['attribute_id'][0]."",
                        "value"=>"".$specialPrice."",
                        "row_id"=>"".$product->getRowId().""
                    ];

            }
            else {
                return false;
            }

            return $dataMarkdown;
        } catch (\Exception $exception) {
            throw new StateException(
                __(__FUNCTION__." - ".$exception->getMessage())
            );
        }
    }

    /**
     * delete promo price by store
     * @param $dataStoreCode mixed
     * @param $attributeIdarray array
     * @return $data mixed
     * @throw logger error
     */
    protected function savePromoPriceStoreEndDate($dataStoreCode, $attributeIdarray)
    {
        try {
            // save to special price
            $product = $this->productRepositoryInterface->get($dataStoreCode['sku']);
            $specialPriceCode = PromotionPriceLogicInterface::PRODUCT_ATTR_PROMO_PRICE.$dataStoreCode['store_code'];
            
            if ($attributeIdarray[$specialPriceCode]['attribute_id']) {
                $dataMarkdown =
                    [
                        "attribute_id"=>"".$attributeIdarray[$specialPriceCode]['attribute_id'][0]."",
                        "value"=> 0,
                        "row_id"=>"".$product->getRowId().""
                    ];

            }
            else {
                return false;
            }

            return $dataMarkdown;
        } catch (\Exception $exception) {
            throw new StateException(
                __(__FUNCTION__." - ".$exception->getMessage())
            );
        }
    }

    /**
     * Save to sales rule
     * @param $dataPass mixed
     * @return $resultData mixed
     * @throw logger error
     */
    protected function saveSalesRule($dataPass)
    {
        try {
            $statusResponse = $this->ruleFactory->create();

            $statusResponse->setName($dataPass['rule_name'])
            ->setDescription($dataPass['desc'])
            ->setFromDate($dataPass['from_date'])
            ->setToDate($dataPass['to_date'])
            ->setDayOfWeek($dataPass['day_of_week'])
            ->setUsesPerCustomer($dataPass['uses_per_customer'])
            ->setCustomerGroupIds($dataPass['customer_group_id'])
            ->setIsActive($dataPass['is_active'])
            ->setStopRulesProcessing($dataPass['stop_rules_processing'])
            ->setIsAdvanced($dataPass['is_advanced'])
            ->setSortOrder($dataPass['sort_order'])
            ->setSimpleAction($dataPass['simple_action'])
            ->setDiscountQty($dataPass['discount_qty'])
            ->setDiscountAmount($dataPass['discount_amount'])
            ->setApplyToShipping($dataPass['apply_to_shipping'])
            ->setTimesUsed($dataPass['times_used'])
            ->setIsRss($dataPass['is_rss'])
            ->setWebsiteIds(array($dataPass['website_ids']))
            ->setCouponType($dataPass['coupon_type'])
            ->setUseAutoGeneration($dataPass['use_auto_generation'])
            ->setUsesPerCoupon($dataPass['uses_per_coupon'])
            ->setSimpleFreeShipping($dataPass['simple_free_shipping'])
            ->setCreatedIn($dataPass['created_in'])
            ->setConditions($this->foundProductRuleFactory->create()->setType('Magento\SalesRule\Model\Rule\Condition\Combine')->setValue('1')->setAggregator('all'))
            ->setActions($this->foundProductRuleFactory->create()->setType('Magento\SalesRule\Model\Rule\Condition\Combine')->setValue('1')->setAggregator('all'));

            if ($dataPass['promotion_type'] == 5) {
                if ($dataPass['item_type'] == 2) {
                    $statusResponse->setDiscountStep($dataPass['required_point']);
                }
            }
            else {
                $statusResponse->setDiscountStep($dataPass['discount_step']);
            }
            $result = $this->ruleResource->save($statusResponse);

            // check if salesrule success save
            if ($result) {
                // save condition serialize
                
                $this->saveSalesRuleConditions($dataPass, $statusResponse);

                // --------------

                //log save
                $this->logger->info("Salesrule saved [" . $dataPass['name'] ."]");
                
                $getSalesruleId = $statusResponse->getRowId();
                $skuAmasty = $dataPass['sku'];
                $promoTypeAmasty = $dataPass['promotion_type'];
                $itemTypeAmasty = $dataPass['item_type'];
                // save to amasty_amrule
                $this->saveAmastySalesRule($getSalesruleId, $skuAmasty, $promoTypeAmasty, $itemTypeAmasty);

                // return data
                $resultData = [
                    'rule_id' => $statusResponse->getData('rule_id'),
                    'row_id' => $statusResponse->getData('row_id')
                ];
            }
        } catch (\Exception $e) {
            $this->logger->info("<=End salesrule" .$e->getMessage());
            throw new StateException(
                __(__FUNCTION__." - ".$e->getMessage())
            );
        }

        return $resultData;
    }

    /**
     * Save to sales rule conditions
     * @param $dataPass mixed
     * @param $statusResponse mixed
     */
    protected function saveSalesRuleConditions($dataPass, $statusResponse)
    {
        // promotion type 8
        if ($dataPass['promotion_type'] == 8) {
            $item_found = $this->foundProductRuleFactory->create()
                ->setType('Magento\SalesRule\Model\Rule\Condition\Product\Found')
                ->setValue('1')
                ->setAggregator('all');
            $statusResponse->getConditions()->addCondition($item_found);

            $qtyCond = $this->foundProductRuleFactory->create()
                ->setType('Magento\SalesRule\Model\Rule\Condition\Product')
                ->setAttribute('quote_item_qty')
                ->setOperator('>=')
                ->setValue($dataPass['sliding_child'][0]);
            $item_found->addCondition($qtyCond);

            $conditions = $this->foundProductRuleFactory->create()
                ->setType('Magento\SalesRule\Model\Rule\Condition\Product')
                ->setAttribute('sku')
                ->setOperator('==')
                ->setValue($dataPass['sku']);
            $item_found->addCondition($conditions);

            // conditions serialize , last condition , only for last level
            if ($dataPass['sliding_child_sequence'] != $dataPass['sliding_disc_type_count']) {
                $actions = $this->foundProductRuleFactory->create()
                    ->setType('Magento\SalesRule\Model\Rule\Condition\Product')
                    ->setAttribute('quote_item_qty')
                    ->setOperator('<=')
                    ->setValue($dataPass['sliding_child'][1]);
                $item_found->addCondition($actions);
            }

            $item_amasty = $this->foundProductRuleFactory->create()
                ->setType('Amasty\Conditions\Model\Rule\Condition\CustomerAttributes')
                ->setAttribute('omni_store_id')
                ->setOperator('()')
                ->setValue($dataPass['store_code']);
            $statusResponse->getConditions()->addCondition($item_amasty);

            //actions serialize
            $actionqtyCond = $this->foundProductRuleFactory->create()
                ->setType('Magento\SalesRule\Model\Rule\Condition\Product')
                ->setAttribute('sku')
                ->setOperator('==')
                ->setValue($dataPass['sku']);
            $statusResponse->getActions()->addCondition($actionqtyCond);

            $this->ruleResource->save($statusResponse);
        }

        // promotion type 2
        if ($dataPass['promotion_type'] == 2) {
            $item_found = $this->foundProductRuleFactory->create()
                ->setType('Magento\SalesRule\Model\Rule\Condition\Product\Found')
                ->setValue('1')
                ->setAggregator('all');
            $statusResponse->getConditions()->addCondition($item_found);

            $qtyCond = $this->foundProductRuleFactory->create()
                ->setType('Magento\SalesRule\Model\Rule\Condition\Product')
                ->setAttribute('sku')
                ->setOperator('==')
                ->setValue($dataPass['sku']);
            $item_found->addCondition($qtyCond);

            $conditions = $this->foundProductRuleFactory->create()
                ->setType('Magento\SalesRule\Model\Rule\Condition\Product')
                ->setAttribute('quote_item_qty')
                ->setOperator('>')
                ->setValue($dataPass['promo_price_qty']);
            $item_found->addCondition($conditions);
            
            $item_amasty = $this->foundProductRuleFactory->create()
                ->setType('Amasty\Conditions\Model\Rule\Condition\CustomerAttributes')
                ->setAttribute('omni_store_id')
                ->setOperator('()')
                ->setValue($dataPass['store_code']);
            $statusResponse->getConditions()->addCondition($item_amasty);

            //actions serialize
            $actionqtyCond = $this->foundProductRuleFactory->create()
                ->setType('Magento\SalesRule\Model\Rule\Condition\Product')
                ->setAttribute('sku')
                ->setOperator('==')
                ->setValue($dataPass['sku']);
            $statusResponse->getActions()->addCondition($actionqtyCond);

            $this->ruleResource->save($statusResponse);
        }

        // promotion type 5
        if ($dataPass['promotion_type'] == 5) {
            if ($dataPass['item_type'] == 1) {
                // $item_found = $this->foundProductRuleFactory->create()
                //     ->setType('Magento\SalesRule\Model\Rule\Condition\Product\Subselect')
                //     ->setAttribute('qty')
                //     ->setOperator('>=')
                //     ->setValue($dataPass['point_per_unit'])
                //     ->setAggregator('all');
                // $statusResponse->getConditions()->addCondition($item_found);

                // $conditions = $this->foundProductRuleFactory->create()
                //     ->setType('Magento\SalesRule\Model\Rule\Condition\Product')
                //     ->setAttribute('sku')
                //     ->setOperator('==')
                //     ->setValue($dataPass['sku']);
                // $item_found->addCondition($conditions);
            }

            $item_amasty = $this->foundProductRuleFactory->create()
                ->setType('Amasty\Conditions\Model\Rule\Condition\CustomerAttributes')
                ->setAttribute('omni_store_id')
                ->setOperator('()')
                ->setValue($dataPass['store_code']);
            $statusResponse->getConditions()->addCondition($item_amasty);

            // if ($dataPass['item_type'] == 1) {
            //     //actions serialize
            //     $actionqtyCond = $this->foundProductRuleFactory->create()
            //         ->setType('Magento\SalesRule\Model\Rule\Condition\Product')
            //         ->setAttribute('sku')
            //         ->setOperator('==')
            //         ->setValue($dataPass['sku']);
            //     $statusResponse->getActions()->addCondition($actionqtyCond);
            // }

            $this->ruleResource->save($statusResponse);
        }

        // promotion type 4
        if ($dataPass['promotion_type'] == 4) {
            $item_found = $this->foundProductRuleFactory->create()
                ->setType('Magento\SalesRule\Model\Rule\Condition\Product\Found')
                ->setValue("1")
                ->setAggregator('all');
            $statusResponse->getConditions()->addCondition($item_found);

            $conditions = $this->foundProductRuleFactory->create()
                ->setType('Magento\SalesRule\Model\Rule\Condition\Product')
                ->setAttribute('sku')
                ->setOperator('==')
                ->setValue($dataPass['sku']);
            $item_found->addCondition($conditions);

            $item_amasty = $this->foundProductRuleFactory->create()
                ->setType('Amasty\Conditions\Model\Rule\Condition\CustomerAttributes')
                ->setAttribute('omni_store_id')
                ->setOperator('()')
                ->setValue($dataPass['store_code']);
            $statusResponse->getConditions()->addCondition($item_amasty);
           

            //actions serialize
            $actionqtyCond = $this->foundProductRuleFactory->create()
                ->setType('Magento\SalesRule\Model\Rule\Condition\Product')
                ->setAttribute('sku')
                ->setOperator('==')
                ->setValue($dataPass['sku']);
            $statusResponse->getActions()->addCondition($actionqtyCond);

            $this->ruleResource->save($statusResponse);
        }

        // promotion type 7
        if ($dataPass['promotion_type'] == 7) {
            $item_found = $this->foundProductRuleFactory->create()
                ->setType('Magento\SalesRule\Model\Rule\Condition\Product\Subselect')
                ->setAttribute('base_row_total')
                ->setOperator('>=')
                ->setValue(0)
                ->setAggregator('all');
            $statusResponse->getConditions()->addCondition($item_found);

            $conditions = $this->foundProductRuleFactory->create()
                ->setType('Magento\SalesRule\Model\Rule\Condition\Product')
                ->setAttribute('sku')
                ->setOperator('!=')
                ->setValue($dataPass['sku']);
            $item_found->addCondition($conditions);

            $item_amasty = $this->foundProductRuleFactory->create()
                ->setType('Amasty\Conditions\Model\Rule\Condition\CustomerAttributes')
                ->setAttribute('omni_store_id')
                ->setOperator('()')
                ->setValue($dataPass['store_code']);
            $statusResponse->getConditions()->addCondition($item_amasty);

            $this->ruleResource->save($statusResponse);
        }
    }

    /**
     * update to salesrule
     * @param $dataPass mixed
     * @return $result mixed
     * @throw logger error
     */
    protected function updateSalesRule($dataPass)
    {
        try {
            $statusResponse = $this->ruleFactory->create();
            $statusResponse->load($dataPass['salesrule_id'], 'rule_id');

            // update condition serialize
            // promotion type 5
            $result = [];
            if ($dataPass['promotion_type'] == 5) {
                if ($dataPass['item_type'] == 1) {
                    // $item_found = $this->foundProductRuleFactory->create()
                    //     ->setType('Magento\SalesRule\Model\Rule\Condition\Product\Subselect')
                    //     ->setAttribute('qty')
                    //     ->setOperator('>=')
                    //     ->setValue($dataPass['point_per_unit'])
                    //     ->setAggregator('all');
                    // $statusResponse->getConditions()->addCondition($item_found);

                    // $conditions = $this->foundProductRuleFactory->create()
                    //     ->setType('Magento\SalesRule\Model\Rule\Condition\Product')
                    //     ->setAttribute('sku')
                    //     ->setOperator('==')
                    //     ->setValue($dataPass['sku']);
                    // $item_found->addCondition($conditions);
                    // $result = $this->ruleResource->save($statusResponse);

                    // get sku already exist
                    $ruleData = $statusResponse->getActionsSerialized();
                    $ruleDataArray = json_decode($ruleData, true);
                    $typeSubSelect = null;

                    if ($ruleData) {
                        
                        if (isset($ruleDataArray['conditions'])) {
                            $conditions = $ruleDataArray['conditions'];
                            foreach ($conditions as $key => $condition) {
                                if ($condition['type'] == 'Magento\SalesRule\Model\Rule\Condition\Product') {
                                    $skuValueNow = $ruleDataArray['conditions'][$key]['value'];
                                    $ruleDataArray['conditions'][$key]['value'] = $skuValueNow.','.$dataPass['sku'];
                                }
                            }

                            $setRuleData = $statusResponse->setActionsSerialized(json_encode($ruleDataArray));
                            $this->ruleResource->save($statusResponse);
                        }
                        else {
                            $statusResponseOk = $this->ruleFactory->create();
                            $statusResponseOk->load($dataPass['salesrule_id'], 'rule_id');

                            $actionqtyCond = $this->foundProductRuleFactory->create()
                                ->setType('Magento\SalesRule\Model\Rule\Condition\Product')
                                ->setAttribute('sku')
                                ->setOperator('()')
                                ->setValue($dataPass['sku']);
                            $statusResponseOk->getActions()->addCondition($actionqtyCond);
                            $this->ruleResource->save($statusResponseOk);
                        }
                    }
                    

                    $updateAmrule = $this->updateAmastyAmrule($dataPass);
                    
                } else {
                    // $actionqtyCond = $this->foundProductRuleFactory->create()
                    //     ->setType('Magento\SalesRule\Model\Rule\Condition\Product')
                    //     ->setAttribute('sku')
                    //     ->setOperator('==')
                    //     ->setValue($dataPass['sku']);
                    // $statusResponse->getActions()->addCondition($actionqtyCond);
                    // $result = $this->ruleResource->save($statusResponse);
                    $statusResponse->setDiscountStep($dataPass['required_point']);
                    $this->ruleResource->save($statusResponse);

                    $updateAmrule = $this->updateAmastyAmrule($dataPass);
                }

                //log save
                $this->logger->info("update Salesrule saved [" . $dataPass['name'] ."]");
            }

            // promotion type 7
            if ($dataPass['promotion_type'] == 7) {
                if ($dataPass['item_type'] == 1) {

                    // get sku already exist
                    $ruleData = $statusResponse->getConditionsSerialized();
                    $ruleDataArray = json_decode($ruleData, true);

                    if ($ruleData) {
                        if (isset($ruleDataArray['conditions'])) {
                            $conditions = $ruleDataArray['conditions'];
                            foreach ($conditions as $key => $condition) {
                                if (isset($condition['conditions'])) {
                                    foreach ($condition['conditions'] as $conditi) {
                                        $skusValue[] = [
                                            'type' => 'Magento\\SalesRule\\Model\\Rule\\Condition\\Product',
                                            'attribute' => 'sku',
                                            'operator' => '!=',
                                            'value' => $conditi['value'],
                                            'aggregator' => 'all',
                                        ];
                                    }
                                }
                                if (isset($condition['type'])) {
                                    $typeSubSelect = $condition['type'];
                                }
                            }
                        }
                    }

                    // add new sku
                    $skusValue[] = [
                        'type' => 'Magento\\SalesRule\\Model\\Rule\\Condition\\Product',
                        'attribute' => 'sku',
                        'operator' => '!=',
                        'value' => $dataPass['sku'],
                        'aggregator' => 'all',
                    ];

                    if ($typeSubSelect) {
                        // update conditions serialize
                        if (isset($ruleDataArray['conditions'])) {
                            $conditions = $ruleDataArray['conditions'];
                            foreach ($conditions as $key => $condition) {    
                                $ruleDataArray['conditions'][$key]['conditions'] = $skusValue;
                            }
                        }

                        $setRuleData = $statusResponse->setConditionsSerialized(json_encode($ruleDataArray));
                    }
                    else {
                        // update conditions serialize
                        $getConditionSerialized = [
                            'type' => 'Magento\\Reminder\\Model\\Rule\\Condition\\Combine',
                            'attribute' => '',
                            'operator' => '',
                            'value' => '1',
                            'aggregator' => 'all',
                            'conditions' => [
                                [
                                    'type' => 'Magento\\SalesRule\\Model\\Rule\\Condition\\Product\\Subselect',
                                    'attribute' => 'base_row_total',
                                    'operator' => '>=',
                                    'value' => $dataPass['required_point'],
                                    'aggregator' => 'all',
                                    'conditions' => $skusValue,
                                ],
                            ],
                        ];

                        $setRuleData = $statusResponse->setConditionsSerialized(json_encode($getConditionSerialized));
                    }
                    
                    $this->ruleResource->save($statusResponse);
                }
            }

            if ($dataPass['promotion_type'] == 2) {
                $ruleData = $statusResponse->getConditionsSerialized();
                $ruleDataArray = json_decode($ruleData, true);

                if ($ruleData) {
                    if ($dataPass['same_rule'] == 1) {
                        if (isset($ruleDataArray['conditions'])) {
                            $conditions = $ruleDataArray['conditions'];
                            foreach ($conditions as $key => $condition) {
                                if (isset($condition['type'])) {
                                    if ($condition['type'] == 'Magento\SalesRule\Model\Rule\Condition\Product\Found') {
                                        foreach ($condition['conditions'] as $keySku => $conditionSku) {
                                            if (isset($conditionSku['attribute'])) {
                                                if ($conditionSku['attribute'] == 'sku') {
                                                    $typeSubSelect = $conditionSku['attribute'];
                                                    $typeSubSelectValue = $conditionSku['value'].','.$dataPass['sku'];
                                                    $ruleDataArray['conditions'][$key]['conditions'][$keySku]['value'] = $typeSubSelectValue;
                                                    $ruleDataArray['conditions'][$key]['conditions'][$keySku]['operator'] = '()';
                                                }                   
                                            }
                                        }
                                    }                   
                                }
                            }
                        }

                        $ruleDataAction = $statusResponse->getActionsSerialized();
                        $ruleDataActionArray = json_decode($ruleDataAction, true);

                        if ($ruleDataAction) {
                            if (isset($ruleDataActionArray['conditions'])) {
                                $conditionsAction = $ruleDataActionArray['conditions'];
                                foreach ($conditionsAction as $keyAction => $conditionAction) {
                                    if ($conditionAction['type'] == 'Magento\SalesRule\Model\Rule\Condition\Product') {
                                        $skuValueNow = $ruleDataActionArray['conditions'][$keyAction]['value'];
                                        $ruleDataActionArray['conditions'][$keyAction]['value'] = $skuValueNow.','.$dataPass['sku'];
                                        $ruleDataActionArray['conditions'][$keyAction]['operator'] = '()';
                                    }
                                }
                            }
                        }
                    }
                    else {
                        if (isset($ruleDataArray['conditions'])) {
                            $conditions = $ruleDataArray['conditions'];
                            foreach ($conditions as $key => $condition) {
                                if (isset($condition['type'])) {
                                    if ($condition['type'] == 'Amasty\Conditions\Model\Rule\Condition\CustomerAttributes') {
                                        $typeSubSelect = $condition['type'];
                                        $typeSubSelectValue = $condition['value'].','.$dataPass['store_code'];
                                        $ruleDataArray['conditions'][$key]['value'] = $typeSubSelectValue;
                                    }                   
                                }
                            }
                        }
                    }
                }



                if ($typeSubSelect) {
                    // update conditions serialize
                    $setRuleData = $statusResponse->setConditionsSerialized(json_encode($ruleDataArray));
                    if ($dataPass['same_rule'] == 1) {
                        $setRuleData = $statusResponse->setActionsSerialized(json_encode($ruleDataActionArray));
                        $setRuleData = $statusResponse->setDescription($statusResponse->getDescription() . " (" . $dataPass['sku'] . ')');
                    }
                    $this->ruleResource->save($statusResponse);
                }
            }

            if ($dataPass['promotion_type'] == 4) {
                $ruleData = $statusResponse->getConditionsSerialized();
                $ruleDataArray = json_decode($ruleData, true);

                if ($ruleData) {
                    if (isset($ruleDataArray['conditions'])) {
                        $conditions = $ruleDataArray['conditions'];
                        foreach ($conditions as $key => $condition) {
                            if (isset($condition['type'])) {
                                if ($condition['type'] == 'Amasty\Conditions\Model\Rule\Condition\CustomerAttributes') {
                                    $typeSubSelect = $condition['type'];
                                    $typeSubSelectValue = $condition['value'].','.$dataPass['store_code'];
                                    $ruleDataArray['conditions'][$key]['value'] = $typeSubSelectValue;
                                }                   
                            }
                        }
                    }
                }

                if ($typeSubSelect) {
                    // update conditions serialize
                    $setRuleData = $statusResponse->setConditionsSerialized(json_encode($ruleDataArray));
                    $this->ruleResource->save($statusResponse);
                }
            }
            // --------------
        } catch (\Exception $e) {
            $this->logger->info("<=End update salesrule" .$e->getMessage());
            throw new StateException(
                __(__FUNCTION__." - ".$e->getMessage())
            );
        }

        return $result;
    }

    /**
     * update to salesrule for requiredpoint
     * @param $dataPass mixed
     * @return $result mixed
     * @throw logger error
     */
    protected function updateSalesRuleRequiredPoint($dataPass)
    {
        try {
            $statusResponse = $this->ruleFactory->create();
            $statusResponse->load($dataPass['salesrule_id'], 'rule_id');

            // update condition serialize
            $result = [];
            // promotion type 7
            if ($dataPass['promotion_type'] == 7) {
                if ($dataPass['item_type'] == 2) {

                    // get sku already exist
                    $ruleData = $statusResponse->getConditionsSerialized();
                    
                    if ($ruleData) {
                        $ruleDataArray = json_decode($ruleData, true);
                        if (isset($ruleDataArray['conditions'])) {
                            $conditions = $ruleDataArray['conditions'];
                            foreach ($conditions as $key => $condition) {
                                if(isset($ruleDataArray['value'])){
                                    $ruleDataArray['conditions'][$key]['value'] = $dataPass['required_point'];                                    
                                }
                            }
                        }
                    }

                    $setRuleData = $statusResponse->setConditionsSerialized(json_encode($ruleDataArray));
                    
                    $result = $statusResponse->save();
                }
            }
            // --------------
        } catch (\Exception $e) {
            $this->logger->info("<=End update salesrule" .$e->getMessage());
            throw new StateException(
                __(__FUNCTION__." - ".$e->getMessage())
            );
        }

        return $result;
    }

    /**
     * Save to amasty sales rule
     * @param $dataPass int
     * @param $skuAmasty string
     * @param $promoTypeAmasty int
     * @param $itemTypeAmasty int
     * @return $result mixed
     * @throw logger error
     */
    protected function saveAmastySalesRule($dataPass, $skuAmasty, $promoTypeAmasty, $itemTypeAmasty)
    {
        try {
            $statusResponse = $this->amastyRuleInterfaceFactory->create();

            $statusResponse->setSalesruleId($dataPass)
                ->setPriceselector(0)
                ->setSkipRule(0)
                ->setApplyDiscountTo('asc')
                ->setUseFor(0);

            if ($promoTypeAmasty == 5) {
                if ($itemTypeAmasty == 2) {
                    $statusResponse->setPromoSkus($skuAmasty);
                }
            }

            if ($promoTypeAmasty != 4) {
                if ($promoTypeAmasty != 7) {
                    $statusResponse->setNqty(2);
                }
            }

            if ($promoTypeAmasty == 4) {
                $statusResponse->setPromoSkus($skuAmasty);
            }

            $result = $this->amastyRuleInterface->save($statusResponse);

            $this->logger->info("amasty_amrules_rule saved [" . $dataPass ."]");
        } catch (\Exception $e) {
            $this->logger->info("<=End amasty_amrules_rule" .$e->getMessage());
            throw new StateException(
                __(__FUNCTION__." - ".$e->getMessage())
            );
        }

        return $result;
    }

    /**
     * update to amasty sales rule amrule
     * @param $dataPass mixed
     * @return $result mixed
     * @throw logger error
     */
    protected function updateAmastyAmrule($dataPass)
    {
        try {
            $statusResponse = $this->amastyRuleInterfaceFactory->create();
            $statusResponse->load($dataPass['row_id'], 'salesrule_id');

            if ($dataPass['promotion_type'] == 5) {
                // add new sku
                $sku = $statusResponse->getData('promo_skus');
                if (empty($sku)) {
                    $sku = $dataPass['sku'];
                }
                elseif ($sku == " ") {
                    $sku = $dataPass['sku'];
                } else {
                    $sku = $sku.','.$dataPass['sku'];
                }

                if ($dataPass['item_type'] == 2) {
                    $statusResponse->setData('promo_skus', $sku);
                }
                
                if ($dataPass['item_type'] == 1) {
                    $statusResponse->setData('nqty', $dataPass['point_per_unit']);
                }

                $statusResponse->setData('salesrule_id', $dataPass['row_id']);

                $result = $statusResponse->save();
            }

            $this->logger->info("amasty_amrules_rule update");
        } catch (\Exception $e) {
            $this->logger->info("<=End amasty_amrules_rule" .$e->getMessage());
            throw new StateException(
                __(__FUNCTION__." - ".$e->getMessage())
            );
        }

        return $result;
    }

    /**
     * update to amasty sales rule promo
     * @param $dataPass mixed
     * @return $result mixed
     * @throw logger error
     */
    protected function updateAmastySalesRule($dataPass)
    {
        try {
            $statusResponse = $this->amastyPromoInterfaceFactory->create();
            $statusResponse->load($dataPass['row_id'], 'salesrule_id');

            // add new sku
            $sku = $statusResponse->getData('sku');
            if (empty($sku)) {
                $sku = $dataPass['sku'];
            } else {
                $sku = $sku.','.$dataPass['sku'];
            }

            $statusResponse->setData('sku', $sku);
            $statusResponse->setData('type', 1);
            $statusResponse->setData('salesrule_id', $dataPass['row_id']);
            $statusResponse->setData('minimal_items_price', 100);


            // set item discount
            if ($dataPass['discount_type'] == 1) {
                $statusResponse->setData('items_discount', $dataPass['promo_selling_price']);
            }
            if ($dataPass['discount_type'] == 2) {
                $statusResponse->setData('items_discount', $dataPass['percent_disc']);
            }
            if ($dataPass['discount_type'] == 3) {
                $statusResponse->setData('items_discount', $dataPass['amount_off']);
            }

            $result = $statusResponse->save();


            $this->logger->info("amasty_ampromo_rule update");
        } catch (\Exception $e) {
            $this->logger->info("<=End amasty_ampromo_rule" .$e->getMessage());
            throw new StateException(
                __(__FUNCTION__." - ".$e->getMessage())
            );
            
        }

        return $result;
    }

    /**
     * Check Attribute Id Exist
     * @param string $attributeCode
     * @return mixed $attributeId
     */
    protected function saveAttributeProduct($attributeCode)
    {
        $attributeId        = null;
        try {
            $attributeData = $this->eavAttribute->getCollection()->addFieldToFilter('attribute_code', $attributeCode);
            
            if ($attributeData->getSize() > 0) {
                foreach ($attributeData as $attributeDatas) {
                    $attributeId = $attributeDatas['attribute_id'];
                }
                $this->logger->info("saveAttributeProduct [" . $attributeCode ."] exist");
            } else {
                $this->createAttributeProduct($attributeCode);
                $this->logger->info("saveAttributeProduct [" . $attributeCode ."] saved");
            }
        } catch (\Exception $exception) {
            throw new StateException(
                __(__FUNCTION__." - ".$exception->getMessage())
            );
        }

        return $attributeId;
    }

    /**
     * Get data list
     *
     * @param array $dataProduct
     * @return array
     */
    protected function getDataList($dataProduct)
    {
        $skuList = [];
        $dataValueList = [];
        $sourceList = [];
        foreach ($dataProduct as $sku => $data) {
            $skuList[] = $sku;
            foreach ($data as $key => $val) {
                $dataValueList[] = $val;
                if (in_array($val['attributes']['store_code'], $sourceList) == false) {
                    $sourceList[] = PromotionPriceLogicInterface::PRODUCT_ATTR_PROMO_PRICE.$val['attributes']['store_code'];
                }
            }
        }
        return [
            'sku_list' => $skuList,
            'store_list' => $sourceList
        ];
    }

    /**
     * Get data list
     *
     * @param array $dataProduct
     * @return array
     */
    protected function getDataListxx($dataProduct)
    {
        $skuList = [];
        $sourceList = [];
        foreach ($dataProduct as $data) {
            $skuList[] = $data['sku'];
            if (in_array($data['attributes']['store_code'], $sourceList) == false) {
                $sourceList[] = PromotionPriceLogicInterface::PRODUCT_ATTR_PROMO_PRICE.$data['attributes']['store_code'];
            }
        }
        return [
            'sku_list' => $skuList,
            'store_list' => $sourceList
        ];
    }

    /**
     * Get product by multiple sku
     */
    protected function getProductByMultipleSku($skuList)
    {
        $prepare = [];
        $result = [];
        if (empty($skuList) == false) {
            $collection = $this->productCollectionFactory->create()->addFieldToFilter('sku', ['in' => $skuList]);
            $collection->getSelect()->reset(\Zend_Db_Select::COLUMNS)->columns(['entity_id','sku','row_id']);
            $prepare = $collection->getItems();

            foreach ($prepare as $value) {
                $result['sku'][] = $value->getSku();
                $result['id'][] = $value->getId();
            }
        }
        return $result;
    }

    /**
     * Get data list attribute id from attribute
     */
    protected function getAttributeIdList($attributeCode)
    {
        $prepare = [];
        $result = [];
        if (empty($attributeCode) == false) {
            $collection = $this->eavAttribute->getCollection()->addFieldToFilter('attribute_code', ['in' => $attributeCode]);
            $prepare = $collection->getItems();

            foreach ($prepare as $value) {
                $result[$value->getAttributeCode()]['attribute_id'][] = $value->getId();
            }
        }

        return $result;
    }

}
