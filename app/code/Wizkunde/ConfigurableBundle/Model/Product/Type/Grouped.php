<?php

namespace Wizkunde\ConfigurableBundle\Model\Product\Type;

class Grouped extends \Magento\GroupedProduct\Model\Product\Type\Grouped
{
    /**
     * Retrieve array of associated products
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return array
     */
    public function getAssociatedProducts($product)
    {
        if (!$product->hasData($this->_keyAssociatedProducts)) {
            $associatedProducts = [];

            $this->setSaleableStatus($product);

            $collection = $this->getAssociatedProductCollection(
                $product
            )->addAttributeToSelect(
                ['name', 'price', 'special_price', 'special_from_date', 'special_to_date']
            )->setPositionOrder()->addStoreFilter(
                $this->getStoreFilter($product)
            )->addAttributeToFilter(
                'status',
                ['in' => $this->getStatusFilters($product)]
            );

            foreach ($collection as $item) {
                $associatedProducts[] = $item;
            }

            $product->setData($this->_keyAssociatedProducts, $associatedProducts);
        }
        return $product->getData($this->_keyAssociatedProducts);
    }

    /**
     * Prepare product and its configuration to be added to some products list.
     * Perform standard preparation process and add logic specific to Grouped product type.
     *
     * @param \Magento\Framework\DataObject $buyRequest
     * @param \Magento\Catalog\Model\Product $product
     * @param string $processMode
     * @return \Magento\Framework\Phrase|array|string
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function _prepareProduct(\Magento\Framework\DataObject $buyRequest, $product, $processMode)
    {
        $products = [];
        $associatedProductsInfo = [];
        $isStrictProcessMode = $this->_isStrictProcessMode($processMode);
        $productsInfo = $this->getProductInfo($buyRequest, $product, $isStrictProcessMode);
        if (is_string($productsInfo)) {
            return $productsInfo;
        }
        $associatedProducts = !$isStrictProcessMode || !empty($productsInfo)
            ? $this->getAssociatedProducts($product)
            : false;

        $originalBuyRequest = clone($buyRequest);
        $superAttributes = $originalBuyRequest->getSuperAttribute();

        foreach ($associatedProducts as $subProduct) {
            $buyRequest = $originalBuyRequest;

            $qty = $productsInfo[$subProduct->getId()];
            if (!is_numeric($qty) || empty($qty)) {
                continue;
            }

            // Restore the attributes on each iteration
            if ($subProduct->getTypeId() == 'configurable') {
                if (isset($superAttributes[$subProduct->getEntityId()])) {
                    $buyRequest->setSuperAttribute($superAttributes[$subProduct->getEntityId()]);
                }
            }


            $_result = $subProduct->getTypeInstance()->_prepareProduct($buyRequest, $subProduct, $processMode);

            if (is_string($_result)) {
                return $_result;
            } elseif (!isset($_result[0])) {
                return __('Cannot process the item.')->render();
            }


            $products[] = $_result[0];
        }

        if (!$isStrictProcessMode || count($associatedProductsInfo)) {
            $product->addCustomOption('product_type', self::TYPE_CODE, $product);
            $product->addCustomOption('info_buyRequest', $this->serializer->serialize($originalBuyRequest->getData()));

            $products[] = $product;
        }

        if (count($products)) {
            return $products;
        }

        return __('Please specify the quantity of product(s).')->render();
    }
}
