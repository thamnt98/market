<?php

/**
 * @category SM
 * @package SM_TodayDeal
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 * @author      Chinhvd <chinhvd@smartosc.com>
 *
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\TodayDeal\Model;

use Magento\CatalogEvent\Model\Event as SaleEvent;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface as MagentoUrlInterface;
use SM\TodayDeal\Api\Data\MenuDealListingMobileInterface;
use SM\TodayDeal\Api\Data\PostSearchResultInterfaceFactory;
use SM\TodayDeal\Model\ResourceModel\Post as ResourcePost;
use SM\TodayDeal\Model\ResourceModel\Post\CollectionFactory as PostCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;
use SM\TodayDeal\Api\PostRepositoryInterface;
use SM\TodayDeal\Api\Data\PostInterface;
use SM\TodayDeal\Api\Data\PostInterfaceFactory;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Catalog\Model\ProductFactory;

class PostRepository implements PostRepositoryInterface
{
    /**
     * @var ResourcePost
     */
    protected $resource;

    /**
     * @var PostFactory
     */
    protected $postFactory;

    /**
     * @var PostCollectionFactory
     */
    protected $postCollectionFactory;

    /**
     * @var PageSearchResultInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var \SM\TodayDeal\Api\Data\PostInterfaceFactory
     */
    protected $dataPostFactory;

    /**
     * @var \SM\MobileApi\Helper\Product
     */
    protected $productHelper;

    /**
     * @var \Magento\Catalog\Api\CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Magento\CatalogEvent\Model\Category\EventList
     */
    protected $categoryEventList;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaInterfaceFactory
     */
    protected $searchCriteriaFactory;

    /**
     * @var \Magento\Framework\Api\Search\FilterGroupBuilder
     */
    protected $filterGroupBuilder;

    /**
     * @var \Magento\Framework\Api\FilterBuilder
     */
    protected $filterBuilder;

    /**
     * @var \Magento\CatalogEvent\Model\ResourceModel\Event
     */
    protected $resourceEvent;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;

    /**
     * @var \SM\Category\Api\CategoryInterface
     */
    protected $smCategoryRepository;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resourceConnection;
    /**
     * @param \SM\Category\Api\CategoryInterface $smCategoryRepository
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param \SM\MobileApi\Helper\Product $productHelper
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\CatalogEvent\Model\Category\EventList $categoryEventList
     * @param \Magento\Framework\Api\SearchCriteriaInterfaceFactory $searchCriteriaFactory
     * @param \Magento\Framework\Api\Search\FilterGroupBuilder $filterGroupBuilder
     * @param \Magento\Framework\Api\FilterBuilder $filterBuilder
     * @param \Magento\CatalogEvent\Model\ResourceModel\Event $resourceEvent
     * @param ResourcePost $resource
     * @param \SM\TodayDeal\Model\PostFactory $postFactory
     * @param \SM\TodayDeal\Api\Data\PostInterfaceFactory $dataPostFactory
     * @param PostCollectionFactory $postCollectionFactory
     * @param PostSearchResultInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \SM\Category\Api\CategoryInterface $smCategoryRepository,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \SM\MobileApi\Helper\Product $productHelper,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\CatalogEvent\Model\Category\EventList $categoryEventList,
        \Magento\Framework\Api\SearchCriteriaInterfaceFactory $searchCriteriaFactory,
        \Magento\Framework\Api\Search\FilterGroupBuilder $filterGroupBuilder,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        \Magento\CatalogEvent\Model\ResourceModel\Event $resourceEvent,
        ResourcePost $resource,
        PostFactory $postFactory,
        PostInterfaceFactory $dataPostFactory,
        PostCollectionFactory $postCollectionFactory,
        PostSearchResultInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor = null,
        \Magento\Framework\App\ResourceConnection $resourceConnection
    ) {
        $this->resource = $resource;
        $this->postFactory = $postFactory;
        $this->postCollectionFactory = $postCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataPostFactory = $dataPostFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->collectionProcessor = $collectionProcessor ?: $this->getCollectionProcessor();
        $this->productHelper = $productHelper;
        $this->categoryRepository = $categoryRepository;
        $this->productRepository = $productRepository;
        $this->categoryEventList = $categoryEventList;
        $this->searchCriteriaFactory = $searchCriteriaFactory;
        $this->filterGroupBuilder = $filterGroupBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->resourceEvent = $resourceEvent;
        $this->timezone = $timezone;
        $this->smCategoryRepository = $smCategoryRepository;
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * Save Post data
     *
     * @param \SM\TodayDeal\Api\Data\PostInterface|Post $post
     * @return Post
     * @throws CouldNotSaveException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function save(\SM\TodayDeal\Api\Data\PostInterface $post)
    {
        if ($post->getStoreId() === null) {
            $storeId = $this->storeManager->getStore()->getId();
            $post->setStoreId($storeId);
        }
        try {
            $this->resource->save($post);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(
                __('Could not save the page: %1', $exception->getMessage()),
                $exception
            );
        }
        return $post;
    }

    /**
     * @inheritDoc
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($postId)
    {
        $post = $this->postFactory->create();
        $this->resource->load($post, $postId);
        if (!$post->getId()) {
            throw new NoSuchEntityException(__('The Today Deal Post with the "%1" ID doesn\'t exist.', $postId));
        }
        $this->filterData($post);
        return $post;
    }

    /**
     * @param \SM\TodayDeal\Model\CampaignDetailsMobile $post
     */
    protected function filterData(&$post)
    {
        $post->setMbTrueTitle($post->getData('mb_signature_true_title'));
        $post->setMbDescription($post->getData('mb_signature_description'));
        try {
            $post->setMbImageUrl($this->getMediaUrl($post->getData('mb_image_path')));
            $post->setMbVideoUrl($this->getMediaUrl($post->getData('mb_video_path')));
        } catch (NoSuchEntityException $e) {
            //do nothing
        }

//        $signatureProducts = $post->getRelatedProducts();
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName('trans_today_deals_post_product');

        //Select Data from table
        $sql = "Select product_id FROM " . $tableName ." WHERE post_id = ".$post->getId();
        $result = $connection->fetchAll($sql);

        $signatureProducts = [];
        foreach ($result as $productData) {
            /** @var \Magento\Catalog\Model\Product $product */
            $product = $this->productRepository->getById($productData["product_id"]);
            $data = $this->productHelper->getProductListToResponseV2($product);
            $signatureProducts[] = $data;
        }
        $post->setMbSignatureProducts($signatureProducts);

        $mainCategoryId = $post->getData('mb_all_products_category');
        $post->setMbSubCategories($this->smCategoryRepository->getSubCategory($mainCategoryId));

        $popularCategoryId = $post->getData('mb_most_popular_products_category');
        /** @var \Magento\Catalog\Model\Category $popularCategory */
        try {
            $popularCategory = $this->categoryRepository->get($popularCategoryId);
            $popularProducts = $popularCategory->getProductCollection()
                ->addAttributeToSelect('*')
                ->setPageSize(5)
                ->getItems();
            /** @var \Magento\Catalog\Model\Product $product */
            foreach ($popularProducts as &$product) {
                try {
                    $product = $this->productHelper->getProductListToResponseV2($product);
                } catch (\Exception $e) {
                    //do nothing
                }
            }
            $post->setMbMostPopularProducts($popularProducts);
        } catch (NoSuchEntityException $e) {
            //do nothing
        }

        if ($relatedIds = $post->getRelatedIds()) {
            if (!$post->getMbRelatedCampaignsBlockTitle()) {
                $post->setMbRelatedCampaignsBlockTitle(__("You'll Love This"));
            }
            /** @var \SM\TodayDeal\Model\ResourceModel\Post\Collection $collection */
            $collection = $this->postCollectionFactory->create()
                ->addFieldToFilter(\SM\TodayDeal\Api\Data\PostInterface::POST_ID, ['in' => $relatedIds]);
            $post->setMbRelatedCampaigns($collection->getItems());
        }
    }

    /**
     * @param string $path
     * @return string
     * @throws NoSuchEntityException
     */
    protected function getMediaUrl($path)
    {
        return $path ?
            $this->storeManager->getStore()
                ->getBaseUrl(MagentoUrlInterface::URL_TYPE_MEDIA) . $path
            : null;
    }

    /**
     * Load Page data collection by given search criteria
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     * @return \SM\TodayDeal\Api\Data\PostSearchResultInterface
     * @throws NoSuchEntityException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $criteria)
    {
        /** @var \Magento\CatalogEvent\Model\Event $event */
        $event = $this->getOpenEvent();
        $items = [];
        $size = $criteria->getPageSize();
        if ($eventId = (int)$event->getId()) {
            $this->resourceEvent->load($event, $eventId);

            $searchData = $this->getSearchData($criteria);
            if ($searchData != null) {
                if (strpos(strtolower($event->getData('mb_short_title')), strtolower($searchData)) !== false
                    || strpos(strtolower($event->getData('mb_short_description')), strtolower($searchData)) !== false
                    || strpos(strtolower($event->getData('mb_title')), strtolower($searchData)) !== false
                    || strpos(strtolower($event->getData('terms_conditions')), strtolower($searchData)) !== false) {
                    $isSearch = true;
                } else {
                    $isSearch = false;
                }
            } else {
                $isSearch = true;
            }

            if ($event->getData("category_id") && $isSearch == true && $this->getPeriodDate($event) != null) {
                $timeStart = (new \DateTime($event->getDateStart()))->getTimestamp();
                $timeEnd = (new \DateTime($event->getDateEnd()))->getTimestamp();
                $imageUrl = ($event->getImageUrl() === false) ? null : $event->getImageUrl();
                $timeNow = gmdate('U');
                if ($timeStart <= $timeNow && $timeEnd >= $timeNow) {
                    $data = [
                        'id' => $eventId,
                        'type' => 1,
                        'title' => $event->getData('mb_short_title'),
                        'thumbnail_url' => $imageUrl,
                        'mb_short_description' => $event->getData('mb_short_description'),
                        'period_date' => $this->getPeriodDate($event),
                        'publish_to' => $event->getDateEnd(),
                        'publish_from' => $event->getDateStart(),
                        "position"=> null,
                        "promo_id"=> null,
                        "promo_name"=> null,
                        "promo_creative"=> null
                    ];
//                    $event->setData($data);
                    $items[] = $data;
                    if ($criteria->getPageSize()) {
                        $criteria->setPageSize($criteria->getPageSize() - 1);
                    }
                }
            }
        }

        if ($size == null || $criteria->getPageSize() > 0) {
            /** @var \SM\TodayDeal\Model\ResourceModel\Post\Collection $collection */
            $collection = $this->postCollectionFactory->create();
            $storeId = $this->storeManager->getStore()->getId();
            $collection->addStoreFilter($storeId);
            $collection->addFieldToFilter(PostInterface::IS_ACTIVE, '1');
            $collection->addPeriodDateFilter();
            $collection->addStartDateFilter();

            $this->collectionProcessor->process($criteria, $collection);

            foreach ($collection->getItems() as $item) {
                $data = [
                    'id' => (int)$item['post_id'],
                    'type' => 2,
                    'title' => $item['title'],
                    'thumbnail_url' => $this->getThumbnailUrl($item['thumbnail_path']),
                    'mb_short_description' => $item['mb_short_description'],
                    'period_date' => $this->convertDate($item['publish_from'], $item['publish_to']),
                    'publish_to' => $item['publish_to'],
                    'publish_from' => $item['publish_from'],
                    "position" => (int)$item['sort_order'] + 1,
                    "promo_id" => (int)$item['promo_id'],
                    "promo_name" => $item['promo_name'],
                    "promo_creative" => $item['promo_creative']
                ];
                $items[] = $data;
            }
        }

