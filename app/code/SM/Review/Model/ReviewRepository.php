<?php
/**
 * @category Magento
 * @package SM\Review\Model
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author Hung Viet Nguyen <hungnv6@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\Review\Model;

use Exception;
use Magento\Catalog\Model\ProductRepository;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\ResourceModel\Customer\Collection as CustomerCollection;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory as CustomerCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Asset\Repository;
use Magento\Review\Model\Rating;
use Magento\Review\Model\RatingFactory;
use Magento\Review\Model\ResourceModel\Review\Collection as ReviewCollection;
use Magento\Review\Model\ResourceModel\Review\CollectionFactory as ReviewCollectionFactory;
use Magento\Review\Model\Review;
use Magento\Review\Model\ReviewFactory;
use Magento\Store\Model\StoreManagerInterface;
use SM\Review\Api\Data\ReviewDataInterface;
use SM\Review\Api\Data\ReviewDataInterfaceFactory;
use SM\Review\Api\Data\ReviewSearchResultsInterface;
use SM\Review\Api\Data\ReviewSearchResultsInterfaceFactory;
use SM\Review\Api\ReviewRepositoryInterface;
use SM\Review\Model\ResourceModel\ReviewImage\Collection as ReviewImageCollection;
use SM\Review\Model\ResourceModel\ReviewImage\CollectionFactory as ReviewImageCollectionFactory;
use SM\Review\Model\Upload\ImageUploader;
use SM\Review\ViewModel\ReviewViewModel;

/**
 * Class ReviewRepository
 * @package SM\Review\Model
 */
class ReviewRepository implements ReviewRepositoryInterface
{
    /**
     * @var ReviewFactory
     */
    protected $reviewFactory;
    /**
     * @var RatingFactory
     */
    protected $ratingFactory;
    /**
     * @var ReviewViewModel
     */
    protected $reviewViewModel;
    /**
     * @var ReviewImageRepository
     */
    protected $reviewImageRepository;
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var CustomerRepository
     */
    protected $customerRepository;
    /**
     * @var ReviewSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;
    /**
     * @var ReviewCollectionFactory
     */
    protected $reviewCollectionFactory;
    /**
     * @var ReviewDataInterfaceFactory
     */
    protected $reviewDataFactory;
    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;
    /**
     * @var ReviewImageCollectionFactory
     */
    protected $reviewImageCollectionFactory;
    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;
    /**
     * @var Repository
     */
    protected $assetRepo;

    /**
     * @var CustomerCollectionFactory
     */
    protected $customerCollectionFactory;

    // For pagination on web
    protected $reviewCollection;

    /**
     * ReviewRepository constructor.
     * @param ReviewFactory $reviewFactory
     * @param RatingFactory $ratingFactory
     * @param ReviewViewModel $reviewViewModel
     * @param ReviewImageRepository $reviewImageRepository
     * @param StoreManagerInterface $storeManager
     * @param CustomerRepositoryInterface $customerRepository
     * @param ReviewCollectionFactory $reviewCollectionFactory
     * @param ReviewSearchResultsInterfaceFactory $searchResultsFactory
     * @param ReviewDataInterfaceFactory $reviewDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param ReviewImageCollectionFactory $reviewImageCollectionFactory
     * @param ProductRepository $productRepository
     * @param UrlInterface $urlBuilder
     * @param Repository $assetRepo
     * @param CustomerCollectionFactory $customerCollectionFactory
     */
    public function __construct(
        ReviewFactory $reviewFactory,
        RatingFactory $ratingFactory,
        ReviewViewModel $reviewViewModel,
        ReviewImageRepository $reviewImageRepository,
        StoreManagerInterface $storeManager,
        CustomerRepositoryInterface $customerRepository,
        ReviewCollectionFactory $reviewCollectionFactory,
        ReviewSearchResultsInterfaceFactory $searchResultsFactory,
        ReviewDataInterfaceFactory $reviewDataFactory,
        DataObjectHelper $dataObjectHelper,
        ReviewImageCollectionFactory $reviewImageCollectionFactory,
        ProductRepository $productRepository,
        UrlInterface $urlBuilder,
        Repository $assetRepo,
        CustomerCollectionFactory $customerCollectionFactory
    ) {
        $this->customerCollectionFactory = $customerCollectionFactory;
        $this->urlBuilder = $urlBuilder;
        $this->assetRepo = $assetRepo;
        $this->productRepository = $productRepository;
        $this->reviewImageCollectionFactory = $reviewImageCollectionFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->reviewDataFactory = $reviewDataFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->reviewCollectionFactory = $reviewCollectionFactory;
        $this->customerRepository = $customerRepository;
        $this->storeManager = $storeManager;
        $this->reviewImageRepository = $reviewImageRepository;
        $this->ratingFactory = $ratingFactory;
        $this->reviewViewModel = $reviewViewModel;
        $this->reviewFactory = $reviewFactory;
    }

