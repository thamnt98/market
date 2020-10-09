<?php

namespace SM\Bundle\Model\Product;

use Magento\Bundle\Model\ResourceModel\Selection\Collection\FilterApplier as SelectionCollectionFilterApplier;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Stdlib\ArrayUtils;

class Type extends \Wizkunde\ConfigurableBundle\Model\Product\Type
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;
    /**
     * @var \Magento\Quote\Model\ResourceModel\Quote\Item\CollectionFactory
     */
    private $quoteItemCollectionFactory;
    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $checkoutSession;

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
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Quote\Model\ResourceModel\Quote\Item\CollectionFactory $quoteItemCollectionFactory,
        \Magento\Checkout\Model\Session $checkoutSession,
        ArrayUtils $arrayUtility = null
    ) {
        $this->request = $request;
        $this->quoteItemCollectionFactory = $quoteItemCollectionFactory;
        $this->checkoutSession = $checkoutSession;
        parent::__construct($catalogProductOption, $eavConfig, $catalogProductType, $eventManager, $fileStorageDb,
            $filesystem, $coreRegistry, $logger, $productRepository, $catalogProduct, $catalogData,
            $bundleModelSelection, $bundleFactory, $bundleCollection, $config, $bundleSelection, $bundleOption,
            $storeManager, $priceCurrency, $stockRegistry, $stockState, $serializer, $metadataPool,
            $selectionCollectionFilterApplier, $scopeConfig, $arrayUtility);
    }

    public function prepareForCartAdvanced(
        \Magento\Framework\DataObject $buyRequest,
        $product,
        $processMode = self::PROCESS_MODE_FULL
    ) {
        $splitBundle = $this->_scopeConfig->getValue('wizkunde/split_products/split_bundle',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        if ($splitBundle !== 'cart') {
            $result = $this->_prepareProduct($buyRequest, $product, $processMode);

            if (is_string($result)) {
                throw new \Magento\Framework\Exception\LocalizedException(__($result));
            }

            $firstProduct = reset($result);

            // Make sure the bundle identity is unique if custom options are the only difference
            if ($buyRequest->getOptions()) {
                $bundleIdentity = $firstProduct->getCustomOption('bundle_identity')->getValue() . implode('_',
                        $buyRequest->getOptions());

                foreach ($result as $item) {
                    $item->addCustomOption('bundle_identity', $bundleIdentity);
                }
            }

            $this->processFileQueue();
            return $result;
        }

        return [];
    }

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
            if ($product->getTypeId() == 'bundle' && !array_key_exists('super_attribute', $buyRequest->getData())) {
                $buyRequest = $this->getBuyRequest($buyRequest);
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
                    if ($selection->getTypeId() == 'configurable' && $selection->getShowAsGrid() == true) {
                        $totalQty = 0;

                        $subProduct = $this->productRepository->getById($selection->getId());

                        $configurableOptions = $subProduct->getTypeInstance()->getConfigurableAttributes($subProduct)->getData();

                        foreach ($buyRequest->getSuperProduct() as $attributeKey => $subAttribute) {
                            foreach ($subAttribute as $subAttributeKey => $qty) {
                                if ($qty > 0) {
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
                                    if ($buyRequest->getSuperAttribute() != null && is_array($buyRequest->getSuperAttribute())) {
                                        $buyRequestAttributes = $buyRequest->getSuperAttribute();

                                        if (isset($buyRequestAttributes[$selection->getOption()->getId()])) {
                                            for ($i = 0; $i < count($buyRequestAttributes[$selection->getOption()->getId()]); $i++) {
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
                        $product->addCustomOption('product_qty_' . $selection->getId(), $totalQty + $beforeQty,
                            $selection);

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

                        if ($selection->getTypeId() == 'configurable') {
                            if ($buyRequest->getSuperAttribute() != null) {
                                $superAttributes = $buyRequest->getSuperAttribute();
                            }
                        }

                        $resultSelectionId = $_result[0]->getSelectionId();
                        $selectionIds[] = $resultSelectionId;
                        $uniqueKey[] = $resultSelectionId;

                        if (count($superAttributes) > 0) {
                            $uniqueKey[] = implode('', array_values($superAttributes));
                        }

                        if ($buyRequest->getBundleCustomOptions() != null) {
                            if (isset($buyRequest->getBundleCustomOptions()[$selectionOptionId])) {
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

    public function getBuyRequest($buyRequest)
    {
        $quoteId = $this->checkoutSession->getQuote()->getId();
        $quoteItemCollection = $this->quoteItemCollectionFactory->create();
        $quoteItemCollection->getSelect()
            ->where('quote_id=?', $quoteId)
            ->where('parent_item_id is not null');
        $buyRequestData = $buyRequest->getData();
        $optionIds = array_keys($buyRequest->getBundleOptionQty());
        $bundleOptions = $buyRequest->getBundleOption();
        $count = 0;
        if (!empty($quoteItemCollection->getData())) {
            $buyRequestData['super_attribute'] = [];
            foreach ($quoteItemCollection as $item) {
                $productOpt = $this->productRepository->getById($item->getProductId());
                if ($productOpt->getTypeId() == 'configurable') {
                    $product = $this->productRepository->get($item->getSku());
                    $attributes = $productOpt->getTypeInstance()->getConfigurableAttributesAsArray($productOpt);
                    $attrVal = [];
                    $attrOrder = 0;
                    foreach ($attributes as $attr) {
                        $attrCode = $attr['attribute_code'];
                        $id = is_array($bundleOptions[$optionIds[$count]]) ? $bundleOptions[$optionIds[$count]][$attrOrder] : $bundleOptions[$optionIds[$count]];
                        $attrVal[$id][$attr['attribute_id']] = $product->getData($attrCode);
                        $buyRequestData['bundle_option'][$optionIds[$count]] = $id;
                        $attrOrder++;
                    }
                    $buyRequestData['super_attribute'][$optionIds[$count]] = $attrVal;
                }
                $count++;
            }
        }
        $buyRequest->setData($buyRequestData);
        return $buyRequest;
    }
}
