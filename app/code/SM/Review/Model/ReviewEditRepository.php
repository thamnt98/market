<?php

namespace SM\Review\Model;

use Exception;
use Magento\Customer\Model\ResourceModel\CustomerRepository;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\App\Area;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObject;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\DB\TransactionFactory;
use Magento\Framework\UrlInterface;
use Magento\Review\Model\Rating\Option\Vote;
use Magento\Review\Model\ResourceModel\Rating\Option;
use Magento\Review\Model\Review;
use Magento\Review\Model\ReviewFactory;
use Magento\Store\Model\Store;
use SM\Email\Model\Repository\SendEmailRepository;
use SM\Review\Api\Data\ReviewEditInterface;
use SM\Review\Api\Data\ReviewEditInterfaceFactory;
use SM\Review\Api\Data\ReviewEditSearchResultsInterface;
use SM\Review\Api\Data\ReviewEditSearchResultsInterfaceFactory;
use SM\Review\Api\Data\ReviewImageInterface;
use SM\Review\Api\Data\ReviewImageInterfaceFactory;
use SM\Review\Api\ReviewEditRepositoryInterface;
use SM\Review\Model\ResourceModel\ReviewEdit\Collection as ReviewEditCollection;
use SM\Review\Model\ResourceModel\ReviewEdit\CollectionFactory as ReviewEditCollectionFactory;
use SM\Review\Model\ResourceModel\ReviewImage\Collection as ReviewImageCollection;
use SM\Review\Model\ResourceModel\ReviewImage\CollectionFactory as ReviewImageCollectionFactory;
use SM\Review\Model\ResourceModel\Vote\Collection as VoteCollection;
use SM\Review\Model\ResourceModel\Vote\CollectionFactory as VoteCollectionFactory;

/**
 * Class ReviewEditRepository
 * @package SM\Review\Model
 */
class ReviewEditRepository implements ReviewEditRepositoryInterface
{
    /**
     * @var ReviewEditSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;
    /**
     * @var ReviewEditCollectionFactory
     */
    protected $reviewEditCollectionFactory;
    /**
     * @var ReviewEditInterfaceFactory
     */
    protected $reviewEditDataFactory;
    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;
    /**
     * @var ReviewEditFactory
     */
    protected $reviewEditFactory;
    /**
     * @var ReviewFactory
     */
    protected $reviewFactory;
    /**
     * @var VoteCollectionFactory
     */
    protected $voteCollectionFactory;
    /**
     * @var TransactionFactory
     */
    protected $transactionFactory;
    /**
     * @var ReviewImageCollectionFactory
     */
    protected $reviewImageCollectionFactory;
    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;

    /**
     * @var SendEmailRepository
     */
    protected $sendEmailRepository;
    /**
     * @var CustomerRepository
     */
    protected $customerRepository;
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var DataObjectFactory
     */
    protected $dataObjectFactory;

    /**
     * @var ReviewImageInterfaceFactory
     */
    protected $reviewImageDataFactory;
    /**
     * @var UrlInterface
     */
    protected $urlInterface;

    protected $reviewImageRepository;

    protected $optionResource;

    /**\
     * ReviewEditRepository constructor.
     * @param ReviewEditCollectionFactory $reviewEditCollectionFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param ReviewEditSearchResultsInterfaceFactory $searchResultsFactory
     * @param ReviewEditInterfaceFactory $reviewEditDataFactory
     * @param ReviewEditFactory $reviewEditFactory
     * @param ReviewFactory $reviewFactory
     * @param VoteCollectionFactory $voteCollectionFactory
     * @param TransactionFactory $transactionFactory
     * @param ReviewImageCollectionFactory $reviewImageCollectionFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param SendEmailRepository $sendEmailRepository
     * @param CustomerRepository $customerRepository
     * @param ScopeConfigInterface $scopeConfig
     * @param DataObjectFactory $dataObjectFactory
     * @param ReviewImageInterfaceFactory $reviewImageDataFactory
     * @param UrlInterface $urlInterface
     * @param ReviewImageRepository $reviewImageRepository
     * @param Option $optionResource
     */
    public function __construct(
        ReviewEditCollectionFactory $reviewEditCollectionFactory,
        DataObjectHelper $dataObjectHelper,
        ReviewEditSearchResultsInterfaceFactory $searchResultsFactory,
        ReviewEditInterfaceFactory $reviewEditDataFactory,
        ReviewEditFactory $reviewEditFactory,
        ReviewFactory $reviewFactory,
        VoteCollectionFactory $voteCollectionFactory,
        TransactionFactory $transactionFactory,
        ReviewImageCollectionFactory $reviewImageCollectionFactory,
        CollectionProcessorInterface $collectionProcessor,
        SendEmailRepository $sendEmailRepository,
        CustomerRepository $customerRepository,
        ScopeConfigInterface $scopeConfig,
        DataObjectFactory $dataObjectFactory,
        ReviewImageInterfaceFactory $reviewImageDataFactory,
        UrlInterface $urlInterface,
        ReviewImageRepository $reviewImageRepository,
        Option $optionResource
    ) {
        $this->optionResource = $optionResource;
        $this->reviewImageRepository = $reviewImageRepository;
        $this->urlInterface = $urlInterface;
        $this->reviewImageDataFactory = $reviewImageDataFactory;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->scopeConfig = $scopeConfig;
        $this->customerRepository = $customerRepository;
        $this->sendEmailRepository = $sendEmailRepository;
        $this->collectionProcessor = $collectionProcessor;
        $this->reviewImageCollectionFactory = $reviewImageCollectionFactory;
        $this->voteCollectionFactory = $voteCollectionFactory;
        $this->reviewEditCollectionFactory = $reviewEditCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->reviewEditDataFactory = $reviewEditDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->reviewEditFactory = $reviewEditFactory;
        $this->reviewFactory = $reviewFactory;
        $this->transactionFactory = $transactionFactory;
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Magento\Framework\Api\SearchResultsInterface|ReviewEditSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var ReviewEditSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        /** @var ReviewEditCollection $reviewEditCollection */
        $reviewEditCollection = $this->reviewEditCollectionFactory->create();

        $this->collectionProcessor->process($searchCriteria, $reviewEditCollection);

        $reviewEditCollection->setCurPage($searchCriteria->getCurrentPage());
        $reviewEditCollection->setPageSize($searchCriteria->getPageSize());

        $reviewEdits = [];
        /** @var ReviewEdit $reviewEdit */
        foreach ($reviewEditCollection as $reviewEdit) {
            $reviewEdits[] = $this->prepareModel($reviewEdit);
        }
        $searchResults->setItems($reviewEdits);
        return $searchResults;
    }

