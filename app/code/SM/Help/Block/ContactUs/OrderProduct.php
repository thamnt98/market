<?php

/**
 * @category SM
 * @package SM_Help
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author      Chinhvd <chinhvd@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\Help\Block\ContactUs;

use Magento\Catalog\Helper\Image;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;
use Magento\Framework\View\Element\Template;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;

class OrderProduct extends Template
{
    /**
     * @var Image
     */
    protected $imageHelper;

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var PriceHelper
     */
    protected $priceHelper;

    /**
     * @param PriceHelper $priceHelper
     * @param ProductRepositoryInterface $productRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param OrderRepositoryInterface $orderRepository
     * @param Template\Context $context
     * @param Image $imageHelper
     * @param array $data
     */
    public function __construct(
        PriceHelper $priceHelper,
        ProductRepositoryInterface $productRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        OrderRepositoryInterface $orderRepository,
        Template\Context $context,
        Image $imageHelper,
        array $data = []
    ) {
        $this->productRepository     = $productRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->orderRepository       = $orderRepository;
        $this->imageHelper           = $imageHelper;
        $this->priceHelper           = $priceHelper;
        parent::__construct($context, $data);
    }

    /**
     * Get Selected Order
     * @param $order
     * @return \Magento\Sales\Api\Data\OrderInterface
     */
    public function getOrder($order)
    {
        try {
            $orderId = $order['data']['orderId'];
            return $this->orderRepository->get($orderId);
        } catch (\Exception $e) {
        }
    }

    /**
     * * Get Selected Product
     * @param $product
     * @return \Magento\Catalog\Api\Data\ProductInterface[]
     */
    public function getProduct($product)
    {
        try {
            $productIds = $product['data']['productIDs'];
            if (isset($productIds)) {
                $productId = array();
                foreach ($productIds as $key => $value) {
                    array_push($productId, $value);
                }
                $searchCriteria = $this->searchCriteriaBuilder
                    ->addFilter('entity_id', $productId, 'in')
                    ->create();
                return $this->productRepository->getList($searchCriteria)->getItems();
            }
        } catch (\Exception $e) {
        }
    }

    /**
     * Get Thumbnail Product of Order
     * @param $item
     * @param $width
     * @param $height
     * @return string
     */
    public function getImage($item, $width, $height)
    {
        return $this->imageHelper->init($item, 'cart_page_product_thumbnail')
            ->setImageFile($item->getImage())
            ->resize($width, $height)
            ->getUrl();
    }

    /**
     * Get currency symbol for current locale and currency code
     *
     * @param $price
     * @return string
     */
    public function getPrice($price)
    {
        return $this->priceHelper->currency($price, true, false);
    }

    /**
     * @param $orderId
     * @return \Magento\Sales\Api\Data\OrderInterface
     */
    public function getParentOrder($orderId)
    {
        $order = $this->orderRepository->get($orderId);
        $parentId = $order->getData('parent_order');
        return $this->orderRepository->get($parentId);
    }
}
