<?php

namespace SM\Review\Block\Product\View;

use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\View\Element\Template;
use Magento\Review\Model\Review;
use SM\Review\Api\Data\ReviewDataInterface;
use SM\Review\Model\ReviewRepository;

/**
 * Class ListView
 * @package SM\Review\Block\Product
 */
class ListView extends Template
{
    /**
     * @var ReviewRepository
     */
    protected $reviewRepository;
    /**
     * @var ProductRepository
     */
    protected $productRepository;
    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * ListView constructor.
     * @param Template\Context $context
     * @param ReviewRepository $reviewRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param ProductRepository $productRepository
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        ReviewRepository $reviewRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        ProductRepository $productRepository,
        array $data = []
    ) {
        $this->productRepository = $productRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->reviewRepository = $reviewRepository;
        parent::__construct($context, $data);
    }

    /**
     * @return array|ReviewDataInterface[]
     */
    public function getReviews()
    {
        if ($this->getProductId() == 0) {
            return [];
        } else {
            $criteria = $this->searchCriteriaBuilder
                ->addFilter(ReviewDataInterface::STATUS_ID, Review::STATUS_APPROVED)
                ->addFilter(ReviewDataInterface::PRODUCT_ID, $this->getProductId())
                ->create();
            $searchResults = $this->reviewRepository->getList($criteria);
            return $searchResults->getItems();
        }
    }

    /**
     * @return mixed
     */
    public function getProductId()
    {
        return $this->getRequest()->getParam("id", 0);
    }

    /**
     * @return mixed
     */
    public function getReviewCollection()
    {
        return $this->reviewRepository->getReviewCollection();
    }

    /**
     * @return \Magento\Catalog\Api\Data\ProductInterface|mixed|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getProduct()
    {
        return $this->productRepository->getById($this->getProductId());
    }
}
