<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace SM\Search\Model\Product\Attribute;

use Magento\Catalog\Model\Product;
use SM\Category\Model\Repository\CategoryRepository;
use SM\Search\Helper\Config;

class Resolver
{
    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * Resolver constructor.
     * @param CategoryRepository $categoryRepository
     */
    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @param Product $product
     * @return string
     */
    public function resolveCategoryNamesAttribute(Product $product): string
    {
        $names = [];

        $categoryIds = $product->getData(Config::CATEGORY_IDS_ATTRIBUTE_CODE);
        if (empty($categoryIds)) {
            return '';
        }
        $categories = $this->categoryRepository->getCategoriesByIds($categoryIds);

        foreach ($categories as $category) {
            $names[] = $category->getName();
        }

        return implode(' | ', $names);
    }
}
