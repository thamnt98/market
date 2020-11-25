<?php
/**
 * @category Trans
 * @package  Trans_IntegrationEntity
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Dwi Septha Kurniawan <septha.kurniawan@transdigital.co.id>
 * @modify   J.P <jaka.pondan@transdigital.co.id>
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationEntity\Model;


use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;

use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Magento\Catalog\Api\ProductAttributeManagementInterface;
use Magento\Catalog\Api\Data\ProductAttributeInterfaceFactory;
// use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute as eavAttribute;
use Magento\Eav\Api\Data\AttributeInterface;
use Magento\Eav\Model\ResourceModel\Entity\Attribute as EavAttributeResource;

use Trans\IntegrationEntity\Api\Data\IntegrationJobInterface;
use Trans\IntegrationEntity\Api\Data\IntegrationDataValueInterface;
use Trans\IntegrationEntity\Api\IntegrationProductAttributeInterface;
use Trans\IntegrationEntity\Api\IntegrationDataValueRepositoryInterface;
use Trans\IntegrationEntity\Api\IntegrationJobRepositoryInterface;
use Trans\Integration\Helper\Curl;
use Trans\Integration\Helper\Validation;
use Trans\Integration\Helper\AttributeOption;

Use Trans\IntegrationEntity\Api\IntegrationProductAttributeRepositoryInterface;
use \Trans\IntegrationEntity\Api\Data\IntegrationProductAttributeTypeInterface;

/**
 * @inheritdoc
 */
class IntegrationProductAttribute implements IntegrationProductAttributeInterface {

	const VAR_VARCHAR = 'varchar';
	const VAR_TEXT    = 'text';
	const VAR_INT     = 'int';
	const VAR_SELECT  = 'select';
	const VAR_SWATCH  = 'swatch';	

	/**
	 * @var \Trans\Integration\Logger\Logger
	 */
	protected $logger;
	
	/**
	 * @var IntegrationJobRepositoryInterface 
	 */
	protected $integrationJobRepositoryInterface;
	
	/**
	 * @var ProductAttributeInterfaceFactory 
	 */
	protected $productAttributeFactory;	

	/**
	 * @var IntegrationDataValueRepositoryInterface 
	 */
	protected $integrationDataValueRepositoryInterface;

	/**
	 * @var ProductAttributeRepositoryInterface 
	 */
	protected $productAttributeRepository;
	
	/**
	 * @var ProductAttributeManagementInterface 
	 */
	protected $productAttributeManagement;	

	/**
	 * @var Curl 
	 */
	protected $curl;

	/**
	 * @var Validation 
	 */
	protected $validation;

	/**
	 * @var AttributeOption
	 */
	protected $attributeOptionHelper;

	/**
	 * @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute 
	 */
	protected $eavAttribute;

	/**
	 * @var IntegrationProductAttributeRepositoryInterface
	 */
	protected $integrationAttributeRepository;

	/**
	 * @var Attribute Group For General Information
	 */
	protected $attrGroupGeneralInfoId;

	/**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory
     */
    protected $attrOptionCollectionFactory;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;

	/**
	 * @var Attribute Group For Product Detail
	 */
	protected $attrGroupProductDetailId;

	/**
	 * @var \Trans\IntegrationEntity\Api\Data\IntegrationProductAttributeSetInterface 
	 */
	protected $integrationProductAttributeSetInterface;

	/**
	 * @var \Trans\IntegrationEntity\Api\Data\IntegrationProductAttributeSetChildInterface 
	 */
	protected $integrationProductAttributeSetChildInterface;

	/**
	 * @var \Trans\IntegrationEntity\Api\Data\IntegrationProductAttributeSetInterfaceFactory 
	 */
	protected $integrationProductAttributeSetInterfaceFactory;

	/**
	 * @var \Trans\IntegrationEntity\Api\Data\IntegrationProductAttributeSetChildInterfaceFactory 
	 */
	protected $integrationProductAttributeSetChildInterfaceFactory;

	/**
	 * @var \Magento\Eav\Api\Data\AttributeSetInterface
	 */
	protected $attributeSetInterface;

	/**
	 * @var \Magento\Eav\Api\Data\AttributeSetInterfaceFactory
	 */
	protected $attributeSetInterfaceFactory;

	/**
	 * @var \Magento\Eav\Model\Entity\TypeFactory
	 */
	protected $entityType;

	/**
	 * @var \Magento\Catalog\Model\ProductFactory
	 */
	protected $productFactory;

	/**
	 * @var \Magento\Eav\Api\AttributeSetRepositoryInterface
	 */
	protected $attributeSetRepositoryInterface;

	/**
	 * @var \Magento\Eav\Api\AttributeRepositoryInterface
	 */
	protected $attributeRepositoryInterface;
	

