<?php

namespace SM\MobileApi\Model;

use Magento\Directory\Helper\Data;

/**
 * Class Store
 *
 * @package SM\MobileApi\Model
 */
class Store implements \SM\MobileApi\Api\StoreInterface
{
    /**
     * @var \Magento\Store\Model\ResourceModel\Store\CollectionFactory
     */
    protected $storeCollectionFactory;
    /**
     * @var Data\Store\StoreViewFactory
     */
    protected $storeViewFactory;
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Store constructor.
     * @param \Magento\Store\Model\ResourceModel\Store\CollectionFactory $storeCollectionFactory
     * @param Data\Store\StoreViewFactory $storeViewFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Store\Model\ResourceModel\Store\CollectionFactory $storeCollectionFactory,
        \SM\MobileApi\Model\Data\Store\StoreViewFactory $storeViewFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->storeCollectionFactory = $storeCollectionFactory;
        $this->storeViewFactory = $storeViewFactory;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param int||null $website_id
     * @return mixed|\SM\MobileApi\Api\Data\Store\StoreViewInterface[]
     */
    public function getList($website_id = null)
    {
        $storeCollection = $this->storeCollectionFactory->create();
        if ($website_id !== null) {
            $storeCollection->addFieldToFilter('website_id', ['eq' => $website_id]);
        }
        $storeCollection->addStatusFilter(1);
        $storeConfigs = [];
        foreach ($storeCollection->load() as $item) {
            $storeConfig = $this->storeViewFactory->create();
            $storeConfig->setStoreId($item->getId())
                ->setStoreCode($item->getCode())
                ->setLanguage($this->getLocaleCode($item));

            $storeConfigs[] = $storeConfig;
        }
        return $storeConfigs;
    }

    /**
     * Get Locale Code
     *
     * @param $store
     * @return mixed
     */
    public function getLocaleCode($store)
    {
        return $this->scopeConfig->getValue(
            Data::XML_PATH_DEFAULT_LOCALE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store->getId()
        );
    }
}
