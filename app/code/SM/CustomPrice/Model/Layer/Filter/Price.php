<?php


namespace SM\CustomPrice\Model\Layer\Filter;


use Amasty\Shopby\Model\Layer\Filter\Traits\FromToDecimal;
use Amasty\Shopby\Model\Source\DisplayMode;
use Magento\Catalog\Model\Layer\Filter\Dynamic\AlgorithmFactory;
use Magento\Search\Model\SearchEngine;

class Price extends \Amasty\Shopby\Model\Layer\Filter\Price
{
    use FromToDecimal;
    protected $priceCurrency;
    protected $resource;
    protected $customerSession;
    protected $priceAlgorithm;
    protected $dataProvider;

    /**
     * @var \Amasty\Shopby\Helper\FilterSetting
     */
    protected $settingHelper;


    /**
     * @var string
     */
    protected $currencySymbol;


    /**
     * @var \Amasty\Shopby\Model\Search\Adapter\Mysql\AggregationAdapter
     */
    protected $aggregationAdapter;

    /**
     * @var \Amasty\Shopby\Model\Request
     */
    protected $shopbyRequest;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\Registry|null
     */
    protected $coreRegistry = null;

    /**
     * @var \Amasty\Shopby\Helper\Group
     */
    protected $groupHelper;

    /**
     * @var SearchEngine
     */
    protected $searchEngine;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    protected $currentAttribute;

    public function __construct(
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Layer $layer,
        \Magento\Catalog\Model\Layer\Filter\Item\DataBuilder $itemDataBuilder,
        \Magento\Catalog\Model\ResourceModel\Layer\Filter\Price $resource,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Search\Dynamic\Algorithm $priceAlgorithm,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        AlgorithmFactory $algorithmFactory,
        \Magento\Catalog\Model\Layer\Filter\DataProvider\PriceFactory $dataProviderFactory,
        \Amasty\Shopby\Helper\FilterSetting $settingHelper,
        \Amasty\Shopby\Model\Search\Adapter\Mysql\AggregationAdapter $aggregationAdapter,
        \Amasty\Shopby\Model\Request $shopbyRequest,
        \Amasty\Shopby\Helper\Group $groupHelper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        SearchEngine $searchEngine,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        array $data = []
    ) {
        parent::__construct($coreRegistry, $filterItemFactory, $storeManager, $layer, $itemDataBuilder, $resource,
            $customerSession, $priceAlgorithm, $priceCurrency, $algorithmFactory, $dataProviderFactory, $settingHelper,
            $aggregationAdapter, $shopbyRequest, $groupHelper, $scopeConfig, $searchEngine, $messageManager, $data);

        $this->_requestVar        = 'price';
        $this->priceCurrency      = $priceCurrency;
        $this->resource           = $resource;
        $this->customerSession    = $customerSession;
        $this->priceAlgorithm     = $priceAlgorithm;
        $this->coreRegistry       = $coreRegistry;
        $this->settingHelper      = $settingHelper;
        $this->currencySymbol     = $priceCurrency->getCurrencySymbol();
        $this->dataProvider       = $dataProviderFactory->create(['layer' => $layer]);
        $this->aggregationAdapter = $aggregationAdapter;
        $this->shopbyRequest      = $shopbyRequest;
        $this->groupHelper        = $groupHelper;
        $this->scopeConfig        = $scopeConfig;
        $this->searchEngine       = $searchEngine;
        $this->messageManager     = $messageManager;

        $this->currentAttribute = $this->_requestVar;
        $this->currentAttribute = $this->customerSession->getOmniFinalPriceAttributeCode();
    }

    /**
     * Apply price range filter
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return \Magento\CatalogSearch\Model\Layer\Filter\Price
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function apply(\Magento\Framework\App\RequestInterface $request)
    {
        if ($this->isApplied()) {
            return $this;
        }

        $filter        = $this->shopbyRequest->getFilterParam($this);
        $noValidate    = 0;
        $filterSetting = $this->settingHelper->getSettingByLayerFilter($this);
        $isSlider      = $filterSetting->getDisplayMode() == DisplayMode::MODE_SLIDER;
        $newValue      = '';

        if (!empty($filter) && is_string($filter)) {
            $copyFilter = $filter;
            $filter     = explode('-', $filter);

            $toValue        = isset($filter[1]) && $filter[1] ? $filter[1] : '';
            $filter         = $filter[0] . '-' . $toValue;
            $validateFilter = $this->dataProvider->validateFilter($copyFilter);

            $values         = explode('-', $copyFilter);
            $displayMode    = $filterSetting->getDisplayMode();
            $includeBorders = $this->isSliderOrFromTo($displayMode) ? self::DELTA_FOR_BORDERS_RANGE : 0;

            //apply delta
            $values[0] = isset($values[0]) && $values[0] ? ((float)$values[0] - $includeBorders) : '';
            $values[1] = isset($values[1]) && $values[1] ? ((float)$values[1] + $includeBorders) : '';

            //apply rate
            $values[0] = $values[0] ? (float)$values[0] / $this->getCurrencyRate() : '';
            $values[1] = $values[1] ? (float)$values[1] / $this->getCurrencyRate() : '';
            $newValue  = $values[0] . '-' . $values[1];

            if (!$validateFilter) {
                $noValidate = 1;
            } else {
                $this->setFromTo($validateFilter[0], $validateFilter[1]);
            }
        }

        $request->setParam($this->getRequestVar(), $newValue ?: $filter);
        $request->setPostValue(self::AM_BASE_PRICE, isset($copyFilter) ? $copyFilter : $filter);

        $apply = $this->grandApply($request);

        if ($noValidate) {
            return $this;
        }

        if (!empty($filter) && !is_array($filter)) {
            if ($isSlider) {
                $this->getLayer()->getProductCollection()->addAttributeToSelect($this->currentAttribute)->addFieldToFilter($this->currentAttribute, $filter);
            }

            if ($this->groupHelper->getGroupsByAttributeId($this->getAttributeModel()->getAttributeId())) {
                $this->getLayer()->getProductCollection()->addAttributeToSelect($this->currentAttribute)->addFieldToFilter(
                    $this->currentAttribute,
                    [
                        'from' => $this->getCurrentFrom(),
                        'to'   => $this->getCurrentTo()
                    ]
                );
            }
        }

        return $apply;
    }

    public function grandApply(\Magento\Framework\App\RequestInterface $request)
    {
        /**
         * Filter must be string: $fromPrice-$toPrice
         */
        $filter = $request->getParam($this->getRequestVar());
        if (!$filter || is_array($filter)) {
            return $this;
        }

        $filterParams = explode(',', $filter);
        $filter       = $this->dataProvider->validateFilter($filterParams[0]);
        if (!$filter) {
            return $this;
        }

        $this->dataProvider->setInterval($filter);
        $priorFilters = $this->dataProvider->getPriorFilters($filterParams);
        if ($priorFilters) {
            $this->dataProvider->setPriorIntervals($priorFilters);
        }

        list($from, $to) = $filter;
        $this->getLayer()->getProductCollection()->addAttributeToSelect($this->currentAttribute)->addFieldToFilter(
            $this->currentAttribute,
            ['from' => $from, 'to' => empty($to) || $from == $to ? $to : $to - self::PRICE_DELTA]
        );

        $this->getLayer()->getState()->addFilter(
            $this->_createItem($this->_renderRangeLabel(empty($from) ? 0 : $from, $to), $filter)
        );

        return $this;
    }
}
