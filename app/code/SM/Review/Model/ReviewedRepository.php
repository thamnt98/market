<?php
/**
 * @category SM
 * @package SM_Review
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 * @author      dungnm<dungnm@smartosc.com>
 *
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */
namespace SM\Review\Model;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Helper\ImageFactory;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterfaceFactory;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\UrlInterface;
use Magento\Review\Model\ResourceModel\Review\Collection as ReviewCollection;
use Magento\Review\Model\ResourceModel\Review\CollectionFactory as ReviewCollectionFactory;
use Magento\Review\Model\Review;
use Magento\Review\Model\ReviewFactory;
use Magento\Sales\Model\Order\InvoiceRepository;
use Magento\Sales\Model\ResourceModel\Order\Collection as OrderCollection;
use Magento\Sales\Model\ResourceModel\Order\Invoice\Item\CollectionFactory as ItemCollectionFactory;
use SM\Review\Api\Data\Product\ProductReviewedInterface;
use SM\Review\Api\Data\Product\ProductReviewedInterfaceFactory;
use SM\Review\Api\Data\Product\ReviewDetailInterface;
use SM\Review\Api\Data\Product\ReviewDetailInterfaceFactory;
use SM\Review\Api\Data\ReviewedInterface;
use SM\Review\Api\Data\ReviewedInterfaceFactory;
use SM\Review\Api\Data\ReviewEditInterface;
use SM\Review\Api\Data\ReviewedSearchResultsInterface;
use SM\Review\Api\Data\ReviewImageInterface;
use SM\Review\Api\Data\ReviewImageInterfaceFactory;
use SM\Review\Api\ReviewedRepositoryInterface;
use SM\Review\Helper\Data;
use SM\Review\Helper\Order;
use SM\Review\Model\ResourceModel\ReviewEdit\Collection as ReviewEditCollection;
use SM\Review\Model\ResourceModel\ReviewEdit\CollectionFactory as ReviewEditCollectionFactory;
use SM\Review\Model\ResourceModel\ReviewImage\Collection as ReviewImageCollection;
use SM\Review\Model\ResourceModel\ReviewImage\CollectionFactory as ReviewImageCollectionFactory;

/**
 * Class ReviewedRepository
 * @package SM\Review\Model
 */
class ReviewedRepository implements ReviewedRepositoryInterface
{
    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @var SearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;
    /**
     * @var ReviewedInterfaceFactory
     */
    protected $reviewedInterfaceFactory;
    /**
     * @var ProductReviewedInterfaceFactory
     */
    protected $productReviewedInterfaceFactory;
    /**
     * @var ReviewCollectionFactory
     */
    protected $reviewCollectionFactory;
    /**
     * @var ReviewDetailInterfaceFactory
     */
    protected $reviewDetailInterfaceFactory;
    /**
     * @var ReviewImageCollectionFactory
     */
    protected $reviewImageCollectionFactory;
    /**
     * @var ReviewEditRepository
     */
    protected $reviewEditRepository;
    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;
    /**
     * @var ReviewImageInterfaceFactory
     */
    protected $reviewImageDataFactory;
    /**
     * @var UrlInterface
     */
    protected $urlInterface;

    /**
     * @var Data
     */
    protected $dataHelper;

    /**
     * @var Order
     */
    protected $orderHelper;

    /**
     * @var ReviewEditCollectionFactory
     */
    protected $reviewEditCollectionFactory;

    private $orderCollection;

