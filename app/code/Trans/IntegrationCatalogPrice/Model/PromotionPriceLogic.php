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
     * @var ProductAttributeManagementInterface
     */
    protected $productAttributeManagement;

    /**
     * @var AttributeOption
     */
    protected $attributeOptionHelper;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;

    /**
     * @var ProductAttributeInterfaceFactory
     */
    protected $productAttributeFactory;

    /**
     * @var ProductAttributeRepositoryInterface
     */
    protected $productAttributeRepository;

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
     * @param \Trans\Integration\Logger\Logger $Logger
     * @param \Trans\IntegrationCatalogPrice\Api\PromotionPriceRepositoryInterface $promotionPriceRepositoryInterface
     * @param \Trans\IntegrationCatalogPrice\Api\Data\PromotionPriceInterfaceFactory $promotionPriceInterfaceFactory
     * @param \Trans\IntegrationCatalogPrice\Api\IntegrationDataValueRepositoryInterface $IntegrationDataValueRepositoryInterface
     * @param Validation $validation
     * @param \Trans\IntegrationCatalogPrice\Api\IntegrationJobRepositoryInterface $integrationJobRepositoryInterface
     * @param \Magento\Catalog\Model\ResourceModel\Eav\Attribute $eavAttribute
     * @param \Magento\Catalog\Api\ProductAttributeManagementInterface $productAttributeManagement
     * @param AttributeOption $attributeOptionHelper
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Catalog\Api\Data\ProductAttributeInterfaceFactory $productAttributeFactory
     * @param \Magento\Catalog\Api\ProductAttributeRepositoryInterface $productAttributeRepository
     * @param \Trans\IntegrationCatalog\Api\IntegrationProductRepositoryInterface $productRepository
     * @param CoreHelper $coreHelper
     * @param HelperPrice $helperPrice
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepositoryInterface
     * @param \Trans\IntegrationEntity\Api\IntegrationProductAttributeRepositoryInterface productRepositoryInterface$integrationAttributeRepository
     */
    public function __construct(
        \Trans\Integration\Logger\Logger $logger,
        \Trans\IntegrationCatalogPrice\Api\PromotionPriceRepositoryInterface $promotionPriceRepositoryInterface,
        \Trans\IntegrationCatalogPrice\Api\Data\PromotionPriceInterfaceFactory $promotionPriceInterfaceFactory,
        \Trans\IntegrationCatalogPrice\Api\IntegrationDataValueRepositoryInterface $integrationDataValueRepositoryInterface,
        Validation $validation,
        \Trans\IntegrationCatalogPrice\Api\IntegrationJobRepositoryInterface $integrationJobRepositoryInterface,
        \Magento\Catalog\Model\ResourceModel\Eav\Attribute $eavAttribute,
        \Magento\Catalog\Api\ProductAttributeManagementInterface $productAttributeManagement,
        AttributeOption $attributeOptionHelper,
        EavConfig $eavConfig,
        \Magento\Catalog\Api\Data\ProductAttributeInterfaceFactory $productAttributeFactory,
        \Magento\Catalog\Api\ProductAttributeRepositoryInterface $productAttributeRepository,
        \Trans\IntegrationCatalog\Api\IntegrationProductRepositoryInterface $integrationProductRepositoryInterface,
        CoreHelper $coreHelper,
        HelperPrice $helperPrice,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepositoryInterface,
        \Trans\IntegrationEntity\Api\IntegrationProductAttributeRepositoryInterface $integrationAttributeRepository
    ) {
        $this->logger                                   = $logger;
        $this->promotionPriceRepositoryInterface        = $promotionPriceRepositoryInterface;
        $this->promotionPriceInterfaceFactory           = $promotionPriceInterfaceFactory;
        $this->integrationDataValueRepositoryInterface  = $integrationDataValueRepositoryInterface;
        $this->validation                               = $validation;
        $this->integrationJobRepositoryInterface        = $integrationJobRepositoryInterface;
        $this->eavAttribute                             = $eavAttribute;
        $this->productAttributeManagement               = $productAttributeManagement;
        $this->attributeOptionHelper                    = $attributeOptionHelper;
        $this->eavConfig                                = $eavConfig;
        $this->productAttributeFactory                  = $productAttributeFactory;
        $this->productAttributeRepository               = $productAttributeRepository;
        $this->integrationProductRepositoryInterface    = $integrationProductRepositoryInterface;
        $this->coreHelper                               = $coreHelper;
        $this->productRepositoryInterface               = $productRepositoryInterface;
        $this->integrationAttributeRepository           = $integrationAttributeRepository;
        $this->attrGroupGeneralInfoId                   = IntegrationProductAttributeInterface::ATTRIBUTE_SET_ID;
        $this->attrGroupProductDetailId                 = $this->integrationAttributeRepository->getAttributeGroupId(IntegrationProductAttributeInterface::ATTRIBUTE_GROUP_CODE_PRODUCT);
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
            'promotion_id' => $dataAttr['promotion_id']
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
            $dataPass['item_type'] = $dataAttr['item_type'];
            $dataPass['point_per_unit'] = $dataAttr['point_per_unit'];
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
        try {
            foreach ($dataProduct as $sku => $data) {

                if (empty($sku)) {
                    continue;
                }
                try {
                    // validate sku exist or not
                    $productInterface[$index] = $this->productRepositoryInterface->get($sku);
                    if (!empty($productInterface[$index])) {
                        $indexO = 0;
                        $defaultPrice = 0;                
                        foreach ($data as $row) {
                            try {
                                $dataPass = $this->prepareDataPromoGlobal($row);
                                switch ($dataPass['promotion_type']) {
                                    // promotype = 1
                                    case 1:
                                            $this->markdownFunction($dataPass);
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
                                            $this->promoType7Function($dataPass);
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
                                $this->logger->error($msgP);
                                continue;
                            }
                        }
                    }
                    
                } catch (\Exception $exception) {
                    foreach ($data as $valueX) {
                        $msgPp = "Error Save to Magento table ".__FUNCTION__." : ".$exception->getMessage();
                        $this->updateDataValueStatus($valueX['data_id'], IntegrationDataValueInterface::STATUS_DATA_FAIL_UPDATE, $msgPp);
                        $this->logger->error($msgPp);
                    }

                    continue;
                }
                        
                // $this->logger->info("promo Updated --->".print_r($sku, true));

                $index++;
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
     * Save promotion
     * @param $dataProduct mixed
     * @return true
     */
    public function saveTest($dataProduct)
    {
        $i = 0;
        $jsonResponse = json_decode($dataProduct, true);
        
        // validate if data exist
        if (!isset($jsonResponse['data'])) {
            return false;
        }

        $datas = $jsonResponse['data'];

        foreach ($datas as $row) {
            $dataPass = $this->prepareDataPromoGlobal($row);

            switch ($dataPass['promotion_type']) {
                // promotype = 1
                case 1:
                        $this->markdownFunction($dataPass);
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
                        $this->promoType7Function($dataPass);
                    break;

                // promotype = 4
                case 4:
                        $this->promoType4Function($dataPass);
                    break;

                default:
                break;
            }

            $i++;
        }
        return true;
    }

    /**
     * function for markdown
     * @param $dataPass mixed
     * @throw new StateException
     */
    protected function markdownFunction($dataPass)
    {
        try {
            // if promo id not exist
            $checkIntPromoId = $this->checkIntegrationPromoByPromoId($dataPass['promotion_id']);

            if (!$checkIntPromoId) {
                // Nested switch discount type
                // data start time
                // $startTime = $dataPass['from_date'].' '.$dataPass['from_time'];
                $startTime = $this->timezone->date(new \DateTime())->format('Y-m-d H:i:s');
                $endTime = $dataPass['to_date'].' '.$dataPass['to_time'];
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
                
                switch ($dataPass['discount_type']) {
                    // promotype = 1 , disctype = 1
                    case 1:
                        // data name and desc
                        $name = $dataPass['promotion_id'].':'.$dataPass['promotion_type'].' - Markdown Price Rp '.$dataPass['promo_selling_price'].' fixed price ('.$dataPass['sku'].')';
                        $desc = $dataPass['promotion_id'].':'.$dataPass['promotion_type'].' - Markdown Price Rp '.$dataPass['promo_selling_price'].' fixed price ('.$dataPass['sku'].')';

                        $this->saveStagingUpdate($startTime, $endTime, $name, $desc, $isCampaign, $isRollback, $dataStoreCode);
                        break;

                    // promotype = 1 , disctype = 2
                    case 2:
                        // data name and desc
                        $name = $dataPass['promotion_id'].':'.$dataPass['promotion_type'].' - Markdown Price '.$dataPass['percent_disc'].' % Off ('.$dataPass['sku'].')';
                        $desc = $dataPass['promotion_id'].':'.$dataPass['promotion_type'].' - Markdown Price '.$dataPass['percent_disc'].' % Off ('.$dataPass['sku'].')';
                        
                        $this->saveStagingUpdate($startTime, $endTime, $name, $desc, $isCampaign, $isRollback, $dataStoreCode);
                        break;

                    // promotype = 1 , disctype = 3
                    case 3:
                        // data name and desc
                        $name = $dataPass['promotion_id'].':'.$dataPass['promotion_type'].' - Markdown Price Rp '.$dataPass['amount_off'].' Off ('.$dataPass['sku'].')';
                        $desc = $dataPass['promotion_id'].':'.$dataPass['promotion_type'].' - Markdown Price Rp '.$dataPass['amount_off'].' Off ('.$dataPass['sku'].')';

                        $this->saveStagingUpdate($startTime, $endTime, $name, $desc, $isCampaign, $isRollback, $dataStoreCode);
                        break;
                }

                // save to integration prom price table
                $dataPass['name'] = $name;
                $saveDataIntPromo = $this->saveIntegrationPromo($dataPass);
            }
            // --------------
        } catch (\Exception $e) {
            $this->logger->error("error saved markdown --->".print_r($dataPass['sku'], true));
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
            $checkIntPromoId = $this->checkIntegrationPromoByPromoId($dataPass['promotion_id']);

            if (!$checkIntPromoId) {
                $dataPass['simple_free_shipping'] = 0;
                $dataPass['discount_qty'] = $dataPass['max_promo_price_qty'];
                // Nested switch discount type
                switch ($dataPass['discount_type']) {
                    // promotype = 4 , disctype = 1
                    case 1:
                        // add dataPass array for name and desc
                        $dataPass['name'] = $dataPass['promotion_id'].':'.$dataPass['promotion_type'].' - Rp '.$dataPass['promo_selling_price'].' Fixed price for SKU '.$dataPass['sku'].' with maximum of '.$dataPass['max_promo_price_qty'].' qty per transaction ('.$dataPass['sku'].')';
                        $dataPass['desc'] = $dataPass['promotion_id'].':'.$dataPass['promotion_type'].' - Rp '.$dataPass['promo_selling_price'].' Fixed price for SKU '.$dataPass['sku'].' with maximum of '.$dataPass['max_promo_price_qty'].' qty per transaction ('.$dataPass['sku'].')';
                        $dataPass['simple_action'] = 'setof_fixed';
                        $dataPass['discount_amount'] = $dataPass['promo_selling_price'];

                        $this->saveSalesRule($dataPass);
                        break;

                    // promotype = 4 , disctype = 2
                    case 2:
                        // add dataPass array for name and desc
                        $dataPass['name'] = $dataPass['promotion_id'].':'.$dataPass['promotion_type'].' - '.$dataPass['percent_disc'].'% off for SKU '.$dataPass['sku'].' with maximum of '.$dataPass['max_promo_price_qty'].' qty per transaction ('.$dataPass['sku'].')';
                        $dataPass['desc'] = $dataPass['promotion_id'].':'.$dataPass['promotion_type'].' - '.$dataPass['percent_disc'].'% off for SKU '.$dataPass['sku'].' with maximum of '.$dataPass['max_promo_price_qty'].' qty per transaction ('.$dataPass['sku'].')';
                        $dataPass['simple_action'] = 'by_percent';
                        $dataPass['discount_amount'] = $dataPass['percent_disc'];

                        $this->saveSalesRule($dataPass);
                        break;

                    // promotype = 4 , disctype = 3
                    case 3:
                        // add dataPass array for name and desc
                        $dataPass['name'] = $dataPass['promotion_id'].':'.$dataPass['promotion_type'].' - Rp '.$dataPass['amount_off'].' amount off for SKU '.$dataPass['sku'].' with maximum of '.$dataPass['max_promo_price_qty'].' qty per transaction ('.$dataPass['sku'].')';
                        $dataPass['desc'] = $dataPass['promotion_id'].':'.$dataPass['promotion_type'].' - Rp '.$dataPass['amount_off'].' amount off for SKU '.$dataPass['sku'].' with maximum of '.$dataPass['max_promo_price_qty'].' qty per transaction ('.$dataPass['sku'].')';
                        $dataPass['simple_action'] = 'by_fixed';
                        $dataPass['discount_amount'] = $dataPass['amount_off'];

                        $this->saveSalesRule($dataPass);
                        break;
                }

                // save to integration prom price table
                $saveDataIntPromo = $this->saveIntegrationPromo($dataPass);
            }
            // --------------
        } catch (\Exception $e) {
            $this->logger->error("error saved promo type 4 --->".print_r($dataPass['sku'], true));
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

            if (!$checkIntPromoId) {
                // add dataPass array for name and desc
                $dataPass['name'] = $dataPass['promotion_id'].':'.$dataPass['promotion_type'].' - spend x amount, get item (pick only one item from multiple reward item) ('.$dataPass['sku'].')';
                $dataPass['desc'] = $dataPass['promotion_id'].':'.$dataPass['promotion_type'].' - spend x amount, get item (pick only one item from multiple reward item) ('.$dataPass['sku'].')';
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
            }
            // --------------
        } catch (\Exception $e) {
            $this->logger->error("error saved promo type 7 --->".print_r($dataPass['sku'], true));
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
            $checkIntPromoId = $this->checkIntegrationPromoByPromoId($dataPass['promotion_id']);

            if (!$checkIntPromoId) {
                // add dataPass array for name and desc
                $dataPass['stop_rules_processing'] = 0;
                $dataPass['discount_step'] = 3;
                $dataPass['simple_free_shipping'] = 0;

                switch ($dataPass['discount_type']) {
                    // promotype = 2 , disctype = 1
                    case 1:
                        // promotype = 2 , disctype = 1, promo selling price == 1
                        if ($dataPass['promo_selling_price'] == 1) {
                            $dataPass['name'] = $dataPass['promotion_id'].':'.$dataPass['promotion_type'].' - Buy Y pcs, Pay for Z pcs only (Free) ('.$dataPass['sku'].')';
                            $dataPass['desc'] = $dataPass['promotion_id'].':'.$dataPass['promotion_type'].' - Buy Y pcs, Pay for Z pcs only: same as buy '.$dataPass['normal_price_qty'].' get '.$dataPass['promo_price_qty'].' free (for the same item) ('.$dataPass['sku'].')';
                            $dataPass['simple_action'] = 'buy_x_get_y';
                            $dataPass['discount_amount'] = $dataPass['promo_selling_price'];
                        }
                        // promotype = 2 , disctype = 1, promo selling price != 1
                        else {
                            $dataPass['name'] = $dataPass['promotion_id'].':'.$dataPass['promotion_type'].' - Buy Y pcs, Pay for Z pcs only (Fixed price) ('.$dataPass['sku'].')';
                            $dataPass['desc'] = $dataPass['promotion_id'].':'.$dataPass['promotion_type'].' - Buy Y pcs, Pay for Z pcs only: same as buy '.$dataPass['normal_price_qty'].' get '.$dataPass['promo_price_qty'].' with Fixed price (for the same item) ('.$dataPass['sku'].')';
                            $dataPass['simple_action'] = 'eachn_fixprice';
                            $dataPass['discount_amount'] = $dataPass['promo_selling_price'];
                        }
                        $this->saveSalesRule($dataPass);
                        break;

                    // promotype = 2 , disctype = 2
                    case 2:
                        $dataPass['name'] = $dataPass['promotion_id'].':'.$dataPass['promotion_type'].' - Buy Y pcs, Pay for Z pcs only (Percentage) ('.$dataPass['sku'].')';
                        $dataPass['desc'] = $dataPass['promotion_id'].':'.$dataPass['promotion_type'].' - Buy Y pcs, Pay for Z pcs only: same as buy '.$dataPass['normal_price_qty'].' get '.$dataPass['promo_price_qty'].' with percentage (for the same item) ('.$dataPass['sku'].')';
                        $dataPass['simple_action'] = 'eachn_perc';
                        $dataPass['discount_amount'] = $dataPass['percent_disc'];
                        $this->saveSalesRule($dataPass);
                        break;

                    // promotype = 2 , disctype = 3
                    case 3:
                        $dataPass['name'] = $dataPass['promotion_id'].':'.$dataPass['promotion_type'].' - Buy Y pcs, Pay for Z pcs only (Amount off) ('.$dataPass['sku'].')';
                        $dataPass['desc'] = $dataPass['promotion_id'].':'.$dataPass['promotion_type'].' - Buy Y pcs, Pay for Z pcs only: same as buy '.$dataPass['normal_price_qty'].' get '.$dataPass['promo_price_qty'].' with amount off (for the same item) ('.$dataPass['sku'].')';
                        $dataPass['simple_action'] = 'eachn_fixdisc';
                        $dataPass['discount_amount'] = $dataPass['amount_off'];
                        $this->saveSalesRule($dataPass);
                        break;
                }

                // save to integration prom price table
                $saveDataIntPromo = $this->saveIntegrationPromo($dataPass);
            }
            // --------------
        } catch (\Exception $e) {
            $this->logger->error("error saved promo type 2 --->".print_r($dataPass['sku'], true));
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
            $checkIntPromoId = $this->checkIntegrationPromoByPromoId($dataPass['promotion_id']);

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
                            $sliding_child_arr = explode(",", $row);
                            $dataPass['sliding_child'] = $sliding_child_arr;
                            $dataPass['sliding_child_sequence'] = $arr;
                            $dataPass['discount_amount'] = $dataPass['sliding_child'][2];

                            // add dataPass array for name and desc
                            $dataPass['name'] = $dataPass['promotion_id'].':'.$dataPass['promotion_type'].' - Tier Price Rp '.$sliding_child_arr[2].' fixed price ('.$dataPass['sku'].')';
                            $dataPass['desc'] = $dataPass['promotion_id'].':'.$dataPass['promotion_type'].' - Tier Price Rp '.$sliding_child_arr[2].' fixed price ('.$dataPass['sku'].')';

                            $this->saveSalesRule($dataPass);
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
                            $sliding_child_arr = explode(",", $row);
                            $dataPass['sliding_child'] = $sliding_child_arr;
                            $dataPass['sliding_child_sequence'] = $arr;
                            $dataPass['discount_amount'] = $dataPass['sliding_child'][2];

                            // add dataPass array for name and desc
                            $dataPass['name'] = $dataPass['promotion_id'].':'.$dataPass['promotion_type'].' - Tier Price '.$sliding_child_arr[2].' % off ('.$dataPass['sku'].')';
                            $dataPass['desc'] = $dataPass['promotion_id'].':'.$dataPass['promotion_type'].' - Tier Price '.$sliding_child_arr[2].' % off ('.$dataPass['sku'].')';

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
                            $sliding_child_arr = explode(",", $row);
                            $dataPass['sliding_child'] = $sliding_child_arr;
                            $dataPass['sliding_child_sequence'] = $arr;
                            $dataPass['discount_amount'] = $dataPass['sliding_child'][2];

                            // add dataPass array for name and desc
                            $dataPass['name'] = $dataPass['promotion_id'].':'.$dataPass['promotion_type'].' - Tier Price Rp '.$sliding_child_arr[2].' Off ('.$dataPass['sku'].')';
                            $dataPass['desc'] = $dataPass['promotion_id'].':'.$dataPass['promotion_type'].' - Tier Price Rp '.$sliding_child_arr[2].' Off ('.$dataPass['sku'].')';

                            $this->saveSalesRule($dataPass);
                            $arr++;
                        }
                        break;
                }
                // save to integration prom price table
                $saveDataIntPromo = $this->saveIntegrationPromo($dataPass);
            }
            // --------------
        } catch (\Exception $e) {
            $this->logger->error("error saved promo type 8 --->".print_r($dataPass['sku'], true));
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

            if (!$checkIntPromoId) {
                $dataPass['discount_step'] = 3;
                $dataPass['simple_free_shipping'] = 0;
                $dataPass['item_type'] = $dataPass['item_type'];
                $dataPass['mix_and_match_code'] = $dataPass['mix_and_match_code'];
                $dataPass['point_per_unit'] = ($dataPass['point_per_unit'] == 0) ? 1 : $dataPass['point_per_unit'] ;

                // Nested switch discount type
                switch ($dataPass['discount_type']) {
                    // promotype = 5 , disctype = 1
                    case 1:
                        $dataPass['name'] = $dataPass['promotion_id'].':'.$dataPass['promotion_type'].' - Buy X qty of A, Get Y qty of B with only IDR '.$dataPass['promo_selling_price'].' - '.$dataPass['promotion_id'].'';
                        $dataPass['desc'] = $dataPass['promotion_id'].':'.$dataPass['promotion_type'].' - Buy X qty of A, Get Y qty of B with only IDR '.$dataPass['promo_selling_price'].' - '.$dataPass['promotion_id'].'';
                        $dataPass['simple_action'] = 'buyxgetn_fixprice';
                        $dataPass['discount_amount'] = $dataPass['promo_selling_price'];
                        
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
                                // update condition serialize , save to salesrule
                                $this->updateSalesRule($dataPass);
                            } else {
                                // save to integration promo for item type 2 , first
                                $dataPass['item_type'] = 2;
                                $saveDataIntPromo = $this->saveIntegrationPromo($dataPass);
                            }
                        }
                        break;

                    // promotype = 5 , disctype = 2
                    case 2:

                        $dataPass['name'] = $dataPass['promotion_id'].':'.$dataPass['promotion_type'].' - Buy X qty of A, Get Y qty of B with '.$dataPass['percent_disc'].'% off - '.$dataPass['promotion_id'].'';
                        $dataPass['desc'] = $dataPass['promotion_id'].':'.$dataPass['promotion_type'].' - Buy X qty of A, Get Y qty of B with '.$dataPass['percent_disc'].'% off - '.$dataPass['promotion_id'].'';
                        $dataPass['simple_action'] = 'buyxgetn_perc';
                        $dataPass['discount_amount'] = $dataPass['percent_disc'];
                        
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
                                // update condition serialize , save to salesrule
                                $this->updateSalesRule($dataPass);
                            } else {
                                // save to integration promo for item type 2 , first
                                $dataPass['item_type'] = 2;
                                $saveDataIntPromo = $this->saveIntegrationPromo($dataPass);
                            }
                        }
                        break;

                    // promotype = 5 , disctype = 3
                    case 3:
                        $dataPass['name'] = $dataPass['promotion_id'].':'.$dataPass['promotion_type'].' - Buy X qty of A, Get Y qty of B with '.$dataPass['amount_off'].' amount off - '.$dataPass['promotion_id'].'';
                        $dataPass['desc'] = $dataPass['promotion_id'].':'.$dataPass['promotion_type'].' - Buy X qty of A, Get Y qty of B with '.$dataPass['amount_off'].' amount off - '.$dataPass['promotion_id'].'';
                        $dataPass['simple_action'] = 'buyxgetn_fixdisc';
                        $dataPass['discount_amount'] = $dataPass['amount_off'];
                       
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
                            // get salesrule_id from item type 1
                            $dataPass['item_type'] = 1;
                            $checkSalesRule = $this->checkIntegrationPromo($dataPass);
                            if ($checkSalesRule) {
                                // get salesruleid
                                $dataPass['salesrule_id'] = $checkSalesRule->getData('salesrule_id');
                                $dataPass['row_id'] = $checkSalesRule->getData('row_id');
                                // save to integration promo and back to original value for item type
                                $dataPass['item_type'] = 2;
                                $saveDataIntPromo = $this->saveIntegrationPromo($dataPass);
                                // update condition serialize , save to salesrule
                                $this->updateSalesRule($dataPass);
                            } else {
                                // save to integration promo for item type 2 , first
                                $dataPass['item_type'] = 2;
                                $saveDataIntPromo = $this->saveIntegrationPromo($dataPass);
                            }
                        }
                        break;
                }
            }
            // --------------
        } catch (\Exception $e) {
            $this->logger->error("error saved promo type 5 --->".print_r($dataPass['sku'], true));
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

            $result = $this->promotionPriceRepositoryInterface->save($query);

            $this->logger->info("saveIntegrationPromo saved [" . $data['name'] ."]");
        } catch (\Exception $e) {
            $this->logger->error("<=End saveIntegrationPromo" .$e->getMessage());
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
            $this->logger->error("<=End checkIntegrationPromo" .$e->getMessage());
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
            $this->logger->error("<=End checkIntegrationPromo" .$e->getMessage());
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
            $this->logger->error("<=End checkIntegrationPromoSku" .$e->getMessage());
            throw new StateException(
                __(__FUNCTION__." - ".$e->getMessage())
            );
        }

        return $query;
    }

    /**
     * Save to staging update
     * @param $startTime datetime
     * @param $endTime datetime
     * @param $name string
     * @param $desc string
     * @param $isCampaign int
     * @param $isRollback int
     * @param $dataStoreCode mixed
     * @return $data mixed
     * @throw logger error
     */
    protected function saveStagingUpdate($startTime, $endTime, $name, $desc, $isCampaign, $isRollback, $dataStoreCode)
    {
        try {
            //Convert to store timezone
            $created = new \DateTime($startTime);
            $createdAsString = $created->modify('-7 hours')->format('Y-m-d H:i:s');
            $lasted = new \DateTime($endTime);
            $lastedAsString = $lasted->modify('-7 hours')->format('Y-m-d H:i:s');

            // save new schedule
            $schedule = $this->updateInterfaceFactory->create();
            $schedule->setName($name);
            $schedule->setDescription($desc); 
            $schedule->setStartTime($createdAsString);
            $schedule->setEndTime($lastedAsString);
            $schedule->setIsCampaign($isCampaign);
            $stagingRepo = $this->updateRepositoryInterface->save($schedule);
            $this->versionManager->setCurrentVersionId($stagingRepo->getId());

            // save promo price by schedule
            $dataStoreCode['staging_id'] = $stagingRepo->getId();
            $dataStoreCode_arr = explode(",", $dataStoreCode['store_code']);
            foreach ($dataStoreCode_arr as $dataStore) {
                $dataStoreCode['store_attr_code'] = $dataStore;
                // save special price
                $this->saveBasePriceStorePrice($dataStoreCode);
            }

            $this->logger->info("staging_update saved [" . $name ."]");
        } catch (\Exception $e) {
            $this->logger->error("<=End staging_update" .$e->getMessage());
            throw new StateException(
                __(__FUNCTION__." - ".$e->getMessage())
            );
        }

        return $stagingRepo;
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

            $statusResponse->setName($dataPass['name'])
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
            ->setDiscountStep($dataPass['discount_step'])
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
                // save to amasty_amrule
                $this->saveAmastySalesRule($getSalesruleId, $skuAmasty, $promoTypeAmasty);

                // return data
                $resultData = [
                    'rule_id' => $statusResponse->getData('rule_id'),
                    'row_id' => $statusResponse->getData('row_id')
                ];
            }
        } catch (\Exception $e) {
            $this->logger->error("<=End salesrule" .$e->getMessage());
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
                ->setOperator('>=')
                ->setValue($dataPass['promo_price_qty']);
            $item_found->addCondition($conditions);
           

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
            $item_found = $this->foundProductRuleFactory->create()
                ->setType('Magento\SalesRule\Model\Rule\Condition\Product\Subselect')
                ->setAttribute('qty')
                ->setOperator('>=')
                ->setValue($dataPass['point_per_unit'])
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
                    $item_found = $this->foundProductRuleFactory->create()
                        ->setType('Magento\SalesRule\Model\Rule\Condition\Product\Subselect')
                        ->setAttribute('qty')
                        ->setOperator('>=')
                        ->setValue($dataPass['point_per_unit'])
                        ->setAggregator('all');
                    $statusResponse->getConditions()->addCondition($item_found);

                    $conditions = $this->foundProductRuleFactory->create()
                        ->setType('Magento\SalesRule\Model\Rule\Condition\Product')
                        ->setAttribute('sku')
                        ->setOperator('==')
                        ->setValue($dataPass['sku']);
                    $item_found->addCondition($conditions);
                    $result = $this->ruleResource->save($statusResponse);
                } else {
                    $actionqtyCond = $this->foundProductRuleFactory->create()
                        ->setType('Magento\SalesRule\Model\Rule\Condition\Product')
                        ->setAttribute('sku')
                        ->setOperator('==')
                        ->setValue($dataPass['sku']);
                    $statusResponse->getActions()->addCondition($actionqtyCond);
                    $result = $this->ruleResource->save($statusResponse);

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
            // --------------
        } catch (\Exception $e) {
            $this->logger->error("<=End update salesrule" .$e->getMessage());
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
                                if (array_key_exists('value', $ruleDataArray)) {
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
            $this->logger->error("<=End update salesrule" .$e->getMessage());
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
     * @return $result mixed
     * @throw logger error
     */
    protected function saveAmastySalesRule($dataPass, $skuAmasty, $promoTypeAmasty)
    {
        try {
            $statusResponse = $this->amastyRuleInterfaceFactory->create();

            $statusResponse->setSalesruleId($dataPass)
                ->setPriceselector(0)
                ->setSkipRule(0)
                ->setApplyDiscountTo('asc')
                ->setUseFor(0);

            if ($promoTypeAmasty == 5) {
                $statusResponse->setPromoSkus(" ");
            }

            if ($promoTypeAmasty != 4) {
                if ($promoTypeAmasty != 7) {
                    $statusResponse->setNqty(2);
                }
            }

            $result = $this->amastyRuleInterface->save($statusResponse);

            $this->logger->info("amasty_amrules_rule saved [" . $dataPass ."]");
        } catch (\Exception $e) {
            $this->logger->error("<=End amasty_amrules_rule" .$e->getMessage());
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

                // count sku for nqty
                $skuCount = explode(',' , $sku);

                $statusResponse->setData('promo_skus', $sku);
                $statusResponse->setData('nqty', count($skuCount));
                $statusResponse->setData('salesrule_id', $dataPass['row_id']);

                $result = $statusResponse->save();
            }

            $this->logger->info("amasty_amrules_rule update");
        } catch (\Exception $e) {
            $this->logger->error("<=End amasty_amrules_rule" .$e->getMessage());
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
            $this->logger->error("<=End amasty_ampromo_rule" .$e->getMessage());
            throw new StateException(
                __(__FUNCTION__." - ".$e->getMessage())
            );
            
        }

        return $result;
    }

    /**
     * special Price Logic Validate & Create Attribute
     * @param mixed $param
     * @return array $specialPriceCodeId
     */
    protected function validateStorePrice($param)
    {
        try {
            $specialPriceCode      = PromotionPriceLogicInterface::PRODUCT_ATTR_SPECIAL_PRICE.$param['store_attr_code'];
            $specialPriceCodeId    = $this->saveAttributeProduct($specialPriceCode);
        } catch (\Exception $exception) {
            throw new StateException(
                __(__FUNCTION__." - ".$exception->getMessage())
            );
        }
        
        return $specialPriceCodeId;
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
     * Create New Attribute
     *
     * @param $attributeCode string
     * @return
     */
    protected function createAttributeProduct($attributeCode = "")
    {
        try {
            $frontentInput    = PromotionPriceLogicInterface::INPUT_TYPE_FRONTEND_FORMAT_PRICE;
            $backendInput     = PromotionPriceLogicInterface::INPUT_TYPE_BACKEND_FORMAT_PRICE;
            
            $attributeValue = $this->productAttributeFactory->create();
            
            
            $attributeValue->setPosition(PromotionPriceLogicInterface::POSITION);
            $attributeValue->setApplyTo(PromotionPriceLogicInterface::APPLY_TO);
            $attributeValue->setIsVisible(PromotionPriceLogicInterface::IS_VISIBLE);
            
            $attributeValue->setScope(PromotionPriceLogicInterface::SCOPE);
            $attributeValue->setAttributeCode($attributeCode);
            $attributeValue->setFrontendInput($frontentInput);
            $attributeValue->setEntityTypeId(PromotionPriceLogicInterface::ENTITY_TYPE_ID);
            $attributeValue->setIsRequired(PromotionPriceLogicInterface::IS_REQUIRED);
            $attributeValue->setIsUserDefined(PromotionPriceLogicInterface::IS_USER_DEFINED);
            $attributeValue->setDefaultFrontendLabel($attributeCode);
            $attributeValue->setBackendType($backendInput);
            $attributeValue->setDefaultValue(0);
            $attributeValue->setIsUnique(PromotionPriceLogicInterface::IS_UNIQUE);

            // Smart OSC Required
            $attributeValue->setIsSearchable(PromotionPriceLogicInterface::IS_SEARCHBLE);
            $attributeValue->setIsFilterable(PromotionPriceLogicInterface::IS_FILTERABLE);
            $attributeValue->setIsComparable(PromotionPriceLogicInterface::IS_COMPARABLE);
            $attributeValue->setIsHtmlAllowedOnFront(PromotionPriceLogicInterface::IS_HTML_ALLOWED_ON_FRONT);
            $attributeValue->setIsVisibleOnFront(PromotionPriceLogicInterface::IS_VISIBLE_ON_FRONT);
            $attributeValue->setIsFilterableInSearch(PromotionPriceLogicInterface::IS_FILTERABLE_IN_SEARCH);
            $attributeValue->setUsedInProductListing(PromotionPriceLogicInterface::USED_IN_PRODUCT_LISTING);
            $attributeValue->setUsedForSortBy(PromotionPriceLogicInterface::USED_FOR_SORT_BY);
            $attributeValue->setIsVisibleInAdvancedSearch(PromotionPriceLogicInterface::IS_VISIBLE_IN_ADVANCED_SEARCH);
            $attributeValue->setIsWysiwygEnabled(PromotionPriceLogicInterface::IS_WYSIWYG_ENABLED);
            $attributeValue->setIsUsedForPromoRules(PromotionPriceLogicInterface::IS_USED_FOR_PROMO_RULES);
            // $attributeValue->setIsRequiredInAdminStore(StorePriceLogicInterface::IS_USED_FOR_PROMO_RULES);
            $attributeValue->setIsUsedInGrid(PromotionPriceLogicInterface::IS_USED_IN_GRID);
            $attributeValue->setIsVisibleInGrid(PromotionPriceLogicInterface::IS_VISIBLE_IN_GRID);
            $attributeValue->setIsFilterableInGrid(PromotionPriceLogicInterface::IS_FILTERABLE_IN_GRID);
            // $attributeValue->setIsPagebuilderEnable();
            
            $attributeValue->setIsUsedForPriceRules(PromotionPriceLogicInterface::IS_USED_FOR_PRICE_RULES);
            
            $this->productAttributeRepository->save($attributeValue);
            
            //Set Attribute to Attribute Set (Default)
            $this->productAttributeManagement->assign($this->attrGroupGeneralInfoId, $this->attrGroupProductDetailId, $attributeCode, PromotionPriceLogicInterface::SORT_ORDER);
        } catch (\Exception $exception) {
            throw new StateException(
                __(__FUNCTION__." - ".$exception->getMessage())
            );
        }
    }

    /**
     * save base price
     * @param mixed $param
     * @return array $result
     */
    protected function saveBasePriceStorePrice($param)
    {
        try {
            // get base price
            $basePriceCode = PromotionPriceLogicInterface::PRODUCT_ATTR_BASE_PRICE.$param['store_attr_code'];
            $product = $this->productRepositoryInterface->get($param['sku']);
            $productPriceBySku = $product->getCustomAttribute($basePriceCode) ? $product->getCustomAttribute($basePriceCode)->getValue() : 0;
            $getId = $product->getId();
            $dataBase = [
                'base_price' => $productPriceBySku,
                'id' => $getId,
            ];
            $this->logger->info("getBasePriceStorePrice [".$basePriceCode."][" . $productPriceBySku ."][ ID is ". $getId ."] saved");

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
            $product->setCustomAttribute($specialPriceCode, $specialPrice);
            
            $result = $this->productStaging->schedule($product, $param['staging_id']);

            $this->logger->info("saveBasePriceStorePrice [" . $specialPriceCode ."][" . $specialPrice ."] saved");
            
            return $result;
        } catch (\Exception $exception) {
            throw new StateException(
                __(__FUNCTION__." - ".$exception->getMessage())
            );
        }
    }
}
