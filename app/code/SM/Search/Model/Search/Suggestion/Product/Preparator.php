<?php

declare(strict_types=1);

namespace SM\Search\Model\Search\Suggestion\Product;

use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\Exception\LocalizedException;
use SM\Category\Model\Repository\CategoryRepository;
use SM\Search\Helper\Config;

class Preparator
{
    const WRAP = 'span';
    const DELIMITER = ',';

    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @var Highlighter
     */
    protected $highlighter;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var array
     */
    protected $mainCategoriesData = [];

    /**
     * @var int
     */
    protected $mainCategoryLimit = 0;

    /**
     * @var int
     */
    protected $mainCategoryCount = 0;

    /**
     * @var array
     */
    protected $currentCategoryNames = [];

    /**
     * Preparator constructor.
     * @param CategoryRepository $categoryRepository
     * @param Highlighter $highlighter
     * @param Config $config
     */
    public function __construct(
        CategoryRepository $categoryRepository,
        Highlighter $highlighter,
        Config $config
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->highlighter = $highlighter;
        $this->config = $config;
    }

    /**
     * @param Product[] $products
     * @param bool $includeCategoryNames
     * @return array
     * @throws LocalizedException
     */
    public function prepareProducts(array $products, bool $includeCategoryNames): array
    {
        $productData = [];

        foreach ($products as $product) {
            $productData[] = [
                Config::PRODUCT_NAME_FIELD_NAME => $this->highlighter->highlightSearchText($product->getName()),
                Config::PRODUCT_URL_FIELD_NAME => $product->getProductUrl(),
                Config::CATEGORY_NAMES_ATTRIBUTE_CODE => $includeCategoryNames ? $this->prepareCategoryNames($product) : '',
            ];
        }

        return $productData;
    }

    /**
     * @param Product $product
     * @return string[]
     * @throws LocalizedException
     */
    public function prepareCategoryNames(Product $product): array
    {
        $this->loadProperties();

        $catNames = [];
        if ($this->mainCategoryCount < $this->mainCategoryLimit) {
            foreach ($product->getCategoryIds() as $categoryId) {
                if (isset($this->mainCategoriesData[$categoryId])
                    && !in_array($this->mainCategoriesData[$categoryId][CategoryInterface::KEY_NAME], $this->currentCategoryNames)
                ) {
                    $catNames[] = $this->mainCategoriesData[$categoryId][CategoryInterface::KEY_NAME];
                    $this->currentCategoryNames[] = $this->mainCategoriesData[$categoryId][CategoryInterface::KEY_NAME];
                    $this->mainCategoryCount++;
                    if ($this->mainCategoryCount == $this->mainCategoryLimit) {
                        break;
                    }
                }
            }
        }
        return $catNames;
    }

    /**
     * @throws LocalizedException
     */
    protected function loadProperties(): void
    {
        if (empty($this->mainCategoriesData)) {
            $this->mainCategoriesData = $this->categoryRepository->getCategoriesInSearchForm();
        }
        if (!$this->mainCategoryLimit) {
            $this->mainCategoryLimit = $this->config->getSuggestionMainCategoryLimit();
        }
    }
}
