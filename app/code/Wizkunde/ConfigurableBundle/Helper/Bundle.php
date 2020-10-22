<?php

namespace Wizkunde\ConfigurableBundle\Helper;

use Magento\Catalog\Model\Product\Configuration\Item\ItemInterface;

class Bundle extends \Magento\Bundle\Helper\Catalog\Product\Configuration
{
    private $productModel = null;
    private $eavConfig = null;
    protected $productRepository;
    protected $checkoutSession;
    protected $scopeConfig;

    /**
     * Bundle helper constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Catalog\Helper\Product\Configuration $productConfiguration
     * @param \Magento\Framework\Pricing\Helper\Data $pricingHelper
     * @param \Magento\Framework\Escaper $escaper
     * @param \Magento\Catalog\Model\Product $productModel
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Catalog\Helper\Product\Configuration $productConfiguration,
        \Magento\Framework\Pricing\Helper\Data $pricingHelper,
        \Magento\Framework\Escaper $escaper,
        \Magento\Catalog\Model\Product $productModel,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
        $this->productConfiguration = $productConfiguration;
        $this->pricingHelper = $pricingHelper;
        $this->escaper = $escaper;

        $this->productModel = $productModel;
        $this->eavConfig = $eavConfig;

        $this->productRepository = $productRepository;
        $this->checkoutSession = $checkoutSession;

        $this->scopeConfig = $scopeConfig;

        parent::__construct($context, $productConfiguration, $pricingHelper, $escaper);
    }

    public function mustShowDescription()
    {
        return ($this->scopeConfig->getValue('configurablebundle/general/short_description') == true);
    }

    public function mustShowImages()
    {
        return ($this->getFrontendInputVisibility() == 'input') ? false : true;
    }

    public function getFrontendInputVisibility()
    {
        return $this->scopeConfig->getValue('configurablebundle/general/input_visibility');
    }

    /**
     * Get bundled selections (slections-products collection)
     *
     * Returns array of options objects.
     * Each option object will contain array of selections objects
     *
     * @param ItemInterface $item
     * @return array
     */
    public function getBundleOptions(ItemInterface $item)
    {
        $options = [];
        $product = $item->getProduct();

        /** @var \Magento\Bundle\Model\Product\Type $typeInstance */
        $typeInstance = $product->getTypeInstance();

        // get bundle options
        $optionsQuoteItemOption = $item->getOptionByCode('bundle_option_ids');

        $bundleOptionsIds = $optionsQuoteItemOption ? json_decode($optionsQuoteItemOption->getValue(), true) : [];

        if ($bundleOptionsIds) {
            /** @var \Magento\Bundle\Model\ResourceModel\Option\Collection $optionsCollection */
            $optionsCollection = $typeInstance->getOptionsByIds($bundleOptionsIds, $product);

            // get and add bundle selections collection
            $selectionsQuoteItemOption = $item->getOptionByCode('bundle_selection_ids');

            $bundleSelectionIds = json_decode($selectionsQuoteItemOption->getValue(), true);

            if (!empty($bundleSelectionIds)) {
                $selectionsCollection = $typeInstance->getSelectionsByIds($bundleSelectionIds, $product);

                $bundleOptions = $optionsCollection->appendSelections($selectionsCollection, true);

                foreach ($bundleOptions as $bundleOption) {
                    $bundleOptionHtml = $this->buildOptionData($bundleOption, $item);

                    if (isset($bundleOptionHtml['value'])) {
                        $options[] = $bundleOptionHtml;
                    }
                }
            }
        }

        return $options;
    }

    /**
     * @param $bundleOption
     * @param $bundleSelection
     * @param ItemInterface $item
     * @return string
     */
    private function buildSelectionData($bundleOption, $bundleSelection, ItemInterface $item)
    {
        $returnData = '';

        $product = $item->getProduct();

        $qty = $this->getSelectionQty($product, $bundleSelection->getSelectionId()) * 1;

        $serializedBuyRequest = $item->getOptionByCode('info_buyRequest');
        $buyRequest = json_decode($serializedBuyRequest->getValue(), true);

        foreach ($item->getChildren() as $childItem) {
            $optionHtml = '';

            $selectionId = $childItem->getOptionByCode('selection_id')->getValue();

            $selectionAttributes = $childItem->getOptionByCode('bundle_selection_attributes');
            $selectionData = json_decode($selectionAttributes->getValue(), true);

            if ($selectionId == $bundleSelection->getSelectionId()) {
                if ($bundleSelection->getTypeId() == 'configurable') {
                    $superAttributes = array();

                    $serializedAttributes = $childItem->getOptionByCode('attributes');
                    if ($serializedAttributes != null) {
                        $superAttributes = json_decode($serializedAttributes->getValue(), true);
                    }

                    foreach ($superAttributes as $code => $value) {
                        $attribute = $this->eavConfig->getAttribute('catalog_product', $code);
                        $attributeOptions = $attribute->getSource()->getAllOptions();

                        foreach ($attributeOptions as $optionData) {
                            if ($optionData['value'] == $value) {
                                $optionHtml .=
                                    '<span class="bundle-cart-item-option">' .
                                    '<span style="font-style: italic;">' . $attribute->getStoreLabel() . ': ' . $optionData['label'] . '</span><br />' .
                                    '</span>';
                            }
                        }
                    }
                }

                $coHtml = $this->getCustomOptionHtml($bundleOption, $bundleSelection, $buyRequest, $item);

                $price = ' (' . $this->pricingHelper->currency($selectionData['qty'] * $bundleSelection->getFinalPrice()) . ')';

                $returnData .= '<span class="product-item-cart-info" style="margin: 0 5px;">' .
                    '<span style="font-weight: 700; line-height: 1.1; font-size: 1.4rem; margin:2px 0 0 0;">' . $selectionData['qty'] . ' x ' . $this->escaper->escapeHtml($bundleSelection->getName()) . $price . '</span>' .
                    '<span class-"cart-details bundle-cart-details">'
                    . $optionHtml . $coHtml . '</span></span>';
            }
        }

        return $returnData;
    }

