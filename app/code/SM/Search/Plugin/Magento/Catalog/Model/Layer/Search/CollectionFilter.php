<?php

declare(strict_types=1);

namespace SM\Search\Plugin\Magento\Catalog\Model\Layer\Search;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Layer\Search\CollectionFilter as BaseCollectionFilter;
use Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection;
use Magento\Search\Model\QueryFactory;

/**
 * Catalog search plugin for search collection filter in layered navigation.
 */
class CollectionFilter
{
    /**
     * @var QueryFactory
     */
    protected $queryFactory;

    /**
     * @param QueryFactory $queryFactory
     */
    public function __construct(QueryFactory $queryFactory)
    {
        $this->queryFactory = $queryFactory;
    }

    /**
     * Add search filter criteria to search collection
     *
     * @param BaseCollectionFilter $subject
     * @param null $result
     * @param Collection $collection
     * @param Category $category
     * @return void
     */
    public function afterFilter(
        \Magento\Catalog\Model\Layer\Search\CollectionFilter $subject,
        $result,
        $collection,
        Category $category
    ) {
        /** @var \Magento\Search\Model\Query $query */
        $query = $this->queryFactory->get();
        if ($query->getQueryText() && $query->isQueryTextShort()
            && method_exists($collection, 'addSearchFilter')) {
            $collection->addSearchFilter($query->getQueryText());
        }
    }
}
