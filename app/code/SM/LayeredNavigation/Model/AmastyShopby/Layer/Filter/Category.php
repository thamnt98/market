<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_LayeredNavigation
 *
 * Date: April, 29 2020
 * Time: 11:28 AM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\LayeredNavigation\Model\AmastyShopby\Layer\Filter;

use Magento\Framework\App\ProductMetadata;

class Category extends \Amasty\Shopby\Model\Layer\Filter\Category
{
    /**
     * @var \Amasty\Shopby\Model\Layer\Filter\Item\CategoryExtendedDataBuilder
     */
    protected $categoryExtendedDataBuilder;

    /**
     * @var \Magento\Framework\Escaper
     */
    protected $escaper;

    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    protected $productMetadata;

    /**
     * Category constructor.
     *
     * @param \Magento\Catalog\Model\Layer\Filter\ItemFactory                    $filterItemFactory
     * @param \Magento\Store\Model\StoreManagerInterface                         $storeManager
     * @param \Magento\Catalog\Model\Layer                                       $layer
     * @param \Magento\Catalog\Model\Layer\Filter\Item\DataBuilder               $itemDataBuilder
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory    $categoryFactory
     * @param \Magento\Framework\Escaper                                         $escaper
     * @param \Magento\Catalog\Model\Layer\Filter\DataProvider\CategoryFactory   $categoryDataProviderFactory
     * @param \Amasty\Shopby\Helper\FilterSetting                                $settingHelper
     * @param \Amasty\Shopby\Model\Search\Adapter\Mysql\AggregationAdapter       $aggregationAdapter
     * @param \Amasty\ShopbyBase\Model\Category\Manager                          $categoryManager
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface                   $categoryRepository
     * @param \Amasty\Shopby\Model\Layer\Filter\Item\CategoryExtendedDataBuilder $categoryExtendedDataBuilder
     * @param \Amasty\Shopby\Model\Layer\Filter\CategoryItemsFactory             $categoryItemsFactory
     * @param \Amasty\Shopby\Helper\Data                                         $helper
     * @param \Amasty\Shopby\Model\Request                                       $shopbyRequest
     * @param \Amasty\Shopby\Helper\Category                                     $categoryHelper
     * @param \Magento\Search\Model\SearchEngine                                 $searchEngine
     * @param \Magento\Framework\Message\ManagerInterface                        $messageManager
     * @param \Magento\Framework\App\ProductMetadataInterface                    $productMetadata
     * @param \Psr\Log\LoggerInterface                                           $logger
     * @param array                                                              $data
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function __construct(
        \Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Layer $layer,
        \Magento\Catalog\Model\Layer\Filter\Item\DataBuilder $itemDataBuilder,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryFactory,
        \Magento\Framework\Escaper $escaper,
        \Magento\Catalog\Model\Layer\Filter\DataProvider\CategoryFactory $categoryDataProviderFactory,
        \Amasty\Shopby\Helper\FilterSetting $settingHelper,
        \Amasty\Shopby\Model\Search\Adapter\Mysql\AggregationAdapter $aggregationAdapter,
        \Amasty\ShopbyBase\Model\Category\Manager $categoryManager,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        \Amasty\Shopby\Model\Layer\Filter\Item\CategoryExtendedDataBuilder $categoryExtendedDataBuilder,
        \Amasty\Shopby\Model\Layer\Filter\CategoryItemsFactory $categoryItemsFactory,
        \Amasty\Shopby\Helper\Data $helper,
        \Amasty\Shopby\Model\Request $shopbyRequest,
        \Amasty\Shopby\Helper\Category $categoryHelper,
        \Magento\Search\Model\SearchEngine $searchEngine,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        \Psr\Log\LoggerInterface $logger,
        array $data = []
    ) {
        parent::__construct(
            $filterItemFactory,
            $storeManager,
            $layer,
            $itemDataBuilder,
            $categoryFactory,
            $escaper,
            $categoryDataProviderFactory,
            $settingHelper,
            $aggregationAdapter,
            $categoryManager,
            $categoryRepository,
            $categoryExtendedDataBuilder,
            $categoryItemsFactory,
            $helper,
            $shopbyRequest,
            $categoryHelper,
            $searchEngine,
            $messageManager,
            $productMetadata,
            $logger,
            $data
        );
        $this->categoryExtendedDataBuilder = $categoryExtendedDataBuilder;
        $this->escaper = $escaper;
        $this->productMetadata = $productMetadata;
    }

    /**
     * @override
     * @return \Magento\Catalog\Model\Category
     */
    public function getStartCategory()
    {
        return $this->getLayer()->getCurrentCategory();
    }

    /**
     *
     * @return array
     */
    protected function getExtendedCategoryData()
    {
        try {
            $optionsFacetedData = $this->getFacetedData();
        } catch (\Magento\Framework\Exception\StateException $e) {
            return [];
        }
        $startCategory = $this->getStartCategory();
        $startPath = $startCategory->getPath();

        $collection = $this->getExtendedCategoryCollection($startCategory);
        foreach ($collection as $category) {
            $isAllowed = $this->isAllowedOnEnterprise($category);
            if (!$isAllowed || empty($optionsFacetedData[$category->getId()]['count'])) {
                continue;
            }

            $this->categoryExtendedDataBuilder->addItemData(
                $category->getParentPath(),
                $this->escaper->escapeHtml($category->getName()),
                $category->getId(),
                $optionsFacetedData[$category->getId()]['count'] ?? 0
            );
        }
        $itemsData = [];
        $itemsData['count'] = $this->categoryExtendedDataBuilder->getItemsCount();
        $itemsData['startPath'] = $startPath;
        $itemsData['items'] = $this->categoryExtendedDataBuilder->build();

        if ($this->getSetting()->getSortOptionsBy() == \Amasty\Shopby\Model\Source\SortOptionsBy::NAME) {
            foreach ($itemsData['items'] as $path => &$items) {
                usort($items, [$this, 'sortOption']);
            }
        }

        return $itemsData;
    }

    /**
     * @param \Magento\Catalog\Model\Category $category
     * @return bool
     */
    protected function isAllowedOnEnterprise($category)
    {
        $isAllowed = true;
        if ($this->productMetadata->getEdition() !== ProductMetadata::EDITION_NAME) {
            $permissions = $category->getPermissions();
            $isAllowed = $permissions['grant_catalog_category_view'] !== self::DENY_PERMISSION;
        }

        return $isAllowed;
    }
}