    /**
     * @param $bundleOption
     * @param ItemInterface $item
     * @return array
     */
    private function buildOptionData($bundleOption, ItemInterface $item)
    {

        if ($bundleOption->getSelections()) {
            $bundleOptionData = ['label' => $bundleOption->getTitle(), 'value' => []];

            $bundleSelections = $bundleOption->getSelections();

            foreach ($bundleSelections as $bundleSelection) {
                $bundleOptionData['value'][] = $this->buildSelectionData($bundleOption, $bundleSelection, $item);
            }

            return $bundleOptionData;
        }

        return [];
    }

    /**
     * Get all the set current options of the current item
     *
     * @param $bundleOption
     * @param $bundleSelection
     * @param $buyRequest
     * @param $item
     * @return string
     */
    private function getCustomOptionHtml($bundleOption, $bundleSelection, $buyRequest, $item)
    {
        $customOptionHtml = '';

        if (isset($buyRequest['bundle_custom_options']) &&
            isset($buyRequest['bundle_custom_options'][$bundleOption->getId()])) {
            $customOptions = $buyRequest['bundle_custom_options'][$bundleOption->getId()];

            foreach ($this->checkoutSession->getQuote()->getAllItems() as $quoteItem) {
                if ($quoteItem->getParentItem() && $quoteItem->getParentItem()->getProduct()->getCustomOption('bundle_identity')->getValue() == $item->getProduct()->getCustomOption('bundle_identity')->getValue()) {
                    if ($quoteItem->getProductId() == $bundleSelection->getProductId()) {
                        if ($quoteItem->getProduct()->getCustomOption('simple_product') && $quoteItem->getProduct()->getCustomOption('simple_product')->getProduct()) {
                            $productModel = $quoteItem->getProduct();

                            if (is_array($productModel->getOptions()) == false) {
                                continue;
                            }

                            foreach ($productModel->getOptions() as $option) {
                                if (isset($customOptions[$option->getId()])) {
                                    if (is_array($option->getValues())) {
                                        foreach ($option->getValues() as $value) {
                                            if (is_string($customOptions[$option->getId()])) {
                                                if ($value->getOptionTypeId() == $customOptions[$option->getId()]) {
                                                    $customOptionHtml .=
                                                        '<span style="margin-left: 28px;">' .
                                                        '<span style="font-style: italic;">' .
                                                        $option->getDefaultTitle() .
                                                        ': ' .
                                                        $value->getTitle() .
                                                        ' (+ ' .
                                                        $this->pricingHelper->currency($value->getDefaultPrice()) .
                                                        ')</span><br />' .
                                                        '</span>';
                                                }
                                            } else {
                                                foreach ($customOptions[$option->getId()] as $coKey => $coValue) {
                                                    if ($value->getOptionTypeId() == $coValue) {
                                                        $customOptionHtml .=
                                                            '<span style="margin-left: 28px;">' .
                                                            '<span style="font-style: italic;">' .
                                                            $option->getDefaultTitle() .
                                                            ': ' .
                                                            $value->getTitle() .
                                                            ' (+ ' .
                                                            $this->pricingHelper->currency($value->getDefaultPrice()) .
                                                            ')</span><br />' .
                                                            '</span>';
                                                    }
                                                }
                                            }
                                        }
                                    } else {
                                        if (is_string($customOptions[$option->getId()])) {
                                            $customOptionHtml .= '<span style="margin-left: 28px;">' .
                                                '<span style="font-style: italic;">' . $option->getDefaultTitle() . ': ' .
                                                $customOptions[$option->getId()] .
                                                ' (+ ' . $this->pricingHelper->currency($option->getPrice()) . ')</span><br />' .
                                                '</span>';
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return $customOptionHtml;
    }
}
