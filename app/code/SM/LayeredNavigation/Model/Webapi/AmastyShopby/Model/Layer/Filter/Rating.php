<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\LayeredNavigation\Model\Webapi\AmastyShopby\Model\Layer\Filter;

use Magento\Framework\Exception\StateException;

/**
 * Class Rating
 * @package SM\LayeredNavigation\Model\Webapi\AmastyShopby\Model\Layer\Filter
 */
class Rating extends \Amasty\Shopby\Model\Layer\Filter\Rating
{
    use \Amasty\Shopby\Model\Layer\Filter\Traits\CustomTrait;

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
     * @var \Amasty\Shopby\Model\Search\Adapter\Mysql\AggregationAdapter
     */
    protected $aggregationAdapter;

    /**
     * @var \Magento\Search\Model\SearchEngine
     */
    protected $searchEngine;

    /**
     * @var \Amasty\Shopby\Helper\FilterSetting
     */
    protected $settingHelper;

    /**
     * @var \Amasty\Shopby\Model\Request
     */
    private $shopbyRequest;

    /**
     * @var string
     */
    protected $attributeCode = 'rating_summary';

    /**
     * Rating constructor.
     * @param \Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\Layer $layer
     * @param \Magento\Catalog\Model\Layer\Filter\Item\DataBuilder $itemDataBuilder
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\View\Element\BlockFactory $blockFactory
     * @param \Amasty\Shopby\Model\Search\Adapter\Mysql\AggregationAdapter $aggregationAdapter
     * @param \Amasty\Shopby\Model\Request $shopbyRequest
     * @param \Magento\Search\Model\SearchEngine $searchEngine
     * @param \Amasty\Shopby\Helper\FilterSetting $settingHelper
     * @param array $data
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
        $this->filterItemFactory = $filterItemFactory;
        $this->storeManager = $storeManager;
        $this->layer = $layer;
        $this->itemDataBuilder = $itemDataBuilder;
        $this->scopeConfig = $scopeConfig;
        $this->blockFactory = $blockFactory;
        $this->aggregationAdapter = $aggregationAdapter;
        $this->searchEngine = $searchEngine;
        $this->settingHelper = $settingHelper;
    }

    /**
     * Get data array for building category filter items
     *
     * @return array
     */
    protected function _getItemsData()
    {
        if ($this->isHide()) {
            return [];
        }

        try {
            $optionsFacetedData = $this->getFacetedData();
        } catch (StateException $e) {
            $optionsFacetedData = [];
        }

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

    /**
     * @param int $countStars
     * @return \Magento\Framework\Phrase|string
     */
    private function getLabelHtml($countStars)
    {
        if ($countStars == 6) {
            return __('Not Yet Rated');
        }

        return $countStars;
    }

    /**
     * @override
     * @return \Magento\Framework\Phrase
     */
    public function getName()
    {
        return __('Rating');
    }
}
