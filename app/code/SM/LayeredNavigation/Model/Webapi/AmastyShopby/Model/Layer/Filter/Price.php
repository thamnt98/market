<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\LayeredNavigation\Model\Webapi\AmastyShopby\Model\Layer\Filter;

use Magento\Search\Model\SearchEngine;

/**
 * Class Price
 * @package SM\LayeredNavigation\Model\Webapi\AmastyShopby\Model\Layer\Filter
 */
class Price extends \SM\CustomPrice\Model\Layer\Filter\Price
{
    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var \Magento\Catalog\Model\Layer\Filter\DataProvider\Price
     */
    protected $dataProvider;

    /**
     * @var \Amasty\Shopby\Helper\Group
     */
    protected $groupHelper;

    /**
     * @var string
     */
    protected $currencySymbol;

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
        \Magento\Customer\Model\Session $session,
        array $data = [])
    {
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
            $session,
            $data
        );
        $this->priceCurrency = $priceCurrency;
        $this->dataProvider = $dataProviderFactory->create(['layer' => $layer]);
        $this->groupHelper = $groupHelper;
        $this->currencySymbol = $priceCurrency->getCurrencySymbol();
    }

    /**
     * @param float|string $fromPrice
     * @param float|string $toPrice
     * @return float|\Magento\Framework\Phrase|string
     */
    protected function _renderRangeLabel($fromPrice, $toPrice)
    {
        $result = parent::_renderRangeLabel($fromPrice, $toPrice);
        return strip_tags($result);
    }
}
