<?php

/**
 * @category Trans
 * @package  Trans_Product
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Rifai <muhammad.rifai@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Integration\Helper;

class AttributeOption extends \Magento\Framework\App\Helper\AbstractHelper {
	/**
	 * @var \Magento\Catalog\Api\ProductAttributeRepositoryInterface
	 */
	protected $attributeRepository;

	/**
	 * @var array
	 */
	protected $attributeValues;

	/**
	 * @var \Magento\Eav\Model\Entity\Attribute\Source\TableFactory
	 */
	protected $tableFactory;

	/**
	 * @var \Magento\Eav\Api\AttributeOptionManagementInterface
	 */
	protected $attributeOptionManagement;

	/**
	 * @var \Magento\Eav\Api\Data\AttributeOptionLabelInterfaceFactory
	 */
	protected $optionLabelFactory;

	/**
	 * @var \Magento\Eav\Api\Data\AttributeOptionInterfaceFactory
	 */
	protected $optionFactory;

	/**
	 * @var \Magento\Eav\Model\Config 
	*/
	protected $eavConfig;

	/**
	 * Data constructor.
	 *
	 * @param \Magento\Framework\App\Helper\Context $context
	 * @param \Magento\Catalog\Api\ProductAttributeRepositoryInterface $attributeRepository
	 * @param \Magento\Eav\Model\Entity\Attribute\Source\TableFactory $tableFactory
	 * @param \Magento\Eav\Api\AttributeOptionManagementInterface $attributeOptionManagement
	 * @param \Magento\Eav\Api\Data\AttributeOptionLabelInterfaceFactory $optionLabelFactory
	 * @param \Magento\Eav\Api\Data\AttributeOptionInterfaceFactory $optionFactory
	 * @param \Magento\Swatches\Model\SwatchFactory $swatchFactory,
	 * @param \Magento\Swatches\Model\ResourceModel\Swatch $resourceModelSwatch
	 * @param \Magento\Eav\Model\Config $eavConfig
	 */
	public function __construct(
		\Magento\Framework\App\Helper\Context $context,
		\Magento\Catalog\Api\ProductAttributeRepositoryInterface $attributeRepository,
		\Magento\Eav\Model\Entity\Attribute\Source\TableFactory $tableFactory,
		\Magento\Eav\Api\AttributeOptionManagementInterface $attributeOptionManagement,
		\Magento\Eav\Api\Data\AttributeOptionLabelInterfaceFactory $optionLabelFactory,
		\Magento\Eav\Api\Data\AttributeOptionInterfaceFactory $optionFactory,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Swatches\Model\SwatchFactory $swatchFactory,
		\Magento\Swatches\Model\ResourceModel\Swatch $resourceModelSwatch,
		\Magento\Eav\Model\Config $eavConfig

	) {
		parent::__construct($context);

		$this->attributeRepository       = $attributeRepository;
		$this->tableFactory              = $tableFactory;
		$this->attributeOptionManagement = $attributeOptionManagement;
		$this->optionLabelFactory        = $optionLabelFactory;
		$this->optionFactory             = $optionFactory;
		$this->storeManager              = $storeManager;
		$this->swatchFactory             = $swatchFactory;
		$this->resourceModelSwatch       = $resourceModelSwatch;
		$this->eavConfig				 = $eavConfig;
	}

	/**
	 * Get store identifier
	 *
	 * @return  int
	 */
	public function getStoreId() {
		return $this->storeManager->getStore()->getId();
	}

	/**
	 * Get attribute by code.
	 *
	 * @param string $attributeCode
	 * @return \Magento\Catalog\Api\Data\ProductAttributeInterface
	 */
	public function getAttribute($attributeCode) {
		return $this->attributeRepository->get($attributeCode);
	}

	/**
	 * Find or create a matching attribute option
	 *
	 * @param string $attributeCode Attribute the option should exist in
	 * @param string $label Label to find or add
	 * @return int
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function createOrGetId($attributeCode, $label) {
		if ($label) {
			// Does it already exist?
			$optionId = $this->getOptionId($attributeCode, $label);

			if (!$optionId) {
				// If no, add it.

				/** @var \Magento\Eav\Model\Entity\Attribute\OptionLabel $optionLabel */
				$optionLabel = $this->optionLabelFactory->create();
				$optionLabel->setStoreId(1);
				$optionLabel->setLabel($label);

				$option = $this->optionFactory->create();
				$option->setLabel((string) $label);
				$option->setStoreLabels([$optionLabel]);
				$option->setSortOrder(0);
				$option->setIsDefault(false);
				$option->setValue($label);

				$this->attributeOptionManagement->add(
					\Magento\Catalog\Model\Product::ENTITY,
					$this->getAttribute($attributeCode)->getAttributeId(),
					$option
				);

				// Get the inserted ID. Should be returned from the installer, but it isn't.
				$optionId = $this->getOptionId($attributeCode, $label, true);
				$attributeData = $this->eavConfig->getAttribute('catalog_product', $attributeCode);
				$additionalDataSwatch = $attributeData->getAdditionalData();
				$isSwatch = false;
				
				if ($additionalDataSwatch!=NULL && strpos($additionalDataSwatch,'swatch_input_type')==true){
					$isSwatch = true;
				}
				
				if ($isSwatch==true) {
					$optionSwatchText = $this->swatchFactory->create();
					$optionSwatchText->setOptionId($optionId);
					$optionSwatchText->setStoreId(0);
					$optionSwatchText->setType(0);
					$optionSwatchText->setValue($label);

					$this->resourceModelSwatch->save($optionSwatchText);
				}
			}
		} else {
			$optionId = NULL;
		}

		return $optionId;
	}

	/**
	 * Find the ID of an option matching $label, if any.
	 *
	 * @param string $attributeCode Attribute code
	 * @param string $label Label to find
	 * @param bool $force If true, will fetch the options even if they're already cached.
	 * @return int|false
	 */
	public function getOptionId($attributeCode, $label, $force = false) {
		/** @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute */
		$attribute = $this->getAttribute($attributeCode);

		if ($force === true || !isset($this->attributeValues[$attribute->getAttributeId()][$label])) {
			$this->attributeValues[$attribute->getAttributeId()] = [];

			/** @var \Magento\Eav\Model\Entity\Attribute\Source\Table $sourceModel */
			$sourceModel = $this->tableFactory->create();
			$sourceModel->setAttribute($attribute);

				// var_dump($sourceModel->getAllOptions());
			$options = array_filter($sourceModel->getAllOptions());

			if($options) {
				$optionsArray = array_column($options, 'value', 'label');

				if (isset($optionsArray[$label])) {
					return $optionsArray[$label];
				} else {
					foreach ($sourceModel->getAllOptions() as $option) {
						$this->attributeValues[$attribute->getAttributeId()][$option['label']] = $option['value'];
					}
				} 
			}
		}

		// Return option ID if exists
		if (isset($this->attributeValues[$attribute->getAttributeId()][$label])) {
			return $this->attributeValues[$attribute->getAttributeId()][$label];
		}

		// Return false if does not exist
		return false;
	}
}