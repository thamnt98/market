<?php

declare(strict_types=1);

namespace SM\Search\Model;

use Magento\AdvancedSearch\Model\SuggestedQueries;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Framework\Api\Search\SearchCriteriaInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Search\Api\SearchInterface as BaseSearchInterface;
use Magento\Search\Model\Query;
use Magento\Search\Model\QueryFactory;
use Magento\Store\Model\StoreManagerInterface;
use SM\MobileApi\Api\ProductInterface as SmProductInterface;
use SM\Search\Api\Data\Response\SearchResultInterface;
use SM\Search\Api\Data\Response\SuggestionSearchResultInterface;
use SM\Search\Api\SearchInterface;
use SM\Search\Helper\Config;
use SM\Search\Model\Search\SearchCriteria\Extractor;
use SM\Search\Model\Search\SearchCriteria\Resolver;
use SM\Search\Model\Search\SearchResult\Converter;
use SM\Search\Model\Search\Suggestion\SearchCriteria\Preparator as SuggestionPreparator;

class Search implements SearchInterface
{
    /**
     * @var BaseSearchInterface
     */
    protected $search;

    /**
     * @var QueryFactory
     */
    protected $queryFactory;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var Extractor
     */
    protected $extractor;

    /**
     * @var Resolver
     */
    protected $resolver;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Converter
     */
    protected $converter;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var SuggestedQueries
     */
    protected $suggestedQueries;

    /**
     * @var SuggestionPreparator
     */
    protected $suggestionPreparator;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var CategoryCollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * @var SmProductInterface
     */
    protected $smProductInterface;

    /**
     * Search constructor.
     * @param BaseSearchInterface $search
     * @param RequestInterface $request
     * @param Extractor $extractor
     * @param Resolver $resolver
     * @param QueryFactory $queryFactory
     * @param StoreManagerInterface $storeManager
     * @param Converter $converter
     * @param Registry $registry
     * @param SuggestedQueries $suggestedQueries
     * @param SuggestionPreparator $suggestionPreparator
     * @param Config $config
     * @param CategoryCollectionFactory $categoryCollectionFactory
     * @param SmProductInterface $smProductInterface
     */
    public function __construct(
        BaseSearchInterface $search,
        RequestInterface $request,
        Extractor $extractor,
        Resolver $resolver,
        QueryFactory $queryFactory,
        StoreManagerInterface $storeManager,
        Converter $converter,
        Registry $registry,
        SuggestedQueries $suggestedQueries,
        SuggestionPreparator $suggestionPreparator,
        Config $config,
        CategoryCollectionFactory $categoryCollectionFactory,
        SmProductInterface $smProductInterface
    ) {
        $this->search = $search;
        $this->request = $request;
        $this->extractor = $extractor;
        $this->resolver = $resolver;
        $this->queryFactory = $queryFactory;
        $this->storeManager = $storeManager;
        $this->converter = $converter;
        $this->registry = $registry;
        $this->suggestedQueries = $suggestedQueries;
        $this->suggestionPreparator = $suggestionPreparator;
        $this->config = $config;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->smProductInterface = $smProductInterface;
    }

    /**
     * @inheritDoc
     */
    public function search(
        int $customerId,
        SearchCriteriaInterface $searchCriteria
    ): SearchResultInterface {
        $this->resolver->resolveVisibilityFilter($searchCriteria);
        $baseSearchResult = $this->search->search($searchCriteria);
        $result = $this->converter->convert($baseSearchResult);

        $query = $this->createQuery($searchCriteria);
        $query->setData(Config::CUSTOMER_ID_ATTRIBUTE_CODE, $customerId);

        if (!$query->isQueryTextShort()) {
            $query->saveIncrementalPopularity();
        }
        $query->saveNumResults($baseSearchResult->getTotalCount());

        return $result;
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return Query
     * @throws LocalizedException
     */
    protected function createQuery(SearchCriteriaInterface $searchCriteria): Query
    {
        if (!$this->request->getParam(QueryFactory::QUERY_VAR_NAME, false)) {
            // prepare to create $query model
            $this->request->setParams(array_merge(
                $this->request->getParams(),
                [
                    QueryFactory::QUERY_VAR_NAME => $this->extractor->extractSearchTextParam($searchCriteria),
                    Config::SEARCH_PARAM_CATEGORY_FIELD_NAME => $this->extractor->extractCategoryParam($searchCriteria),
                ]
            ));
        }

        $query = $this->queryFactory->get();
        $query->setStoreId($this->storeManager->getStore()->getId());

        return $query;
    }

    /**
     * @inheritDoc
     */
    public function suggest(
        SearchCriteriaInterface $searchCriteria
    ): SuggestionSearchResultInterface {
        $type = SuggestionSearchResultInterface::TYPE_NO_RESULT;
        $typoSuggestKeyword = '';

        $this->registry->register(Config::IS_QUICK_SUGGESTION_REQUEST, true);

        $this->resolver->resolveVisibilityFilter($searchCriteria);
        $baseSearchResult = $this->search->search($searchCriteria);

        if ($baseSearchResult->getTotalCount() > 0) {
            $type = SuggestionSearchResultInterface::TYPE_MATCH_RESULTS;
        } else {
            // typo handler
            $query = $this->createQuery($searchCriteria);
            $suggestKeywords = $this->suggestedQueries->getItems($query);
            if (!empty($suggestKeywords)) {
                $suggestKeyword = reset($suggestKeywords);
                $typoSuggestKeyword = $suggestKeyword->getQueryText();
                $query->setQueryText($typoSuggestKeyword);

                $this->suggestionPreparator->updateSearchText($searchCriteria, $typoSuggestKeyword);
                $baseSearchResult = $this->search->search($searchCriteria);

                if ($baseSearchResult->getTotalCount() > 0) {
                    $type = SuggestionSearchResultInterface::TYPE_TYPO_SUGGEST;
                }
            }
        }

        return $this->converter->convertSuggestion($baseSearchResult, $type, $typoSuggestKeyword);
    }

    /**
     * @inheritDoc
     */
    public function suggestByKeyword(string $suggestKeyword, int $catId): SuggestionSearchResultInterface
    {
        $searchCriteria = $this->suggestionPreparator->prepareSearchCriteriaForSuggestion($suggestKeyword, $catId);

        return $this->suggest($searchCriteria);
    }

    /**
     * @inheritDoc
     */
    public function getSuggestProductOnNoResult($p = 1, $limit = 12)
    {
        $category_id = $this->config->getSuggestCategoryIdOnNoResult();
        $categoryCollection = $this->categoryCollectionFactory->create()
            ->addAttributeToFilter('entity_id', ['eq' => $category_id]);

        if (!$category_id && $categoryCollection->getSize() == 0) {
            return [];
        }

        return $this->smProductInterface->getList($category_id, $limit, $p, false);
    }
}
