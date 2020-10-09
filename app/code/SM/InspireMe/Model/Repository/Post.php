<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\InspireMe\Model\Repository;

use Magento\Framework\Exception\NoSuchEntityException;
use Mirasvit\Blog\Api\Data\PostInterface;

/**
 * Class Post
 * @package SM\InspireMe\Model\Repository
 */
class Post implements \SM\InspireMe\Api\PostRepositoryInterface
{
    /**
     * @var \SM\InspireMe\Model\Data\PostListingFactory
     */
    protected $postListingFactory;

    /**
     * @var \Mirasvit\Blog\Api\Repository\PostRepositoryInterface
     */
    protected $postRepository;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;

    /**
     * @var \Mirasvit\Blog\Model\ResourceModel\Post\CollectionFactory
     */
    protected $postCollectionFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \SM\InspireMe\Helper\Data
     */
    protected $dataHelper;

    /**
     * @var \Mirasvit\Blog\Api\Data\PostInterfaceFactory
     */
    protected $postFactory;

    /**
     * @var \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface
     */
    protected $collectionProcessor;

    /**
     * @var \SM\InspireMe\Api\Data\ArticleSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var \Mirasvit\Blog\Api\Repository\CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @var \Mirasvit\Blog\Model\ResourceModel\Post
     */
    protected $postResource;

    /**
     * @var \MGS\Lookbook\Model\LookbookFactory
     */
    protected $lookBookFactory;

    /**
     * @var \MGS\Lookbook\Model\ResourceModel\Lookbook
     */
    protected $lookbookResource;

    /**
     * @var \SM\InspireMe\Api\TopicRepositoryInterface
     */
    protected $topicRepository;

    /**
     * @var \SM\InspireMe\Api\Data\ArticleFilterInterfaceFactory
     */
    protected $articleFilterFactory;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $serializer;

    /**
     * @var \SM\InspireMe\Api\Data\PinDetailInterfaceFactory
     */
    protected $pinDetailFactory;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \SM\MobileApi\Helper\Product
     */
    protected $productHelper;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resourceConnection;

    /**
     * @var \SM\InspireMe\Api\Data\PostTopicInterfaceFactory
     */
    protected $postTopicInterfaceFactory;

    /**
     * @var \SM\InspireMe\Model\Data\RelatedProductResultFactory
     */
    protected $relatedProductResult;