	/**
     * @param \Trans\Integration\Logger\Logger $logger
     * @param ProductAttributeRepositoryInterface $productAttributeRepository
     * @param IntegrationJobRepositoryInterface $integrationJobRepositoryInterface
     * @param ProductAttributeInterfaceFactory $productAttributeFactory	
	 * @param IntegrationDataValueRepositoryInterface $integrationDataValueRepositoryInterface
	 * @param ProductAttributeManagementInterface $productAttributeManagement
	 * @param Curl $curl
	 * @param Validation $validation
	 * @param AttributeOption $attributeOptionHelper
	 * @param \Magento\Catalog\Model\ResourceModel\Eav\Attribute $eavAttribute
	 * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $attrOptionCollectionFactory
     * @param \Magento\Eav\Model\Config $eavConfig
	 * @param IntegrationProductAttributeRepositoryInterface
	 * @param \Trans\IntegrationEntity\Api\Data\IntegrationProductAttributeSetInterface $integrationProductAttributeSetInterface
	 * @param \Trans\IntegrationEntity\Api\Data\IntegrationProductAttributeSetInterfaceFactory $integrationProductAttributeSetInterfaceFactory
	 * @param \Magento\Eav\Api\Data\AttributeSetInterface $attributeSetInterface
	 * @param \Magento\Eav\Api\Data\AttributeSetInterfaceFactory $attributeSetInterfaceFactory
	 * @param \Magento\Eav\Model\Entity\TypeFactory $entityType
	 * @param \Magento\Catalog\Model\ProductFactory $productFactory
	 * @param \Magento\Eav\Api\AttributeSetRepositoryInterface $attributeSetRepositoryInterface
	 * @param \Magento\Eav\Api\AttributeRepositoryInterface $attributeRepositoryInterface
	 * @param AttributeInterface $eavAttributeInterface
	 * @param \Trans\IntegrationEntity\Api\Data\IntegrationProductAttributeSetChildInterface $integrationProductAttributeSetChildInterface
	 * @param \Trans\IntegrationEntity\Api\Data\IntegrationProductAttributeSetChildInterfaceFactory $integrationProductAttributeSetChildInterfaceFactory
     */
	public function __construct
	(	
		\Trans\Integration\Logger\Logger $logger,
        ProductAttributeRepositoryInterface $productAttributeRepository,
        ProductAttributeInterfaceFactory $productAttributeFactory,
		IntegrationJobRepositoryInterface $integrationJobRepositoryInterface,
		IntegrationDataValueRepositoryInterface $integrationDataValueRepositoryInterface,
		ProductAttributeManagementInterface $productAttributeManagement,
		Curl $curl,
		Validation $validation,
		AttributeOption $attributeOptionHelper,
		\Magento\Catalog\Model\ResourceModel\Eav\Attribute $eavAttribute,
		\Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $attrOptionCollectionFactory,
        \Magento\Eav\Model\Config $eavConfig,
        EavAttributeResource $eavAttributeResource,
		IntegrationProductAttributeRepositoryInterface $integrationAttributeRepository,
		\Trans\IntegrationEntity\Api\Data\IntegrationProductAttributeSetInterface $integrationProductAttributeSetInterface,
		\Trans\IntegrationEntity\Api\Data\IntegrationProductAttributeSetInterfaceFactory $integrationProductAttributeSetInterfaceFactory,
		\Magento\Eav\Api\Data\AttributeSetInterface $attributeSetInterface,
		\Magento\Eav\Api\Data\AttributeSetInterfaceFactory $attributeSetInterfaceFactory,
		\Magento\Eav\Model\Entity\TypeFactory $entityType,
		\Magento\Catalog\Model\ProductFactory $productFactory,
		\Magento\Eav\Api\Data\AttributeInterfaceFactory $eavAttributeInterface,
		\Magento\Eav\Api\AttributeSetRepositoryInterface $attributeSetRepositoryInterface,
		\Magento\Eav\Api\AttributeRepositoryInterface $attributeRepositoryInterface,
		\Trans\IntegrationEntity\Api\Data\IntegrationProductAttributeSetChildInterface $integrationProductAttributeSetChildInterface,
		\Trans\IntegrationEntity\Api\Data\IntegrationProductAttributeSetChildInterfaceFactory $integrationProductAttributeSetChildInterfaceFactory
	) {	

		$this->attrOptionCollectionFactory = $attrOptionCollectionFactory;
        $this->eavAttributeInterface = $eavAttributeInterface;
        $this->eavAttributeResource = $eavAttributeResource;
        $this->eavConfig = $eavConfig;
        $this->logger = $logger;
        $this->productAttributeRepository = $productAttributeRepository;
        $this->productAttributeManagement = $productAttributeManagement;
        $this->productAttributeFactory = $productAttributeFactory;
		$this->curl = $curl;
		$this->validation = $validation;
		$this->attributeOptionHelper = $attributeOptionHelper;
		$this->integrationDataValueRepositoryInterface = $integrationDataValueRepositoryInterface;
		$this->integrationJobRepositoryInterface = $integrationJobRepositoryInterface;
		$this->eavAttribute = $eavAttribute;
		$this->integrationAttributeRepository = $integrationAttributeRepository;

		$this->attrGroupGeneralInfoId = IntegrationProductAttributeInterface::ATTRIBUTE_SET_ID;
		$this->attrGroupProductDetailId = $this->integrationAttributeRepository->getAttributeGroupId(IntegrationProductAttributeInterface::ATTRIBUTE_GROUP_CODE_PRODUCT);

		$this->integrationProductAttributeSetInterface = $integrationProductAttributeSetInterface;
		$this->integrationProductAttributeSetInterfaceFactory = $integrationProductAttributeSetInterfaceFactory;
		$this->attributeSetInterface = $attributeSetInterface;
		$this->attributeSetInterfaceFactory = $attributeSetInterfaceFactory;
		$this->entityType = $entityType;
		$this->productFactory = $productFactory;
		$this->attributeSetRepositoryInterface = $attributeSetRepositoryInterface;
		$this->attributeRepositoryInterface = $attributeRepositoryInterface;
		$this->integrationProductAttributeSetChildInterface = $integrationProductAttributeSetChildInterface;
		$this->integrationProductAttributeSetChildInterfaceFactory = $integrationProductAttributeSetChildInterfaceFactory;

		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/integration_attribute.log');
        $logger = new \Zend\Log\Logger();
        $this->logger = $logger->addWriter($writer);
	}

