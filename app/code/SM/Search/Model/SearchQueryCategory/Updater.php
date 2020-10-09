<?php

declare(strict_types=1);

namespace SM\Search\Model\SearchQueryCategory;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Search\Model\Query;
use SM\Search\Api\Entity\SearchQueryCategoryInterface;
use SM\Search\Api\Repository\SearchQueryCategoryRepositoryInterface;
use SM\Search\Helper\Config;

class Updater
{
    /**
     * @var SearchQueryCategoryRepositoryInterface
     */
    protected $repository;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var bool
     */
    protected $isSaved = false;

    /**
     * Updater constructor.
     * @param SearchQueryCategoryRepositoryInterface $repository
     * @param RequestInterface $request
     */
    public function __construct(
        SearchQueryCategoryRepositoryInterface $repository,
        RequestInterface $request
    ) {
        $this->repository = $repository;
        $this->request = $request;
    }

    /**
     * @param Query $query
     * @throws LocalizedException
     */
    public function updateOnSearch(Query $query): void
    {
        $catId = $query->getData(Config::SEARCH_PARAM_CATEGORY_FIELD_NAME)
            ?? $this->request->getParam(Config::SEARCH_PARAM_CATEGORY_FIELD_NAME);

        if (!$catId) {
            return;
        }

        if ($this->isSaved) {
            return;
        }

        $entityData = [
            SearchQueryCategoryInterface::QUERY_TEXT => trim($query->getQueryText()),
            SearchQueryCategoryInterface::NUM_RESULTS => $query->getNumResults(),
            SearchQueryCategoryInterface::POPULARITY => !$query->isQueryTextShort() ? 1 : 0,
            SearchQueryCategoryInterface::STORE_ID => $query->getStoreId(),
            SearchQueryCategoryInterface::CATEGORY_ID => $catId,
            SearchQueryCategoryInterface::QUERY_ID => $query->getId() ?: null,
        ];

        $this->repository->saveEntity($entityData);

        $this->isSaved = true;
    }
}