    /**
     * @param ReviewEdit $reviewEditModel
     * @return ReviewEditInterface
     */
    public function prepareModel($reviewEditModel)
    {
        /** @var ReviewEditInterface $reviewEditData */
        $reviewEditData = $this->reviewEditDataFactory->create();

        $this->dataObjectHelper->populateWithArray(
            $reviewEditData,
            $reviewEditModel->getData(),
            'SM\Review\Api\Data\ReviewEditInterface'
        );

        /** @var ReviewImageCollection $reviewImageCollection */
        $reviewImageCollection = $this->reviewImageCollectionFactory->create();
        $reviewImageCollection->addFieldToFilter("review_id", ["eq" => $reviewEditData->getReviewId()]);
        $reviewImageCollection->addFieldToFilter("is_edit", ["eq" => ReviewImageRepository::IS_EDIT]);

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
                $images[] = $reviewImageData;
            }
        }
        $reviewEditData->setImages($images);

        return $reviewEditData;
    }

    /**
     * @param ReviewEditInterface|ReviewEdit $reviewEdit
     * @param string[] $images
     * @return ReviewEditInterface
     */
    public function create(ReviewEditInterface $reviewEdit, $images)
    {
        /** @var ReviewEditCollection $reviewEditCollection */
        $reviewEditCollection = $this->reviewEditCollectionFactory->create();
        /** @var ReviewEdit $reviewEditModel */
        $reviewEditModel = $reviewEditCollection->loadByReviewId($reviewEdit->getReviewId());

        if (is_null($reviewEditModel)) {
            /** @var ReviewEdit $reviewEditModel */
            $reviewEditModel = $this->reviewEditFactory->create();
        }

        try {
            $optionData = $this->optionResource->loadDataById($reviewEdit->getVoteValue());

            $reviewEditModel->setReviewId($reviewEdit->getReviewId());
            $reviewEditModel->setDetail($reviewEdit->getDetail());
            $reviewEditModel->setTitle($reviewEdit->getTitle());
            $reviewEditModel->setVoteValue($optionData["value"]);
            $reviewEditModel->setImageChanged($reviewEdit->getImageChanged());
            $reviewEditModel->save();

            $this->reviewImageRepository->saveImage(
                $reviewEdit->getReviewId(),
                $images,
                ReviewImageRepository::IS_EDIT
            );

            return $this->prepareModel($reviewEditModel);
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * @param int $entityId
     * @return ReviewEditInterface
     */
    public function getById($entityId)
    {
        /** @var ReviewEdit $reviewEditModel */
        $reviewEditModel = $this->reviewEditFactory->create()->load($entityId);
        if ($reviewEditModel->getId()) {
            return $this->prepareModel($reviewEditModel);
        }
        return null;
    }

    /**
     * @param int $reviewId
     * @return ReviewEditInterface
     */
    public function getByReviewId($reviewId)
    {
        $reviewEditCollection = $this->reviewEditCollectionFactory->create();
        $reviewEditCollection->addFieldToFilter("review_id", ["eq" => $reviewId]);
        if ($reviewEditCollection->getSize()) {
            /** @var ReviewEdit $reviewEditModel */
            $reviewEditModel = $reviewEditCollection->getFirstItem();
            return $this->prepareModel($reviewEditModel);
        }
        return null;
    }

    /**
     * @param ReviewEditInterface $reviewEdit
     * @return bool
     */
    public function apply(ReviewEditInterface $reviewEdit)
    {
        /** @var Review $reviewModel */
        $reviewModel = $this->reviewFactory->create()->load($reviewEdit->getReviewId());
        if ($reviewModel->getId()) {
            try {
                $transaction = $this->transactionFactory->create();
                $reviewModel->setTitle($reviewEdit->getTitle());
                $reviewModel->setDetail($reviewEdit->getDetail());
                $transaction->addObject($reviewModel);

                /** @var VoteCollection $voteCollection */
                $voteCollection = $this->voteCollectionFactory->create();
                $voteCollection->getSelectQuality();
                $voteCollection->addFieldToFilter("review_id", ["eq" => $reviewEdit->getReviewId()]);

                if ($voteCollection->getSize()) {
                    /** @var Vote $vote */
                    $vote = $voteCollection->getLastItem();
                    $vote->setValue($reviewEdit->getVoteValue());
                    $vote->setPercent($reviewEdit->getVoteValue() * 20);
                    $transaction->addObject($vote);
                }
                $transaction->save();
                $reviewModel->aggregate();
                $this->updateImage($reviewEdit->getReviewId());
                $this->sendEmail($reviewModel);
                $this->delete($reviewEdit);
                return true;
            } catch (Exception $e) {
                return false;
            }
        }
        return false;
    }

    /**
     * @param ReviewEditInterface $reviewEdit
     * @return bool
     */
    public function delete(ReviewEditInterface $reviewEdit)
    {
        /** @var Review $reviewEditModel */
        $reviewEditModel = $this->reviewEditFactory->create()->load($reviewEdit->getEntityId());

        if ($reviewEditModel->getId()) {
            try {
                $reviewEditModel->delete();
                return true;
            } catch (Exception $e) {
                return false;
            }
        }
        return false;
    }

    /**
     * @param int $entityId
     * @return bool
     */
    public function deleteById($entityId)
    {
        $reviewEdit = $this->getById($entityId);
        return $this->delete($reviewEdit);
    }

    /**
     * @param int $reviewId
     */
    private function updateImage($reviewId)
    {
        /** @var ReviewImageCollection $oldCollection */
        $oldCollection = $this->reviewImageCollectionFactory->create();
        $oldCollection->addFieldToFilter("review_id", ["eq" => $reviewId]);
        $oldCollection->addFieldToFilter("is_edit", ["eq" => ReviewImageRepository::NOT_EDIT]);
        $oldCollection->walk("delete");

        /** @var ReviewImageCollection $newCollection */
        $newCollection = $this->reviewImageCollectionFactory->create();
        $newCollection->addFieldToFilter("review_id", ["eq" => $reviewId]);
        $newCollection->addFieldToFilter("is_edit", ["eq" => ReviewImageRepository::IS_EDIT]);
        if ($newCollection->getSize()) {
            /** @var ReviewImage $item */
            foreach ($newCollection as $item) {
                $item->setIsEdit(0);
            }
            $newCollection->save();
        }
    }

    /**
     * Delete Edit Image When Edit Rejected
     * @param int $reviewId
     */
    private function cleanImages($reviewId)
    {
        $imageCollection = $this->reviewImageCollectionFactory->create();
        $imageCollection->addFieldToFilter("review_id", ["eq" => $reviewId]);
        $imageCollection->addFieldToFilter("is_edit", ["eq" => 1]);
        $imageCollection->walk("delete");
    }

    /**
     * @param ReviewEditInterface $reviewEdit
     * @return bool
     */
    public function reject(ReviewEditInterface $reviewEdit)
    {
        if ($this->delete($reviewEdit)) {
            $this->cleanImages($reviewEdit->getReviewId());
            return true;
        }
        return false;
    }

    /**
     * @param Review $review
     * @return bool
     */
    public function sendEmail($review)
    {
        try {
            $customer = $this->customerRepository->getById($review->getCustomerId());
            $receiver = $customer->getEmail();

            $requestData = [];
            $requestData["title"] = $review->getTitle();
            $requestData["detail"] = $review->getDetail();
            /** @var DataObject $postObject */
            $postObject = $this->dataObjectFactory->create();
            $postObject->setData($requestData);

            $templateVars = [
                'name' => $customer->getFirstname(),
                'email' => $customer->getEmail()
            ];

            $this->sendEmailRepository->send(
                $this->scopeConfig->getValue(
                    'sm_review/email_edit/email_template',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                ),
                $this->scopeConfig->getValue(
                    'sm_review/email_edit/email_identity',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                ),
                $receiver,
                "",
                $templateVars,
                Store::DEFAULT_STORE_ID,
                Area::AREA_FRONTEND
            );
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
