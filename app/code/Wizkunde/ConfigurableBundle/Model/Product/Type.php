<?php

namespace Wizkunde\ConfigurableBundle\Model\Product;

use Magento\Bundle\Model\ResourceModel\Selection\Collection as Selections;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Bundle\Model\ResourceModel\Selection\Collection\FilterApplier as SelectionCollectionFilterApplier;
use Magento\Framework\Stdlib\ArrayUtils;

class Type extends \Magento\Bundle\Model\Product\Type
{
    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @var SelectionCollectionFilterApplier
     */
    private $selectionCollectionFilterApplier;

    protected $_scopeConfig = null;

    /**
     * @param \Magento\Catalog\Model\Product\Option $catalogProductOption
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Catalog\Model\Product\Type $catalogProductType
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\MediaStorage\Helper\File\Storage\Database $fileStorageDb
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Psr\Log\LoggerInterface $logger
     * @param ProductRepositoryInterface $productRepository
     * @param \Magento\Catalog\Helper\Product $catalogProduct
     * @param \Magento\Catalog\Helper\Data $catalogData
     * @param \Magento\Bundle\Model\SelectionFactory $bundleModelSelection
     * @param \Magento\Bundle\Model\ResourceModel\BundleFactory $bundleFactory
     * @param \Magento\Bundle\Model\ResourceModel\Selection\CollectionFactory $bundleCollection
     * @param \Magento\Catalog\Model\Config $config
     * @param \Magento\Bundle\Model\ResourceModel\Selection $bundleSelection
     * @param \Magento\Bundle\Model\OptionFactory $bundleOption
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param PriceCurrencyInterface $priceCurrency
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param \Magento\CatalogInventory\Api\StockStateInterface $stockState
     * @param \Magento\Framework\Serialize\Serializer\Json $serializer
     * @param MetadataPool $metadataPool
     * @param SelectionCollectionFilterApplier $selectionCollectionFilterApplier
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param ArrayUtils $arrayUtility
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Catalog\Model\Product\Option $catalogProductOption,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Catalog\Model\Product\Type $catalogProductType,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\MediaStorage\Helper\File\Storage\Database $fileStorageDb,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Registry $coreRegistry,
        \Psr\Log\LoggerInterface $logger,
        ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Helper\Product $catalogProduct,
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\Bundle\Model\SelectionFactory $bundleModelSelection,
        \Magento\Bundle\Model\ResourceModel\BundleFactory $bundleFactory,
        \Magento\Bundle\Model\ResourceModel\Selection\CollectionFactory $bundleCollection,
        \Magento\Catalog\Model\Config $config,
        \Magento\Bundle\Model\ResourceModel\Selection $bundleSelection,
        \Magento\Bundle\Model\OptionFactory $bundleOption,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        PriceCurrencyInterface $priceCurrency,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\CatalogInventory\Api\StockStateInterface $stockState,
        Json $serializer,
        MetadataPool $metadataPool,
        SelectionCollectionFilterApplier $selectionCollectionFilterApplier,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        ArrayUtils $arrayUtility = null
    ) {
        parent::__construct(
            $catalogProductOption,
            $eavConfig,
            $catalogProductType,
            $eventManager,
            $fileStorageDb,
            $filesystem,
            $coreRegistry,
            $logger,
            $productRepository,
            $catalogProduct,
            $catalogData,
            $bundleModelSelection,
            $bundleFactory,
            $bundleCollection,
            $config,
            $bundleSelection,
            $bundleOption,
            $storeManager,
            $priceCurrency,
            $stockRegistry,
            $stockState,
            $serializer,
            $metadataPool,
            $selectionCollectionFilterApplier,
            $arrayUtility
        );

        $this->_scopeConfig = $scopeConfig;
        $this->metadataPool = $metadataPool;
        $this->selectionCollectionFilterApplier = $selectionCollectionFilterApplier;
    }

    /**
     * Retrieve bundle selections collection based on used options
     *
     * @param array $optionIds
     * @param \Magento\Catalog\Model\Product $product
     * @return \Magento\Bundle\Model\ResourceModel\Selection\Collection
     */
    public function getSelectionsCollection($optionIds, $product)
    {
        $storeId = $product->getStoreId();

        $metadata = $this->metadataPool->getMetadata(
            \Magento\Catalog\Api\Data\ProductInterface::class
        );

        /** @var Selections $selectionsCollection */
        $selectionsCollection = $this->_bundleCollection->create();
        $selectionsCollection
            ->addAttributeToSelect($this->_config->getProductAttributes())
            ->addAttributeToSelect('tax_class_id') //used for calculation item taxes in Bundle with Dynamic Price
            ->setFlag('product_children', true)
            ->setPositionOrder()
            ->addStoreFilter($this->getStoreFilter($product))
            ->setStoreId($storeId)
            ->setOptionIdsFilter($optionIds);

        $this->selectionCollectionFilterApplier->apply(
            $selectionsCollection,
            'parent_product_id',
            $product->getData($metadata->getLinkField())
        );

        if (!$this->_catalogData->isPriceGlobal() && $storeId) {
            $websiteId = $this->_storeManager->getStore($storeId)
                ->getWebsiteId();
            $selectionsCollection->joinPrices($websiteId);
        }

        return $selectionsCollection;
    }


