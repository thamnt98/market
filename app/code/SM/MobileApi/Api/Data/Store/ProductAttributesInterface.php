<?php

namespace SM\MobileApi\Api\Data\Store;

/**
 * Interface ProductAttributesInterface
 * @package SM\MobileApi\Api\Data\Store
 */
interface ProductAttributesInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const ATTRIBUTES = 'attributes';

    /**
     * Get product attributes
     *
     * @return \SM\MobileApi\Api\Data\Store\Setting\ProductAttributeInterface[]
     */
    public function getAttributes();

    /**
     * @param $values \SM\MobileApi\Api\Data\Store\Setting\ProductAttributeInterface[]
     *
     * @return $this
     */
    public function setAttributes($values);
}
