<?php


namespace SM\CustomPrice\Plugin;


use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\App\Request\Http;
use SM\CustomPrice\Helper\ProductCollection;

class AddCustomPriceProductCollection
{

    /**
     * @var ProductCollection
     */
    protected $productCollectionHelper;
    /**
     * @var Http
     */
    protected $request;

    /**
     * AddRegionToProductionCollection constructor.
     * @param ProductCollection $productCollectionHelper
     * @param Http              $request
     */
    public function __construct(
        ProductCollection $productCollectionHelper,
        Http $request
    ) {
        $this->productCollectionHelper = $productCollectionHelper;
        $this->request                 = $request;
    }

    /**
     * @param Collection $productCollection
     * @param bool       $printQuery
     * @param bool       $logQuery
     * @return array
     */
    public function beforeLoad(Collection $productCollection, $printQuery = false, $logQuery = false)
    {
        $this->productCollectionHelper->addCustomPriceToProductCollection($productCollection);
        return [$printQuery, $logQuery];
    }
}
