<?php

namespace SM\Review\Controller\Customer;

use Magento\Framework\App\Action\Context;
use SM\Review\Api\Data\ReviewEditInterface;
use SM\Review\Api\Data\ReviewEditInterfaceFactory;
use SM\Review\Api\ReviewEditRepositoryInterface;
use SM\Review\Model\ReviewImageRepository;
use SM\Review\Model\Upload\ImageUploader;

/**
 * Class SubmitEdit
 * @package SM\Review\Controller\Customer
 */
class SubmitEdit extends \Magento\Framework\App\Action\Action
{
    /**
     * @var ReviewEditInterfaceFactory
     */
    protected $reviewEditDataFactory;
    /**
     * @var ReviewEditRepositoryInterface
     */
    protected $reviewEditRepository;
    /**
     * @var ReviewImageRepository
     */
    protected $reviewImageRepository;
    /**
     * @var ImageUploader
     */
    protected $imageUploader;

    /**
     * SubmitEdit constructor.
     * @param Context $context
     * @param ReviewEditInterfaceFactory $reviewEditDataFactory
     * @param ReviewEditRepositoryInterface $reviewEditRepository
     * @param ReviewImageRepository $reviewImageRepository
     * @param ImageUploader $imageUploader
     */
    public function __construct(
        Context $context,
        ReviewEditInterfaceFactory $reviewEditDataFactory,
        ReviewEditRepositoryInterface $reviewEditRepository,
        ReviewImageRepository $reviewImageRepository,
        ImageUploader $imageUploader
    ) {
        $this->imageUploader = $imageUploader;
        $this->reviewImageRepository = $reviewImageRepository;
        $this->reviewEditRepository = $reviewEditRepository;
        $this->reviewEditDataFactory = $reviewEditDataFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        try {
            /** @var ReviewEditInterface $reviewEditData */
            $reviewEditData = $this->reviewEditDataFactory->create();
            $reviewEditData->setReviewId($data["review_id"]);
            $reviewEditData->setDetail($data["detail"]);
            $reviewEditData->setTitle($data["title"]);
            $reviewEditData->setVoteValue($data["rating"]);

            $images = [];
            if (isset($data["images"])) {
                foreach ($data["images"] as $image) {
                    $imagePart = json_decode($image, true);
                    if (strpos($image, "/uploads/") !== false) {
                        $result = $this->imageUploader->moveFileFromTmp($imagePart["file"]);
                        if ($result) {
                            $images[] = str_replace("/uploads/", "/images/", $image);
                        }
                    } else {
                        $images[] = $image;
                    }
                }
            } else {
                $data["images"] = null;
            }

            $reviewEdit = $this->reviewEditRepository->create($reviewEditData, $images);

            if (!is_null($reviewEdit)) {
                $this->messageManager->addSuccessMessage(__("You have successfully edited your review."));
                $redirectUrl = $this->_url->getUrl("review/customer/list", ["tab" => "reviewed"]);
            } else {
                $this->messageManager->addErrorMessage(__("Error Occurred."));
                $redirectUrl = $this->_redirect->getRefererUrl();
            }
        } catch (\Exception $exception) {
            $this->messageManager->addErrorMessage(__("Error Occurred."));
            $redirectUrl = $this->_redirect->getRefererUrl();
        }

        $this->_redirect($redirectUrl);
    }
}