    /**
     * ReviewedRepository constructor.
     * @param ProductRepository $productRepositoryFactory
     * @param SearchResultsInterfaceFactory $searchResultsFactory
     * @param ReviewedInterfaceFactory $reviewedInterfaceFactory
     * @param ProductReviewedInterfaceFactory $productReviewedInterfaceFactory
     * @param ReviewCollectionFactory $reviewCollectionFactory
     * @param ReviewDetailInterfaceFactory $reviewDetailInterfaceFactory
     * @param ReviewImageCollectionFactory $reviewImageCollectionFactory
     * @param ReviewEditRepository $reviewEditRepository
     * @param DataObjectHelper $dataObjectHelper
     * @param UrlInterface $urlInterface
     * @param ReviewImageInterfaceFactory $reviewImageDataFactory
     * @param Data $dataHelper
     * @param Order $orderHelper
     * @param ReviewEditCollectionFactory $reviewEditCollectionFactory
     */
    public function __construct(
        ProductRepository $productRepositoryFactory,
        SearchResultsInterfaceFactory $searchResultsFactory,
        ReviewedInterfaceFactory $reviewedInterfaceFactory,
        ProductReviewedInterfaceFactory $productReviewedInterfaceFactory,
        ReviewCollectionFactory $reviewCollectionFactory,
        ReviewDetailInterfaceFactory $reviewDetailInterfaceFactory,
        ReviewImageCollectionFactory $reviewImageCollectionFactory,
        ReviewEditRepository $reviewEditRepository,
        DataObjectHelper $dataObjectHelper,
        UrlInterface $urlInterface,
        ReviewImageInterfaceFactory $reviewImageDataFactory,
        Data $dataHelper,
        Order $orderHelper,
        ReviewEditCollectionFactory $reviewEditCollectionFactory
    ) {
        $this->reviewEditCollectionFactory = $reviewEditCollectionFactory;
        $this->dataHelper = $dataHelper;
        $this->orderHelper = $orderHelper;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->urlInterface = $urlInterface;
        $this->reviewImageDataFactory = $reviewImageDataFactory;
        $this->reviewEditRepository = $reviewEditRepository;
        $this->reviewImageCollectionFactory = $reviewImageCollectionFactory;
        $this->productRepository = $productRepositoryFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->reviewedInterfaceFactory = $reviewedInterfaceFactory;
        $this->productReviewedInterfaceFactory = $productReviewedInterfaceFactory;
        $this->reviewCollectionFactory = $reviewCollectionFactory;
        $this->reviewDetailInterfaceFactory = $reviewDetailInterfaceFactory;
    }

    public function getOrderCollection()
    {
        return $this->orderCollection;
    }

    public function setOrderCollection($value)
    {
        $this->orderCollection = $value;
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @param int $customerId
     * @return ReviewedSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria, $customerId)
    {
        /** @var ReviewedSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        /** @var ReviewCollection $reviewCollection */
        $reviewCollection = $this->reviewCollectionFactory->create();
        $reviewCollection->addFieldToFilter("detail.customer_id", ["eq" => $customerId]);
        $reviewCollection->addRateVotes();
        $reviewCollection = $this->filterCollectionByCriteria($searchCriteria, $reviewCollection);
        $reviewCollection = $this->sortCollection($searchCriteria, $reviewCollection);
        $productIdsAndOrderIds = $this->prepareProductIdsAndOrderIds($reviewCollection);
        $productIds = $productIdsAndOrderIds["product_ids"];
        $orderIds = $productIdsAndOrderIds["order_ids"];
        $products = $this->dataHelper->getProducts($productIds);

        /**
         * Order Collection
         */
        $orderCollection = $this->orderHelper->getMainOrderCollection($orderIds);
//        $orderCollection = $this->orderHelper->sortCollection($searchCriteria, $orderCollection);
        $this->setOrderCollection($orderCollection);
        $orderCollection->setCurPage($searchCriteria->getCurrentPage());
        $orderCollection->setPageSize($searchCriteria->getPageSize());

        /**
         * Prepare list item
         */
        $reviewIds = $this->getReviewIdsFromCollection($reviewCollection);
        $reviewEdits = $this->getReviewEdits($reviewIds);
        $listItem = $this->prepareAndPopulateListItem($reviewCollection, $products, $orderIds, $reviewEdits);
        $preparedList = $this->prepareListReviewed($orderCollection);

        $items = [];
        foreach ($preparedList as $reviewed) {
            if (isset($listItem[$reviewed->getOrderId()])) {
                $reviewed->setProducts($listItem[$reviewed->getOrderId()]);

                if (count($listItem[$reviewed->getOrderId()])) {
                    $first = reset($listItem[$reviewed->getOrderId()]);
                    $reviewed->setTimeCreated($this->dataHelper->dateFormat($first->getCreatedAt()));
                }
            }
            $items[] = $reviewed;
        }
        $searchResults->setItems($items);
        return $searchResults;
    }

    /**
     * @param $title
     * @return string
     */
    public function setLimitTitleVote($title)
    {
        if (strlen($title) > 100) {
            $title = substr($title, 0, strpos($title, ' ', 100)) . '...';
        }
        return $title;
    }