    /**
     * Retrieve bundle selections collection based on ids
     *
     * @param array $selectionIds
     * @param \Magento\Catalog\Model\Product $product
     * @return \Magento\Bundle\Model\ResourceModel\Selection\Collection
     */
    public function getSelectionsByIds($selectionIds, $product)
    {
        sort($selectionIds);

        $metadata = $this->metadataPool->getMetadata(
            \Magento\Catalog\Api\Data\ProductInterface::class
        );

        $usedSelections = $product->getData($this->_keyUsedSelections);
        $usedSelectionsIds = $product->getData($this->_keyUsedSelectionsIds);

        if (!$usedSelections || $usedSelectionsIds !== $selectionIds) {
            $storeId = $product->getStoreId();
            /** @var Selections $usedSelections */
            $usedSelections = $this->_bundleCollection->create();
            $usedSelections
                ->addAttributeToSelect('*')
                ->setFlag('product_children', true)
                ->addStoreFilter($this->getStoreFilter($product))
                ->setStoreId($storeId)
                ->setPositionOrder()
                ->setSelectionIdsFilter($selectionIds);

            $this->selectionCollectionFilterApplier->apply(
                $usedSelections,
                'parent_product_id',
                $product->getData($metadata->getLinkField())
            );

            if (!$this->_catalogData->isPriceGlobal() && $storeId) {
                $websiteId = $this->_storeManager->getStore($storeId)
                    ->getWebsiteId();
                $usedSelections->joinPrices($websiteId);
            }
            $product->setData($this->_keyUsedSelections, $usedSelections);
            $product->setData($this->_keyUsedSelectionsIds, $selectionIds);
        }

        return $usedSelections;
    }

    /**
     * @param \Magento\Framework\DataObject $selection
     * @param int[] $qtys
     * @param int $selectionOptionId
     * @return float
     */
    protected function getQty($selection, $qtys, $selectionOptionId)
    {
        if ($selection->getSelectionCanChangeQty() && isset($qtys[$selectionOptionId])) {
            if (is_array($qtys[$selectionOptionId])) {
                $qty = (float)$qtys[$selectionOptionId][$selection->getId()];
            } else {
                $qty = (float)$qtys[$selectionOptionId];
            }
        } else {
            $qty = (float)$selection->getSelectionQty();
        }

        return $qty;
    }