	/**
     * Prepare Data
     * @param array $channel
     * @return mixed
     */
	public function prepareData($channel = []) {
		if (empty($channel)) {
			throw new StateException(__(
				'Parameter Channel are empty !'
			));
		}
		$jobs      = $channel['jobs']->getFirstItem();

		if($jobs instanceof \Trans\IntegrationEntity\Api\Data\IntegrationJobInterface) {
			$jobId     = (int) $jobs->getId();
			$jobStatus = (int) $jobs->getStatus();
			$status    = IntegrationProductAttributeInterface::STATUS_JOB;
	
			if ($jobStatus != IntegrationJobInterface::STATUS_READY) {
				throw new NoSuchEntityException(__('Data already updated. $jobStatus = ' . $jobStatus));
			}
			
			$result = $this->integrationDataValueRepositoryInterface->getByJobIdWithStatus($jobId, $status);
			if(!$result->getSize()){
				$dataJobs = $this->integrationJobRepositoryInterface->getById($jobId);
				$dataJobs->setMessage(IntegrationJobInterface::MSG_DATA_NOTAVAILABLE);
				$dataJobs->setStatus(IntegrationJobInterface::STATUS_PROGRESS_FAIL);
				$this->integrationJobRepositoryInterface->save($dataJobs);

				throw new StateException(__('Requested Data Config doesn\'t exist'));
			}
			
		} else {
			throw new \Exception("Job data doesnt exists");
		}

		return $result;
	}

	/**
	 * generate attribute code by name
	 *
	 * @param string $string
	 * @return string
	 */
	protected function generateAttrCodeByString(string $string)
	{
	   $string = strtolower(str_replace(' ', '_', $string)); // Replaces all spaces with hyphens.
	   $string = strtolower(str_replace('-', '_', $string)); // Replaces all spaces with hyphens.

	   return preg_replace('/[^A-Za-z0-9\_]/', '', $string); // Removes special chars.
	}

