<?php

namespace SM\Category\Model\Data\Catalog;

/**
 * Class CategoryTree
 * @package SM\Category\Model\Data\Catalog
 */
class CategoryTree extends \Magento\Framework\Model\AbstractExtensibleModel implements \SM\Category\Api\Data\Catalog\CategoryTreeInterface
{
    public function getCategories()
    {
        return $this->getData(self::CATEGORIES);
    }

    public function setCategories($data)
    {
        return $this->setData(self::CATEGORIES, $data);
    }
}
