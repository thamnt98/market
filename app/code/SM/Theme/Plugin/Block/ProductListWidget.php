<?php

namespace SM\Theme\Plugin\Block;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\CatalogWidget\Block\Product\ProductsList;

/**
 * Class ProductListWidget
 * @package SM\Theme\Plugin\Block
 */
class ProductListWidget
{
    /**
     * @param ProductsList $subject
     * @param Collection $result
     * @return Collection
     */
    public function afterCreateCollection($subject, $result)
    {
        $result->addFieldToFilter(ProductInterface::STATUS, Status::STATUS_ENABLED);
        return $result;
    }
}