	/**
     * Save Stock
     * @param array $datas
     * @return mixed
     */
	public function save($datas)
	{
		$this->logger->info("Save Attribute Start.");
		$dataAttribute = [];

		$jobId    = $datas->getFirstItem()->getJbId();
		$this->updateJobData($jobId,IntegrationJobInterface::STATUS_PROGRESS_CATEGORY);

		try {
			$i 					= 0;
			$pimIsDeleted 		= [];
			$pimDataAttribute 	= [];
			$pimAttrCode 		= [];
			$pimAttrOption		= [];
			$pimAttrType		= [];
			$pimLabelName		= [];

			$attributeId		= [];
			$queryAttribute 	= [];
			$msgDataValue		= [];
			$attrTypeData		= [];
			$attributeDefault = 0;
			
			foreach ($datas as $data) {
				try {
					$pimDataAttribute[$i] = $this->curl->jsonToArray($data->getDataValue());

					// $pimAttrCode[$i] = str_replace(" ","_",strtolower($this->validation->validateArray(IntegrationProductAttributeInterface::PIM_LABEL, $pimDataAttribute[$i])));
					$pimAttrCode[$i] = $pimDataAttribute[$i]['code'] ? $this->generateAttrCodeByString($pimDataAttribute[$i]['code']) : $this->generateAttrCodeByString($pimDataAttribute[$i]['name']);
					$this->logger->info("Data Value = " . $data->getDataValue());
					
					$pimIsDeleted[$i] = $this->validation->validateArray(IntegrationProductAttributeInterface::PIM_DELETED, $pimDataAttribute[$i] );

					$attributeId[$i] = $this->checkAttributeIdExist($pimAttrCode[$i]);
					
					if ($pimIsDeleted[$i] > 0) {
						$this->logger->info("Deleting Attribute Code = " . $pimAttrCode[$i]);
						// Check data
						if($attributeId[$i]>0){
							// Delete Attr Data
							try {
								$this->productAttributeRepository->deleteById($attributeId[$i]);
								$this->saveStatusMessage($data, IntegrationProductAttributeInterface::MSG_ATTRIBUTE_DELETED, IntegrationDataValueInterface::STATUS_DATA_SUCCESS);
							} catch (CouldNotSaveException $e) {
								$this->logger->info("Error delete Attribute Code = " . $pimAttrCode[$i] . ". Message = " . $e->getMessage());
								continue;
							} catch (\Exception $e) {
								$this->logger->info("Error delete Attribute Code = " . $pimAttrCode[$i] . ". Message = " . $e->getMessage());
								continue;
							}
						} else {
							$this->logger->info("No data found. Attribute Code = " . $pimAttrCode[$i]);
							$this->saveStatusMessage($data, "Deleteing fail. No attribute data found in magento. Attribute Code = " . $pimAttrCode[$i], IntegrationDataValueInterface::STATUS_DATA_FAIL_UPDATE);		
						}
					} else {
						$this->logger->info("Create/Update Attribute Code = " . $pimAttrCode[$i]);

						$pimAttrOption[$i]  = array_filter($this->validation->validateArray(IntegrationProductAttributeInterface::PIM_OPTION, $pimDataAttribute[$i]));
						$pimLabelName[$i]   = $this->validation->validateArray(IntegrationProductAttributeInterface::PIM_LABEL, $pimDataAttribute[$i]);

						$pimAttrType[$i]    = $this->validation->validateArray(IntegrationProductAttributeInterface::PIM_ATTRIBUTE_TYPE, $pimDataAttribute[$i]);
						$attrTypeData[$i] = $this->integrationAttributeRepository->getAttributeTypeMap($pimAttrType[$i]);

						// Insert Data by Id attribute
						$queryAttribute[$i] = $this->productAttributeFactory->create();

						$msgDataValue[$i] = IntegrationProductAttributeInterface::MSG_ATTRIBUTE_NEW;
						// Check data
						if ($attributeId[$i] > 0) {
							// Update Data by Id attribute if data attr exist
							$queryAttribute[$i]->setAttributeId($attributeId[$i]);
							$msgDataValue[$i] = IntegrationProductAttributeInterface::MSG_ATTRIBUTE_UPDATE;
						}
						
						$queryAttribute[$i]->setIsHtmlAllowedOnFront(IntegrationProductAttributeInterface::IS_HTML_ALLOWED_ON_FRONT);
						$queryAttribute[$i]->setIsUsedInGrid(IntegrationProductAttributeInterface::IS_USED_IN_GRID);
						$queryAttribute[$i]->setIsVisibleInGrid(IntegrationProductAttributeInterface::IS_VISIBLE_IN_GRID);
						$queryAttribute[$i]->setIsFilterableInGrid(IntegrationProductAttributeInterface::IS_FILTERABLE_IN_GRID);
						$queryAttribute[$i]->setPosition(IntegrationProductAttributeInterface::POSITION);
						$queryAttribute[$i]->setApplyTo(IntegrationProductAttributeInterface::APPLY_TO);
						$queryAttribute[$i]->setIsVisible(IntegrationProductAttributeInterface::IS_VISIBLE);
						$queryAttribute[$i]->setScope(IntegrationProductAttributeInterface::SCOPE);
						$queryAttribute[$i]->setAttributeCode($pimAttrCode[$i]);

						$queryAttribute[$i]->setFrontendInput($attrTypeData[$i][IntegrationProductAttributeTypeInterface::FRONTEND_CODE]);
						if ((int) $attrTypeData[$i][IntegrationProductAttributeTypeInterface::IS_SWATCH] == 1) {
							$queryAttribute[$i]->setFrontendInput('select');
						}
						
						$queryAttribute[$i]->setEntityTypeId(IntegrationProductAttributeInterface::ENTITY_TYPE_ID);
						$queryAttribute[$i]->setIsRequired(IntegrationProductAttributeInterface::IS_REQUIRED);
						$queryAttribute[$i]->setIsUserDefined(IntegrationProductAttributeInterface::IS_USER_DEFINED);
						$queryAttribute[$i]->setDefaultFrontendLabel(ucwords($pimLabelName[$i]));

						$queryAttribute[$i]->setBackendType($attrTypeData[$i][IntegrationProductAttributeTypeInterface::BACKEND_CODE]);
						
						if($attrTypeData[$i][IntegrationProductAttributeTypeInterface::PIM_TYPE_CODE] === 'yes_no') {
							$queryAttribute[$i]->setSourceModel('Magento\Eav\Model\Entity\Attribute\Source\Boolean');
						}

						$queryAttribute[$i]->setIsUnique(IntegrationProductAttributeInterface::IS_UNIQUE);
						
						if (!empty($pimAttrOption[$i])) {
							if (isset($pimAttrOption[$i][0])) {
								$queryAttribute[$i]->setDefaultValue($attributeDefault);
							} else {
								$queryAttribute[$i]->setDefaultValue("");
							}
						}
						
						if ((int) $attrTypeData[$i][IntegrationProductAttributeTypeInterface::IS_SWATCH] == 1) {
							$queryAttribute[$i]->setSwatchInputType($attrTypeData[$i][IntegrationProductAttributeTypeInterface::BACKEND_CODE]);
						}

						if($queryAttribute[$i] instanceof AttributeInterface && $queryAttribute[$i]->getId()) {
							try {
								//update frontend_input direct with SQL
								$conn = $this->eavAttributeResource->getConnection();
								$query = "UPDATE " . $conn->getTableName('eav_attribute') . " SET frontend_input = '" . $attrTypeData[$i][IntegrationProductAttributeTypeInterface::FRONTEND_CODE] . "' WHERE attribute_id = " . $queryAttribute[$i]->getId();
        						$conn->query($query);
							} catch (\Exception $e) {
								$this->logger->info("Error update Attribute frontend_input = " . $queryAttribute[$i]->getAttributeCode() . ". Message = " . $e->getMessage());
							}
						}
						
						try {
							$this->productAttributeRepository->save($queryAttribute[$i]);
						} catch (CouldNotSaveException $e) {
							$this->logger->info("CouldNotSaveException Error save Attribute Code = " . $pimAttrCode[$i] . ". Message = " . $e->getMessage());
							$this->saveStatusMessage($data, $$e->getMessage(), IntegrationDataValueInterface::STATUS_DATA_FAIL_UPDATE);		
							continue;
						} catch (NoSuchEntityException $e) {
							$this->logger->info("NoSuchEntityException Error save Attribute Code = " . $pimAttrCode[$i] . ". Message = " . $e->getMessage());
							$this->saveStatusMessage($data, $$e->getMessage(), IntegrationDataValueInterface::STATUS_DATA_FAIL_UPDATE);		
							continue;
						} catch (\Exception $e) {
							$this->logger->info("Exception Error save Attribute Code = " . $pimAttrCode[$i] . ". Message = " . $e->getMessage());
							$this->saveStatusMessage($data, $$e->getMessage(), IntegrationDataValueInterface::STATUS_DATA_FAIL_UPDATE);		
							continue;
						}

						if (!$attributeId[$i]) {
							//Set Attribute Option
							if ($attrTypeData[$i][IntegrationProductAttributeTypeInterface::IS_SWATCH] == 1) {
								if($pimAttrOption[$i]) {
									foreach($pimAttrOption[$i] as $labelOption){
										try {
											$this->attributeOptionHelper->createOrGetId($pimAttrCode[$i], $labelOption);	
										} catch (\Exception $e) {									
											$this->logger->info("Error save Attribute Option = " . $labelOption . '. ' . $e->getMessage());
										}
									}
								} else {
									try {
										$this->attributeOptionHelper->createOrGetId($pimAttrCode[$i], 'default');	
									} catch (\Exception $e) {
										$this->logger->info("Error save Attribute Option = " . 'default. ' . $e->getMessage());
									}
								}
							}
						}

						//Set Attribute to Attribute Set (Default)
						$this->productAttributeManagement->assign($this->attrGroupGeneralInfoId, $this->attrGroupProductDetailId,$pimAttrCode[$i], IntegrationProductAttributeInterface::SORT_ORDER);
						
						if (!$attributeId[$i]) {
							if ($attrTypeData[$i][IntegrationProductAttributeTypeInterface::IS_SWATCH] == 1) {
								$this->convertAttrToSwatches($pimAttrCode[$i]);
							}
						}
						
						$this->saveStatusMessage($data, $msgDataValue[$i], IntegrationDataValueInterface::STATUS_DATA_SUCCESS);		
					}
					
					$i++;
				} catch (\Exception $e) {
					try {
						$this->saveStatusMessage($data, $$e->getMessage(), IntegrationDataValueInterface::STATUS_DATA_FAIL_UPDATE);		
					} catch (\Exception $exc) {
						$this->logger->info('Error save status ' . $exc->getMessage());
					}
					$this->logger->info("Exception 2 Error save Attribute = " . $e->getMessage());
					continue;
				}
			}
		} catch (\Exception $exception) {
			$this->logger->info("Exception 3 Error save Attribute Code = " . $pimAttrCode[$i] . ". Message = " . $exception->getMessage());
			$this->updateJobData($jobId,IntegrationJobInterface::STATUS_PROGRES_UPDATE_FAIL);
			throw new StateException(__($exception->getMessage()));
		}

		$this->updateJobData($jobId,IntegrationJobInterface::STATUS_COMPLETE);
		$this->logger->info("Save Attribute End.");
		return $dataAttribute;
	}

