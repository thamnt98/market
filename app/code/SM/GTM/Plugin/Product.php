<?php


namespace SM\GTM\Plugin;


class Product
{
    public function aroundGetAttributeText(\Magento\Catalog\Model\Product $object, callable $proceed, $attributeCode)
    {
        if ($attribute = $object->getResource()->getAttribute($attributeCode)) {
            return $proceed($attributeCode);
        } else {
            return null;
        }

    }
}
