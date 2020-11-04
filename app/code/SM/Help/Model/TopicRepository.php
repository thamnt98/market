<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\Help\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\ScopeInterface;
use SM\Help\Api\Data\QuestionInterface;
use SM\Help\Api\TopicRepositoryInterface;
use SM\Help\Api\Data;

/**
 * Class TopicRepository
 * @package SM\Help\Model
 */
class TopicRepository implements TopicRepositoryInterface
{
    const CONTENT_URL = 'help/help/question/';
    const TOPIC_MY_ORDER = 'my_order';
    const TOPIC_RETURN_REFUND = 'return_refund';
    const TOPIC_NORMAL = 'normal';
    const TOPIC_DELIVERY = 'delivery';

    /**
     * @var ResourceModel\Topic\CollectionFactory
     */
    protected $topicCollectionFactory;

    /**
     * @var Data\TopicInterfaceFactory
     */
    protected $topicFactory;

    /**
     * @var ResourceModel\Topic
     */
    protected $topicResource;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var ResourceModel\Question\CollectionFactory
     */
    protected $questionCollectionFactory;

    /**
     * @var \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface
     */
    protected $collectionProcessor;

    /**
     * @var Data\TopicSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var QuestionFactory
     */
    protected $questionFactory;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * TopicRepository constructor.
     * @param \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface $collectionProcessor
     * @param Data\TopicSearchResultsInterfaceFactory $searchResultsFactory
     * @param ResourceModel\Question\CollectionFactory $questionCollectionFactory
     * @param ResourceModel\Topic\CollectionFactory $topicCollectionFactory
     * @param ResourceModel\Topic $topicResource
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param Data\TopicInterfaceFactory $topicFactory
     * @param QuestionFactory $questionFactory
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface $collectionProcessor,
        \SM\Help\Api\Data\TopicSearchResultsInterfaceFactory $searchResultsFactory,
        \SM\Help\Model\ResourceModel\Question\CollectionFactory $questionCollectionFactory,
        \SM\Help\Model\ResourceModel\Topic\CollectionFactory $topicCollectionFactory,
        \SM\Help\Model\ResourceModel\Topic $topicResource,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \SM\Help\Api\Data\TopicInterfaceFactory $topicFactory,
        \SM\Help\Model\QuestionFactory $questionFactory,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->topicCollectionFactory = $topicCollectionFactory;
        $this->topicFactory = $topicFactory;
        $this->topicResource = $topicResource;
        $this->storeManager = $storeManager;
        $this->questionCollectionFactory = $questionCollectionFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->questionFactory = $questionFactory;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Sac Topic data
     *
     * @param Data\TopicInterface $topic
     * @return Data\TopicInterface
     * @throws CouldNotSaveException
     */
    public function save(Data\TopicInterface $topic)
    {
        try {
            $this->topicResource->save($topic);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $topic;
    }

    /**
     * Load Topic data by given Topic Identity
     *
     * @param int $topicId
     * @return Data\TopicInterface
     * @throws NoSuchEntityException
     */
    public function getById($topicId)
    {
        $topic = $this->topicFactory->create();
        $this->topicResource->load($topic, $topicId);
        if (!$topic->getId()) {
            throw new NoSuchEntityException(__('The Help Topic with the "%1" ID doesn\'t exist.', $topicId));
        }
        return $topic;
    }

    /**
     * Delete Topic
     *
     * @param Data\TopicInterface $topic
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(Data\TopicInterface $topic)
    {
        try {
            $this->topicResource->delete($topic);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__($e->getMessage()));
        }
        return true;
    }

    /**
     * Delete Topic by given Topic Identity
     *
     * @param int $topicId
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById($topicId)
    {
        return $this->delete($this->getById($topicId));
    }

    /**
     * Get Topics of current Store view
     *
     * @return Data\TopicInterface[]
     */
    public function getParentTopics()
    {
        $topics = $this->topicCollectionFactory->create()
            ->addVisibilityFilter()
            ->addStoreFilter()
            ->addFieldToFilter(Data\TopicInterface::PARENT_ID, \SM\Help\Model\Topic::TREE_ROOT_ID)
            ->getItems();

        foreach ($topics as $topic) {
            $topic->setType(self::TOPIC_NORMAL);
            $topicId = $topic->getId();
            if ($topicId == $this->getMyOrderId()) {
                $topic->setType(self::TOPIC_MY_ORDER);
            }
            if ($topicId == $this->getReturnRefundId()) {
                $topic->setType(self::TOPIC_RETURN_REFUND);
            }
            if ($topicId == $this->getDeliveryId()) {
                $topic->setType(self::TOPIC_DELIVERY);
            }
        }

        return $topics;
    }

    /**
     * Get Childes of Parent Topic with Store view
     *
     * @param int $parentId
     * @return Data\TopicInterface[]
     */
    public function getChildTopics($parentId)
    {
        return $this->topicCollectionFactory->create()
            ->addVisibilityFilter()
            ->addStoreFilter()
            ->addFieldToFilter(Data\TopicInterface::PARENT_ID, ['eq' => $parentId])
            ->getItems();
    }

    /**
     * Get Child Questions of current Topic with Store view
     *
     * @param int $topicId
     * @return \SM\Help\Api\Data\QuestionInterface[]
     */
    public function getChildQuestions($topicId)
    {
        $questionCollection = $this->questionCollectionFactory->create()
            ->addStoreFilter($this->getStoreId())
            ->addTopicFilter($topicId)
            ->addVisibilityFilter()
            ->setOrder(QuestionInterface::SORT_ORDER, 'asc')
            ->getItems();

        $data = [];
        foreach ($questionCollection as $question) {
            $ques = $this->questionFactory->create();
            $ques->setId($question->getData('question_id'));
            $ques->setTitle($question->getData('title'));
            $ques->setStatus($question->getData('status'));
            $ques->setUrlKey($question->getData('url_key'));
            $ques->setTopicId($question->getData('topic_ids'));
            $ques->setContent($question->getData('content'));
            $ques->setCreatedAt($question->getData('created_at'));
            $ques->setStoreIds($question->getData('store_id'));
            $ques->setSortOrder($question->getData('sort_order'));
            $url = $this->storeManager->getStore()->getBaseUrl() . self::CONTENT_URL . 'id/'.$question->getData('question_id');
            $ques->setContentUrl($url);

            $topic = $this->topicFactory->create();
            $this->topicResource->load($topic, $question->getData('topic_ids'));
            if ($topic->getId()) {
                $ques->setTopicName($topic->getName());
            } else {
                $ques->setTopicName("");
            }

            $data[] = $ques;
        }

        return $data;
    }

    /**
     * Load Topic data collection by given search criteria
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     * @return Data\TopicSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $criteria)
    {
        /** @var \SM\Help\Model\ResourceModel\Topic\Collection $collection */
        $collection = $this->topicCollectionFactory->create()
                        ->addStoreFilter();

        $this->collectionProcessor->process($criteria, $collection);

        /** @var Data\TopicSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * @return int
     */
    protected function getStoreId()
    {
        try {
            return $this->storeManager->getStore()->getId();
        } catch (NoSuchEntityException $e) {
            return 0;
        }
    }

    /**
     * @return int
     */
    public function getMyOrderId()
    {
        return $this->scopeConfig->getValue(
            'sm_help/main_page/contact_us_my_order',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return int
     */
    public function getReturnRefundId()
    {
        return $this->scopeConfig->getValue(
            'sm_help/main_page/contact_us_return_refund',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return int
     */
    public function getDeliveryId()
    {
        return $this->scopeConfig->getValue(
            'sm_help/main_page/contact_us_delivery',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get Topics Category Contact us
     *
     * @return Data\TopicInterface[]
     */
    public function getListCategory()
    {
        $data = [
            "id"          => 0,
            "name"        => "General",
            "status"      => 1,
            "description" => "",
            "url_key"     => "",
            "path"        =>  "",
            "level"       => 1,
            "position"    => 0,
            "parent_id"   => 1,
            "created_at"  => "",
            "image"       => "",
            "image_url"   => "",
            "type"        => "normal"
        ];
        $topics = $this->getParentTopics();
        array_push($topics, $data);
        return $topics;
    }
}