	/**
	 * Update Job data
	 * @param object $datas
	 * @param int $status
	 * @param string $msg
	 * @throw error 
	 */
	protected function updateJobData($jobId=0,$status="" , $msg=""){
	
		if ($jobId < 1) {
			throw new StateException(
				__(IntegrationJobInterface::MSG_DATA_NOTAVAILABLE)
			);
		}
		try{
			$dataJobs = $this->integrationJobRepositoryInterface->getById($jobId);
			$dataJobs->setStatus($status);
			if(!empty($msg)){
				$dataJobs->setMessages($msg);
			}	
			$this->integrationJobRepositoryInterface->save($dataJobs);
		}catch (\Exception $exception) {
			
			throw new CouldNotSaveException(__("Error : Cannot Update Job data - ".$exception->getMessage()));
		}

	}

	/**
	 * convert select attribute to swatches
	 *
	 * @param string $code
	 * @return void
	 */
	public function convertAttrToSwatches($code)
    {
        $attribute = $this->eavConfig->getAttribute('catalog_product', $code);
        if (!$attribute) {
            return;
        }
        $attributeData['option'] = $this->addExistingOptions($attribute);
        $attributeData['frontend_input'] = 'select';
        $attributeData['swatch_input_type'] = 'text';
        $attributeData['update_product_preview_image'] = 1;
        $attributeData['use_product_image_for_swatch'] = 0;
        $attributeData['optiontext'] = $this->getOptionSwatch($attributeData);
        $attributeData['defaulttext'] = $this->getOptionDefaultText($attributeData);
        $attributeData['swatchtext'] = $this->getOptionSwatchText($attributeData);
        $attribute->addData($attributeData);
        $attribute->save();
    }

