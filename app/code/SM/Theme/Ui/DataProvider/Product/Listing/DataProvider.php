<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace SM\Theme\Ui\DataProvider\Product\Listing;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\Reporting;
use Magento\Store\Model\StoreManager;
use Magento\Widget\Helper\Conditions;
use Magento\CatalogWidget\Model\Rule;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Rule\Model\Condition\Sql\Builder;
use Magento\Catalog\Model\Config;

/**
 * Provide information about current store and currency for product listing ui component
 */
class DataProvider extends \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider
{
    /**
     * @var StoreManager
     */
    private $storeManager;

    /**
     * @var Conditions
     */
    protected $conditionsHelper;

    protected $rule;

    protected $productCollectionFactory;

    protected $catalogProductVisibility;

    protected $sqlBuilder;

    protected $catalogConfig;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param Reporting $reporting
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param RequestInterface $request
     * @param FilterBuilder $filterBuilder
     * @param StoreManager $storeManager
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        Reporting $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        StoreManager $storeManager,
        Conditions $conditionsHelper,
        Rule $rule,
        CollectionFactory $productCollectionFactory,
        Visibility $catalogProductVisibility,
        Builder $sqlBuilder,
        Config $catalogConfig,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct(
            $name,
            '',
            '',
            $reporting,
            $searchCriteriaBuilder,
            $request,
            $filterBuilder,
            $meta,
            $data
        );

        $this->rule = $rule;
        $this->conditionsHelper = $conditionsHelper;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->catalogProductVisibility = $catalogProductVisibility;
        $this->sqlBuilder = $sqlBuilder;
        $this->catalogConfig = $catalogConfig;
        $this->name = $name;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $data = [];
        $store = $this->storeManager->getStore();
        $data['store'] = $store->getId();
        $data['currency'] = $store->getCurrentCurrency()->getCode();
        $defaultItemIds = [];
        foreach ($this->getDefaultCollection() as $item) {
            $defaultItemIds[] = $item->getId();
        }
        $data['defaultIds'] = $defaultItemIds;

        $configData = $this->getConfigData();
        $data['pageSize'] = $configData['page_size'];

        return $data;
    }

    protected function getConditions()
    {
        $configData = $this->getConfigData();
        $conditions = !empty($configData['conditions_encoded']) ? $configData['conditions_encoded'] : '';

        if (!empty($conditions)) {
            $conditions = $this->conditionsHelper->decode($conditions);
        }

        foreach ($conditions as $key => $condition) {
            if (!empty($condition['attribute'])
                && in_array($condition['attribute'], ['special_from_date', 'special_to_date'])
            ) {
                $conditions[$key]['value'] = date('Y-m-d H:i:s', strtotime($condition['value']));
            }
        }

        $this->rule->loadPost(['conditions' => $conditions]);
        return $this->rule->getConditions();
    }

    /**
     * Prepare and return product collection
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected function getDefaultCollection()
    {
        $configData = $this->getConfigData();
        /** @var $collection \Magento\Catalog\Model\ResourceModel\Product\Collection */
        $collection = $this->productCollectionFactory->create();
        $collection->setVisibility($this->catalogProductVisibility->getVisibleInCatalogIds());

        $collection->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents()
            ->addAttributeToSelect($this->catalogConfig->getProductAttributes())
            ->addUrlRewrite()
            ->addStoreFilter()
            ->setPageSize($configData['page_size'])
            ->setCurPage(1);

        $conditions = $this->getConditions();
        $conditions->collectValidatedAttributes($collection);
        $this->sqlBuilder->attachConditionToCollection($collection, $conditions);

        /**
         * Prevent retrieval of duplicate records. This may occur when multiselect product attribute matches
         * several allowed values from condition simultaneously
         */
        $collection->distinct(true);

        return $collection;
    }
}
