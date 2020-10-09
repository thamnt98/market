<?php

namespace SM\MobileApi\Model\Review;

/**
 * Class Get
 * @package SM\MobileApi\Model\Review
 */
class Get
{
    const IMAGE_REVIEW_URl = 'sm/review/images';
    protected $reviewCollectionFactory;
    protected $storeManager;
    protected $apiReviewFactory;
    protected $apiRatingFactory;
    protected $escaper;
    protected $ratingFactory;
    protected $reviewHelper;
    protected $_reviewsCollection;
    protected $_ratingOptions;
    protected $magentoReview;
    protected $overviewFactory;
    protected $localeDate;
    protected $reviewViewModel;
    protected $customerRepository;
    protected $appEmulation;

    public function __construct(
        \Magento\Review\Model\ResourceModel\Review\CollectionFactory $reviewCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \SM\MobileApi\Model\Data\Review\ReviewFactory $apiReviewFactory,
        \SM\MobileApi\Model\Data\Review\RatingFactory $apiRatingFactory,
        \Magento\Framework\Escaper $escaper,
        \Magento\Review\Model\RatingFactory $ratingFactory,
        \Magento\Review\Helper\Data $reviewHelper,
        \Magento\Review\Model\Review $magentoReview,
        \SM\MobileApi\Model\Data\Review\OverviewFactory $overviewFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \SM\Review\ViewModel\ReviewViewModel $reviewViewModel,
        \Magento\Customer\Model\ResourceModel\CustomerRepository $customerRepository,
        \Magento\Store\Model\App\Emulation $appEmulation
    ) {
        $this->reviewCollectionFactory  = $reviewCollectionFactory;
        $this->storeManager             = $storeManager;
        $this->apiReviewFactory         = $apiReviewFactory;
        $this->apiRatingFactory         = $apiRatingFactory;
        $this->escaper                  = $escaper;
        $this->ratingFactory            = $ratingFactory;
        $this->reviewHelper             = $reviewHelper;
        $this->magentoReview            = $magentoReview;
        $this->overviewFactory          = $overviewFactory;
        $this->localeDate               = $localeDate;
        $this->reviewViewModel          = $reviewViewModel;
        $this->customerRepository       = $customerRepository;
        $this->appEmulation             = $appEmulation;
    }

    /**
     * @param $productId
     * @param $limit
     * @param $p
     * @throws \Magento\Framework\Webapi\Exception
     */
    public function init($productId, $limit, $p)
    {
        if (!$productId) {
            throw new \Magento\Framework\Webapi\Exception(
                __('Product ID not valid.'),
                0,
                \Magento\Framework\Webapi\Exception::HTTP_BAD_REQUEST
            );
        }

        $this->_getReviewsCollection($productId, $limit, $p);
    }

    /**
     * Get number of reviews
     *
     * @return int
     */
    public function getReviewCounter()
    {
        return $this->_getReviewsCollection()->getSize();
    }

    /**
     * Get product reviews
     *
     * @return \SM\MobileApi\Api\Data\Review\ReviewInterface[]
     */
    public function getReviews()
    {
        $collection = $this->_getReviewsCollection();
        $collection->addRateVotes();

        $result = [];
        foreach ($collection as $key => $review) {
            /* @var $review \Magento\Review\Model\Review */
            /* @var $reviewData \SM\MobileApi\Api\Data\Review\ReviewInterface */
            $customerId = $review->getCustomerId();
            $reviewData = $this->apiReviewFactory->create();
            $reviewData->setNickname($review->getNickname());
            $reviewData->setCreateAt($this->formatDate($review->getCreatedAt()));
            $reviewData->setCustomerImage($this->getCustomerImage($customerId));
            $votes = $review->getRatingVotes();
            if ($votes) {
                $ratingsData = [];
                foreach ($votes as $vote) {
                    /* @var $vote \Magento\Review\Model\Rating\Option\Vote */
                    /* @var $ratingData \SM\MobileApi\Api\Data\Review\RatingInterface */
                    $ratingData = $this->apiRatingFactory->create();
                    $ratingData->setTitle($this->escaper->escapeHtml($vote->getRatingCode()));
                    $ratingData->setCode('ratings');
                    $ratingData->setId((string)$vote->getRatingId());
                    $ratingData->setPercent($vote->getPercent());
                    $ratingData->setSelected((string)$vote->getOptionId());
                    $ratingData->setType('radio');
                    $ratingData->setValues($this->_getRatingOptions($vote->getRatingId()));

                    $ratingsData[] = $ratingData;
                }

                $titleData = $this->apiRatingFactory->create();
                $titleData->setTitle(__('Review Title'));
                $titleData->setCode('title');
                $titleData->setType('field');
                $titleData->setSelected($this->escaper->escapeHtml($review->getTitle()));
                $ratingsData[] = $titleData;

                $detailData = $this->apiRatingFactory->create();
                $detailData->setTitle(__('Review Detail'));
                $detailData->setCode('detail');
                $detailData->setType('area');
                $detailData->setSelected($this->escaper->escapeHtml($review->getDetail()));
                $ratingsData[] = $detailData;

                $reviewImage = $this->apiRatingFactory->create();
                $reviewImage->setTitle(__('Review Image'));
                $reviewImage->setCode('image');
                $reviewImage->setType('field');
                $reviewImage->setValues($this->getReviewImages($review->getId()));
                $ratingsData[] = $reviewImage;

                $reviewData->setReview($ratingsData);
            }
            $result[] = $reviewData;
        }

        return $result;
    }

