<?php

declare(strict_types=1);

namespace SM\Search\Model\Search\SearchCriteria;

use Magento\Framework\Api\Search\SearchCriteriaInterface;

class Resolver
{
    /**
     * @var Extractor
     */
    protected $extractor;

    /**
     * @var Preparator
     */
    protected $preparator;

    /**
     * Resolver constructor.
     * @param Extractor $extractor
     * @param Preparator $preparator
     */
    public function __construct(
        Extractor $extractor,
        Preparator $preparator
    ) {
        $this->extractor = $extractor;
        $this->preparator = $preparator;
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     */
    public function resolveVisibilityFilter(SearchCriteriaInterface $searchCriteria): void
    {
        if (empty($this->extractor->extractVisibilityParam($searchCriteria))) {
            $visibilityFilter = $this->preparator->prepareVisibilityFilter();

            $filterGroup = $searchCriteria->getFilterGroups()[0];

            $filters = $filterGroup->getFilters();
            $filters[] = $visibilityFilter;

            $filterGroup->setFilters($filters);
        }
    }
}