    /**
     * Post constructor.
     * @param \SM\MobileApi\Helper\Product $productHelper
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \SM\InspireMe\Api\Data\PinDetailInterfaceFactory $pinDetailFactory
     * @param \Magento\Framework\Serialize\Serializer\Json $serializer
     * @param \SM\InspireMe\Model\Data\PostListingFactory $postListingFactory
     * @param \Mirasvit\Blog\Api\Repository\PostRepositoryInterface $postRepository
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param \Mirasvit\Blog\Model\ResourceModel\Post\CollectionFactory $postCollectionFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \SM\InspireMe\Helper\Data $dataHelper
     * @param \Mirasvit\Blog\Api\Data\PostInterfaceFactory $postFactory
     * @param \MGS\Lookbook\Model\LookbookFactory $lookBookFactory
     * @param \Mirasvit\Blog\Model\ResourceModel\Post $postResource
     * @param \MGS\Lookbook\Model\ResourceModel\Lookbook $LookbookResource
     * @param \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface $collectionProcessor
     * @param \SM\InspireMe\Api\Data\ArticleSearchResultsInterfaceFactory $searchResultsFactory
     * @param \Mirasvit\Blog\Api\Repository\CategoryRepositoryInterface $categoryRepository
     * @param \SM\InspireMe\Api\TopicRepositoryInterface $topicRepository
     * @param \SM\InspireMe\Api\Data\ArticleFilterInterfaceFactory $articleFilterFactory
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     * @param \SM\InspireMe\Api\Data\PostTopicInterfaceFactory $postTopicInterfaceFactory
     * @param \SM\InspireMe\Model\Data\RelatedProductResultFactory $relatedProductResult
     */
    public function __construct(
        \SM\MobileApi\Helper\Product $productHelper,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \SM\InspireMe\Api\Data\PinDetailInterfaceFactory $pinDetailFactory,
        \Magento\Framework\Serialize\Serializer\Json $serializer,
        \SM\InspireMe\Model\Data\PostListingFactory $postListingFactory,
        \Mirasvit\Blog\Api\Repository\PostRepositoryInterface $postRepository,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Mirasvit\Blog\Model\ResourceModel\Post\CollectionFactory $postCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \SM\InspireMe\Helper\Data $dataHelper,
        \Mirasvit\Blog\Api\Data\PostInterfaceFactory $postFactory,
        \MGS\Lookbook\Model\LookbookFactory $lookBookFactory,
        \Mirasvit\Blog\Model\ResourceModel\Post $postResource,
        \MGS\Lookbook\Model\ResourceModel\Lookbook $LookbookResource,
        \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface $collectionProcessor,
        \SM\InspireMe\Api\Data\ArticleSearchResultsInterfaceFactory $searchResultsFactory,
        \Mirasvit\Blog\Api\Repository\CategoryRepositoryInterface $categoryRepository,
        \SM\InspireMe\Api\TopicRepositoryInterface $topicRepository,
        \SM\InspireMe\Api\Data\ArticleFilterInterfaceFactory $articleFilterFactory,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \SM\InspireMe\Api\Data\PostTopicInterfaceFactory $postTopicInterfaceFactory,
        \SM\InspireMe\Model\Data\RelatedProductResultFactory $relatedProductResult
    ) {
        $this->postListingFactory = $postListingFactory;
        $this->postRepository = $postRepository;
        $this->timezone = $timezone;
        $this->postCollectionFactory = $postCollectionFactory;
        $this->storeManager = $storeManager;
        $this->dataHelper = $dataHelper;
        $this->postResource = $postResource;
        $this->postFactory = $postFactory;
        $this->lookBookFactory = $lookBookFactory;
        $this->lookbookResource = $LookbookResource;
        $this->collectionProcessor = $collectionProcessor;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->categoryRepository = $categoryRepository;
        $this->topicRepository = $topicRepository;
        $this->articleFilterFactory = $articleFilterFactory;
        $this->serializer = $serializer;
        $this->pinDetailFactory = $pinDetailFactory;
        $this->productRepository = $productRepository;
        $this->productHelper = $productHelper;
        $this->resourceConnection = $resourceConnection;
        $this->postTopicInterfaceFactory = $postTopicInterfaceFactory;
        $this->relatedProductResult = $relatedProductResult;
    }

    /**
     * {@inheritDoc}
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        /** @var \Mirasvit\Blog\Model\ResourceModel\Post\Collection $articleCollection */
        $articleCollection = $this->postCollectionFactory->create()
            ->addVisibilityFilter()
            ->addStoreFilter($this->storeManager->getStore()->getId());

        $this->collectionProcessor->process($searchCriteria, $articleCollection);

        /** @var \SM\InspireMe\Api\Data\ArticleSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($articleCollection->getItems());
        $searchResults->setTotalCount($articleCollection->getSize());

        $data = [
            'articles'   => $articleCollection->getItems(),
            'filters'    => $this->categoryRepository->getCollection()
                                ->addVisibilityFilter()
                                ->setOrder(\Mirasvit\Blog\Api\Data\CategoryInterface::ID, 'ASC')
                                ->getItems(),
            'orders'     => [\Mirasvit\Blog\Api\Data\PostInterface::CREATED_AT],
            'directions' => ['ASC', 'DESC'],
        ];

        $result = $this->articleFilterFactory->create();
        $result->setData($data);

        return $result;
    }

    /**
     * @param string $date
     * @return string
     * @throws \Exception
     */
    public function _formatDate($date)
    {
        return $this->timezone->date(new \DateTime($date))->format('d F Y');
    }

