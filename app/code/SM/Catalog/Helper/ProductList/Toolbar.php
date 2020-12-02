<?php

/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Catalog
 *
 * Date: April, 18 2020
 * Time: 2:39 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Catalog\Helper\ProductList;

class Toolbar
{
    const SORT_BY_BEST_SELLERS      = 'best_sellers';
    const SORT_BY_PRICE_LOW_TO_HIGH = 'price_low_to_high';
    const SORT_BY_PRICE_HIGH_TO_LOW = 'price_high_to_low';
    const SORT_BY_HIGHEST_RATING    = 'rating_summary';
    const SORT_BY_NEW               = 'created_at';
    const SORT_BY_DISCOUNT          = 'discount_percent';

    const SORT_BY_DIRECTION_DESC = 'desc';
    const SORT_BY_DIRECTION_ASC  = 'asc';

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Customer\Model\SessionFactory
     */
    protected $customerSessionFactory;

    /**
     * Toolbar constructor.
     *
     * @param \Magento\Customer\Model\SessionFactory     $customerSessionFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Customer\Model\SessionFactory $customerSessionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
        $this->customerSessionFactory = $customerSessionFactory;
    }

    /**
     * @return array
     */
    public static function getAdditionalOptions()
    {
        return [
            self::SORT_BY_BEST_SELLERS      => __('Best Sellers'),
            self::SORT_BY_PRICE_LOW_TO_HIGH => __('Price: Low to High'),
            self::SORT_BY_PRICE_HIGH_TO_LOW => __('Price: High to Low'),
            self::SORT_BY_HIGHEST_RATING    => __('Highest Rating'),
            self::SORT_BY_NEW               => __('New'),
            self::SORT_BY_DISCOUNT          => __('Discount')
        ];
    }

    /**
     * Get additional options direction.
     *
     * @return array
     */
    public static function getDirection()
    {
        return [
            self::SORT_BY_BEST_SELLERS      => self::SORT_BY_DIRECTION_DESC,
            self::SORT_BY_PRICE_LOW_TO_HIGH => self::SORT_BY_DIRECTION_ASC,
            self::SORT_BY_PRICE_HIGH_TO_LOW => self::SORT_BY_DIRECTION_DESC,
            self::SORT_BY_HIGHEST_RATING    => self::SORT_BY_DIRECTION_DESC,
            self::SORT_BY_NEW               => self::SORT_BY_DIRECTION_DESC,
            self::SORT_BY_DISCOUNT          => self::SORT_BY_DIRECTION_DESC
        ];
    }

    /**
     * Get method "sort by" by sort by key.
     *
     * @param $sortKey
     *
     * @return string
     */
    public function getAddSortByMethodName($sortKey)
    {
        return 'addSortBy' . str_replace(' ', '', ucwords(str_replace('_', ' ', $sortKey)));
    }

    /**
     * Add sort by rating to collection.
     *
     * @param \Magento\Catalog\Model\ResourceModel\Collection\AbstractCollection $collection
     * @param string                                                  $direction
     */
    public function addSortByRatingSummary($collection, $direction)
    {
        $joinAlias = 'sort_by_rate';
        try {
            $collection->getSelect()
                ->joinLeft(
                    [$joinAlias => 'review_entity_summary'],
                    sprintf(
                        "`${joinAlias}`.entity_pk_value=`e`.entity_id
                        AND `${joinAlias}`.entity_type = 1
                        AND `${joinAlias}`.store_id = %d",
                        $this->storeManager->getStore()->getId()
                    ),
                    []
                )->order("${joinAlias}.rating_summary ${direction}");
        } catch (\Exception $e) {
        }
    }

    /**
     * Add sort by discount percent to collection.
     *
     * @param \Magento\Catalog\Model\ResourceModel\Collection\AbstractCollection $collection
     * @param string                             $direction
     */
    public function addSortByDiscountPercent($collection, $direction)
    {
        $field = 'search_result.' . \SM\Search\Helper\ProductList\Sort::DISCOUNT_FIELD_NAME;
        $collection->getSelect()->order("{$field} {$direction}");
    }

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Collection\AbstractCollection $collection
     * @param                                                                    $direction
     */
    public function addSortByPrice($collection, $direction)
    {
        $field = 'search_result.' . \SM\Search\Helper\ProductList\Sort::PRICE_FIELD_NAME;
        $collection->getSelect()->order("{$field} {$direction}");
    }
}
