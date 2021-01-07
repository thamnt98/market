<?php
/**
 * @category Trans
 * @package  Trans_IntegrationCatalog
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Ilma Dinnia Alghani <ilma.dinnia@transdigital.co.id>
 * @author   hadi <ashadi.sejati@transdigital.co.id>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationCatalog\Model;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\RuntimeException;
use Magento\Framework\Exception\StateException;
use Trans\IntegrationCatalog\Api\Data\IntegrationJobInterface;
use Trans\IntegrationCatalog\Api\Data\IntegrationProductInterface;
use Trans\IntegrationCatalog\Api\IntegrationDataValueRepositoryInterface;
use Trans\IntegrationCatalog\Api\IntegrationJobRepositoryInterface;
use Trans\Integration\Logger\Logger;
use Magento\Framework\App\Action\Context;
use Magento\TargetRule\Model\ResourceModel\Rule\Collection;
use Trans\IntegrationCatalog\Api\ProductAssociationLogicInterface;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Trans\IntegrationCatalog\Api\Data\ProductAssociationInterface;
use Trans\IntegrationCatalog\Helper\Config;
use Trans\IntegrationCategory\Api\Data\IntegrationCategoryInterface;
use Trans\IntegrationCategory\Api\IntegrationCategoryRepositoryInterface;
use Trans\IntegrationCatalog\Api\Data\IntegrationDataValueInterface;

class ProductAssociationLogic implements ProductAssociationLogicInterface 
{
    /**
    * @var IntegrationCategoryRepositoryInterface
    */
    protected $integrationCategoryRepositoryInterface;  
    /**
     * @var \Trans\IntegrationCatalog\Api\ProductAssociationRepositoryInterface $productAssociationRepository
     */
    protected $productAssociationRepository;

    /**
     * @var \Trans\IntegrationCatalog\Api\Data\ProductAssociationInterfaceFactory
     */
    protected $productAssociationInterface;
     /**
     * @var categoryFactory
     */
    protected $categoryFactory;

    /**
     * @var \Trans\IntegrationCatalog\Model\IntegrationDataValue 
     */
    protected $dataValueInterface;
    /**
     * @var \Magento\TargetRule\Model\ResourceModel\Rule
     */

    protected $targetRuleResource;
    /**
     * @var \Magento\TargetRule\Model\Rule
     */
    protected $rule;

    /**
     * @var IntegrationJobRepositoryInterface
     */
    protected $integrationJobRepositoryInterface;

    /**
     * @var IntegrationDataValueRepositoryInterface
     */
    protected $integrationDataValueRepositoryInterface;

    /**
     * @var array
     */
    protected $result;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var productRepo
     */
    protected $prodRepo;
    /**
     * @var productLoader
     */
    protected $productLoader;
     /**
     * @var catalogProduct
     */
    protected $catalogProduct;

    /**
     * @param Logger $Logger
     * @param IntegrationJobRepositoryInterface $integrationJobRepositoryInterface
     * @param IntegrationDataValueRepositoryInterface $integrationDataValueRepositoryInterface
     * @param \Trans\IntegrationCatalog\Model\IntegrationDataValueFactory $dataValueInterface
     * @param \Magento\TargetRule\Model\ResourceModel\Rule $targetRuleResource
     * @param \Magento\TargetRule\Model\RuleFactory $rule
     * @param \Magento\Catalog\Model\ProductLink\Repository $prodRepo
     * @param \Magento\Catalog\Model\ResourceModel\Product\Link $linked
     * @param \Magento\Catalog\Model\ProductFactory $catalogProduct
     * @param \Trans\IntegrationCatalog\Api\Data\ProductAssociationInterfaceFactory $productAssociationInterface
     * @param \Trans\IntegrationCatalog\Api\ProductAssociationRepositoryInterface $productAssociationRepository
     * @param IntegrationCategoryRepositoryInterface $integrationCategoryRepositoryInterface
     */

    public function __construct(
      Logger $logger, 
      \Trans\IntegrationCatalog\Helper\Config $config,
      CategoryFactory $categoryFactory,
      IntegrationJobRepositoryInterface $integrationJobRepositoryInterface, 
      IntegrationDataValueRepositoryInterface $integrationDataValueRepositoryInterface,
      IntegrationCategoryRepositoryInterface $integrationCategoryRepositoryInterface,
      \Magento\Framework\App\Action\Context $context, 
      \Trans\IntegrationCatalog\Model\IntegrationDataValueFactory $dataValueInterface, 
      \Magento\TargetRule\Model\ResourceModel\Rule $targetRuleResource, 
      \Magento\TargetRule\Model\RuleFactory $rule,
      \Magento\Catalog\Model\Product\LinkFactory $linkProd,
      \Magento\Catalog\Model\ResourceModel\Product\Link $linkedCreate,
      \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
      \Magento\Catalog\Model\ProductFactory $catalogProduct,
      \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
      \Trans\IntegrationCatalog\Api\Data\ProductAssociationInterfaceFactory $productAssociationInterface,
      \Trans\IntegrationCatalog\Api\ProductAssociationRepositoryInterface $productAssociationRepository,
      \Trans\IntegrationCategory\Model\IntegrationCategoryFactory $dataCategoryInterface,
      \Trans\IntegrationCategory\Model\IntegrationDataValueRepository $dataRepository
       )
      {
     
        $this->logger = $logger;
        $this->integrationJobRepositoryInterface = $integrationJobRepositoryInterface;
        $this->integrationDataValueRepositoryInterface = $integrationDataValueRepositoryInterface;
        $this->_targetRule = $rule;
        $this->_targetRuleResource = $targetRuleResource;
        $this->dataValueInterface = $dataValueInterface;
        $this->productLoader = $productRepository;
        $this->catalogProduct = $catalogProduct;
        $this->linkedProd = $linkProd;
        $this->_linkedCreate = $linkedCreate;
        $this->categoryFactory = $categoryFactory;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->productAssociationInterface = $productAssociationInterface;
        $this->productAssociationRepository = $productAssociationRepository;
        $this->config= $config;
        $this->dataCategoryInterface= $dataCategoryInterface;
        $this->dataRepository=$dataRepository;
        $this->integrationCategoryRepositoryInterface  = $integrationCategoryRepositoryInterface;
    }

    /**
     * Update Job data
     * @param object $datas
     * @param int $status
     * @param string $msg
     * @throw error
     */
    protected function updateJobData($jobId = 0, $status = "", $msg = "")
    {
        if ($jobId < 1){
            throw new StateException(__(IntegrationJobInterface::MSG_DATA_NOTAVAILABLE));
        }
        try{
            $dataJobs = $this->integrationJobRepositoryInterface->getById($jobId);
            $dataJobs->setStatus($status);
            if (!empty($msg)){
                $dataJobs->setMessages($msg);
            }
            $this->integrationJobRepositoryInterface->save($dataJobs);
        }
        catch(\Exception $exception){
            $this->logger->error(__FUNCTION__ . "------ ERROR " . $exception->getMessage());
            throw new CouldNotSaveException(__("Error : Cannot Update Job data - " . $exception->getMessage()));
        }
    }

    /**
     * @param array $channel
     * @return mixed
     * @throws NoSuchEntityException
     * @throws StateException
     */
    public function prepareData($channel = [])
    {
        if (empty($channel)){
            throw new StateException(__('Parameter Channel are empty !'));
        }
        try{
            $jobs       = $channel['jobs'];
            if($jobs->getFirstItem()){
              $jobId      = $jobs->getFirstItem()->getId();
              $jobStatus  = $jobs->getFirstItem()->getStatus();
              $status     = IntegrationProductInterface::STATUS_JOB;
  
              $result = $this->integrationDataValueRepositoryInterface->getByJobIdWithStatus($jobId, $status);
              if ($result->getSize() < 1){
                  throw new NoSuchEntityException(__('Result Data Value Are Empty!!'));
              }
            }else{
              throw new NoSuchEntityException(__('Result Data Value Are Empty!!'));
            }
        }
        catch(\Exception $exception){
            $this->logger->error(__FUNCTION__ . "------ ERROR " . $exception->getMessage());
            throw new StateException(__($exception->getMessage()));
        }
        return $result;
    }

    /**
   * Save Status and Message to Integration Data Value
   *
   * @param IntegrationDataValueInterface $objStatusMessage
   * @param string $message
   * @param int $status
   */
    protected function saveStatusMessage($objStatusMessage, $message, $status) {
      $objStatusMessage->setMessage($message);
      $objStatusMessage->setStatus($status);
      $this->integrationDataValueRepositoryInterface->save($objStatusMessage);
    }

    /**
     * Save table magento_targetrule & magento_targetrule_product
     * @param mixed $datas
     * @return mixed
     * @throws StateException
     */
    public function saveProductAssociation($datas)
    {
        $checkData = array_filter($datas->getData());
        if (empty($checkData)) {
          throw new StateException(
            __(IntegrationJobInterface::MSG_DATA_NOTAVAILABLE)
          );
        }

        
        $jobId    = $datas->getFirstItem()->getJbId();
        $this->updateJobData($jobId,IntegrationJobInterface::STATUS_PROGRESS_CATEGORY);
        try {
            $i = 0;
            foreach ($datas as $data)
            {   
              try {
                $resultData = json_decode($data->getDataValue());
                $integrationProduct = $this->dataCollection($resultData);

                // validasi by pim id
                
                $checkIntProductId = $this->checkIntegrationProductByPimId($integrationProduct['id']);
                if (!$checkIntProductId) {
                  if ($integrationProduct['deleted'] == 0) {
                    $saveTargetRule = $this->targetRuleCreate($integrationProduct);
                    if ($saveTargetRule) {
                      $this->saveStatusMessage($data,NULL,IntegrationDataValueInterface::STATUS_DATA_SUCCESS);
                    }
                    else {
                      $msgsave = __FUNCTION__." : Save SKU/Category empty";
                      $this->saveStatusMessage($data,$msgsave,IntegrationDataValueInterface::STATUS_DATA_SUCCESS);
                    }
                  }
                  else {
                    $msgsave = __FUNCTION__." : ".$integrationProduct['id'] . " -> deleted";
                    $this->saveStatusMessage($data,$msgsave,IntegrationDataValueInterface::STATUS_DATA_SUCCESS);
                  }
                }
                else {
                  // check modified at greater
                  // $date_modifiednow = $checkIntProductId->getModifiedAt();
                  // $date_modifiednew = $integrationProduct['modified_at'];
                  // if ($date_modifiednew > $date_modifiednow) {
                      $updateTargetRule = $this->updateTargetRuleCreate($integrationProduct);

                      if ($updateTargetRule) {
                        $msgsave = __FUNCTION__." : ".$integrationProduct['id'] . " -> exist";
                        $this->saveStatusMessage($data,$msgsave,IntegrationDataValueInterface::STATUS_DATA_SUCCESS);
                      }
                      else {
                        $msgsave = __FUNCTION__." : ".$integrationProduct['id'] . " -> deleted";
                        $this->saveStatusMessage($data,$msgsave,IntegrationDataValueInterface::STATUS_DATA_SUCCESS);
                      }
                  // }
                }
                $i++;
                
              }
              catch (\Exception $e) {
                $msg = __FUNCTION__." : ".$e->getMessage();
                $this->logger->error($msg);
                $this->saveStatusMessage($data,$msg,IntegrationDataValueInterface::STATUS_DATA_FAIL_UPDATE);
                continue;
              }
            }
        } catch (\Exception $e) {
            $msg = __FUNCTION__ . " ERROR : Save Product Association : " . $e->getMessage();
            $this->logger->error($msg);
            $this->updateJobData($jobId,IntegrationJobInterface::STATUS_PROGRES_UPDATE_FAIL);
            throw new StateException(__($msg));
        }

        $this->updateJobData($jobId,IntegrationJobInterface::STATUS_COMPLETE);
        return true;
    }

    /**
     * check to integration catalog promotion price by promo id
     * @param $data mixed
     * @return $query mixed
     * @throw logger error
     */
    protected function checkIntegrationProductByPimId($data)
    {
        try {
            $query = $this->productAssociationRepository->loadDataPromoByPromoId($data);
        } catch (\Exception $e) {
            $this->logger->error("<=End checkIntegrationProduct association" .$e->getMessage());
            throw new StateException(
                __(__FUNCTION__." - ".$e->getMessage())
            );
        }

        return $query;
    }

    /**
     * Get Data from table integration_catalog_data
     * @param \Trans\IntegrationCatalog\Model\IntegrationDataValueFactory $resultData
     * @return mixed
     * @throws StateException
     */
    protected function dataCollection($resultData) {
      $toDate = $this->config->getToDate();
      $dataIntegrationCatalog = [
          'id' => $resultData->id,
          'name' => $resultData->name,
          'status' => $resultData->status,
          'deleted' => $resultData->deleted,
          'display_rule_by' => $resultData->display_rule_by,
          'display_rule' => $resultData->display_rule,
          'product_display_by' => $resultData->product_display_by,
          'product_display' => $resultData->product_display,
          'from_date' => $resultData->created_at,
          'to_date' => $toDate,
          'sort_order' => 1,
          'apply_to' => 1,
          'positions_limit' => 15,
          'except_product' => $resultData->except_product,
          'display_sequence' => $resultData->display_sequence,
          'modified_at' => $resultData->modified_at
        ];
        return $dataIntegrationCatalog;     
    }

    /**
     * Create condition target rule association
     * @param \Trans\IntegrationCatalog\Model\IntegrationDataValueFactory $dataIntegrationCatalog
     * @return mixed
     * @throws StateException
     */
    protected function targetRuleCreate($dataIntegrationCatalog) {

        $model = $this->_targetRule->create();
        $model->setName($dataIntegrationCatalog['name']);
        $model->setSortOrder($dataIntegrationCatalog['sort_order']);
        $model->setIsActive($dataIntegrationCatalog['status']);
        $model->setApplyTo($dataIntegrationCatalog['apply_to']);
        $model->setFromDate($dataIntegrationCatalog['from_date']);
        $model->setToDate($dataIntegrationCatalog['to_date']);
        $model->setPositionsLimit($dataIntegrationCatalog['positions_limit']);
        $model->setActionSelect(null);
        $model->setActionSelectBind(null);

        // Association Related Product 1-1
        if ($dataIntegrationCatalog['display_rule_by']=="1" && $dataIntegrationCatalog['product_display_by']=="1") {
          $ruleDisplayData = implode(",",$dataIntegrationCatalog['display_rule']);
          $productDisplayData = implode(",",$dataIntegrationCatalog['product_display']);

          $conditions = [
            'type' => \Magento\TargetRule\Model\Rule\Condition\Combine::class, 
            'attribute' => null,  
            'operator' => null,
            'value' => '1', 
            'is_value_processed' => null, 
            'aggregator' => 'all',
            'conditions' => [] ];
          $conditions['conditions']['value'] = [
            'type' => \Magento\TargetRule\Model\Rule\Condition\Product\Attributes::class , 
            'attribute' => 'sku', 
            'operator' => '==', 
            'value' => $ruleDisplayData,
            'is_value_processed' => false
           ];
          $model->getConditions()->setConditions([])->loadArray($conditions);

          $actions = [
            'type' => \Magento\TargetRule\Model\Actions\Condition\Combine::class, 
            'attribute' => null,  
            'operator' => null, 
            'value' => '1',
            'is_value_processed' => null,
            'aggregator' => 'all',
            'new_child' => '', 
            'actions' => [] ];
          $actions['actions']['value'] = [
            'type' => \Magento\TargetRule\Model\Actions\Condition\Product\Attributes::class , 
            'attribute' => 'sku', 
            'operator' => '()',
            'value' => $productDisplayData,
            'is_value_processed' => false, 
            'value_type' => 'constant'
             ];
          $model->getActions()->setActions([])->loadArray($actions, 'actions');
          $saveLink = $this->productLink($model,$dataIntegrationCatalog);

          if ($saveLink) {
            $saveData = $this->_targetRuleResource->save($model);
            $dataIntegrationCatalog['rule_id'] = $model->getData('rule_id');
            $dataIntegrationCatalog['link_id'] = $saveLink;
            $saveProductAssociation = $this->saveIntegrationProductAssociation($dataIntegrationCatalog);

            return true;
          }
          else {
            return false;
          }
        } 
        // Association Related Product 1-2
        else if ($dataIntegrationCatalog['display_rule_by']=="1" && $dataIntegrationCatalog['product_display_by']=="2") {
          // loop data from pim id
          $dataDisplayRuleArray = array();
          foreach ($dataIntegrationCatalog['product_display'] as $dataDisplayRule) {
            try {
              $productDisplayCollection = $this->integrationCategoryRepositoryInterface->loadDataByPimId($dataDisplayRule);
              $dataProdDis = $productDisplayCollection->getMagentoEntityId();
              if ($dataProdDis) {
                $dataDisplayRuleArray[] = $dataProdDis;
              }
            }
            catch (\Exception $e) {
              $this->logger->error('Parameter PimId are empty !');
              continue;
            }
          }
          $dataDisplayRuleImplode = implode( ',', $dataDisplayRuleArray );
          $productDisplayData = implode(",",$dataIntegrationCatalog['display_rule']);
          $productExcerptData = implode(",",$dataIntegrationCatalog['except_product']);

          $conditions = [
            'type' => \Magento\TargetRule\Model\Rule\Condition\Combine::class, 
            'attribute' => null,  
            'operator' => null,
            'value' => '1', 
            'is_value_processed' => null, 
            'aggregator' => 'all',
            'conditions' => [] ];
          $conditions['conditions']['value'] = [
            'type' => \Magento\TargetRule\Model\Rule\Condition\Product\Attributes::class , 
            'attribute' => 'sku', 
            'operator' => '==', 
            'value' => $productDisplayData,
            'is_value_processed' => false
           ];
          $model->getConditions()->setConditions([])->loadArray($conditions);

          $actions = [
            'type' => \Magento\TargetRule\Model\Actions\Condition\Combine::class, 
            'attribute' => null,  
            'operator' => null, 
            'value' => '1',
            'is_value_processed' => null,
            'aggregator' => 'all',
            'actions' => [] ];
          $act = [];
          $act[] = [
            'type' => \Magento\TargetRule\Model\Actions\Condition\Product\Attributes::class , 
            'attribute' => 'category_ids', 
            'operator' => '==',
            'value' => $dataDisplayRuleImplode,
            'is_value_processed' => false, 
            'value_type' => 'constant'
          ];
          $act[] = [
              'type' => \Magento\TargetRule\Model\Actions\Condition\Product\Attributes::class , 
              'attribute' => 'sku', 
              'operator' => '!()',
              'value' => $productExcerptData,
              'is_value_processed' => false, 
              'value_type' => 'same_as'
               ];
          $actions['actions'] = $act;
          $model->getActions()->setActions([])->loadArray($actions, 'actions');  
          $saveLink = $this->categoryLink($model,$dataIntegrationCatalog);

          if ($saveLink) {
            $saveData = $this->_targetRuleResource->save($model);
            $dataIntegrationCatalog['rule_id'] = $model->getData('rule_id');
            $dataIntegrationCatalog['link_id'] = $saveLink;
            $saveProductAssociation = $this->saveIntegrationProductAssociation($dataIntegrationCatalog);

            return true;
          }
          else {
            return false;
          }
        }
        // Association Related Product 2-1
        else if ($dataIntegrationCatalog['display_rule_by']=="2" && $dataIntegrationCatalog['product_display_by']=="1") {

            // loop data from pim id
            $dataDisplayRuleArray = array();
            foreach ($dataIntegrationCatalog['display_rule'] as $dataDisplayRule) {
              try {
                $categoryCollection = $this->integrationCategoryRepositoryInterface->loadDataByPimId($dataDisplayRule);
                $dataCat = $categoryCollection->getMagentoEntityId();
                if ($dataCat) {
                  $dataDisplayRuleArray[] = $dataCat;
                }
              }
              catch (\Exception $e) {
                $this->logger->error('Parameter PimId are empty !');
                continue;
              }
            }
            $dataDisplayRuleImplode = implode( ',', $dataDisplayRuleArray );
            $productDisplayData = implode(",",$dataIntegrationCatalog['product_display']);

            $conditions = [
              'type' => \Magento\TargetRule\Model\Rule\Condition\Combine::class, 
              'attribute' => null,  
              'operator' => null,
              'value' => '1', 
              'is_value_processed' => null, 
              'aggregator' => 'all',
              'conditions' => [] ];
            $conditions['conditions']['value'] = [
              'type' => \Magento\TargetRule\Model\Rule\Condition\Product\Attributes::class , 
              'attribute' => 'category_ids', 
              'operator' => '==', 
              'value' => $dataDisplayRuleImplode,
              'is_value_processed' => false
             ];
            $model->getConditions()->setConditions([])->loadArray($conditions);

            $actions = [
              'type' => \Magento\TargetRule\Model\Actions\Condition\Combine::class, 
              'attribute' => null,  
              'operator' => null, 
              'value' => '1',
              'is_value_processed' => null,
              'aggregator' => 'all',
              'actions' => [] ];
            $actions['actions']['value'] = [
              'type' => \Magento\TargetRule\Model\Actions\Condition\Product\Attributes::class , 
              'attribute' => 'sku', 
              'operator' => '()',
              'value' => $productDisplayData,
              'is_value_processed' => false, 
              'value_type' => 'constant'
            ];
            $model->getActions()->setActions([])->loadArray($actions, 'actions');  
            $saveLink = $this->categoryLink2($model,$dataIntegrationCatalog);

            if ($saveLink) {
              $saveData = $this->_targetRuleResource->save($model);
            
              $dataIntegrationCatalog['rule_id'] = $model->getData('rule_id');
              $dataIntegrationCatalog['link_id'] = $saveLink;
              $saveProductAssociation = $this->saveIntegrationProductAssociation($dataIntegrationCatalog);
              return true;
            }
            else {
              return false;
            }
            
          } 
          // Association Related Product 2-2
          else if ($dataIntegrationCatalog['display_rule_by']=="2" && $dataIntegrationCatalog['product_display_by']=="2") {

              // loop data display rule from pim id
              $dataDisplayRuleArray = array();
              foreach ($dataIntegrationCatalog['display_rule'] as $dataDisplayRule) {
                try {
                  $categoryCollection = $this->integrationCategoryRepositoryInterface->loadDataByPimId($dataDisplayRule);
                  $dataCat = $categoryCollection->getMagentoEntityId();
                  if ($dataCat) {
                    $dataDisplayRuleArray[] = $dataCat;
                  }
                }
                catch (\Exception $e) {
                  $this->logger->error('Parameter PimId are empty !');
                  continue;
                }
              }
              $dataDisplayRuleImplode = implode( ',', $dataDisplayRuleArray );

              // loop data display product from pim id
              $dataDisplayProdArray = array();
              foreach ($dataIntegrationCatalog['product_display'] as $dataDisplayProd) {
                try {
                  $categoryCollectionProd = $this->integrationCategoryRepositoryInterface->loadDataByPimId($dataDisplayProd);
                  $dataCatProd = $categoryCollectionProd->getMagentoEntityId();
                  if ($dataCatProd) {
                    $dataDisplayProdArray[] = $dataCatProd;
                  }
                }
                catch (\Exception $e) {
                  $this->logger->error('Parameter PimId are empty !');
                  continue;
                }
              }
              $dataDisplayProdImplode = implode( ',', $dataDisplayProdArray );

              $conditions = [
                'type' => \Magento\TargetRule\Model\Rule\Condition\Combine::class, 
                'attribute' => null,  
                'operator' => null,
                'value' => '1', 
                'is_value_processed' => null, 
                'aggregator' => 'all',
                'conditions' => [] ];
              $conditions['conditions']['value'] = [
                'type' => \Magento\TargetRule\Model\Rule\Condition\Product\Attributes::class , 
                'attribute' => 'category_ids', 
                'operator' => '==', 
                'value' => $dataDisplayRuleImplode,
                'is_value_processed' => false
               ];
              $model->getConditions()->setConditions([])->loadArray($conditions);
              $actions = [
                'type' => \Magento\TargetRule\Model\Actions\Condition\Combine::class, 
                'attribute' => null,  
                'operator' => null, 
                'value' => '1',
                'is_value_processed' => null,
                'aggregator' => 'all',
                'actions' => [] ];
              $actions['actions']['value'] = [
                'type' => \Magento\TargetRule\Model\Actions\Condition\Product\Attributes::class , 
                'attribute' => 'category_ids', 
                'operator' => '==',
                'value' => $dataDisplayProdImplode,
                'is_value_processed' => false, 
                'value_type' => 'constant'
              ];
              
              $model->getActions()->setActions([])->loadArray($actions, 'actions'); 
              $saveLink = $this->categoryLink22($model,$dataIntegrationCatalog);
              
              if ($saveLink) {
                $saveData = $this->_targetRuleResource->save($model); 
                $dataIntegrationCatalog['rule_id'] = $model->getData('rule_id');
                $dataIntegrationCatalog['link_id'] = $saveLink;
                $saveProductAssociation = $this->saveIntegrationProductAssociation($dataIntegrationCatalog);
                return true;
              }
              else {
                return false;
              }
          }
    }

    /**
     * update target rule association
     * @param \Trans\IntegrationCatalog\Model\IntegrationDataValueFactory $dataIntegrationCatalog
     * @return mixed
     * @throws StateException
     */
    protected function updateTargetRuleCreate($dataIntegrationCatalog) {
      // get data by pim id
      $getDataByPimId = $this->checkIntegrationProductByPimId($dataIntegrationCatalog['id']);

      // deleted target rule
      $model = $this->_targetRule->create();
      $model->load($getDataByPimId->getRuleId());
      $model->delete();

      // deleted catalog product link
      $getLinkId_arrs = explode(",", $getDataByPimId->getLinkId());
      foreach ($getLinkId_arrs as $getLinkId_arr) {
        $setType = $this->linkedProd->create();
        $setType->load($getLinkId_arr);
        $setType->delete();
      }

      // deleted custom
      $query = $this->productAssociationInterface->create();
      $query->load($getDataByPimId->getId());
      $query->delete();

      // save new data
      if ($dataIntegrationCatalog['deleted'] == 0) {
        $saveTargetRule = $this->targetRuleCreate($dataIntegrationCatalog);
        return true;
      }
      return false;
    }

    /**
     * Set Product Relations for table catalog_product_link from module Catalog Product
     * Target Rule conditions 1-1 
     * @param \Magento\TargetRule\Model\RuleFactory $model
     * @param \Trans\IntegrationCatalog\Model\IntegrationDataValueFactory $dataIntegrationCatalog
     * @return mixed
     * @throws StateException
     */
    protected function productLink($model, $dataIntegrationCatalog) {
      $dataLinkIdCatalog = [];
      foreach ($dataIntegrationCatalog['display_rule'] as $displayRuleData) {
        try {
          // validasi sku on display rule
          $idDisplayLinks = $this->productLoader->get($displayRuleData);
          if ($idDisplayLinks) {
            foreach ($dataIntegrationCatalog['product_display'] as $productDisplayData) {
              // validasi sku on product display
              try {
                
                $idProductLinks = $this->productLoader->get($productDisplayData);
                if ($idProductLinks) {
                  $setType = $this->linkedProd->create();
                  $setType->setProductId($idDisplayLinks->getRowId());
                  $setType->setLinkedProductId($idProductLinks->getData('entity_id'));
                  $setType->setLinkTypeId(1);        
                  $saveDatas = $this->_linkedCreate->save($setType);

                  $dataLinkIdCatalog[] = $setType->getLinkId();
                }
              } catch (\Exception $e) {
                continue;
              }
            }
          }
        } 
        catch (\Exception $e) {
          continue;
        }
      }      

      if (!empty($dataLinkIdCatalog)) {
        $dataLinkIdCatalogImplode = implode(",", $dataLinkIdCatalog);
        return $dataLinkIdCatalogImplode;
      }
      else {
        return false;
      }
    }

    /**
     * Get List Categories
     * @return mixed
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $ids
     * @throws StateException
     */
    public function getProductCollectionByCategories($ids){
        $collection = $this->_productCollectionFactory->create();
        $collection->addAttributeToSelect('*');
        $collection->addCategoriesFilter(['in' => $ids]);
        return $collection;
    }
    /**
     * Set Product Relations for table catalog_product_link from module Catalog Product
     * Conditions 1-2
     * @param \Magento\TargetRule\Model\RuleFactory $model
     * @param \Trans\IntegrationCatalog\Model\IntegrationDataValueFactory $dataIntegrationCatalog
     * @return mixed
     * @throws StateException
     */
    protected function categoryLink($model, $dataIntegrationCatalog) {
      $dataLinkIdCatalog = [];
      foreach ($dataIntegrationCatalog['display_rule'] as $dataDisplayRule) {
        try {
          // validasi sku on display rule
          $getDataBySku = $this->productLoader->get($dataDisplayRule);
          if ($getDataBySku) {
            foreach ($dataIntegrationCatalog['product_display'] as $dataDisplayProd) {
              $categoryCollectionProd = $this->integrationCategoryRepositoryInterface->loadDataByPimId($dataDisplayProd);
              // validasi pim id in integrasi category
              if ($categoryCollectionProd) {
                $ids = $categoryCollectionProd->getMagentoEntityId();
                $categoryProducts = $this->getProductCollectionByCategories($ids);

                foreach ($categoryProducts as $productts) {
                  
                  // check except SKU
                  try {
                    if (!in_array($productts->getData('sku'), $dataIntegrationCatalog['except_product'])) {
                        $setType = $this->linkedProd->create();
                        $setType->setProductId($getDataBySku->getData("row_id"));
                        $setType->setLinkedProductId($productts->getId());
                        $setType->setLinkTypeId(1); 
                        $saveData = $this->_linkedCreate->save($setType); 

                        $dataLinkIdCatalog[] = $setType->getLinkId();
                        $collection = $this->_productCollectionFactory->create()->setLinkModel($this->linkedProd);  
                      }
                  }
                  catch (\Exception $e) {
                    continue;
                  }
                }
              }
            }
          }
        }
        catch (\Exception $e) {
          continue;
        }
      }

      if (!empty($dataLinkIdCatalog)) {
        $dataLinkIdCatalogImplode = implode(",", $dataLinkIdCatalog);
        return $dataLinkIdCatalogImplode;
      }
      else {
        return false;
      }
    } 

    /**
     * Set Product Relations for table catalog_product_link from module Catalog Product
     * Conditions 2-1
     * @param \Magento\TargetRule\Model\RuleFactory $model
     * @param \Trans\IntegrationCatalog\Model\IntegrationDataValueFactory $dataIntegrationCatalog
     * @return mixed
     * @throws StateException
     */
       protected function categoryLink2($model, $dataIntegrationCatalog) {  
        $dataLinkIdCatalog = [];  
        // save catalog product link
        foreach ($dataIntegrationCatalog['display_rule'] as $dataDisplayRule) {
          $categoryCollection = $this->integrationCategoryRepositoryInterface->loadDataByPimId($dataDisplayRule);
          // validasi pim id in integrasi category
          if ($categoryCollection) {
            $cat = $categoryCollection->getMagentoEntityId();
            $categoryProductss = $this->getProductCollectionByCategories($cat);

            foreach ($categoryProductss as $productt) {
              foreach ($dataIntegrationCatalog['product_display'] as $dataDisplayProds) {
                // validasi sku on product_display
                try {
                  $getDataBySku = $this->productLoader->get($dataDisplayProds);
                  if ($getDataBySku) {
                    $setType = $this->linkedProd->create();
                    $setType->setProductId($productt->getRowId());
                    $setType->setLinkedProductId($getDataBySku->getData("entity_id"));
                    $setType->setLinkTypeId(1); 
                    $saveDatas = $this->_linkedCreate->save($setType);

                    $dataLinkIdCatalog[] = $setType->getLinkId();
                  }
                }
                catch (\Exception $e) {
                  continue;
                }
              }
            } 
          }
        }

        if (!empty($dataLinkIdCatalog)) {
          $dataLinkIdCatalogImplode = implode(",", $dataLinkIdCatalog);
          return $dataLinkIdCatalogImplode;
        }
        else {
          return false;
        }
      } 

    /**
     * Set Product Relations for table catalog_product_link from module Catalog Product
     * Conditions 2-2
     * @param \Magento\TargetRule\Model\RuleFactory $model
     * @param \Trans\IntegrationCatalog\Model\IntegrationDataValueFactory $dataIntegrationCatalog
     * @return mixed
     * @throws StateException
     */
      protected function categoryLink22($model, $dataIntegrationCatalog) {    
        $dataLinkIdCatalog = [];  
        foreach ($dataIntegrationCatalog['display_rule'] as $dataDisplayRule) {
          $categoryCollection = $this->integrationCategoryRepositoryInterface->loadDataByPimId($dataDisplayRule);
          $cat = $categoryCollection->getMagentoEntityId();
          if ($cat) {
            $categoryProductss = $this->getProductCollectionByCategories($cat);
            foreach ($categoryProductss as $productt) {
              foreach ($dataIntegrationCatalog['product_display'] as $dataDisplayProd) {
                $categoryCollectionProd = $this->integrationCategoryRepositoryInterface->loadDataByPimId($dataDisplayProd);
                $ids = $categoryCollectionProd->getMagentoEntityId();
                if ($ids) {
                  $categoryProducts = $this->getProductCollectionByCategories($ids);
                  foreach ($categoryProducts as $productts) {
                    try {
                      $setType = $this->linkedProd->create();
                      $setType->setProductId($productt->getRowId());
                      $setType->setLinkedProductId($productts->getId());
                      $setType->setLinkTypeId(1); 
                      $saveData = $this->_linkedCreate->save($setType); 
                      $dataLinkIdCatalog[] = $setType->getLinkId();
                     
                      // $collection = $this->_productCollectionFactory->create()->setLinkModel($this);
                    }
                    catch (\Exception $e) {
                      continue;
                    }
                  }
                }
              }
            }
          }
        }

        if (!empty($dataLinkIdCatalog)) {
          $dataLinkIdCatalogImplode = implode(",", $dataLinkIdCatalog);
          return $dataLinkIdCatalogImplode;
        }
        else {
          return false;
        }
      } 
        
    /** 
     * Save to integration catalog product association 
     * @param \Trans\IntegrationCatalog\Model\IntegrationDataValueFactory $dataIntegrationCatalog
     * @return $result mixed
     * @throw logger error
     */
     protected function saveIntegrationProductAssociation($dataIntegrationCatalog)
     {
         try {
            $query = $this->productAssociationInterface->create();

            if (!empty($dataIntegrationCatalog['id'])) {
              $query->setPimId($dataIntegrationCatalog['id']);
            }

            if (!empty($dataIntegrationCatalog['rule_id'])) {
              $query->setRuleId($dataIntegrationCatalog['rule_id']);
            }

            if (!empty($dataIntegrationCatalog['link_id'])) {
              $query->setLinkId($dataIntegrationCatalog['link_id']);
            }

            if (!empty($dataIntegrationCatalog['name'])) {
              $query->setPimName($dataIntegrationCatalog['name']);
              $query->setName($dataIntegrationCatalog['name']);
            }

            $displayRule = implode(",", $dataIntegrationCatalog['display_rule']);
            if (!empty($displayRule)) {
              $query->setDisplayRule($displayRule);
            }

            $productDisplay = implode(",", $dataIntegrationCatalog['product_display']);
            if (!empty($productDisplay)) {
              $query->setProductDisplay($productDisplay);
            }

            if (!empty($dataIntegrationCatalog['display_rule_by'])) {
              $query->setDisplayRuleBy($dataIntegrationCatalog['display_rule_by']);
            }

            if (!empty($dataIntegrationCatalog['product_display_by'])) {
              $query->setProductDisplayBy($dataIntegrationCatalog['product_display_by']);
            }

            $exceptProduct = implode(",", $dataIntegrationCatalog['except_product']);
            if (!empty($exceptProduct)) {
              $query->setExceptProduct($exceptProduct);
            }

            $displaySequence = implode(",", $dataIntegrationCatalog['display_sequence']);
            if (!empty($displaySequence)) {
              $query->setDisplaySequence($displaySequence);
            }
           
            $query->setDeleted($dataIntegrationCatalog['deleted']);
            
            $query->setStatus($dataIntegrationCatalog['status']);


            if (!empty($dataIntegrationCatalog['modified_at'])) {
              // $modifiedAt = new \DateTime($dataIntegrationCatalog['modified_at']);
              // $modifiedAtString = $modifiedAt->modify('-7 hours')->format('Y-m-d H:i:s');
              $query->setModifiedAt($dataIntegrationCatalog['modified_at']);
            }
             
            $result = $this->productAssociationRepository->save($query);
            
       } catch (\Exception $e) {
            $this->logger->error("<=End Integration Product Association" .$e->getMessage());
          return false;
      }

    }

}