    /**
     * Prepare selected options for bundle product
     *
     * @param  \Magento\Catalog\Model\Product $product
     * @param  \Magento\Framework\DataObject $buyRequest
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function processBuyRequest($product, $buyRequest)
    {
        $option = $buyRequest->getBundleOption();
        $optionQty = $buyRequest->getBundleOptionQty();

        $option = is_array($option) ? array_filter($option, 'intval') : [];
        $optionQty = is_array($optionQty) ? array_filter($optionQty, 'intval') : [];

        $options = ['bundle_option' => $option, 'bundle_option_qty' => $optionQty];

        return $options;
    }

    /**
     * Checking if we can sale this bundle
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return bool
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function isSalable($product)
    {
        return true;
    }

    /**
     * Initialize product(s) for add to cart process.
     * Advanced version of func to prepare product for cart - processMode can be specified there.
     *
     * @param \Magento\Framework\DataObject $buyRequest
     * @param \Magento\Catalog\Model\Product $product
     * @param null|string $processMode
     * @return array|string
     */
    public function prepareForCartAdvanced(\Magento\Framework\DataObject $buyRequest, $product, $processMode = self::PROCESS_MODE_FULL)
    {
        $splitBundle = $this->_scopeConfig->getValue('wizkunde/split_products/split_bundle', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        if ($splitBundle !== 'cart') {
            $result = $this->_prepareProduct($buyRequest, $product, $processMode);

            if (is_string($result)) {
                throw new \Magento\Framework\Exception\LocalizedException(__($result));
            }

            $firstProduct = reset($result);

            // Make sure the bundle identity is unique if custom options are the only difference
            if($buyRequest->getOptions())
            {
                $bundleIdentity = $firstProduct->getCustomOption('bundle_identity')->getValue() . implode('_', $buyRequest->getOptions());

                foreach ($result as $item) {
                    $item->addCustomOption('bundle_identity', $bundleIdentity);
                }
            }

            $this->processFileQueue();
            return $result;
        }

        return [];
    }

    /**
     * Prepare product and its configuration to be added to some products list.
     * Perform standard preparation process and then prepare of bundle selections options.
     *
     * @param \Magento\Framework\DataObject $buyRequest
     * @param \Magento\Catalog\Model\Product $product
     * @param string $processMode
     * @return \Magento\Framework\Phrase|array|string
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareProduct(\Magento\Framework\DataObject $buyRequest, $product, $processMode)
    {
        try {
            try {
                $options = $this->_prepareOptions($buyRequest, $product, $processMode);
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                return $e->getMessage();
            }

            if (is_string($options)) {
                return $options;
            }
            // try to found super product configuration
            $superProductConfig = $buyRequest->getSuperProductConfig();
            if (!empty($superProductConfig['product_id']) && !empty($superProductConfig['product_type'])) {
                $superProductId = (int)$superProductConfig['product_id'];
                if ($superProductId) {
                    /** @var \Magento\Catalog\Model\Product $superProduct */
                    $superProduct = $this->_coreRegistry->registry('used_super_product_' . $superProductId);
                    if (!$superProduct) {
                        $superProduct = $this->productRepository->getById($superProductId);
                        $this->_coreRegistry->register('used_super_product_' . $superProductId, $superProduct);
                    }
                    $assocProductIds = $superProduct->getTypeInstance()->getAssociatedProductIds($superProduct);
                    if (in_array($product->getId(), $assocProductIds)) {
                        $productType = $superProductConfig['product_type'];
                        $product->addCustomOption('product_type', $productType, $superProduct);

                        $buyRequest->setData(
                            'super_product_config',
                            ['product_type' => $productType, 'product_id' => $superProduct->getId()]
                        );
                    }

	        }

            }

            $product->prepareCustomOptions();
            $buyRequest->unsetData('_processing_params');
            // One-time params only
	        $product->addCustomOption('info_buyRequest', $this->serializer->serialize($buyRequest->getData()));

            if ($options) {
                $optionIds = array_keys($options);
                $product->addCustomOption('option_ids', implode(',', $optionIds));
                foreach ($options as $optionId => $optionValue) {
                    $product->addCustomOption(self::OPTION_PREFIX . $optionId, $optionValue);
                }
            }

            // set quantity in cart
            if ($this->_isStrictProcessMode($processMode)) {
                $product->setCartQty($buyRequest->getQty());
            }
            $product->setQty($buyRequest->getQty());
            $result = [$product];

            $selections = [];
            $isStrictProcessMode = $this->_isStrictProcessMode($processMode);

            $skipSaleableCheck = $this->_catalogProduct->getSkipSaleableCheck();
            $_appendAllSelections = (bool)$product->getSkipCheckRequiredOption() || $skipSaleableCheck;

            $options = $buyRequest->getBundleOption();
            if (is_array($options)) {
                $options = $this->recursiveIntval($options);
                $optionIds = array_keys($options);

                if (empty($optionIds) && $isStrictProcessMode) {
                    throw new \Magento\Framework\Exception\LocalizedException(__('Please specify product option(s).'));
                }

                $product->getTypeInstance()
                    ->setStoreFilter($product->getStoreId(), $product);
                $optionsCollection = $this->getOptionsCollection($product);
                $this->checkIsAllRequiredOptions(
                    $product,
                    $isStrictProcessMode,
                    $optionsCollection,
                    $options
                );

                $selectionIds = $this->multiToFlatArray($options);
                // If product has not been configured yet then $selections array should be empty
                if (!empty($selectionIds)) {
                    $selections = $this->getSelectionsByIds($selectionIds, $product);

                    if (count($selections->getItems()) !== count($selectionIds)) {
                        throw new \Magento\Framework\Exception\LocalizedException(
                            __('The options you selected are not available.')
                        );
                    }

                    // Check if added selections are still on sale
                    $this->checkSelectionsIsSale(
                        $selections,
                        $skipSaleableCheck,
                        $optionsCollection,
                        $options
                    );

                    $optionsCollection->appendSelections($selections, true, $_appendAllSelections);

                    $selections = $selections->getItems();
                } else {
                    $selections = [];
                }
            } else {
                $product->setOptionsValidationFail(true);
                $product->getTypeInstance()
                    ->setStoreFilter($product->getStoreId(), $product);

                $optionCollection = $product->getTypeInstance()
                    ->getOptionsCollection($product);
                $optionIds = $product->getTypeInstance()
                    ->getOptionsIds($product);
                $selectionCollection = $product->getTypeInstance()
                    ->getSelectionsCollection($optionIds, $product);
                $options = $optionCollection->appendSelections($selectionCollection, true, $_appendAllSelections);

                $selections = $this->mergeSelectionsWithOptions($options, $selections);
            }
            if (count($selections) > 0 || !$isStrictProcessMode) {
                $uniqueKey = [$product->getId()];
                $selectionIds = [];
                $qtys = $buyRequest->getBundleOptionQty();

                // Shuffle selection array by option position
                usort($selections, [$this, 'shakeSelections']);

                foreach ($selections as $selection) {
                    $selectionOptionId = $selection->getOptionId();
                    $qty = $this->getQty($selection, $qtys, $selectionOptionId);

                    $selectionId = $selection->getSelectionId();
                    $beforeQty = $this->getBeforeQty($product, $selection);

                    // Process GRID product
                    if($selection->getTypeId() == 'configurable' && $selection->getShowAsGrid() == true)
                    {
                        $totalQty = 0;

                        $subProduct = $this->productRepository->getById($selection->getId());

                        $configurableOptions = $subProduct->getTypeInstance()->getConfigurableAttributes($subProduct)->getData();

                        foreach($buyRequest->getSuperProduct() as $attributeKey => $subAttribute)
                        {
                            foreach($subAttribute as $subAttributeKey => $qty)
                            {
                                if($qty > 0)
                                {
                                    $totalQty += $qty;

                                    $clonedSubProduct = clone($subProduct);
                                    $clonedSubProduct->addCustomOption('selection_id', $selectionId);

                                    /*
                                     * Create extra attributes that will be converted to product options in order item
                                     * for selection (not for all bundle)
                                     */
                                    $price = $product->getPriceModel()
                                        ->getSelectionFinalTotalPrice($product, $clonedSubProduct, 0, $qty);
                                    $attributes = [
                                        'price' => $this->priceCurrency->convert($price),
                                        'qty' => $qty,
                                        'option_label' => $selection->getOption()
                                            ->getTitle(),
                                        'option_id' => $selection->getOption()
                                            ->getId(),
                                    ];

                                    $clonedCo = $configurableOptions;

                                    $superAttributes = array();

                                    // If we have attributes in the normal array, we merge them and prevent setting them as values
                                    if($buyRequest->getSuperAttribute() != null && is_array($buyRequest->getSuperAttribute())) {
                                        $buyRequestAttributes = $buyRequest->getSuperAttribute();

                                        if(isset($buyRequestAttributes[$selection->getOption()->getId()]))
                                        {
                                            for($i=0;$i<count($buyRequestAttributes[$selection->getOption()->getId()]);$i++)
                                            {
                                                $superAttributes[array_shift($clonedCo)['attribute_id']] = (int)array_values($buyRequestAttributes[$selection->getOption()->getId()])[$i];
                                            }
                                        }
                                    }

                                    $superAttributes[array_shift($clonedCo)['attribute_id']] = (string)$subAttributeKey;
                                    $superAttributes[array_shift($clonedCo)['attribute_id']] = (string)$attributeKey;

                                    $params = clone($buyRequest);
                                    $params->setProduct($subProduct->getId());
                                    $params->setQty($qty);
                                    $params->setSuperAttribute($superAttributes);
                                    $params->setParentProductId($product->getId());

                                    $_result = $clonedSubProduct->getTypeInstance()
                                        ->prepareForCart($params, $clonedSubProduct);

                                    $this->checkIsResult($_result);

                                    $result[] = $_result[0]->setParentProductId($product->getId())
                                        ->addCustomOption(
                                            'bundle_option_ids',
                                            $this->serializer->serialize(array_map('intval', $optionIds))
                                        )
                                        ->addCustomOption(
                                            'bundle_selection_attributes',
                                            $this->serializer->serialize($attributes)
                                        );

                                    if ($isStrictProcessMode) {
                                        $_result[0]->setCartQty($qty);
                                    }

                                    $selectionIds[] = $selection->getSelectionId();
                                    $uniqueKey[] = $_result[0]->getSelectionId();
                                    $uniqueKey[] = implode('', array_values($superAttributes));
                                    $uniqueKey[] = $qty;
                                }
                            }
                        }

                        $product->addCustomOption('selection_qty_' . $selectionId, $totalQty, $selection);
                        $product->addCustomOption('product_qty_' . $selection->getId(), $totalQty + $beforeQty, $selection);

		    } else {

			$superAttributes = array();

                        $product->addCustomOption('selection_qty_' . $selectionId, $qty, $selection);
                        $product->addCustomOption('product_qty_' . $selection->getId(), $qty + $beforeQty, $selection);

                        $selection->addCustomOption('selection_id', $selectionId);

                        /*
                         * Create extra attributes that will be converted to product options in order item
                         * for selection (not for all bundle)
                         */
                        $price = $product->getPriceModel()
                            ->getSelectionFinalTotalPrice($product, $selection, 0, $qty);
                        $attributes = [
                            'price' => $this->priceCurrency->convert($selection->getFinalPrice()),
                            'qty' => $qty,
                            'option_label' => $selection->getOption()
                                ->getTitle(),
                            'option_id' => $selection->getOption()
                                ->getId(),
                        ];


                        $_result = $selection->getTypeInstance()
                            ->prepareForCart($buyRequest, $selection);
                        $this->checkIsResult($_result);

                        $result[] = $_result[0]->setParentProductId($product->getId())
                            ->addCustomOption(
                                'bundle_option_ids',
                                $this->serializer->serialize(array_map('intval', $optionIds))
                            )
                            ->addCustomOption(
                                'bundle_selection_attributes',
                                $this->serializer->serialize($attributes)
                            );

                        if ($isStrictProcessMode) {
                            $_result[0]->setCartQty($qty);
			}

			if($selection->getTypeId() == 'configurable') {
                            if($buyRequest->getSuperAttribute() != null) {
				$superAttributes = $buyRequest->getSuperAttribute();
                            }
			}

                        $resultSelectionId = $_result[0]->getSelectionId();
                        $selectionIds[] = $resultSelectionId;
			$uniqueKey[] = $resultSelectionId;

			if(count($superAttributes) > 0) {
			     $uniqueKey[] = implode('', array_values($superAttributes));
			}

			if($buyRequest->getBundleCustomOptions() != null) {
				if(isset($buyRequest->getBundleCustomOptions()[$selectionOptionId])) {
				    $uniqueKey[] = implode('', $buyRequest->getBundleCustomOptions()[$selectionOptionId]);
				}
		        }

                        $uniqueKey[] = $qty;
                    }
                }

                // "unique" key for bundle selection and add it to selections and bundle for selections
		$uniqueKey = implode('_', $uniqueKey);

                foreach ($result as $item) {
                    $item->addCustomOption('bundle_identity', $uniqueKey);
                }
                $product->addCustomOption(
                    'bundle_option_ids',
                    $this->serializer->serialize(
                        array_map('intval', $optionIds)
                    )
                );
                $product->addCustomOption('bundle_selection_ids', $this->serializer->serialize($selectionIds));

                return $result;
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            return $e->getMessage();
        }

        return $this->getSpecifyOptionMessage();
    }

    /**
     * @param array $array
     * @return int[]|int[][]
     */
    private function recursiveIntval(array $array)
    {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $array[$key] = $this->recursiveIntval($value);
            } elseif (is_numeric($value) && (int)$value != 0) {
                $array[$key] = (int)$value;
            } else {
                unset($array[$key]);
            }
        }

        return $array;
    }

    /**
     * @param array $array
     * @return int[]
     */
    private function multiToFlatArray(array $array)
    {
        $flatArray = [];
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $flatArray = array_merge($flatArray, $this->multiToFlatArray($value));
            } else {
                $flatArray[$key] = $value;
            }
        }

        return $flatArray;
    }

    /**
     * Unset all empty items
     *
     * @param \Magento\Framework\DataObject $buyRequest
     */
    protected function unsetEmptyItems(\Magento\Framework\DataObject $buyRequest)
    {
        $qtys = $buyRequest->getBundleOptionQty();
        $optionData = $buyRequest->getBundleOption();
        $superAttributes = $buyRequest->getSuperAttribute();

        if (is_array($qtys)) {
            foreach ($qtys as $id => $value) {
                if ($value == 0) {
                    unset($qtys[$id]);
                    unset($optionData[$id]);
                    unset($superAttributes[$id]);
                }
            }
        }

        $buyRequest->setBundleOptionQty($qtys);
        $buyRequest->setBundleOption($optionData);
        $buyRequest->setSuperAttribute($superAttributes);
    }
}
