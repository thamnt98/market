<?php

declare(strict_types=1);

namespace SM\Search\Model\Search\SearchCriteria;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Api\Filter;
use Magento\Framework\Api\FilterBuilder;

class Preparator
{
    const VISIBILITY_SEARCH = 3;
    const VISIBILITY_CATALOG_SEARCH = 4;
    const STORE_FRONT_VISIBILITY = [
        self::VISIBILITY_SEARCH,
        self::VISIBILITY_CATALOG_SEARCH,
    ];
    const CONDITION_OPERATOR_IN = 'in';

    /**
     * @var FilterBuilder
     */
    protected $filterBuilder;

    /**
     * Preparator constructor.
     * @param FilterBuilder $filterBuilder
     */
    public function __construct(
        FilterBuilder $filterBuilder
    ) {
        $this->filterBuilder = $filterBuilder;
    }

    /**
     * @return Filter
     */
    public function prepareVisibilityFilter(): Filter
    {
        $visibilityFilter = $this->filterBuilder
                            ->setField(ProductInterface::VISIBILITY)
                            ->setValue(self::STORE_FRONT_VISIBILITY)
                            ->setConditionType(self::CONDITION_OPERATOR_IN)
                            ->create();

        return $visibilityFilter;
    }
}
