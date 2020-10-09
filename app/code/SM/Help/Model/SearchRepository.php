<?php
/**
 * Class SearchRepository
 * @package SM\Help\Model
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

declare(strict_types=1);

namespace SM\Help\Model;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use SM\Help\Api\Data\SearchResultsInterface;
use SM\Help\Model\ResourceModel\Question\Collection;
use SM\Help\Model\ResourceModel\Question\CollectionFactory as QuestionCollectionFactory;
use SM\Help\Api\Data\SearchResultsInterfaceFactory as SearchResultsFactory;

class SearchRepository implements \SM\Help\Api\SearchRepositoryInterface
{
    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var QuestionCollectionFactory
     */
    private $questionCollectionFactory;

    /**
     * @var SearchResultsFactory
     */
    private $searchResultsFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * SearchRepository constructor.
     * @param QuestionCollectionFactory $questionCollectionFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param SearchResultsFactory $searchResultsFactory
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        QuestionCollectionFactory $questionCollectionFactory,
        CollectionProcessorInterface $collectionProcessor,
        SearchResultsFactory $searchResultsFactory,
        StoreManagerInterface $storeManager
    ) {
        $this->collectionProcessor = $collectionProcessor;
        $this->questionCollectionFactory = $questionCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * @inheritDoc
     * @throws NoSuchEntityException
     */
    public function searchQuestions(\Magento\Framework\Api\SearchCriteriaInterface $criteria)
    {
        /** @var Collection $searchResults */
        $collection = $this->questionCollectionFactory->create()
            ->addStoreFilter($this->storeManager->getStore()->getId())
            ->addVisibilityFilter();

        $this->collectionProcessor->process($criteria, $collection);

        /** @var SearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * @inheritDoc
     * @throws NoSuchEntityException
     */
    public function searchQuestionsFullData(\Magento\Framework\Api\SearchCriteriaInterface $criteria)
    {
        return $this->searchQuestions($criteria);
    }
}