//        $items = array_merge($items, $collection->getItems());
        $items = $this->getSortDate($items, $this->getSort($criteria)['field'], strtolower($this->getSort($criteria)['direction']));

        $criteria->setPageSize($size);
        /** @var \SM\TodayDeal\Api\Data\PostSearchResultInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $searchResults->setItems($items);
        $searchResults->setTotalCount(count($items));
        return $searchResults;
    }

    /**
     * Get search query
     * @param $criteria
     * @return null
     */
    public function getSearchData($criteria){
        $searchData = $criteria->getFilterGroups();
        $searchQuery = null;
        foreach ($searchData as $data){
            foreach ($data->getFilters() as $filter) {
                if($filter->getField() == "search") {
                    $searchQuery = $filter->getValue();
                    break;
                }
            }
        }
        return $searchQuery;
    }

    /** Get sort order
     * @param $criteria
     * @return array
     */
    public function getSort($criteria){
        $sortOrder = "";
        $sortField = "";
        $searchData = $criteria->getSortOrders();
        if($searchData != null) {
            foreach ($searchData as $sort) {
                $sortField = $sort->getField();
                $sortOrder = $sort->getDirection();

            }
        }
        return ["direction" => $sortOrder,"field" => $sortField];
    }

    /**
     * Sort array by date
     * @param $array
     * @param $column
     * @param $direction
     * @return array
     */
    public function getSortDate(&$array, $column, $direction) {

        if($direction == "desc") $direction = SORT_DESC;
        else if($direction == "asc") $direction = SORT_ASC;
        else return $array;

        $reference_array = array();

        foreach($array as $key => $row) {
            $reference_array[$key] = $row[$column];
        }

        array_multisort($reference_array, $direction, $array);
        return $array;
    }

    public function getThumbnailUrl($path)
    {
        return $path ?
            $this->storeManager->getStore()
                ->getBaseUrl(MagentoUrlInterface::URL_TYPE_MEDIA) . $path
            : null;
    }

    /**
     * @param \Magento\CatalogEvent\Model\Event $event
     * @return string
     */
    protected function getPeriodDate($event)
    {
        return $this->convertDate($event->getStoreDateStart(), $event->getStoreDateEnd());
    }

    /**
     * @param string $from
     * @param string $to
     * @return string
     */
    protected function convertDate($from, $to)
    {
        if (!$from || !$to) {
            return null;
        }

        $from = $this->formatDate($from);
        $to = $this->formatDate($to);

        if ($from[2] === $to[2]) {
            if ($from[1] === $to[1]) {
                return $from[0] . ' - ' . implode(' ', $to);
            }
            return implode(' ', [$from[0], $from[1]]) . ' - ' . implode(' ', $to);
        }
        return implode(' ', $from) . ' - ' . implode(' ', $to);
    }

    /**
     * @param mixed $date
     * @return string[]
     */
    protected function formatDate($date)
    {
        $date = $this->timezone->formatDateTime(
            $date,
            null,
            null,
            null,
            $this->timezone->getConfigTimezone(),
            'd MMM YYYY'
        );
        return explode(' ', $date);
    }

    /**
     * Delete Page
     *
     * @param \SM\TodayDeal\Api\Data\PostInterface $post
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(\SM\TodayDeal\Api\Data\PostInterface $post)
    {
        try {
            $this->resource->delete($post);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(
                __('Could not delete the page: %1', $exception->getMessage())
            );
        }
        return true;
    }

    /**
     * Delete Page by given Page Identity
     *
     * @param string $postId
     * @return bool
     * @throws CouldNotDeleteException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function deleteById($postId)
    {
        return $this->delete($this->getById($postId));
    }

    /**
     * Retrieve collection processor
     *
     * @return CollectionProcessorInterface
     */
    private function getCollectionProcessor()
    {
        if (!$this->collectionProcessor) {
            $this->collectionProcessor = \Magento\Framework\App\ObjectManager::getInstance()->get(
                \Magento\Cms\Model\Api\SearchCriteria\PageCollectionProcessor::class
            );
        }
        return $this->collectionProcessor;
    }

    /**
     * @inheritDoc
     */
    public function getProducts($searchCriteria)
    {
        $result = $this->productRepository->getList($searchCriteria);
        $products = $result->getItems();

        foreach ($products as &$product) {
            if ($product->getData("is_tobacco")) {
                continue;
            }
            $product = $this->productHelper->getProductListToResponseV2($product);
            if (!is_array($product->getCategoryNames())) {
                $product->setCategoryNames([]);
            }
        }

        return $products;
    }

    /**
     * @inheritDoc
     */
    public function getListDeals()
    {
        $result = [];

        $result[] = [
            MenuDealListingMobileInterface::ID => null,
            MenuDealListingMobileInterface::TYPE => MenuDealListingMobileInterface::TYPE_ALL,
            MenuDealListingMobileInterface::TITLE => __('All')
        ];

        $event = $this->getOpenEvent();

        if ($event->getData() && $event->getData("category_id")) {
            $result[] = [
                MenuDealListingMobileInterface::ID => $event->getData('event_id'),
                MenuDealListingMobileInterface::TYPE => MenuDealListingMobileInterface::TYPE_SURPRISE_DEAL,
                MenuDealListingMobileInterface::TITLE =>
                    $event->getData('mb_short_title') != null ? $event->getData('mb_short_title') : __('Surprise Sale')
            ];
        }

        /** @var \SM\TodayDeal\Model\ResourceModel\Post\Collection $postCollection */
        $postCollection = $this->postCollectionFactory->create();
        $postCollection->addPeriodDateFilter()
            ->addStartDateFilter()
            ->addFieldToFilter('mb_is_highlighted', 1)
            ->setOrder('publish_to', 'ASC')
            ->setPageSize(5);

        /** @var \SM\TodayDeal\Model\Post $post */
        foreach ($postCollection as $post) {
            $result[] = [
                MenuDealListingMobileInterface::ID => $post->getId(),
                MenuDealListingMobileInterface::TYPE => MenuDealListingMobileInterface::TYPE_CAMPAIGN,
                MenuDealListingMobileInterface::TITLE =>
                    $post->getMbShortTitle() != null ? $post->getMbShortTitle() : $post->getTitle()
            ];
        }

        return $result;
    }

    /**
     * Get current open event
     * @return \Magento\Framework\DataObject
     */
    protected function getOpenEvent()
    {
        return $this->categoryEventList->getEventCollection()
            ->addFieldToFilter('status', SaleEvent::STATUS_OPEN)
            ->addVisibilityFilter()
            ->getFirstItem();
    }

    /**
     * @inheritDoc
     */
    public function getFlashSaleDetail($limit = 12, $p = 1)
    {
        /** @var \Magento\CatalogEvent\Model\Event $event */
        $event = $this->getOpenEvent();
        $eventId = $event->getId();

        $this->resourceEvent->load($event, $eventId);

        if ($event->getData() && $event->getData("category_id")) {
            $filterGroups = $this->filterGroupBuilder->setFilters([
                $this->filterBuilder->setField('category_id')
                    ->setValue($event->getCategoryId())
                    ->setConditionType('eq')
                    ->create()
            ])->create();

            /** @var \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria */
            $searchCriteria = $this->searchCriteriaFactory->create();
            $searchCriteria->setFilterGroups([$filterGroups]);

            $event->setData('total_products', $this->productRepository->getList($searchCriteria)->getTotalCount());

            $searchCriteria->setPageSize($limit);
            $searchCriteria->setCurrentPage($p);

            /** @var \SM\MobileApi\Api\Data\Product\ListItemInterface[] $products */
            $products = $this->getProducts($searchCriteria);

            if (empty($event->getData('terms_conditions'))) {
                $termCond = $event->getData('terms_conditions_default');
                $event->setData('terms_conditions', $termCond);
            }

            if (empty($event->getData('image'))) {
                $image = $event->getData('image_default');
                $event->setData('image', $image);
            }

            $event->setData('products', $products);
            $event->setData('period_date', $this->getPeriodDate($event));

            //Convert end date and start date to current timezone
            $dateStartConverted = $this->timezone->date(new \DateTime($event->getData("date_start")))->format('Y-m-d H:i:s');
            $dateEndConverted = $this->timezone->date(new \DateTime($event->getData("date_end")))->format('Y-m-d H:i:s');
            $event->setDateStartConverted($dateStartConverted);
            $event->setDateEndConverted($dateEndConverted);

            return $event;
        }
        return null;
    }
}
