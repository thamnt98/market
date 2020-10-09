<?php
/**
 * Class Brand
 *
 * PHP version 7
 *
 * @category Trans
 * @package  Trans_Brand
 * @author   J.P <jaka.pondan@transdigital.co.id>
 */
namespace Trans\Brand\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * Class Brand
 *
 * @category Trans
 * @package  Trans_Brand
 * @author   J.P <jaka.pondan@transdigital.co.id>
 */
class Brand extends AbstractModel implements IdentityInterface
{
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

    const CACHE_TAG = 'trans_brand_grid';

    /**
     * Cache Tag
     *
     * @var string
     */
    protected $_cacheTag = 'trans_brand_grid';

    /**
     * EventPrefix
     *
     * @var string
     */
    protected $_eventPrefix = 'trans_brand_grid';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Trans\Brand\Model\ResourceModel\Brand::class);
    }

    /**
     * {@inheritdoc}
     *
     * @return array|string[]
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Get products of current Brand
     *
     * @param Brand $object object
     *
     * @return array
     */
    public function getProducts(\Trans\Brand\Model\Brand $object)
    {
        $tbl = $this->getResource()->getTable(
            \Trans\Brand\Model\ResourceModel\Brand::TBL_BRAND_PRODUCTS
        );
        $select = $this->getResource()->getConnection()->select()
        ->from(
            $tbl,
            ['product_id']
        )->where(
            'brand_id = ?',
            (int)$object->getId()
        );
        return $this->getResource()->getConnection()->fetchCol($select);
    }

    /**
     * Get Available Status for enable/disable
     *
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [
            self::STATUS_ENABLED => __('Enabled'),
            self::STATUS_DISABLED => __('Disabled')
        ];
    }
}
