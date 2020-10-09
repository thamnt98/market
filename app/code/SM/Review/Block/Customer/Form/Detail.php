<?php

namespace SM\Review\Block\Customer\Form;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Helper\ImageFactory;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductRepository;
use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\View\Element\Template\Context;
use SM\Review\Api\Data\Product\ReviewDetailInterface;
use SM\Review\Api\ReviewedRepositoryInterface;
use SM\Review\Api\ReviewImageRepositoryInterface;
use SM\Review\Model\ReviewImageRepository;
use SM\Review\Model\ToBeReviewedRepository;

/**
 * Class Detail
 * @package SM\Review\Block\Customer\Form
 */
class Detail extends \Magento\Framework\View\Element\Template
{

    /**
     * @var ProductInterface| Product
     */
    protected $product;
    /**
     * @var ReviewDetailInterface
     */
    protected $review;
    /**
     * @var ImageFactory
     */
    protected $imageHelperFactory;
    /**
     * @var CurrentCustomer
     */
    protected $customerSession;
    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;
    /**
     * @var ReviewImageRepositoryInterface
     */
    protected $reviewImageRepository;
    /**
     * @var ReviewedRepositoryInterface
     */
    protected $reviewedRepository;
    /**
     * @var ProductRepository
     */
    protected $productRepository;
    /**
     * @var ToBeReviewedRepository
     */
    protected $toBeReviewedRepository;
    /**
     * Detail constructor.
     * @param Context $context
     * @param ImageFactory $imageHelperFactory
     * @param CurrentCustomer $customerSession
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param ReviewImageRepositoryInterface $reviewImageRepository
     * @param ReviewedRepositoryInterface $reviewedRepository
     * @param ProductRepository $productRepository
     * @param ToBeReviewedRepository $toBeReviewedRepository
     * @param array $data
     */
    public function __construct(
        Context $context,
        ImageFactory $imageHelperFactory,
        CurrentCustomer $customerSession,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        ReviewImageRepositoryInterface $reviewImageRepository,
        ReviewedRepositoryInterface $reviewedRepository,
        ProductRepository $productRepository,
        ToBeReviewedRepository $toBeReviewedRepository,
        array $data = []
    ) {
        $this->toBeReviewedRepository = $toBeReviewedRepository;
        $this->productRepository = $productRepository;
        $this->reviewedRepository = $reviewedRepository;
        $this->reviewImageRepository = $reviewImageRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->customerSession = $customerSession;
        $this->imageHelperFactory = $imageHelperFactory;
        parent::__construct($context, $data);
    }

    /**
     * @param ProductInterface| Product $product
     */
    public function setProduct($product)
    {
        $this->product = $product;
    }

    /**
     * @return ProductInterface| Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @return mixed
     */
    public function getProductImage()
    {
        return $this->imageHelperFactory->create()->init($this->getProduct(), 'product_base_image')->getUrl();
    }

    /**
     * @return string
     */
    public function getImageUrl()
    {
        return $this->getUrl("media/sm/review") . "images";
    }

    /**
     * @return int
     */
    public function getVoteValue()
    {
        return $this->getReview()->getVotePercent() / 20;
    }

    /**
     * @return ReviewDetailInterface
     */
    public function getReview()
    {
        return $this->review;
    }

    /**
     * @param ReviewDetailInterface $review
     */
    public function setReview($review)
    {
        $this->review = $review;
    }

    /**
     * @return bool
     */
    public function isEditForm()
    {
        return ($this->getRequest()->getActionName() == "edit");
    }

    /**
     * @return int
     */
    public function getOrderId()
    {
        return $this->getRequest()->getParam("orderId", 0);
    }
}
