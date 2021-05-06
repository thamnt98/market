<?php

/**
 * @category SM
 * @package SM_Theme
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 * @author      Chinhvd <chinhvd@smartosc.com>
 *
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

declare(strict_types=1);

namespace SM\Theme\Block\Product\Widget;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\CatalogInventory\Model\Stock\StockItemRepository;
use Magento\CatalogWidget\Model\Rule;
use Magento\Framework\App\Http\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Url\EncoderInterface;
use Magento\Framework\View\LayoutFactory;
use Magento\Rule\Model\Condition\Sql\Builder;
use Magento\Widget\Helper\Conditions;
use SM\Catalog\Block\Product\ProductList\Item\AddTo\Iteminfo;

class TransCarousel extends \Magento\CatalogWidget\Block\Product\ProductsList
{
    /**
     * Default value for products count that will be shown
     */
    const DEFAULT_PRODUCTS_COUNT = 5;

    /**
     * @var StockItemRepository
     */
    protected $stockItemRepository;

    /**
     * @var Iteminfo
     */
    public $itemInfo;

    /**
     * TransCarousel constructor.
     * @param StockItemRepository $stockItemRepository
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param CollectionFactory $productCollectionFactory
     * @param Visibility $catalogProductVisibility
     * @param Context $httpContext
     * @param Builder $sqlBuilder
     * @param Rule $rule
     * @param Conditions $conditionsHelper
     * @param array $data
     * @param Json|NULL $json
     * @param LayoutFactory|NULL $layoutFactory
     * @param EncoderInterface|NULL $urlEncoder
     * @param Iteminfo $itemInfo
     */
/*    public function __constructx(
        StockItemRepository $stockItemRepository,
        \Magento\Catalog\Block\Product\Context $context,
        CollectionFactory $productCollectionFactory,
        Visibility $catalogProductVisibility,
        Context $httpContext,
        Builder $sqlBuilder,
        Rule $rule,
        Conditions $conditionsHelper,
        array $data = [],
        Json $json = null,
        LayoutFactory $layoutFactory = null,
        EncoderInterface $urlEncoder = null,
        Iteminfo $itemInfo
    ) {
        $this->stockItemRepository = $stockItemRepository;
        $this->itemInfo = $itemInfo;
        parent::__construct(
            $context,
            $productCollectionFactory,
            $catalogProductVisibility,
            $httpContext,
            $sqlBuilder,
            $rule,
            $conditionsHelper,
            $data,
            $json,
            $layoutFactory,
            $urlEncoder
        );
    }*/

    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        CollectionFactory $productCollectionFactory,
        Visibility $catalogProductVisibility,
        Context $httpContext,
        Builder $sqlBuilder,
        Rule $rule,
        Conditions $conditionsHelper,
        CategoryRepositoryInterface $categoryRepository,
        Json $json = null,
        LayoutFactory $layoutFactory = null,
        EncoderInterface $urlEncoder = null,
        StockItemRepository $stockItemRepository,
        Iteminfo $itemInfo,
        array $data = []

    ) {
        $this->stockItemRepository = $stockItemRepository;
        $this->itemInfo = $itemInfo;
        parent::__construct(
            $context,
            $productCollectionFactory,
            $catalogProductVisibility,
            $httpContext,
            $sqlBuilder,
            $rule,
            $conditionsHelper,
            $categoryRepository,
            $data,
            $json,
            $layoutFactory,
            $urlEncoder
        );
    }

    /**
     * Get stock product
     * @param $productId
     * @return float
     * @throws \Exception
     */
    public function getStockItem($productId)
    {
        try {
            $stockItem = $this->stockItemRepository->get($productId);
            $qty = $stockItem->getQty();
        } catch (NoSuchEntityException $e) {
            $qty = 0;
        }
        return $qty;
    }

    /**
     * Get percent sale
     * @param $price
     * @param $finalPrice
     * @return string
     */
    public function getPercent($price, $finalPrice)
    {
        return 100 - round($finalPrice / $price * 100);
    }

    /**
     * @param $product
     * @return float|null
     */
    public function getDiscountPercent($product)
    {
        return $this->itemInfo->getDiscountPercent($product);
    }

    /**
     * @param $product
     * @return bool
     */
    public function isConfigProduct($product)
    {
        return $this->itemInfo->isConfigProduct($product);
    }
}
