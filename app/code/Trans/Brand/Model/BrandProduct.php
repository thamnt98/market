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
namespace Trans\Brand\Model;

use Magento\Framework\DataObject\IdentityInterface;

/**
 * Class BrandProduct
 *
 * @category Trans
 * @package  Trans_Brand
 * @author   J.P <jaka.pondan@transdigital.co.id>
 */
class BrandProduct extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Trans\Brand\Model\ResourceModel\BrandProduct::class);
    }
}
