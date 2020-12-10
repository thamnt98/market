<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_LayeredNavigation
 *
 * Date: May, 21 2020
 * Time: 6:31 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\LayeredNavigation\Model\AmastyShopby\Layer\Filter;

use Magento\Search\Model\SearchEngine;

class Price extends \SM\CustomPrice\Model\Layer\Filter\Price
{
    /**
     * @var \Amasty\Shopby\Helper\Group
     */
    protected $groupHelper;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var \Magento\Catalog\Model\Layer\Filter\DataProvider\Price
     */
    protected $dataProvider;


    /**
     * Price constructor.
     * @param \Magento\Framework\Registry                                   $coreRegistry
     * @param \Magento\Catalog\Model\Layer\Filter\ItemFactory               $filterItemFactory
     * @param \Magento\Store\Model\StoreManagerInterface                    $storeManager
     * @param \Magento\Catalog\Model\Layer                                  $layer
     * @param \Magento\Catalog\Model\Layer\Filter\Item\DataBuilder          $itemDataBuilder
     * @param \Magento\Catalog\Model\ResourceModel\Layer\Filter\Price       $resource
     * @param \Magento\Customer\Model\Session                               $customerSession
     * @param \Magento\Framework\Search\Dynamic\Algorithm                   $priceAlgorithm
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface             $priceCurrency
     * @param \Magento\Catalog\Model\Layer\Filter\Dynamic\AlgorithmFactory  $algorithmFactory
     * @param \Magento\Catalog\Model\Layer\Filter\DataProvider\PriceFactory $dataProviderFactory
     * @param \Amasty\Shopby\Helper\FilterSetting                           $settingHelper
     * @param \Amasty\Shopby\Model\Search\Adapter\Mysql\AggregationAdapter  $aggregationAdapter
     * @param \Amasty\Shopby\Model\Request                                  $shopbyRequest
     * @param \Amasty\Shopby\Helper\Group                                   $groupHelper
     * @param \Magento\Framework\App\Config\ScopeConfigInterface            $scopeConfig
     * @param SearchEngine                                                  $searchEngine
     * @param \Magento\Framework\Message\ManagerInterface                   $messageManager
     * @param \Magento\Customer\Model\Session                               $session
     * @param array                                                         $data
     */
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
        \Magento\Catalog\Model\Layer\Filter\Dynamic\AlgorithmFactory $algorithmFactory,
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
        parent::__construct(
            $coreRegistry,
            $filterItemFactory,
            $storeManager,
            $layer,
            $itemDataBuilder,
            $resource,
            $customerSession,
            $priceAlgorithm,
            $priceCurrency,
            $algorithmFactory,
            $dataProviderFactory,
            $settingHelper,
            $aggregationAdapter,
            $shopbyRequest,
            $groupHelper,
            $scopeConfig,
            $searchEngine,
            $messageManager,
            $data
        );
        $this->groupHelper = $groupHelper;
        $this->priceCurrency = $priceCurrency;
        $this->dataProvider = $dataProviderFactory->create(['layer' => $layer]);
    }

    /**
     * @param float|string $fromPrice
     * @param float|string $toPrice
     *
     * @return float|\Magento\Framework\Phrase
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _renderRangeLabel($fromPrice, $toPrice)
    {
        $fromPrice = round($fromPrice * $this->getCurrencyRate(), self::NUMBERS_AFTER_POINT);
        if (!$toPrice) {
            $toPrice = 0;
        }
        if ($this->getCurrencyRate() != 1.0) {
            $toPrice = round($toPrice * $this->getCurrencyRate(), self::NUMBERS_AFTER_POINT);
        }

        $ranges = $this->groupHelper->getGroupAttributeMinMaxRanges($this->getAttributeModel()->getAttributeId());
        if ($ranges) {
            if (isset($ranges[$fromPrice . '-' . $toPrice])) {
                return __($ranges[$fromPrice . '-' . $toPrice]);
            }
        }
        $formattedFromPrice = $this->priceCurrency->format($fromPrice);
        if (!$toPrice) {
            return __('>%1', $formattedFromPrice);
        } elseif ($fromPrice == $toPrice && $this->dataProvider->getOnePriceIntervalValue()) {
            return $formattedFromPrice;
        } else {
            return __('%1 - %2', $formattedFromPrice, $this->priceCurrency->format($toPrice));
        }
    }
}
