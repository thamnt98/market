<?php

declare(strict_types=1);

namespace SM\Search\Plugin\Magento\Search\Model;

use Magento\Framework\App\RequestInterface;
use Magento\Search\Model\Query as BaseQuery;
use Psr\Log\LoggerInterface;
use SM\Search\Helper\Config;
use SM\Search\Model\SearchQueryCategory\Updater as SearchQueryCategoryUpdater;
use SM\Search\Model\SearchQueryPersonal\Updater as SearchQueryPersonalUpdater;

class Query
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var SearchQueryPersonalUpdater
     */
    protected $searchQueryPersonalUpdater;

    /**
     * @var SearchQueryCategoryUpdater
     */
    protected $searchQueryCategoryUpdater;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Query constructor.
     * @param RequestInterface $request
     * @param SearchQueryPersonalUpdater $searchQueryPersonalUpdater
     * @param SearchQueryCategoryUpdater $searchQueryCategoryUpdater
     * @param LoggerInterface $logger
     */
    public function __construct(
        RequestInterface $request,
        SearchQueryPersonalUpdater $searchQueryPersonalUpdater,
        SearchQueryCategoryUpdater $searchQueryCategoryUpdater,
        LoggerInterface $logger
    ) {
        $this->request = $request;
        $this->searchQueryPersonalUpdater = $searchQueryPersonalUpdater;
        $this->searchQueryCategoryUpdater = $searchQueryCategoryUpdater;
        $this->logger = $logger;
    }

    /**
     * @param BaseQuery $subject
     * @param \Closure $proceed
     * @param int $numResults
     * @return BaseQuery
     */
    public function aroundSaveNumResults(BaseQuery $subject, \Closure $proceed, int $numResults): BaseQuery
    {
        $result = $subject;
        $result->setNumResults($numResults);

        // Prevent save numResults on `search_query` when search on 1 category
        if (!$this->request->getParam(Config::SEARCH_PARAM_CATEGORY_FIELD_NAME)) {
            $proceed($numResults);
        }

        try {
            // save customer latest search
            $this->searchQueryPersonalUpdater->updateOnSearch($result);
        } catch (\Exception $exception) {
            $this->logger->critical('Error on save personal query', [
                'exception_code' => $exception->getCode(),
                'exception_message' => $exception->getMessage(),
                'exception_trace' => $exception->getTraceAsString(),
            ]);
        }

        try {
            // save popularity & numberResults for 1 category
            $this->searchQueryCategoryUpdater->updateOnSearch($result);
        } catch (\Exception $exception) {
            $this->logger->critical('Error on save category query', [
                'exception_code' => $exception->getCode(),
                'exception_message' => $exception->getMessage(),
                'exception_trace' => $exception->getTraceAsString(),
            ]);
        }

        return $result;
    }
}
