<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Catalog
 *
 * Date: October, 08 2020
 * Time: 2:25 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Catalog\Override\MagentoCatalog\Model;

class Product extends \Magento\Catalog\Model\Product
{
    public function getPriceInfo()
    {
        if ($this->getTypeId() !== \Magento\Bundle\Model\Product\Type::TYPE_CODE) {
            $minProduct = $this->getMinProduct();
        }

        if (empty($minProduct)) {
            $minProduct = $this;
        }

        if (!$this->_priceInfo) {
            $this->_priceInfo = $this->_catalogProductType->getPriceInfo($minProduct);
        }

        return $this->_priceInfo;
    }

    /**
     * @param Product[] $children
     *
     * @return Product
     */
    protected function getMinChildren($children)
    {
        if (empty($children)) {
            return null;
        } else {
            $minProduct = null;
            $minPrice = null;

            foreach ($children as $item) {
                if ($item->getTypeId() === \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
                    $item = $item->getMinConfigurable();
                }

                if (is_null($minProduct) || $minPrice > $item->getFinalPrice()) {
                    $minPrice = $item->getFinalPrice();
                    $minProduct = $item;
                }
            }

            return $minProduct;
        }
    }

    /**
     * @return Product
     */
    protected function getMinConfigurable()
    {
        if ($this->getTypeId() !== \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
            return $this;
        }

        /** @var \Magento\Catalog\Model\Product[] $children */
        $children = $this->getTypeInstance()->getUsedProducts($this);

        return $this->getMinChildren($children);
    }

    /**
     * @return Product
     */
    protected function getMinBundle()
    {
        if ($this->getTypeId() !== \Magento\Bundle\Model\Product\Type::TYPE_CODE) {
            return $this;
        }

        /** @var \Magento\Catalog\Model\Product[] $children */
        $childrenIds = $this->getTypeInstance()->getChildrenIds($this->getId());
        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $coll */
        $coll = $this->getCollection();
        $coll->addFieldToFilter('entity_id', $childrenIds);

        return $this->getMinChildren($coll->getItems());
    }

    /**
     * @return Product
     */
    protected function getMinGrouped()
    {
        if ($this->getTypeId() !== \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE) {
            return $this;
        }

        /** @var \Magento\Catalog\Model\Product[] $associatedProducts */
        $associatedProducts = $this->getTypeInstance()->getAssociatedProducts($this);

        if (count($associatedProducts) == 0) {
            return null;
        }

        return $this->getMinChildren($associatedProducts);
    }

    /**
     * @return Product|null
     */
    public function getMinProduct()
    {
        switch ($this->getTypeId()) {
            case \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE:
                return $this->getMinConfigurable();
            case \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE:
                return $this->getMinGrouped();
            case \Magento\Bundle\Model\Product\Type::TYPE_CODE:
                return $this->getMinBundle();
            default:
                return $this;
        }
    }
}
