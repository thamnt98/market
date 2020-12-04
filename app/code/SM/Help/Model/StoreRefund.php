<?php

namespace SM\Help\Model;

use Magento\InventoryApi\Api\Data\SourceItemInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class StoreRefund
 * @package SM\Help\Model
 */
class StoreRefund implements \SM\Help\Api\StoreRefundInterface
{
    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var \Magento\InventoryApi\Api\SourceRepositoryInterface
     */
    protected $sourceRepository;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * StoreRefund constructor.
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\InventoryApi\Api\SourceRepositoryInterface $sourceRepository
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\InventoryApi\Api\SourceRepositoryInterface $sourceRepository,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sourceRepository = $sourceRepository;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function getStoreList()
    {
        $useAllStores = (bool)$this->scopeConfig->getValue(
            'sm_help/refund/allow_all',
            ScopeInterface::SCOPE_STORE
        );
        if (!$useAllStores) {
            $storeIds = $this->scopeConfig->getValue(
                'sm_help/refund/store_codes_allow',
                ScopeInterface::SCOPE_STORE
            );
            $storeIds = explode(',', $storeIds);
            $searchCriteria = $this->searchCriteriaBuilder->addFilter(SourceItemInterface::SOURCE_CODE, ['in' => $storeIds])
                ->addFilter('enabled', 1)
                ->create();
        } else {
            $searchCriteria = $this->searchCriteriaBuilder->addFilter(SourceItemInterface::SOURCE_CODE, 'default', 'neq')
                ->addFilter('enabled', 1)
                ->create();
        }
        $sourceData = $this->sourceRepository->getList($searchCriteria);
        return $sourceData->getItems();
    }
}
