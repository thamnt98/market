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

    /**
     * Get product price.
     *
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getProductPrice(\Magento\Catalog\Model\Product $product)
    {
        $priceRender = $this->getPriceRender();

        $price = '';
        if ($priceRender) {
            $price = $priceRender->render(
                \Magento\Catalog\Pricing\Price\FinalPrice::PRICE_CODE,
                $product,
                [
                    'include_container'     => true,
                    'display_minimal_price' => true,
                    'zone'                  => \Magento\Framework\Pricing\Render::ZONE_ITEM_LIST,
                    'list_category_page'    => true,
                ]
            );
        }

        return $price;
    }

    /**
     * Specifies that price rendering should be done for the list of products.
     * (rendering happens in the scope of product list, but not single product)
     *
     * @return \Magento\Framework\Pricing\Render
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getPriceRender()
    {
        return $this->getLayout()
            ->getBlock('product.price.render.default')
            ->setData('is_product_list', true);
    }
}
