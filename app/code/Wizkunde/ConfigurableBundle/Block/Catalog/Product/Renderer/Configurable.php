<?php

namespace Wizkunde\ConfigurableBundle\Block\Catalog\Product\Renderer;

use Magento\Catalog\Model\Product;

class Configurable extends \Magento\Swatches\Block\Product\Renderer\Configurable
{
    /**
     * Path to template file with Swatch renderer.
     */
    const SWATCH_RENDERER_TEMPLATE = 'Wizkunde_ConfigurableBundle::product/view/type/options/swatches.phtml';

    /**
     * Must override this very method to make sure "self" works...
     *
     * @return string
     */
    protected function getRendererTemplate()
    {
        return self::SWATCH_RENDERER_TEMPLATE;
    }

    public function getOptionBlock()
    {
        $optionBlock = $this->getChildBlock('configurableOptions'. $this->getOption()->getId());
        $optionBlock->setProduct($this->getProduct());
        return $optionBlock;
    }

    /**
     * Get Allowed Products
     *
     * @return array
     */
    public function getAllowProducts()
    {
        $products = [];
        $skipSaleableCheck = $this->catalogProduct->getSkipSaleableCheck();

        $allProducts = $this->getProduct()->getTypeInstance()->getUsedProducts($this->getProduct(), null);
        foreach ($allProducts as $product) {
            if ($product->isSaleable() || $skipSaleableCheck) {
                $product->load($product->getId());
                $products[] = $product;
            }
        }
        $this->setAllowProducts($products);

        return $this->getData('allow_products');
    }

    /**
     * Composes configuration for js
     *
     * @return string
     */
    public function getJsonConfig()
    {
        $store = $this->getCurrentStore();

        $regularPrice = $this->getProduct()->getPriceInfo()->getPrice('regular_price');
        $finalPrice = $this->getProduct()->getPriceInfo()->getPrice('final_price');

        $options = $this->helper->getOptions($this->getProduct(), $this->getAllowProducts());

        $attributesData = $this->configurableAttributeData->getAttributesData($this->getProduct(), $options);

        $config = [
            'attributes' => $attributesData['attributes'],
            'template' => str_replace('%s', '<%- data.price %>', $store->getCurrentCurrency()->getOutputFormat()),
            'optionPrices' => $this->getOptionPrices(),
            'prices' => [
                'oldPrice' => [
                    'amount' => $this->_registerJsPrice($regularPrice->getAmount()->getValue()),
                ],
                'basePrice' => [
                    'amount' => $this->_registerJsPrice(
                        $finalPrice->getAmount()->getBaseAmount()
                    ),
                ],
                'finalPrice' => [
                    'amount' => $this->_registerJsPrice($finalPrice->getAmount()->getValue()),
                ],
            ],
            'productId' => $this->getProduct()->getId(),
            'chooseText' => __('Choose an Option...'),
            'images' => isset($options['images']) ? $options['images'] : [],
            'index' => isset($options['index']) ? $options['index'] : [],
        ];

        if ($this->getProduct()->hasPreconfiguredValues() && !empty($attributesData['defaultValues'])) {
            $config['defaultValues'] = $attributesData['defaultValues'];
        }

        $config = array_merge($config, $this->_getAdditionalConfig());

        return $this->jsonEncoder->encode($config);
    }

    /**
     * Get Swatch config data
     *
     * @return string
     */
    public function getJsonSwatchConfig()
    {
        $attributesData = $this->getSwatchAttributesData();
        $allOptionIds = $this->getConfigurableOptionsIds($attributesData);
        $swatchesData = $this->swatchHelper->getSwatchesByOptionsId($allOptionIds);

        $config = [];
        foreach ($attributesData as $attributeId => $attributeDataArray) {
            if (isset($attributeDataArray['options'])) {
                $config[$attributeId] = $this->addSwatchDataForAttribute(
                    $attributeDataArray['options'],
                    $swatchesData,
                    $attributeDataArray
                );
            }
            if (isset($attributeDataArray['additional_data'])) {
                $config[$attributeId]['additional_data'] = $attributeDataArray['additional_data'];
            }
        }

        return $this->jsonEncoder->encode($config);
    }
}
