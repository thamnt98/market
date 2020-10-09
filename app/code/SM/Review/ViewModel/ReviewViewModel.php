<?php

namespace SM\Review\ViewModel;

use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Customer\Model\ResourceModel\CustomerRepository;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Asset\Repository;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Review\Model\RatingFactory;
use Magento\Review\Model\ResourceModel\Rating\Collection as RatingCollection;
use Magento\Review\Model\ReviewFactory;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use SM\Review\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use SM\Review\Model\ResourceModel\ReviewImage\Collection as ReviewImageCollection;
use SM\Review\Model\ResourceModel\ReviewImage\CollectionFactory as ReviewImageCollectionFactory;
use SM\Review\Model\ReviewImage;

/**
 * Class ReviewViewModel
 * @package SM\Review\ViewModel
 */
class ReviewViewModel implements ArgumentInterface
{
    /**
     * @var ReviewImageCollectionFactory
     */
    protected $reviewImageCollectionFactory;
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;
    /**
     * @var CurrentCustomer
     */
    protected $currentCustomer;
    /**
     * @var OrderCollectionFactory
     */
    protected $orderCollectionFactory;

    /**
     * @var RatingFactory
     */
    protected $ratingFactory;
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;
    /**
     * @var Repository
     */
    protected $assetRepo;
    /**
     * @var ReviewFactory
     */
    protected $reviewFactory;
    /**
     * @var CustomerRepository
     */
    protected $customerRepository;

    /**
     * ReviewViewModel constructor.
     * @param ReviewImageCollectionFactory $reviewImageCollectionFactory
     * @param ScopeConfigInterface $scopeConfig
     * @param CurrentCustomer $currentCustomer
     * @param OrderCollectionFactory $orderCollectionFactory
     * @param RatingFactory $ratingFactory
     * @param StoreManagerInterface $storeManager
     * @param UrlInterface $urlBuilder
     * @param Repository $assetRepo
     * @param ReviewFactory $reviewFactory
     * @param CustomerRepository $customerRepository
     */
    public function __construct(
        ReviewImageCollectionFactory $reviewImageCollectionFactory,
        ScopeConfigInterface $scopeConfig,
        CurrentCustomer $currentCustomer,
        OrderCollectionFactory $orderCollectionFactory,
        RatingFactory $ratingFactory,
        StoreManagerInterface $storeManager,
        UrlInterface $urlBuilder,
        Repository $assetRepo,
        ReviewFactory $reviewFactory,
        CustomerRepository $customerRepository
    ) {
        $this->customerRepository = $customerRepository;
        $this->reviewFactory = $reviewFactory;
        $this->assetRepo = $assetRepo;
        $this->urlBuilder = $urlBuilder;
        $this->reviewImageCollectionFactory = $reviewImageCollectionFactory;
        $this->scopeConfig = $scopeConfig;
        $this->currentCustomer = $currentCustomer;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->ratingFactory = $ratingFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * @param $reviewId
     * @return array
     */
    public function getReviewImages($reviewId)
    {
        /** @var ReviewImageCollection $reviewImageCollection */
        $reviewImageCollection = $this->reviewImageCollectionFactory->create();
        $reviewImageCollection->addFieldToFilter("review_id", ["eq" => $reviewId]);
        $reviewImageCollection->addFieldToFilter("is_edit", ["eq" => 0]);

        $images = [];
        /** @var ReviewImage $reviewImage */
        foreach ($reviewImageCollection as $reviewImage) {
            $image = json_decode($reviewImage->getImage(), true);
            $images[] = $image["file"];
        }
        return $images;
    }

    /**
     * @return mixed
     */
    public function isUploadEnabled()
    {
        return $this->scopeConfig->getValue(
            'sm_review/upload_image/active',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return string
     */
    public function getCustomerNickname()
    {
        if ($this->currentCustomer->getCustomerId()) {
            $customer = $this->currentCustomer->getCustomer();
            return (($customer->getFirstname()) ? $customer->getFirstname() . " " : "") .
                (($customer->getMiddlename()) ? $customer->getMiddlename() . " " : "") .
                (($customer->getLastname()) ? $customer->getLastname() : "");
        }
        return "";
    }

    /**
     * @param $customerId
     * @return string
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getProfilePicture($customerId)
    {
        if ($customerId == null) {
            $profile_picture = $this->assetRepo->getUrlWithParams('Trans_CustomerMyProfile::images/no-profile-photo.png', []);
        } else {
            $customer = $this->customerRepository->getById($customerId);

            if ($customer->getCustomAttribute('profile_picture')) {
                $profile_picture = $this->urlBuilder->getUrl('customermyprofile/myprofile/profilepictureview/', ['image' => base64_encode($customer->getCustomAttribute('profile_picture')->getValue())]);
            } else {
                $profile_picture = $this->assetRepo->getUrlWithParams('Trans_CustomerMyProfile::images/no-profile-photo.png', []);
            }
        }

        return $profile_picture;
    }

    /**
     * @return int
     */
    public function getCustomerId()
    {
        if ($customerId = $this->currentCustomer->getCustomerId()) {
            return $customerId;
        }
        return 0;
    }

    /**
     * @return RatingCollection
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getRatings()
    {
        return $this->ratingFactory->create()->getResourceCollection()->addEntityFilter(
            'product'
        )->setPositionOrder()->addRatingPerStoreName(
            $this->storeManager->getStore()->getId()
        )->setStoreFilter(
            $this->storeManager->getStore()->getId()
        )->setActiveFilter(
            true
        )->load()->addOptionToItems();
    }

    /**
     * @return DataObject
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getQualityRating()
    {
        $ratingCollection = $this->getRatings();
        $ratingCollection->addFieldToFilter("rating_code", ["eq" => "Quality"]);
        return $ratingCollection->getFirstItem();
    }
}
