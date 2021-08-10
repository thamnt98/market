<?php

declare(strict_types=1);

namespace SM\Search\Model\Search\Suggestion\SearchCriteria;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\Search\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Api\SortOrderBuilder;
use SM\Search\Helper\Config;

class Preparator
{
    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var FilterBuilder
     */
    protected $filterBuilder;

    /**
     * @var SortOrderBuilder
     */
    protected $sortOrderBuilder;

    /**
     * @var Config
     */
    protected $config;

    /**
     * Preparator constructor.
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param FilterBuilder $filterBuilder
     * @param SortOrderBuilder $sortOrderBuilder
     * @param Config $config
     */
    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder,
        SortOrderBuilder $sortOrderBuilder,
        Config $config
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->sortOrderBuilder = $sortOrderBuilder;
        $this->config = $config;
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @param string $suggestKeyword
     */
    public function updateSearchText(SearchCriteriaInterface $searchCriteria, string $suggestKeyword): void
    {
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() == Config::SEARCH_PARAM_SEARCH_TEXT_FIELD_NAME) {
                    $filter->setValue($suggestKeyword);
                }
            }
        }
    }

    /**
     * @param string $suggestKeyword
     * @param int $catId
     * @return SearchCriteriaInterface
     */
    public function prepareSearchCriteriaForSuggestion(string $suggestKeyword, int $catId, $pageSize, $currentPage): SearchCriteriaInterface
    {
        $filterQuery = $this->filterBuilder
                            ->setField(Config::SEARCH_PARAM_SEARCH_TEXT_FIELD_NAME)
                            ->setValue($suggestKeyword)
                            ->create();

        $this->searchCriteriaBuilder->addFilter($filterQuery);
        if ($catId) {
            $filterCategory = $this->filterBuilder
                                    ->setField(Config::CATEGORY_IDS_ATTRIBUTE_CODE)
                                    ->setValue($catId)
                                    ->create();
            $this->searchCriteriaBuilder->addFilter($filterCategory);
        }

        $searchCriteria = $this->searchCriteriaBuilder
//            ->addSortOrder(Config::PRODUCT_NAME_FIELD_NAME, SortOrder::SORT_ASC)
            ->setPageSize(is_null($pageSize) ? $this->config->getSearchAutocompleteLimit() : $pageSize)
            ->setCurrentPage(is_null($currentPage) ? 0 : $currentPage)
            ->create();

        $searchCriteria->setRequestName(Config::QUICK_SEARCH_CONTAINER);

        return $searchCriteria;
    }
}
