<?php

namespace SM\Review\Block\Customer;

use Magento\Catalog\Model\ProductRepository;
use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use SM\Review\Api\Data\Product\ReviewDetailInterface;
use SM\Review\Api\ReviewedRepositoryInterface;

/**
 * Class EditReviewed
 * @package SM\Review\Block\Customer
 */
class EditReviewed extends Template
{
    /**
     * @var ReviewedRepositoryInterface
     */
    protected $reviewedRepository;
    /**
     * @var CurrentCustomer
     */
    protected $currentCustomer;
    /**
     * @var ProductRepository
     */
    protected $productRepository;
    /**
     * @var ReviewDetailInterface
     */
    protected $review;

    /**
     * EditReviewed constructor.
     * @param Context $context
     * @param ReviewedRepositoryInterface $reviewedRepository
     * @param CurrentCustomer $currentCustomer
     * @param ProductRepository $productRepository
     * @param array $data
     */
    public function __construct(
        Context $context,
        ReviewedRepositoryInterface $reviewedRepository,
        CurrentCustomer $currentCustomer,
        ProductRepository $productRepository,
        array $data = []
    ) {
        $this->productRepository = $productRepository;
        $this->currentCustomer = $currentCustomer;
        $this->reviewedRepository = $reviewedRepository;
        parent::__construct($context, $data);
    }

    protected function _prepareLayout()
    {
        $reviewId = $this->getRequest()->getParam('id', '');

        $this->setReview($this->reviewedRepository->getById($reviewId));
        return parent::_prepareLayout();
    }

    /**
     * @param ReviewDetailInterface $review
     */
    public function setReview($review)
    {
        $this->review = $review;
    }

    /**
     * @return bool|\SM\Review\Api\Data\Product\ReviewDetailInterface
     */
    public function getReview()
    {
        return $this->review;
    }

    /**
     * @return bool|\Magento\Catalog\Api\Data\ProductInterface
     */
    public function getProduct()
    {
        if ($this->getReview()->getProductId()) {
            try {
                return $this->productRepository->getById($this->getReview()->getProductId());
            } catch (NoSuchEntityException $e) {
                return false;
            }
        }
        return false;
    }
}
