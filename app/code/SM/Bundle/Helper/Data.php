<?php
namespace SM\Bundle\Helper;
use \Magento\Framework\App\Helper\AbstractHelper;
use \Magento\Catalog\Model\Product\Configuration\Item\ItemInterface;

class Data extends AbstractHelper
{
    private $eavConfig = null;
    protected $productConfiguration;
    protected $pricingHelper;
    protected $escaper;
    protected $productModel;
    protected $productRepository;
    protected $checkoutSession;
    protected $scopeConfig;

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
        parent::__construct($context);
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
                $bundleOptionData['product_name'] = $bundleSelection->getName();
            }

            return $bundleOptionData;
        }

        return [];
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

        $serializedBuyRequest = $item->getOptionByCode('info_buyRequest');
        $buyRequest = json_decode($serializedBuyRequest->getValue(), true);

        foreach ($item->getChildren() as $childItem) {
            $optionHtml = '';
            $selectionId = $childItem->getOptionByCode('selection_id')->getValue();

            if ($selectionId == $bundleSelection->getSelectionId()) {
                if ($bundleSelection->getTypeId() == 'configurable') {
                    $optionHtml = $this->getOptionHtml($childItem, $optionHtml);
                }

                $coHtml = $this->getCustomOptionHtml($bundleOption, $bundleSelection, $buyRequest, $item);

                if ($bundleSelection->getTypeId() !== 'configurable') {
                    $returnData .= $this->escaper->escapeHtml($bundleSelection->getName());
                } else {
                    $returnData .=  $optionHtml . $coHtml;
                }
            }
        }

        return $returnData;
    }

    public function getOptionHtml($childItem, $optionHtml)
    {
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
                    if($optionHtml == '') {
                        $optionHtml .= $optionData['label'];
                    }else{
                        $optionHtml .= "," . $optionData['label'];
                    }
                }
            }
        }
        return $optionHtml;
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
                                    $customOptionHtml = $this->getNewCustomOptionHtml($option, $customOptions, $customOptionHtml);
                                }
                            }
                        }
                    }
                }
            }
        }

        return $customOptionHtml;
    }

    public function getNewCustomOptionHtml($option, $customOptions, $customOptionHtml)
    {
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

        return $customOptionHtml;
    }


}