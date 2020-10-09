<?php

declare(strict_types=1);

namespace SM\Search\Model\Search\SearchResult;

use Magento\Framework\Api\Search\SearchResultInterface as BaseSearchResultInterface;
use SM\Product\Api\Repository\ProductRepositoryInterface;
use SM\Search\Api\Data\Response\SearchResultInterface;
use SM\Search\Api\Data\Response\SearchResultInterfaceFactory;
use SM\Search\Api\Data\Response\SuggestionSearchResultInterface;
use SM\Search\Api\Data\Response\SuggestionSearchResultInterfaceFactory;

class Converter
{
    /**
     * @var SearchResultInterfaceFactory
     */
    protected $searchResultFactory;

    /**
     * @var SuggestionSearchResultInterfaceFactory
     */
    protected $suggestionSearchResultFactory;

    /**
     * @var Extractor
     */
    protected $extractor;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * Converter constructor.
     * @param SearchResultInterfaceFactory $searchResultFactory
     * @param SuggestionSearchResultInterfaceFactory $suggestionSearchResultFactory
     * @param Extractor $extractor
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        SearchResultInterfaceFactory $searchResultFactory,
        SuggestionSearchResultInterfaceFactory $suggestionSearchResultFactory,
        Extractor $extractor,
        ProductRepositoryInterface $productRepository
    ) {
        $this->searchResultFactory = $searchResultFactory;
        $this->suggestionSearchResultFactory = $suggestionSearchResultFactory;
        $this->extractor = $extractor;
        $this->productRepository = $productRepository;
    }

    /**
     * @param BaseSearchResultInterface $baseSearchResult
     * @return SearchResultInterface
     */
    public function convert(BaseSearchResultInterface $baseSearchResult): SearchResultInterface
    {
        /** @var SearchResultInterface $searchResult */
        $searchResult = $this->searchResultFactory->create();
        $searchResult->setAggregations($baseSearchResult->getAggregations());
        $searchResult->setItems($baseSearchResult->getItems());
        $searchResult->setSearchCriteria($baseSearchResult->getSearchCriteria());
        $searchResult->setTotalCount($baseSearchResult->getTotalCount());

        $productIds = $this->extractor->extractProductIds($baseSearchResult->getItems());
        if (!empty($productIds)) {
            $searchResult->setProducts(
                $this->productRepository->getStoreFrontProductByIds($productIds)
            );
        }

        return $searchResult;
    }

    /**
     * @param BaseSearchResultInterface $baseSearchResult
     * @param string $type
     * @param string $typoSuggestKeyword
     * @return SuggestionSearchResultInterface
     */
    public function convertSuggestion(
        BaseSearchResultInterface $baseSearchResult,
        string $type,
        string $typoSuggestKeyword
    ): SuggestionSearchResultInterface {
        /** @var SuggestionSearchResultInterface $searchResult */
        $searchResult = $this->suggestionSearchResultFactory->create();
        $searchResult->setAggregations($baseSearchResult->getAggregations());
        $searchResult->setItems($baseSearchResult->getItems());
        $searchResult->setSearchCriteria($baseSearchResult->getSearchCriteria());
        $searchResult->setTotalCount($baseSearchResult->getTotalCount());
        $searchResult->setType($type);
        $searchResult->setTypoSuggestKeyword($typoSuggestKeyword);

        $productIds = $this->extractor->extractProductIds($baseSearchResult->getItems());
        if (!empty($productIds)) {
            $searchResult->setProducts(
                $this->productRepository->getStoreFrontProductByIds($productIds)
            );
        }

        return $searchResult;
    }
}