    /**
     * Check guest can write review
     *
     * @return bool
     */
    public function getIsGuestAllowToWrite()
    {
        return $this->reviewHelper->getIsGuestAllowToWrite();
    }

    /**
     * Get collection of reviews
     *
     * @param int $productId
     * @param int $limit
     * @param int $p
     * @return \Magento\Review\Model\ResourceModel\Review\Collection
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function _getReviewsCollection($productId = null, $limit = 4, $p = 1)
    {
        if (null === $this->_reviewsCollection && $productId) {
            $this->_reviewsCollection = $this->reviewCollectionFactory->create()
                ->addStoreFilter($this->storeManager->getStore()->getId())
                ->addStatusFilter(\Magento\Review\Model\Review::STATUS_APPROVED)
                ->addEntityFilter('product', $productId)
                ->setDateOrder()
                ->setPageSize($limit)
                ->setCurPage($p);
        }

        return $this->_reviewsCollection;
    }

    /**
     * Get rating options (star value)
     *
     * @param int $ratingId
     */
    protected function _getRatingOptions($ratingId)
    {
        if (empty($this->_ratingOptions[$ratingId])) {
            $rating = $this->ratingFactory->create()->load($ratingId);
            $ratingOptions = $rating->getOptions();
            foreach ($ratingOptions as $ratingOption) {
                $this->_ratingOptions[$ratingId][] = $ratingOption->getId();
            }
        }

        return $this->_ratingOptions[$ratingId];
    }

    /**
     * @param int $productId
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getProductInfo($productId)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $product = $objectManager->create('Magento\Catalog\Model\Product')->load($productId);
        $storeId = $this->storeManager->getStore()->getId();
        $this->magentoReview->getEntitySummary($product, $storeId);
        return $product;
    }

    /**
     * @param int $product_id
     * @return \SM\MobileApi\Model\Data\Review\Overview
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getOverview($product_id)
    {
        $rating = $this->getProductInfo($product_id)->getRatingSummary();
        $overview = $this->overviewFactory->create();
        if ($rating) {
            $ratingValue = number_format((float)($rating->getRatingSummary() / 20), 2, '.', '');
            $overview->setProductName($this->getProductInfo($product_id)->getName());
            $overview->setRatingSummaryPercent((int)$rating->getRatingSummary());
            $overview->setRatingSummaryAmount((float)$ratingValue);
            $overview->setReviewCounter((int)$rating->getReviewsCount());
        }
        return $overview;
    }

    /**
     * @param string $date
     * @return string
     * @throws \Exception
     */
    protected function formatDate($date)
    {
        $format = \IntlDateFormatter::MEDIUM;
        $date = $date instanceof \DateTimeInterface ? $date : new \DateTime($date);
        return $this->localeDate->formatDateTime(
            $date,
            $format,
            \IntlDateFormatter::NONE,
            null,
            null
        );
    }

    /**
     * @param int $reviewId
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getReviewImages($reviewId)
    {
        $images = $this->reviewViewModel->getReviewImages($reviewId);
        $mediaUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $result = [];
        if (count($images) > 0) {
            foreach ($images as $key => $image) {
                $result[] = $mediaUrl . self::IMAGE_REVIEW_URl . $image;
            }
        }
        return $result;
    }

    /**
     * @param int $customerId
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getCustomerImage($customerId)
    {
        $storeId = $this->storeManager->getStore()->getId();
        $this->appEmulation->startEnvironmentEmulation($storeId, \Magento\Framework\App\Area::AREA_FRONTEND, true);
        $image = $this->reviewViewModel->getProfilePicture($customerId);
        $this->appEmulation->stopEnvironmentEmulation();
        return $image;
    }
}