    /**
     * @param int $reviewId
     * @return ReviewDetailInterface
     * @throws NoSuchEntityException
     */
    public function getById($reviewId)
    {
        /** @var ReviewCollection $reviewCollection */
        $reviewCollection = $this->reviewCollectionFactory->create();
        $reviewCollection->addFieldToFilter("main_table.review_id", ["eq" => $reviewId]);
        $reviewCollection->addRateVotes();
        /** @var Review $reviewData */
        $reviewData = $reviewCollection->getLastItem();

        /** @var Product $product */
        $product = $this->productRepository->getById($reviewData->getEntityPkValue());

        /** @var ReviewDetailInterface $productReview */
        $productReview = $this->reviewDetailInterfaceFactory->create();
        $productReview->setReviewId($reviewData->getReviewId());
        $productReview->setProductImage($this->dataHelper->getMediaUrl($product->getData("image")));
        $productReview->setProductName($product->getName());
        $productReview->setProductUrl($product->getProductUrl());
        $productReview->setProductId($reviewData->getEntityPkValue());

        /** @var ReviewEditInterface $reviewEdit */
        $reviewEdit = $this->reviewEditRepository->getByReviewId($reviewId);
        if (is_null($reviewEdit)) {
            $productReview->setVoteTitle($reviewData->getTitle());
            $productReview->setVoteComment($reviewData->getDetail());
            $productReview->setVotePercent($reviewData->getRatingVotes()->getFirstItem()->getPercent());
            $productReview->setImages(
                $this->getReviewImages(
                    $reviewData->getReviewId(),
                    ReviewImageRepository::NOT_EDIT
                )
            );
        } else {
            $productReview->setVoteTitle($reviewEdit->getTitle());
            $productReview->setVoteComment($reviewEdit->getDetail());
            $productReview->setVotePercent($reviewEdit->getVoteValue() * 20);
            $productReview->setImages($reviewEdit->getImages());
        }
        return $productReview;
    }

    /**
     * @param int[] $reviewIds
     * @param int $type
     * @return array
     */
    private function getReviewImages($reviewIds, $type)
    {
        /** @var ReviewImageCollection $reviewImageCollection */
        $reviewImageCollection = $this->reviewImageCollectionFactory->create();
        $reviewImageCollection->addFieldToFilter("review_id", ["in" => $reviewIds]);
        $reviewImageCollection->addFieldToFilter("is_edit", ["eq" => $type]);

        $images = [];
        if ($reviewImageCollection->getSize()) {
            /** @var ReviewImage $reviewImage */
            foreach ($reviewImageCollection as $reviewImage) {
                /** @var ReviewImageInterface $reviewImageData */
                $reviewImageData = $this->reviewImageDataFactory->create();
                $this->dataObjectHelper->populateWithArray(
                    $reviewImageData,
                    $reviewImage->getData(),
                    'SM\Review\Api\Data\ReviewImageInterface'
                );
                $jsonImage = $reviewImage->getImage();

                $reviewImageData->setImage($jsonImage);
                $images[] = $reviewImageData;
            }
        }
        return $images;
    }

    /**
     * @param OrderCollection $orderCollection
     * @return ReviewedInterface[]
     */
    private function prepareListReviewed($orderCollection)
    {
        $list = [];
        /** @var \Magento\Sales\Model\Order $order */
        foreach ($orderCollection as $order) {
            /** @var ReviewedInterface $reviewedData */
            $reviewedData = $this->reviewedInterfaceFactory->create();

            $this->dataObjectHelper->populateWithArray(
                $reviewedData,
                [
                    "time_created" => $this->dataHelper->dateFormat($order->getCreatedAt()),
                    "reference_number" => $order->getData('reference_order_id'),
                    "order_id" => $order->getEntityId()
                ],
                ReviewedInterface::class
            );
            $list[$order->getEntityId()] = $reviewedData;
        }
        return $list;
    }

    /**
     * @param ReviewCollection $reviewCollection
     * @return int[] array
     */
    private function getReviewIdsFromCollection($reviewCollection)
    {
        $reviewIds = [];
        foreach ($reviewCollection as $review) {
            $reviewIds[] = $review->getReviewId();
        }
        return $reviewIds;
    }