    /**
     * @param ReviewDataInterface $review
     * @param string[] $images
     * @param int $customerId
     * @return ReviewDataInterface
     * @throws Exception
     */
    public function create(ReviewDataInterface $review, $images, $customerId)
    {
        try {
            $customer = $this->customerRepository->getById($customerId);

            /** @var Review $reviewModel */
            $reviewModel = $this->reviewFactory->create();

            $reviewModel->setData("title", $review->getTitle());
            $reviewModel->setData("detail", $review->getDetail());
            $reviewModel->setData("nickname", $this->getFullName($customer));

            $reviewModel->setEntityId($reviewModel->getEntityIdByCode(Review::ENTITY_PRODUCT_CODE))
                ->setEntityPkValue($review->getProductId())
                ->setStatusId(Review::STATUS_PENDING)
                ->setCustomerId($customer->getId())
                ->setStoreId($review->getStoreId())
                ->setStores([$review->getStoreId()])
                ->setData("order_id", $review->getOrderId())
                ->save();

            /** @var Rating $quality */
            $quality = $this->reviewViewModel->getQualityRating();

            $this->ratingFactory->create()
                ->setRatingId($quality->getId())
                ->setReviewId($reviewModel->getId())
                ->setCustomerId($customer->getId())
                ->addOptionVote($review->getRating(), $review->getProductId());

            $processedImage = [];
            foreach ($images as $image) {
                $mediaUrl = $this->storeManager->getStore(
                    $review->getStoreId()
                )->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
                $imageData = [
                    "file" => $image,
                    "url" => $mediaUrl . ImageUploader::FILE_DIR . $image,
                    "previewType" => "document"
                ];
                $processedImage[] = json_encode($imageData);
            }

            $resultImages = $this->reviewImageRepository->saveImage(
                $reviewModel->getId(),
                $processedImage,
                ReviewImageRepository::NOT_EDIT
            );
            $reviewModel->aggregate();
            $review->setReviewId($reviewModel->getId())
                ->setNickname($this->getFullName($customer))
                ->setCustomerId($customer->getId())
                ->setStatusId(Review::STATUS_PENDING)
                ->setImages($resultImages);

            return $review;
        } catch (Exception $exception) {
            throw new Exception(__($exception->getMessage()));
        }
    }

    /**
     * @param CustomerInterface $customer
     * @return string
     */
    private function getFullName($customer)
    {
        return (($customer->getFirstname()) ? $customer->getFirstname() . " " : "") .
            (($customer->getMiddlename()) ? $customer->getMiddlename() . " " : "") .
            (($customer->getLastname()) ? $customer->getLastname() : "");
    }

    /**
     * @param SearchCriteria $searchCriteria
     * @return ReviewSearchResultsInterface
     * @throws NoSuchEntityException
     */
    public function getList(SearchCriteria $searchCriteria)
    {
        $storeId = $this->storeManager->getStore()->getId();

        /** @var ReviewSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        /** @var ReviewCollection $reviewCollection */
        $reviewCollection = $this->reviewCollectionFactory->create();
        $reviewCollection->addRateVotes();
        $reviewCollection = $this->itemFilter($reviewCollection, $searchCriteria);
        $reviewCollection = $this->itemSort($reviewCollection, $searchCriteria);
        $reviewCollection->addFieldToFilter("detail.customer_id", ["neq" => "null"]);
        $reviewCollection->addFieldToFilter("main_table.order_id", ["neq" => "null"]);
        $reviewCollection->addFieldToFilter("detail.store_id", $storeId);

        $reviewCollection->setCurPage($searchCriteria->getCurrentPage());
        $reviewCollection->setPageSize($searchCriteria->getPageSize());

        $reviewIdsAndCustomerIds = $this->getReviewIdsAndCustomerIds($reviewCollection);
        $reviewIds = $reviewIdsAndCustomerIds["reviewIds"];
        $customerIds = $reviewIdsAndCustomerIds["customerIds"];

        $customers = $this->getCustomers($customerIds);
        $reviewImages = $this->getReviewImages($reviewIds);

        $this->setReviewCollection($reviewCollection);

        $reviews = [];
        foreach ($reviewCollection as $review) {
            if (in_array($review->getReviewId(), $reviewIds)) {
                $reviews[] = $this->prepareData($review, $reviewImages, $customers);
            }
        }

        $searchResults->setTotalCount(count($reviews));
        $searchResults->setItems($reviews);
        return $searchResults;
    }

    // For pagination on web
    public function setReviewCollection($reviewCollection)
    {
        $this->reviewCollection = $reviewCollection;
    }

    // For pagination on web
    public function getReviewCollection()
    {
        return $this->reviewCollection;
    }

    // Delete reviews those do not belong to any invoice
    public function cleanOldData()
    {
        /** @var ReviewCollection $reviewCollection */
        $reviewCollection = $this->reviewCollectionFactory->create();
        $reviewCollection->addFieldToFilter("main_table.order_id", ["eq" => 0]);
        $reviewCollection->walk("delete");
    }

