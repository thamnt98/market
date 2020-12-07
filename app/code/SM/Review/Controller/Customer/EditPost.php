<?php

namespace SM\Review\Controller\Customer;

use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Review\Controller\Product as ProductController;
use Magento\Framework\Controller\Result\Redirect;

class EditPost extends ProductController implements HttpPostActionInterface
{
    /**
     * @var \SM\Review\Api\ReviewedRepositoryInterface
     */
    private $reviewedRepository;

    /**
     * EditPost constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Review\Model\ReviewFactory $reviewFactory
     * @param \Magento\Review\Model\RatingFactory $ratingFactory
     * @param \Magento\Catalog\Model\Design $catalogDesign
     * @param \Magento\Framework\Session\Generic $reviewSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param \SM\Review\Api\ReviewedRepositoryInterface $reviewedRepository
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Review\Model\ReviewFactory $reviewFactory,
        \Magento\Review\Model\RatingFactory $ratingFactory,
        \Magento\Catalog\Model\Design $catalogDesign,
        \Magento\Framework\Session\Generic $reviewSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \SM\Review\Api\ReviewedRepositoryInterface $reviewedRepository
    ) {
        $this->reviewedRepository = $reviewedRepository;
        parent::__construct($context, $coreRegistry, $customerSession, $categoryRepository, $logger, $productRepository,
            $reviewFactory, $ratingFactory, $catalogDesign, $reviewSession, $storeManager, $formKeyValidator);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|Redirect|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
        if (!$this->formKeyValidator->validate($this->getRequest())) {
            $resultRedirect->setUrl($this->_redirect->getRefererUrl());
            return $resultRedirect;
        }
        if ($this->reviewedRepository->postReview($this->customerSession->getId())) {
            $productId = (int)$this->getRequest()->getParam('id');
            $product = $this->productRepository->getById($productId);
            $this->messageManager->addSuccessMessage(__('Thank you for reviewing %1!', $product->getName()));
        } else {
            $this->messageManager->addErrorMessage(__('We can\'t post your review right now.'));
        }
        $resultRedirect->setUrl($this->_url->getUrl('review/customer/index'));
        return $resultRedirect;
    }


}