    /**
     * @param int[] $reviewIds
     * @return ReviewEditInterface[]
     */
    private function getReviewEdits($reviewIds)
    {
        /** @var ReviewEditCollection $reviewEditCollection */
        $reviewEditCollection = $this->reviewEditCollectionFactory->create();
        $reviewEditCollection->addFieldToFilter("review_id", ["in" => $reviewIds]);
        $reviewEdits = [];
        foreach ($reviewEditCollection as $reviewEdit) {
            $reviewEdits[$reviewEdit->getReviewId()] = $reviewEdit;
        }
        return $reviewEdits;
    }

    /**
     * @param ReviewCollection reviewCollection
     * @param ProductInterface[] $products
     * @param int[] $orderIds
     * @param ReviewEditInterface[] $reviewEdits
     * @return array
     * @throws NoSuchEntityException
     */
    private function prepareAndPopulateListItem($reviewCollection, $products, $orderIds, $reviewEdits)
    {
        $list = [];
        /** @var Review $review */
        foreach ($reviewCollection as $review) {
            if (in_array($review->getOrderId(), $orderIds)) {
                $productReviewed = $this->productReviewedInterfaceFactory->create();
                $this->dataObjectHelper->populateWithArray(
                    $productReviewed,
                    $review->getData(),
                    ProductReviewedInterface::class
                );
                if (isset($products[$review->getEntityPkValue()])) {
                    $productReviewed
                        ->setProductName($products[$review->getEntityPkValue()]->getName())
                        ->setProductImage(
                            $this->dataHelper->getMediaUrl($products[$review->getEntityPkValue()]->getData("image"))
                        )
                        ->setProductUrl($products[$review->getEntityPkValue()]->getProductUrl());
                }

                if (isset($reviewEdits[$review->getReviewId()])) {
                    $productReviewed
                        ->setPercentVote($reviewEdits[$review->getReviewId()]->getVoteValue() * 20)
                        ->setTitleVote($this->setLimitTitleVote($reviewEdits[$review->getReviewId()]->getTitle()));
                } else {
                    $productReviewed
                        ->setPercentVote($review->getRatingVotes()->getFirstItem()->getPercent())
                        ->setTitleVote($this->setLimitTitleVote($review->getTitle()));
                }
                $productReviewed->setCreatedAt($review->getCreatedAt());
                $list[$review->getOrderId()][] = $productReviewed;
            }
        }
        return $list;
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @param ReviewCollection $reviewCollection
     * @return ReviewCollection
     */
    private function filterCollectionByCriteria($searchCriteria, $reviewCollection)
    {
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            $fields = [];
            $conditions = [];
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() == "key") {
                    $reviewCollection->addFieldToFilter(
                        [
                            "sales_order_item.name",
                            "sales_order_item.sku",
                            "sales_order.reference_number",
                            "detail.title",
                            "detail.detail"
                        ],
                        [
                            ["like" => "%" . $filter->getValue() . "%"],
                            ["like" => "%" . $filter->getValue() . "%"],
                            ["like" => "%" . $filter->getValue() . "%"],
                            ["like" => "%" . $filter->getValue() . "%"],
                            ["like" => "%" . $filter->getValue() . "%"]
                        ]
                    );
                    continue;
                }

                $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
                $fields[] = 'main_table.' . $filter->getField();
                if ($condition == 'like') {
                    $conditions[] = [$condition => '%' . $filter->getValue() . '%'];
                } else {
                    $conditions[] = [$condition => $filter->getValue()];
                }
            }
            if ($fields) {
                $reviewCollection->addFieldToFilter($fields, $conditions);
            }
        }
        return $reviewCollection;
    }

    /**
     * @param ReviewCollection $reviewCollection
     * @return array[]
     */
    private function prepareProductIdsAndOrderIds($reviewCollection)
    {
        $productIds = [];
        $orderIds = [];
        /** @var Review $review */
        foreach ($reviewCollection as $review) {
            $productIds[] = $review->getEntityPkValue();
            $orderIds[] = $review->getOrderId();
        }
        return [
            "order_ids" => $orderIds,
            "product_ids" => $productIds
        ];
    }

    public function sortCollection($searchCriteria, $reviewCollection)
    {
        $sortOrders = $searchCriteria->getSortOrders();
        if ($sortOrders) {
            /** @var SortOrder $sortOrder */
            foreach ($sortOrders as $sortOrder) {
                $reviewCollection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == strtolower(SortOrder::SORT_ASC)) ? 'ASC' : 'DESC'
                );
            }
        }
        return $reviewCollection;
    }
}
