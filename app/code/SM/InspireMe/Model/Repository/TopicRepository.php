<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\InspireMe\Model\Repository;

/**
 * Class TopicRepository
 * @package SM\InspireMe\Model\Repository
 */
class TopicRepository implements \SM\InspireMe\Api\TopicRepositoryInterface
{
    /**
     * @var \Mirasvit\Blog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $topicCollectionFactory;

    /**
     * @var \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface
     */
    protected $collectionProcessor;

    /**
     * @var \SM\InspireMe\Api\Data\TopicSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * TopicRepository constructor.
     * @param \Mirasvit\Blog\Model\ResourceModel\Category\CollectionFactory $topicCollectionFactory
     * @param \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface $collectionProcessor
     * @param \SM\InspireMe\Api\Data\TopicSearchResultsInterfaceFactory $searchResultsFactory
     */
    public function __construct(
        \Mirasvit\Blog\Model\ResourceModel\Category\CollectionFactory $topicCollectionFactory,
        \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface $collectionProcessor,
        \SM\InspireMe\Api\Data\TopicSearchResultsInterfaceFactory $searchResultsFactory
    ) {
        $this->topicCollectionFactory = $topicCollectionFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->searchResultsFactory = $searchResultsFactory;
    }

    /**
     * @inheritDoc
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        /** @var \Mirasvit\Blog\Model\ResourceModel\Category\Collection $topicCollection */
        $topicCollection = $this->topicCollectionFactory->create()
            ->excludeRoot()
            ->addVisibilityFilter();

        $this->collectionProcessor->process($searchCriteria, $topicCollection);

        /** @var \SM\InspireMe\Api\Data\TopicSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($topicCollection->getItems());
        $searchResults->setTotalCount($topicCollection->getSize());
        return $searchResults;
    }
}
