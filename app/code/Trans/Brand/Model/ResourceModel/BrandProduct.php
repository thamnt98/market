<?php
/**
 * Class BrandProduct
 *
 * PHP version 7
 *
 * @category Trans
 * @package  Trans_Brand
 * @author   J.P <jaka.pondan@transdigital.co.id>
 */
namespace Trans\Brand\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class BrandProduct
 *
 * PHP version 7
 *
 * @category Trans
 * @package  Trans_Brand
 * @author   J.P <jaka.pondan@transdigital.co.id>
 */
class BrandProduct extends AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('trans_brand_products', 'brand_product_id');
    }
}