    /**
     * {@inheritDoc}
     */
    public function getMostPopular()
    {
        $result = $this->getConfigPosts();

        $selectedId[] = null;
        foreach ($result as $item) {
            if ($item) {
                $selectedId[] = $item->getId();
            }
        }

        $collection = $this->postCollectionFactory->create()
            ->addVisibilityFilter()
            ->setOrder('views_count', 'DESC');

        if (!is_null($selectedId)) {
            $collection->addFieldToFilter(PostInterface::ID, ['nin' => $selectedId]);
        }

        try {
            $collection->addStoreFilter($this->storeManager->getStore()->getId());
        } catch (NoSuchEntityException $e) {
            $collection->addStoreFilter(0);
        }

        $collection->setPageSize(3);

        $itemArray = array_values($collection->getItems());
        $collectionSize = count($itemArray);
        $collectionSelect = 0;

        /** @var \Mirasvit\Blog\Model\Post $item */
        foreach ($result as &$item) {
            if (!$item && $collectionSelect < $collectionSize) {
                if (isset($itemArray[$collectionSelect])) {
                    $item = $itemArray[$collectionSelect];
                }
                $collectionSelect++;
            }
        }

        return $result;
    }

    /**
     * @return array
     */
    protected function getConfigPosts()
    {
        $result = [];
        $config = $this->dataHelper->getMostPopularConfig();

        foreach ($config as $item) {
            if ((int)$item[\SM\InspireMe\Helper\Data::MP_BASED_ON]) {
                try {
                    $result[] = $this->postRepository->get((int)$item[\SM\InspireMe\Helper\Data::MP_SELECT_ARTICLE_ID]);
                } catch (\Exception $e) {
                    $result[] = null;
                }
            } else {
                $result[] = null;
            }
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function getHomeArticles()
    {
        $positionConfig = $this->dataHelper->getHomepagePositionConfig();

        switch ($positionConfig) {
            case $this->dataHelper::CONFIG_POSITION_MOST_VIEW:
                /** @var \Mirasvit\Blog\Model\ResourceModel\Post\Collection $collection */
                $collection = $this->postCollectionFactory->create()
                    ->addVisibilityFilter()
                    ->setOrder('views_count', 'DESC')
                    ->setPageSize(5);
                break;
            case $this->dataHelper::CONFIG_POSITION_RECENT_UPLOAD:
                $collection = $this->postCollectionFactory->create()
                    ->addVisibilityFilter()
                    ->setOrder(PostInterface::CREATED_AT, 'DESC')
                    ->setPageSize(5);
                break;
            default:
                $collection = $this->postCollectionFactory->create()
                    ->addVisibilityFilter()
                    ->setOrder('position', 'DESC')
                    ->setPageSize(5);
        }

        return $collection->getItems();
    }


    /**
     * {@inheritDoc}
     */
    public function getById($postId)
    {
        /** @var \Mirasvit\Blog\Model\Post $post */
        $post = $this->postFactory->create();
        $this->postResource->load($post, $postId);

        if (!$post->getId()) {
            throw new NoSuchEntityException(__('The Articles with the "%1" ID doesn\'t exist.', $postId));
        }

        if ($post->getData('show_hot_spot')) {
            $hotSpot = $this->getHotSpot($post->getData('look_book_id'));
            $post->setData('hot_spot', $hotSpot);
        }

        $topicsData = $this->getPostTopic($post->getData('category_ids'));

        $topicName = "";
        foreach ($topicsData as $data){
            if($data["name"] == "All Topics") continue;
            if($topicName == "") $topicName = $data["name"];
        }
        $post->setTopicName($topicName);
        $post->setArticleAuthor($post->getAuthor() ? $post->getAuthor()->getName() : '');

        $post->setData('is_shop_ingredient', $this->isShopIngredient($postId));

        return $post;
    }

    public function getPostTopic($topics){
        $data = [];
        foreach ($topics as $topic){
            $topicData = $this->categoryRepository->get($topic);
            $data[] = [
                "name" => $topicData->getName(),
                "id" => $topicData->getId()
            ];
        }
        return $data;
    }
    /**
     * @param $id
     * @return \MGS\Lookbook\Model\Lookbook
     * @throws NoSuchEntityException
     */
    private function getHotSpot($id)
    {
        $lookBook = $this->lookBookFactory->create();
        $this->lookbookResource->load($lookBook, $id);
        if (!$lookBook->getId()) {
            throw new NoSuchEntityException(__('The Hot Spot with the "%1" ID doesn\'t exist.', $id));
        }

        $lookBook->setData('pins', $this->filterPinsData($lookBook->getPins()));
        return $lookBook;
    }

    /**
     * @param mixed $data
     * @return \SM\InspireMe\Api\Data\PinDetailInterface[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function filterPinsData($data)
    {
        if ($data) {
            $data = $this->serializer->unserialize($data);
            foreach ($data as &$pin) {
                /** @var \SM\InspireMe\Model\Data\PinDetail $pinDetail */
                $pinDetail = $this->pinDetailFactory->create();
                foreach ($pin as $key => $value) {
                    $pinDetail->setData($key, $value);
                }
                try {
                    $product = $this->productRepository->get($pinDetail['text']);
                    $product = $this->productHelper->getProductListToResponseV2($product);
                    $pinDetail->setProduct($product);
                } catch (NoSuchEntityException $e) {
                    $pinDetail->setProduct(null);
                }
                $pin = $pinDetail;
            }
        }

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function updateViewsCount($postId)
    {
        try {
            /** @var \Mirasvit\Blog\Model\Post $post */
            $post = $this->postFactory->create();
            $this->postResource->load($post, $postId);

            $tempViewsCount = $post->getTempViewsCount();
            $post->setTempViewsCount($tempViewsCount + 1);
            $post->setFlagViewsChanged(1);

            $this->postResource->save($post);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @inheritDoc
     */
    public function isShopIngredient($postId)
    {
        /** @var \Mirasvit\Blog\Model\Post $post */
        $post = $this->postFactory->create();
        $this->postResource->load($post, $postId);

        $parentIds = $post->getCategoryIds();
        $appliedTopicIds = explode(',', $this->dataHelper->getShopIngredientConfig());

        return (bool)count(array_intersect($parentIds, $appliedTopicIds));
    }

    /**
     * @return \SM\InspireMe\Api\Data\PostListingInterface[]
     * @throws \Exception
     */
    public function getPost()
    {
        $articleCollection = $this->postCollectionFactory->create()
            ->addVisibilityFilter()
            ->addStoreFilter($this->storeManager->getStore()->getId());

        foreach ($articleCollection as $post) {
            if ($post->getStatus() != \Mirasvit\Blog\Api\Data\PostInterface::STATUS_PUBLISHED) {
                continue;
            }
            $postListingInfo = $this->postListingFactory->create();
            $postListingInfo->setId($post->getEntityId());
            $postListingInfo->setType($post->getType());
            $postListingInfo->setName($post->getName());
            $postListingInfo->setShortContent($post->getShortContent());
            $postListingInfo->setPublishedDate($this->_formatDate($post->getPublishedDate()));
            $postListingInfo->setHomeImage($post->getFeaturedImageUrl());
            $postListingInfo->setPosition($post->getPosition());
            $postListingInfo->setSource($post->getSource());
            $postListingInfo->setFormatCreatedAt($post->getFormatCreatedAt());
            $postListingInfo->setTopicName($post->getTopicName());
            $postListingInfo->setGtmCreatedAt($post->getGtmCreatedAt());
            $postListingInfo->setArticleList($post->getArticleList());
            $data[] = $postListingInfo;
        }

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function getPagingConfig()
    {
        return $this->dataHelper->getPagingConfig();
    }

    /**
     * @inheritDoc
     */
    public function getProducts($postId)
    {
        try {
            $article = $this->getById($postId);
            $relatedProductTitle = $article->getData('related_products_title');
        } catch (NoSuchEntityException $e) {
            return [];
        }

        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName('mst_blog_post_product');
        //Select Data from table
        $sql = "Select product_id FROM " . $tableName . " WHERE post_id = " . $article->getId();
        $result = $connection->fetchAll($sql);

        $relatedProduct = [];
        foreach ($result as $productData) {
            /** @var \Magento\Catalog\Model\Product $product */
            $product = $this->productRepository->getById($productData["product_id"]);
            $data = $this->productHelper->getProductListToResponseV2($product);
            $relatedProduct[] = $data;
        }

        $result = $this->relatedProductResult->create();
        $result->setTitle($relatedProductTitle);
        $result->setProducts($relatedProduct);

        return $result;
    }
}
