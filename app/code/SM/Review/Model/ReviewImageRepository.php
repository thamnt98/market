<?php

namespace SM\Review\Model;

use Exception;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ImageContent;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\UrlInterface;
use SM\FileManagement\Model\UploadImage;
use SM\Review\Api\Data\ReviewImageInterface;
use SM\Review\Api\Data\ReviewImageInterfaceFactory;
use SM\Review\Api\Data\ReviewImageSearchResultsInterface;
use SM\Review\Api\Data\ReviewImageSearchResultsInterfaceFactory;
use SM\Review\Api\ReviewImageRepositoryInterface;
use SM\Review\Model\ResourceModel\ReviewImage\Collection as ReviewImageCollection;
use SM\Review\Model\ResourceModel\ReviewImage\CollectionFactory as ReviewImageCollectionFactory;
use SM\Review\Model\Upload\ImageUploader;

/**
 * Class ReviewImageRepository
 * @package SM\Review\Model
 */
class ReviewImageRepository implements ReviewImageRepositoryInterface
{
    const IS_EDIT = 1;
    const NOT_EDIT = 0;

    const IMAGE_PATH = "sm/review/images";

    /**
     * @var ReviewImageCollectionFactory
     */
    protected $reviewImageCollectionFactory;
    /**
     * @var ReviewImageSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;
    /**
     * @var ReviewImageInterfaceFactory
     */
    protected $reviewImageDataFactory;
    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;
    /**
     * @var ReviewImageFactory
     */
    protected $reviewImageFactory;

    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;
    /**
     * @var UrlInterface
     */
    protected $urlInterface;

    /**
     * @var ImageUploader
     */
    protected $imageUploader;
    /**
     * @var UploadImage
     */
    protected $uploadImage;

    /**
     * ReviewImageRepository constructor.
     * @param ReviewImageCollectionFactory $reviewImageCollectionFactory
     * @param ReviewImageSearchResultsInterfaceFactory $searchResultsFactory
     * @param ReviewImageInterfaceFactory $reviewImageDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param ReviewImageFactory $reviewImageFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param UrlInterface $urlInterface
     * @param ImageUploader $imageUploader
     * @param UploadImage $uploadImage
     */
    public function __construct(
        ReviewImageCollectionFactory $reviewImageCollectionFactory,
        ReviewImageSearchResultsInterfaceFactory $searchResultsFactory,
        ReviewImageInterfaceFactory $reviewImageDataFactory,
        DataObjectHelper $dataObjectHelper,
        ReviewImageFactory $reviewImageFactory,
        CollectionProcessorInterface $collectionProcessor,
        UrlInterface $urlInterface,
        ImageUploader $imageUploader,
        UploadImage $uploadImage
    ) {
        $this->uploadImage = $uploadImage;
        $this->imageUploader = $imageUploader;
        $this->urlInterface = $urlInterface;
        $this->collectionProcessor = $collectionProcessor;
        $this->reviewImageFactory = $reviewImageFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->reviewImageCollectionFactory = $reviewImageCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->reviewImageDataFactory = $reviewImageDataFactory;
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return \SM\Review\Api\Data\ReviewImageSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var ReviewImageSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        /** @var ReviewImageCollection $reviewImageCollection */
        $reviewImageCollection = $this->reviewImageCollectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $reviewImageCollection);

        $reviewImageCollection->setCurPage($searchCriteria->getCurrentPage());
        $reviewImageCollection->setPageSize($searchCriteria->getPageSize());
        $searchResults->setTotalCount($reviewImageCollection->getSize());

        $reviewImages = [];
        /** @var ReviewImage $reviewImage */
        foreach ($reviewImageCollection as $reviewImage) {
            $reviewImages[] = $this->prepareModel($reviewImage);
        }
        $searchResults->setItems($reviewImages);
        return $searchResults;
    }

    /**
     * @param ReviewImage $reviewImageModel
     * @return ReviewImageInterface
     */
    public function prepareModel($reviewImageModel)
    {
        /** @var ReviewImageInterface $reviewImageData */
        $reviewImageData = $this->reviewImageDataFactory->create();

        $this->dataObjectHelper->populateWithArray(
            $reviewImageData,
            $reviewImageModel->getData(),
            'SM\Review\Api\Data\ReviewImageInterface'
        );

        return $reviewImageData;
    }

    /**
     * @param int $reviewId
     * @param array $images
     * @param int $isEdit
     * @return ReviewImageInterface[]
     */
    public function saveImage($reviewId, $images, $isEdit)
    {
        $this->cleanBeforeSave($reviewId);
        $imageData = [];
        foreach ($images as $image) {
            /** @var ReviewImage $reviewImage */
            $reviewImage = $this->reviewImageFactory->create();
            $reviewImage->setReviewId($reviewId);
            $reviewImage->setImage($image);
            $reviewImage->setIsEdit($isEdit);
            try {
                $reviewImage->save();
                $imageData[] = $this->prepareModel($reviewImage);
            } catch (Exception $e) {
            }
        }

        return $imageData;
    }

    /**
     * @param int $reviewId
     */
    private function cleanBeforeSave($reviewId)
    {
        /** @var ReviewImageCollection $reviewImageCollection */
        $reviewImageCollection = $this->reviewImageCollectionFactory->create();
        $reviewImageCollection->addFieldToFilter("review_id", ["eq" => $reviewId]);
        $reviewImageCollection->addFieldToFilter("is_edit", ["eq" => self::IS_EDIT]);

        $reviewImageCollection->walk("delete");
    }

    /**
     * @param ImageContent $imageContent
     * @return bool|string
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function uploadImage(ImageContent $imageContent)
    {
        return $this->uploadImage->uploadImage($imageContent, "", self::IMAGE_PATH);
    }
}