    /**
     * @param Review $review
     * @param $reviewImages
     * @param Customer[] $customers
     * @return ReviewDataInterface
     */
    protected function prepareData($review, $reviewImages, $customers)
    {
        /** @var ReviewDataInterface $reviewData */
        $reviewData = $this->reviewDataFactory->create();

        $this->dataObjectHelper->populateWithArray(
            $reviewData,
            $review->getData(),
            'SM\Review\Api\Data\ReviewDataInterface'
        );
        if (isset($reviewImages[$reviewData->getReviewId()])) {
            $reviewData->setImages($reviewImages[$reviewData->getReviewId()]);
        } else {
            $reviewData->setImages([]);
        }

        $reviewData->setRating($review->getRatingVotes()->getFirstItem()->getPercent() / 20.0);
        $reviewData->setProductId($review->getEntityPkValue());
        $reviewData->setProfileImage($this->getProfilePicture($reviewData->getCustomerId(), $customers));
        return $reviewData;
    }

    /**
     * @param ReviewCollection $collection
     * @return array
     */
    public function getReviewIdsAndCustomerIds($collection)
    {
        $reviewIds = [];
        $customerIds = [];
        foreach ($collection->getData() as $review) {
            $reviewIds[] = $review["review_id"];
            $customerIds[] = $review["customer_id"];
        }
        return [
            "reviewIds" => array_unique($reviewIds),
            "customerIds" => array_unique($customerIds)
        ];
    }

    /**
     * @param $reviewIds
     * @return array
     */
    protected function getReviewImages($reviewIds)
    {
        /** @var ReviewImageCollection $reviewImageCollection */
        $reviewImageCollection = $this->reviewImageCollectionFactory->create();
        $reviewImageCollection->addFieldToFilter("review_id", ["in" => $reviewIds]);
        $reviewImageCollection->addFieldToFilter("is_edit", ["eq" => ReviewImageRepository::NOT_EDIT]);
        $images = [];
        /** @var ReviewImage $reviewImage */
        foreach ($reviewImageCollection as $reviewImage) {
            $imageParts =  json_decode($reviewImage->getImage(), true);
            if (isset($imageParts["url"])) {
                $images[$reviewImage->getReviewId()][] = $imageParts["url"];
            }
        }
        return $images;
    }

    /**
     * @param int $productId
     * @param int $storeId
     * @return float
     */
    public function getRatingSummary($productId, $storeId)
    {
        try {
            $this->cleanOldData();
            $product = $this->productRepository->getById($productId);
            $this->reviewFactory->create()->getEntitySummary($product, $storeId);
            return (float)$product->getRatingSummary()->getRatingSummary() / 20;
        } catch (Exception $exception) {
            return 0;
        }
    }

    /**
     * @param int $customerId
     * @param Customer[] $customers
     * @return string
     */
    public function getProfilePicture($customerId, $customers)
    {
        if ($customerId == null || !isset($customers[$customerId])) {
            return $profile_picture = $this->assetRepo->getUrlWithParams(
                'Trans_CustomerMyProfile::images/no-profile-photo.png',
                []
            );
        } else {
            try {
                $customer = $customers[$customerId];
                if ($customer->getData('profile_picture')) {
                    return $this->urlBuilder->getUrl(
                        'customermyprofile/myprofile/profilepictureview/',
                        ['image' => base64_encode($customer->getData('profile_picture'))]
                    );
                }
            } catch (LocalizedException | NoSuchEntityException$e) {
            }
        }

        return $this->assetRepo->getUrlWithParams(
            'Trans_CustomerMyProfile::images/no-profile-photo.png',
            []
        );
    }

    /**
     * @param int[] $customerIds
     * @return Customer[]
     * @throws LocalizedException
     */
    private function getCustomers($customerIds)
    {
        /** @var CustomerCollection $customerCollection */
        $customerCollection = $this->customerCollectionFactory
            ->create()
            ->addFieldToFilter("entity_id", ["in" => $customerIds])
            ->addAttributeToSelect(["profile_picture"]);
        $customers = [];
        /** @var Customer $customer */
        foreach ($customerCollection as $customer) {
            $customers[$customer->getId()] = $customer;
        }
        return $customers;
    }

    /**
     * @param ReviewCollection $collection
     * @param SearchCriteria $searchCriteria
     * @return ReviewCollection
     */
    public function itemSort($collection, $searchCriteria)
    {
        $sortOrders = $searchCriteria->getSortOrders();
        if ($sortOrders) {
            /** @var SortOrder $sortOrder */
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == strtolower(SortOrder::SORT_ASC)) ? 'ASC' : 'DESC'
                );
            }
        }
        return $collection;
    }

    /**
     * @param ReviewCollection $collection
     * @param SearchCriteria $searchCriteria
     * @return ReviewCollection
     */
    public function itemFilter($collection, $searchCriteria)
    {
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            $fields = [];
            $conditions = [];
            foreach ($filterGroup->getFilters() as $filter) {
                $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
                if ($filter->getField() == 'product_id') {
                    $fields[] = 'main_table.entity_pk_value';
                } else {
                    $fields[] = 'main_table.' . $filter->getField();
                }
                $conditions[] = [$condition => $filter->getValue()];
            }
            if ($fields) {
                $collection->addFieldToFilter($fields, $conditions);
            }
        }
        return $collection;
    }
}