    /**
     * @param array $attributeData
     * @return array
     */
    protected function getOptionSwatch(array $attributeData)
    {
        $optionSwatch = ['order' => [], 'value' => [], 'delete' => []];
        $i = 0;
        foreach ($attributeData['option'] as $optionKey => $optionValue) {
            $optionSwatch['delete'][$optionKey] = '';
            $optionSwatch['order'][$optionKey] = (string)$i++;
            $optionSwatch['value'][$optionKey] = [$optionValue, ''];
        }
        return $optionSwatch;
    }

    /**
     * @param array $attributeData
     * @return array
     */
    private function getOptionSwatchVisual(array $attributeData)
    {
        $optionSwatch = ['value' => []];
        foreach ($attributeData['option'] as $optionKey => $optionValue) {
            if (substr($optionValue, 0, 1) == '#' && strlen($optionValue) == 7) {
                $optionSwatch['value'][$optionKey] = $optionValue;
            } else if (!empty($this->colorMap[$optionValue])) {
                $optionSwatch['value'][$optionKey] = $this->colorMap[$optionValue];
            } else {
                $optionSwatch['value'][$optionKey] = $this->colorMap['White'];
            }
        }
        return $optionSwatch;
    }

    /**
     * @param array $attributeData
     * @return array
     */
    private function getOptionDefaultVisual(array $attributeData)
    {
        $optionSwatch = $this->getOptionSwatchVisual($attributeData);
        return [array_keys($optionSwatch['value'])[0]];
    }

    /**
     * @param array $attributeData
     * @return array
     */
    private function getOptionSwatchText(array $attributeData)
    {
        $optionSwatch = ['value' => []];
        foreach ($attributeData['option'] as $optionKey => $optionValue) {
            $optionSwatch['value'][$optionKey] = [$optionValue, ''];
        }
        return $optionSwatch;
    }

    /**
     * @param array $attributeData
     * @return array
     */
    private function getOptionDefaultText(array $attributeData)
    {
        $optionSwatch = $this->getOptionSwatchText($attributeData);
        $keys = array_keys($optionSwatch['value']);
        
        if (isset($keys[0])) {
        	return [$keys[0]];
        }
        
        return [];
    }

    /**
     * @param $attributeId
     * @return void
     */
    private function loadOptionCollection($attributeId)
    {
        if (empty($this->optionCollection[$attributeId])) {
            $this->optionCollection[$attributeId] = $this->attrOptionCollectionFactory->create()
                ->setAttributeFilter($attributeId)
                ->setPositionOrder('asc', true)
                ->load();
        }
    }

    /**
     * @param eavAttribute $attribute
     * @return array
     */
    private function addExistingOptions(eavAttribute $attribute)
    {
        $options = [];
        $attributeId = $attribute->getId();
        if ($attributeId) {
            $this->loadOptionCollection($attributeId);
            /** @var \Magento\Eav\Model\Entity\Attribute\Option $option */
            foreach ($this->optionCollection[$attributeId] as $option) {
                $options[$option->getId()] = $option->getValue();
            }
        }
        return $options;
    }
	
	/**
     * Check Attribute Id Exist
     * @param string $attributeCode
     * @return mixed
     */
	public function checkAttributeIdExist($attributeCode) {
		$attributeId = NULL;
		$attributeData = $this->eavConfig->getAttribute('catalog_product', $attributeCode);
        
        if ($attributeData && $attributeData->getId()) {
        	$attributeId = $attributeData->getId();
        }

        return $attributeId;
	}

	/**
     * Save Status & Message Data Value
     */
	public function saveStatusMessage($data, $message, $status) {
		if($data instanceof \Trans\IntegrationEntity\Api\Data\IntegrationDataValueInterface) {
			$data->setMessage($message);
			$data->setStatus($status);
			$this->integrationDataValueRepositoryInterface->save($data);
		}
	}

