<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_GroupProduct
 *
 * Date: May, 18 2020
 * Time: 2:18 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\GroupProduct\Model\MagentoGroupedProduct\Product\Type;

class Grouped extends \Magento\GroupedProduct\Model\Product\Type\Grouped
{
    /**
     * @override
     * @param \Magento\Framework\DataObject  $buyRequest
     * @param \Magento\Catalog\Model\Product $product
     * @param string                         $processMode
     *
     * @return array|\Magento\Framework\Phrase|string
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
        $superAttributes = $originalBuyRequest->getData('super_attribute');

        foreach ($associatedProducts as $subProduct) {
            $buyRequest = $originalBuyRequest;
            $qty = $productsInfo[$subProduct->getId()];
            if (!is_numeric($qty) || $qty < 1) {
                continue;
            }

            if ($qty > 99) {
                $qty = 99;
            }

            if ($subProduct->getTypeId() == 'configurable') {
                if (isset($superAttributes[$subProduct->getEntityId()])) {
                    $buyRequest->setData('super_attribute', $superAttributes[$subProduct->getEntityId()]);
                }
            }

            $_result = $subProduct->getTypeInstance()->_prepareProduct($buyRequest, $subProduct, $processMode);

            if (is_string($_result)) {
                return $_result;
            } elseif (!isset($_result[0])) {
                return __('Cannot process the item.')->render();
            }

            if ($isStrictProcessMode) {
                $_result[0]->setCartQty($qty);
                $products[] = $_result[0];
                if (isset($_result[1])) {
                    $products[] = $_result[1];
                }
            } else {
                $associatedProductsInfo[] = [$subProduct->getId() => $qty];
                $product->addCustomOption('associated_product_' . $subProduct->getId(), $qty);
            }
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

    /**
     * @override
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return array|mixed|null
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
}
