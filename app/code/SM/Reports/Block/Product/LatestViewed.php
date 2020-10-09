<?php

declare(strict_types=1);

namespace SM\Reports\Block\Product;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Catalog\Block\Product\Context;
use Magento\Customer\Model\Session as CustomerSession;
use SM\Reports\Api\Repository\ReportViewedProductSummaryRepositoryInterface;

class LatestViewed extends AbstractProduct
{
    const BLOCK_NAME = 'latest_viewed_products';
    const PRODUCTS = 'products';

    /**
     * Path to template file in theme.
     *
     * @var string
     */
    protected $_template = 'product/latest_viewed.phtml';

    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * @var ReportViewedProductSummaryRepositoryInterface
     */
    protected $repository;

    /**
     * LatestViewed constructor.
     * @param Context $context
     * @param CustomerSession $customerSession
     * @param ReportViewedProductSummaryRepositoryInterface $repository
     * @param array $data
     */
    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        ReportViewedProductSummaryRepositoryInterface $repository,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->customerSession = $customerSession;
        $this->repository = $repository;
    }

    /**
     * @return ProductInterface[]
     * @codeCoverageIgnore
     */
    public function getProducts(): array
    {
        if (!$this->customerSession->getCustomerId()) {
            return [];
        }
        $searchResult = $this->repository->getRecommendationProducts((int) $this->customerSession->getCustomerId());
        return $searchResult->getProducts();
    }
}