	/**
	 * Save attribute set data
	 *
	 * @param mixed $datas
	 * @return mixed
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function saveAttributeSet($datas)
	{
		$this->logger->info("Save Attribute set Start.");
		$dataAttribute = [];
		$i = 0;
		$jobId    = $datas->getFirstItem()->getJbId();
		$this->updateJobData($jobId,IntegrationJobInterface::STATUS_PROGRESS_CATEGORY);

		try {
			foreach ($datas as $data) {
				$pimDataAttribute[$i] = $this->curl->jsonToArray($data->getDataValue());

				$pimAttributeList[$i] = $this->validation->validateArray(IntegrationProductAttributeInterface::PIM_ATTRIBUTE_LIST, $pimDataAttribute[$i] );

				$pimIsDeleted[$i] = $this->validation->validateArray(IntegrationProductAttributeInterface::PIM_DELETED, $pimDataAttribute[$i] );

				$msgDataValue = IntegrationProductAttributeInterface::MSG_ATTRIBUTE_SET;
				
				if (!empty($pimAttributeList[$i])) {
					$attributeList = [];
					try {
						$getAttrSetPimId = $this->integrationAttributeRepository->loadAttributeSetByPimId($pimDataAttribute[$i]['id']);
					} catch (\Exception $exception) {
						continue;
					}

					if ($getAttrSetPimId) {
						// delete attribute set and custom table
						if ($pimDataAttribute[$i]['deleted'] > 0) {
							//delete attribute set
							$getAttributeSetIdInt = $getAttrSetPimId->getFirstItem()->getData();
							try {
								$this->attributeSetRepositoryInterface->deleteById($getAttributeSetIdInt['attribute_set_id']);
							} catch (\Exception $exception) {
								continue;
							}

							//delete custom table parent
							$queryDeleteAttrSet = $this->integrationAttributeRepository->loadAttributeSetByPimId($getAttributeSetIdInt['pim_id']);
							foreach ($queryDeleteAttrSet as $valueDeleteAttrSet) {
								$resultDeleteAttrSet = $this->integrationAttributeRepository->deleteAttributeSetIntegration($valueDeleteAttrSet);
							}

							//delete custom table child
							$queryDeleteAttrSetChild = $this->integrationAttributeRepository->loadAttributeSetChildByPimId($getAttributeSetIdInt['pim_id']);
							foreach ($queryDeleteAttrSetChild as $valueDeleteAttrSetChild) {
								$resultDeleteAttrSetChild = $this->integrationAttributeRepository->deleteAttributeSetChildIntegration($valueDeleteAttrSetChild);
							}
						}
						else {
							// update and deleted = 0
							$getAttributeSetIdInt = $getAttrSetPimId->getFirstItem()->getData();
							$getAttrGroupId = $this->integrationAttributeRepository->loadAttributeGroupId($getAttributeSetIdInt['attribute_set_id']);

							// foreach attribute list
							foreach ($pimDataAttribute[$i]['attribute_list'] as $valueAttr) {
								// check if code already exist on custom table
								$queryCodeAttrSet = $this->integrationAttributeRepository->loadAttributeSetByPimIdCode($getAttributeSetIdInt['pim_id'], $valueAttr['code']);

								// if not exist
								if (!$queryCodeAttrSet) {
									if ($valueAttr['deleted'] == 0) {
										try {
											$this->productAttributeManagement->assign($getAttributeSetIdInt['attribute_set_id'], $getAttrGroupId['attribute_group_id'], $valueAttr['code'], IntegrationProductAttributeInterface::SORT_ORDER);

											$attributeList['attribute_set_id'] = $getAttributeSetIdInt['attribute_set_id'];
											$attributeList['deleted'] = $valueAttr['deleted'];
											$attributeList['is_active'] = $valueAttr['is_active'];
											$attributeList['code'] = $valueAttr['code'];
											$this->saveIntegrationAttributeSetChild($pimDataAttribute[$i], $attributeList);
										} catch (\Exception $exception) {
											$this->logger->info("Error assign Attribute set . Message = " . $exception->getMessage());
											continue;
										}
									}
								}
								// if exist
								else {
									if ($valueAttr['deleted'] > 0) {
										try {
											// unassign attribute
											$this->productAttributeManagement->unassign($getAttributeSetIdInt['attribute_set_id'], $valueAttr['code']);

											// delete custom table
											$collectCodeAttrSet = $this->integrationAttributeRepository->collectionAttributeSetByPimIdCode($getAttributeSetIdInt['pim_id'], $valueAttr['code']);
											foreach ($collectCodeAttrSet as $valueCodeAttrSet) {
												$resultCodeAttrSet = $this->integrationAttributeRepository->deleteAttributeSetChildIntegration($valueCodeAttrSet);
											}
										} catch (\Exception $exception) {
											$this->logger->info("Error unassign Attribute set . Message = " . $exception->getMessage());
											continue;
										}
									}
								}
							}
						}
						
						$msgDataValue = IntegrationProductAttributeInterface::MSG_ATTRIBUTE_UPDATE;
					}
					else {
						// create attribute set
						try {
							$attributeSet = $this->attributeSetInterfaceFactory->create();

							$entityType = $this->entityType->create()->loadByCode('catalog_product');
							$defaultSetId = $this->productFactory->create()->getDefaultAttributeSetid();

							$dataOk = [
								'attribute_set_name' => $pimDataAttribute[$i]['category_name'] . '-' . $pimDataAttribute[$i]['id'],
								'entity_type_id' => $entityType->getId(),
								'sort_order' => 100,
							];

							$attributeSet->setData($dataOk);
							$attributeSet->validate();
							$attributeSet->save();
							$attributeSet->initFromSkeleton($defaultSetId);
							$attributeSet->save();

							$attributeList['attribute_set_id'] = $attributeSet->getId();
						}
						catch (\Exception $exception) {
							$msgDataValue = $exception->getMessage();
							$this->saveStatusMessage($data, $msgDataValue, IntegrationDataValueInterface::STATUS_DATA_FAIL_UPDATE);
							continue;
						}

						try {
							$getAttrGroupId = $this->integrationAttributeRepository->loadAttributeGroupId($attributeSet->getId());

							$this->saveIntegrationAttributeSet($pimDataAttribute[$i], $attributeList);

							// assign to attribute set
							foreach ($pimDataAttribute[$i]['attribute_list'] as $valueAssign) {
								// check if attribute exist
								$attributeExist = $this->eavConfig->getAttribute('catalog_product', $valueAssign['code']);

								try {
									// get attribute by code
									$getAttributeidByCode = $this->integrationAttributeRepository->loadAttributeSetByCode($valueAssign['code']);
									
									//unasign from default
									if ($attributeExist->getAttributeId()) {
										if (!$getAttributeidByCode) {
											$this->productAttributeManagement->unassign($this->attrGroupGeneralInfoId, $valueAssign['code']);
										}
									}

									if ($attributeExist->getAttributeId()) {
										$this->productAttributeManagement->assign($attributeSet->getId(), $getAttrGroupId['attribute_group_id'], $valueAssign['code'], IntegrationProductAttributeInterface::SORT_ORDER);

										// update custom table
										$attributeList['deleted'] = $valueAssign['deleted'];
										$attributeList['is_active'] = $valueAssign['is_active'];
										$attributeList['code'] = $valueAssign['code'];
										$this->saveIntegrationAttributeSetChild($pimDataAttribute[$i], $attributeList);
									}

								} catch (\Exception $e) {
									$this->logger->info("Error unassign Attribute set default. Message = " . $e->getMessage());

									if ($attributeExist->getAttributeId()) {
										$this->productAttributeManagement->assign($attributeSet->getId(), $getAttrGroupId['attribute_group_id'], $valueAssign['code'], IntegrationProductAttributeInterface::SORT_ORDER);

										// update custom table
										$attributeList['deleted'] = $valueAssign['deleted'];
										$attributeList['is_active'] = $valueAssign['is_active'];
										$attributeList['code'] = $valueAssign['code'];
										$this->saveIntegrationAttributeSetChild($pimDataAttribute[$i], $attributeList);
									}

									continue;
								}

								// try {
								// 	if ($attributeExist->getAttributeId()) {
								// 		$this->productAttributeManagement->assign($attributeSet->getId(), $getAttrGroupId['attribute_group_id'], $valueAssign['code'], IntegrationProductAttributeInterface::SORT_ORDER);

								// 		// update custom table
								// 		$attributeList['deleted'] = $valueAssign['deleted'];
								// 		$attributeList['is_active'] = $valueAssign['is_active'];
								// 		$attributeList['code'] = $valueAssign['code'];
								// 		$this->saveIntegrationAttributeSetChild($pimDataAttribute[$i], $attributeList);
								// 	}
								// } catch (\Exception $exception) {
								// 	$this->logger->info("Error assign Attribute set . Message = " . $exception->getMessage());
								// 	continue;
								// }
							}
						} catch (\Exception $exception) {
							continue;
						}
					}
				}
				else {
					$msgDataValue = IntegrationProductAttributeInterface::MSG_ATTRIBUTE_SET_NULL;
				}

				$this->saveStatusMessage($data, $msgDataValue, IntegrationDataValueInterface::STATUS_DATA_SUCCESS);	

				$i++;
			}
		} catch (\Exception $exception) {
			$this->logger->info("Error save Attribute set . Message = " . $exception->getMessage());
			$this->updateJobData($jobId,IntegrationJobInterface::STATUS_PROGRES_UPDATE_FAIL);
			throw new StateException(__($exception->getMessage()));
		}

		$this->updateJobData($jobId,IntegrationJobInterface::STATUS_COMPLETE);
		$this->logger->info("Save Attribute set End.");
		return $dataAttribute;
	}

	/**
     * Save to integration integration attribute set
     * @param $data mixed
     * @return $result mixed
     * @throw logger error
     */
    protected function saveIntegrationAttributeSet($data, $attributeList)
    {
        try {
        	$query = $this->integrationProductAttributeSetInterfaceFactory->create();
            $query->setPimId($data['id']);
            $query->setName($data['category_name']);
            $query->setAttributeSetId($attributeList['attribute_set_id']);
            $query->setDeleted($data['deleted']);
            $query->setStatus($data['is_active']);
            $result = $this->integrationAttributeRepository->saveAttributeSetIntegration($query);
        } catch (\Exception $e) {
            throw new StateException(
                __(__FUNCTION__." - ".$e->getMessage())
            );
        }

        return $result;
    }

    /**
     * Save to integration integration attribute set child
     * @param $data mixed
     * @return $result mixed
     * @throw logger error
     */
    protected function saveIntegrationAttributeSetChild($data, $attributeList)
    {
        try {
        	
            $query = $this->integrationProductAttributeSetChildInterfaceFactory->create();
            $query->setPimId($data['id']);
            $query->setCode($attributeList['code']);
            $query->setDeletedAttributeList($attributeList['deleted']);
            $query->setStatus($attributeList['is_active']);
            $result = $this->integrationAttributeRepository->saveAttributeSetChildIntegration($query);
        } catch (\Exception $e) {
            throw new StateException(
                __(__FUNCTION__." - ".$e->getMessage())
            );
        }

        return $result;
    }

	
}