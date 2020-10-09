<?php

/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_LayeredNavigation
 *
 * Date: May, 17 2020
 * Time: 5:03 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\LayeredNavigation\Model\AmastyShopby\Layer\Filter;

class Rating extends \Amasty\Shopby\Model\Layer\Filter\Rating
{
    use \Amasty\Shopby\Model\Layer\Filter\Traits\CustomTrait;

    /**
     * @var \Amasty\Shopby\Model\Request
     */
    protected $shopbyRequest;

    /**
     * @var string
     */
    protected $attributeCode = 'rating_summary';

    /**
     * @var \Amasty\Shopby\Helper\FilterSetting
     */
    protected $settingHelper;

    /**
     * @var \Magento\Search\Model\SearchEngine
     */
    protected $searchEngine;

    /**
     * @var \Amasty\Shopby\Model\Search\Adapter\Mysql\AggregationAdapter
     */
    protected $aggregationAdapter;

    /**
     * @var \Magento\Catalog\Model\Layer\Filter\ItemFactory
     */
    protected $filterItemFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Catalog\Model\Layer
     */
    protected $layer;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\View\Element\BlockFactory
     */
    protected $blockFactory;


    /**
     * Rating constructor.
     *
     * @param \Magento\Catalog\Model\Layer\Filter\ItemFactory              $filterItemFactory
     * @param \Magento\Store\Model\StoreManagerInterface                   $storeManager
     * @param \Magento\Catalog\Model\Layer                                 $layer
     * @param \Magento\Catalog\Model\Layer\Filter\Item\DataBuilder         $itemDataBuilder
     * @param \Magento\Framework\App\Config\ScopeConfigInterface           $scopeConfig
     * @param \Magento\Framework\View\Element\BlockFactory                 $blockFactory
     * @param \Amasty\Shopby\Model\Search\Adapter\Mysql\AggregationAdapter $aggregationAdapter
     * @param \Amasty\Shopby\Model\Request                                 $shopbyRequest
     * @param \Magento\Search\Model\SearchEngine                           $searchEngine
     * @param \Amasty\Shopby\Helper\FilterSetting                          $settingHelper
     * @param array                                                        $data
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function __construct(
        \Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Layer $layer,
        \Magento\Catalog\Model\Layer\Filter\Item\DataBuilder $itemDataBuilder,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\View\Element\BlockFactory $blockFactory,
        \Amasty\Shopby\Model\Search\Adapter\Mysql\AggregationAdapter $aggregationAdapter,
        \Amasty\Shopby\Model\Request $shopbyRequest,
        \Magento\Search\Model\SearchEngine $searchEngine,
        \Amasty\Shopby\Helper\FilterSetting $settingHelper,
        array $data = []
    ) {
        parent::__construct(
            $filterItemFactory,
            $storeManager,
            $layer,
            $itemDataBuilder,
            $scopeConfig,
            $blockFactory,
            $aggregationAdapter,
            $shopbyRequest,
            $searchEngine,
            $settingHelper,
            $data
        );

        $this->shopbyRequest = $shopbyRequest;
        $this->settingHelper = $settingHelper;
        $this->searchEngine = $searchEngine;
        $this->aggregationAdapter = $aggregationAdapter;
        $this->filterItemFactory = $filterItemFactory;
        $this->storeManager = $storeManager;
        $this->layer = $layer;
        $this->itemDataBuilder = $itemDataBuilder;
        $this->scopeConfig = $scopeConfig;
        $this->blockFactory = $blockFactory;
    }

    protected function _getItemsData()
    {
        if ($this->isHide()) {
            return [];
        }

        $optionsFacetedData = $this->getFacetedData();
        $listData = [];
        $allCount = 0;
        for ($i = 5; $i >= 1; $i--) {
            $count = isset($optionsFacetedData[$i]) ? $optionsFacetedData[$i]['count'] : 0;

            $allCount += $count;

            $listData[] = [
                'label' => $this->getLabelHtml($i),
                'value' => $i,
                'count' => $allCount,
                'real_count' => $count,
            ];
        }

        foreach ($listData as $data) {
            $this->itemDataBuilder->addItemData(
                $data['label'],
                $data['value'],
                $data['count']
            );
        }

        return $this->itemDataBuilder->build();
    }

    protected function getLabelHtml($countStars)
    {
        if ($countStars == 6) {
            return __('Not Yet Rated');
        }

        $percent = $countStars * 20;

        return '<div class="rating-summary">' .
                    '<div class="rating-result" title="' . $percent . '%">' .
                        '<span style="width:' . $percent . '%">' .
                            '<span>' . $countStars . '</span>' .
                        '</span>' .
                    '</div>' .
                '</div>';
    }
}
