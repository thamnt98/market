<?php

namespace SM\Review\Controller\Product;

use Exception;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Design;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Registry;
use Magento\Framework\Session\Generic;
use Magento\Review\Controller\Product as ProductController;
use Magento\Review\Model\RatingFactory;
use Magento\Review\Model\Review;
use Magento\Review\Model\ReviewFactory;
use Magento\Sales\Model\OrderRepository;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use SM\Review\Api\Data\ReviewDataInterfaceFactory;
use SM\Review\Model\ReviewImageRepository;
use SM\Review\Model\ReviewRepository;
use SM\Review\Model\Upload\ImageUploader;

class Post extends ProductController implements HttpPostActionInterface
{
    const MAXIMUM_SIZE = 1048576;
    const MAXIMUM_COUNT = 3;
    /**
     * @var ReviewImageRepository
     */
    protected $reviewImageRepository;
    /**
     * @var OrderRepository
     */
    protected $orderRepository;
    /**
     * @var ImageUploader
     */
    protected $imageUploader;
    /**
     * @var ReviewRepository
     */
    protected $reviewRepository;
    /**
     * @var ReviewDataInterfaceFactory
     */
    protected $reviewDataFactory;

    /**
     * Post constructor.
     * @param Context $context
     * @param Registry $coreRegistry
     * @param Session $customerSession
     * @param CategoryRepositoryInterface $categoryRepository
     * @param LoggerInterface $logger
     * @param ProductRepositoryInterface $productRepository
     * @param ReviewFactory $reviewFactory
     * @param RatingFactory $ratingFactory
     * @param Design $catalogDesign
     * @param Generic $reviewSession
     * @param StoreManagerInterface $storeManager
     * @param Validator $formKeyValidator
     * @param ReviewImageRepository $reviewImageRepository
     * @param OrderRepository $orderRepository
     * @param ImageUploader $imageUploader
     * @param ReviewRepository $reviewRepository
     * @param ReviewDataInterfaceFactory $reviewDataFactory
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        Session $customerSession,
        CategoryRepositoryInterface $categoryRepository,
        LoggerInterface $logger,
        ProductRepositoryInterface $productRepository,
        ReviewFactory $reviewFactory,
        RatingFactory $ratingFactory,
        Design $catalogDesign,
        Generic $reviewSession,
        StoreManagerInterface $storeManager,
        Validator $formKeyValidator,
        ReviewImageRepository $reviewImageRepository,
        OrderRepository $orderRepository,
        ImageUploader $imageUploader,
        ReviewRepository $reviewRepository,
        ReviewDataInterfaceFactory $reviewDataFactory
    ) {
        $this->reviewDataFactory = $reviewDataFactory;
        $this->reviewRepository = $reviewRepository;
        $this->imageUploader = $imageUploader;
        $this->orderRepository = $orderRepository;
        $this->reviewImageRepository = $reviewImageRepository;
        parent::__construct(
            $context,
            $coreRegistry,
            $customerSession,
            $categoryRepository,
            $logger,
            $productRepository,
            $reviewFactory,
            $ratingFactory,
            $catalogDesign,
            $reviewSession,
            $storeManager,
            $formKeyValidator
        );
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|Redirect|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        if (!$this->formKeyValidator->validate($this->getRequest())) {
            $resultRedirect->setUrl($this->_redirect->getRefererUrl());
            return $resultRedirect;
        }

        $data = $this->getRequest()->getPostValue();
        $rating = isset($data["rating"]) ? $data["rating"] : null;
        if (is_null($rating)) {
            $this->messageManager->addErrorMessage(__('Please select rating.'));
            $resultRedirect->setUrl($this->getUrlAfterSubmitFailed());
            return $resultRedirect;
        }
        // Check order exist
        if (isset($data["order_id"])) {
            $orderId = $data["order_id"];
        } else {
            $this->messageManager->addErrorMessage(__('We can\'t post your review right now.'));
            $resultRedirect->setUrl($this->getUrlAfterSubmitFailed());
            return $resultRedirect;
        }
        try {
            $this->orderRepository->get($orderId);
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage(__('We can\'t post your review right now.'));
            $resultRedirect->setUrl($this->getUrlAfterSubmitFailed());
            return $resultRedirect;
        }
        if (isset($data["images"])) {
            if (count($data["images"]) > self::MAXIMUM_COUNT) {
                $this->messageManager->addErrorMessage(__('You can select only 3 images.'));
                $resultRedirect->setUrl($this->getUrlAfterSubmitFailed());
                return $resultRedirect;
            }
            foreach ($data["images"] as $image) {
                $imagePart = json_decode($image, true);
                if (isset($imagePart["size"])) {
                    if ($imagePart["size"] > self::MAXIMUM_SIZE) {
                        $this->messageManager->addErrorMessage(__('You can only choose pictures under 1 MB.'));
                        $resultRedirect->setUrl($this->getUrlAfterSubmitFailed());
                        return $resultRedirect;
                    }
                }
            }
        }
        if (($product = $this->initProduct()) && !empty($data)) {
            /** @var Review $review */
            $review = $this->reviewFactory->create()->setData($data);
            $review->unsetData('review_id');

            $validate = $review->validate();
            if ($validate === true) {
                try {
                    $reviewData = $this->reviewDataFactory->create();
                    $reviewData
                        ->setTitle(isset($data["title"]) ? $data["title"] : "")
                        ->setDetail(isset($data["detail"]) ? $data["detail"] : "")
                        ->setProductId($product->getId())
                        ->setStoreId($this->storeManager->getStore()->getId())
                        ->setOrderId($orderId)
                        ->setRating(isset($data["rating"]) ? $data["rating"] : 0);

                    $images = [];
                    if (isset($data["images"])) {
                        foreach ($data["images"] as $image) {
                            $imagePart = json_decode($image, true);
                            $result = $this->imageUploader->moveFileFromTmp($imagePart["file"]);
                            if ($result && isset($imagePart["file"])) {
                                $images[] = $imagePart["file"];
                            }
                        }
                    }
                    $this->reviewRepository->create($reviewData, $images, $this->customerSession->getCustomerId());

                    $this->messageManager->addSuccessMessage(__('Thank you for reviewing %1', $product->getName()));
                    $resultRedirect->setUrl($this->getUrlAfterSubmitSuccess());
                } catch (Exception $e) {
                    $this->messageManager->addErrorMessage(__('We can\'t post your review right now.'));
                    $resultRedirect->setUrl($this->getUrlAfterSubmitFailed());
                }
            } else {
                if (is_array($validate)) {
                    foreach ($validate as $errorMessage) {
                        $this->messageManager->addErrorMessage($errorMessage);
                    }
                } else {
                    $this->messageManager->addErrorMessage(__('We can\'t post your review right now.'));
                }
                $resultRedirect->setUrl($this->getUrlAfterSubmitFailed());
            }
        }

        return $resultRedirect;
    }

    /**
     * @return string
     */
    public function getUrlAfterSubmitSuccess()
    {
        $part = explode("/", $this->_redirect->getRefererUrl());
        if (isset($part[4])) {
            if ($part[4] == "customer") {
                return $this->_url->getUrl("review/customer/index", ["tab" => "reviewed"]);
            }
        }
        return $this->_redirect->getRefererUrl();
    }

    /**
     * @return string
     */
    public function getUrlAfterSubmitFailed()
    {
        $part = explode("/", $this->_redirect->getRefererUrl());
        if (isset($part[4])) {
            if ($part[4] == "customer") {
                return $this->_url->getUrl("review/customer/index");
            }
        }
        return $this->_redirect->getRefererUrl();
    }
}
