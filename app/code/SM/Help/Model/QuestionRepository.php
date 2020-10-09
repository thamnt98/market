<?php

/**
 * @category SM
 * @package SM_Help
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 * @author      Chinhvd <chinhvd@smartosc.com>
 *
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\Help\Model;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use SM\Help\Api\Data\QuestionInterface;
use SM\Help\Api\QuestionRepositoryInterface;
use SM\Help\Api\Data\QuestionInterfaceFactory;
use SM\Help\Model\ResourceModel\Question as ResourceQuestion;
use SM\Help\Model\ResourceModel\Question\CollectionFactory as QuestionCollectionFactory;

class QuestionRepository implements QuestionRepositoryInterface
{
    const CONTENT_URL = 'help/help/question/';
    /**
     * @var ResourceQuestion
     */
    protected $resource;

    /**
     * @var QuestionFactory
     */
    protected $questionFactory;

    /**
     * @var QuestionCollectionFactory
     */
    protected $questionCollectionFactory;

    /**
     * @var \SM\Help\Api\Data\QuestionInterfaceFactory
     */
    protected $dataQuestionFactory;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var TopicFactory
     */
    protected $topicFactory;

    /**
     * @var ResourceModel\Topic
     */
    protected $topicResource;

    /**
     * @param ResourceQuestion $resource
     * @param QuestionFactory $questionFactory
     * @param QuestionInterfaceFactory $dataQuestionFactory
     * @param QuestionCollectionFactory $questionCollectionFactory
     * @param StoreManagerInterface $storeManager
     * @param Config $config
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        ResourceQuestion $resource,
        QuestionFactory $questionFactory,
        QuestionInterfaceFactory $dataQuestionFactory,
        QuestionCollectionFactory $questionCollectionFactory,
        StoreManagerInterface $storeManager,
        \SM\Help\Model\Config $config,
        \SM\Help\Model\TopicFactory $topicFactory,
        \SM\Help\Model\ResourceModel\Topic $topicResource
    ) {
        $this->resource = $resource;
        $this->questionFactory = $questionFactory;
        $this->questionCollectionFactory = $questionCollectionFactory;
        $this->dataQuestionFactory = $dataQuestionFactory;
        $this->storeManager = $storeManager;
        $this->config = $config;
        $this->topicFactory = $topicFactory;
        $this->topicResource = $topicResource;
    }

    /**
     * Save Question data
     *
     * @param \SM\Help\Api\Data\QuestionInterface|Question $question
     * @return \SM\Help\Model\Question
     * @throws CouldNotSaveException
     */
    public function save(\SM\Help\Api\Data\QuestionInterface $question)
    {
        try {
            $this->resource->save($question);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(
                __('Could not save the question: %1', $exception->getMessage()),
                $exception
            );
        }
        return $question;
    }

    /**
     * Load Question data by given Page Identity
     *
     * @param string $questionId
     * @return \SM\Help\Model\Question
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Exception
     */
    public function getById($questionId)
    {
        $question = $this->questionFactory->create();
        $this->resource->load($question, $questionId);
        return $question;
    }

    /**
     * Delete Question
     *
     * @param \SM\Help\Api\Data\QuestionInterface $question
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(\SM\Help\Api\Data\QuestionInterface $question)
    {
        try {
            $this->resource->delete($question);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(
                __('Could not delete the page: %1', $exception->getMessage())
            );
        }
        return true;
    }

    /**
     * Delete Question by given Question Identity
     *
     * @param string $questionId
     * @return bool
     * @throws CouldNotDeleteException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($questionId)
    {
        return $this->delete($this->getById($questionId));
    }

    /**
     * @inheritDoc
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $criteria)
    {
    }

    /**
     * @return QuestionInterface[]|null
     */
    public function getTopQuestion()
    {
        $questionIds = $this->config->getTopQuestion();

        if ($questionIds) {
            $questionCollection = $this->questionCollectionFactory->create()
                ->addFieldToFilter('main_table.question_id', ['in' => $questionIds])
                ->addStoreFilter($this->getStoreId())
                ->addVisibilityFilter()
                ->getItems();

            $data = [];

            foreach ($questionCollection as $question){
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
                if($topic->getId()) {
                    $ques->setTopicName($topic->getName());
                }else{
                    $ques->setTopicName("");
                }

                $data[] = $ques;
            }

            return $data;
        }

        return null;
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
}
