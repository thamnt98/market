<?php
/**
 * Class Collection
 *
 * PHP version 7
 *
 * @category Trans
 * @package  Trans_Brand
 * @author   J.P <jaka.pondan@transdigital.co.id>
 */
namespace Trans\Brand\Model\ResourceModel\BrandProduct;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 *
 * @category Trans
 * @package  Trans_Brand
 * @author   J.P <jaka.pondan@transdigital.co.id>
 */
class Collection extends AbstractCollection
{
    /**
     * Primary field name of table
     *
     * @var string
     */
    protected $_idFieldName = 'brand_product_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Trans\Brand\Model\BrandProduct::class,
            \Trans\Brand\Model\ResourceModel\BrandProduct::class
        );
    }

    /**
     * Return Products of given brandId
     *
     * @param int $brandId brandId
     *
     * @return void
     */
    public function filterBrandProducts($brandId)
    {
        $this->transBrandTable = \Trans\Brand\Model\ResourceModel\Brand::TBL_BRAND;
        $this->transBrandProductsTable = $this->getTable(\Trans\Brand\Model\ResourceModel\Brand::TBL_BRAND_PRODUCTS);

        $this->getSelect()
            ->join(
                ['brand' => $this->transBrandTable],
                'main_table.brand_id= brand.brand_id and brand.status='.\Trans\Brand\Model\Brand::STATUS_ENABLED
            );
        $this->getSelect()->where("brand.brand_id=".$brandId);
    }

    /**
     * Return Brands of given productId
     *
     * @param int $productId productId
     *
     * @return void
     */
    public function filterBrands($productId)
    {
        $this->transBrandTable = \Trans\Brand\Model\ResourceModel\Brand::TBL_BRAND;
        $this->transBrandProductsTable = $this->getTable(\Trans\Brand\Model\ResourceModel\Brand::TBL_BRAND_PRODUCTS);

        $this->getSelect()
            ->join(
                ['brand' => $this->transBrandTable],
                'main_table.brand_id= brand.brand_id and brand.status='.\Trans\Brand\Model\Brand::STATUS_ENABLED
            );
        $this->getSelect()->where("main_table.product_id=".$productId);
    }
}
