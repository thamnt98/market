<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_LayeredNavigation
 *
 * Date: May, 22 2020
 * Time: 2:20 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\LayeredNavigation\Model\AmastyShopby\Layer\Filter;

use Amasty\Shopby\Model\Layer\Filter\OnSale\Helper;
use Magento\Search\Model\SearchEngine;

class OnSale extends \Amasty\Shopby\Model\Layer\Filter\OnSale
{
    /**
     * @var \Amasty\Shopby\Model\Request
     */
    private $shopbyRequest;

    public function __construct(
        \Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Layer $layer,
        \Magento\Catalog\Model\Layer\Filter\Item\DataBuilder $itemDataBuilder,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Amasty\Shopby\Model\Search\Adapter\Mysql\AggregationAdapter $aggregationAdapter,
        \Amasty\Shopby\Model\Request $shopbyRequest,
        Helper $helper,
        \Amasty\Shopby\Helper\FilterSetting $settingHelper,
        SearchEngine $searchEngine,
        array $data = []
    ) {
        parent::__construct(
            $filterItemFactory,
            $storeManager,
            $layer,
            $itemDataBuilder,
            $scopeConfig,
            $aggregationAdapter,
            $shopbyRequest,
            $helper,
            $settingHelper,
            $searchEngine,
            $data
        );
        $this->shopbyRequest = $shopbyRequest;
    }

    protected function _getItemsData()
    {
        $items = parent::_getItemsData();

        foreach ($items as &$item) {
            if ($item['value'] === self::FILTER_ON_SALE) {
                $item['label'] = __('All Special Offers');
            }
        }

        return $items;
    }

    public function apply(\Magento\Framework\App\RequestInterface $request)
    {
        if ($this->isApplied()) {
            return $this;
        }

        $filter = $this->shopbyRequest->getFilterParam($this);
        if (!in_array($filter, [self::FILTER_ON_SALE])) {
            return $this;
        }

        $this->setCurrentValue($filter);
        if ($filter == self::FILTER_ON_SALE) {
            $this->getLayer()->getProductCollection()->addFieldToFilter('am_on_sale', $filter);
            $name = __('All Special Offers');
            $this->getLayer()->getState()->addFilter($this->_createItem($name, $filter));
        }
        return $this;
    }
}
