<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\Help\Block\Help;

use Magento\Framework\View\Element\Template;
use Magento\Theme\Block\Html\Breadcrumbs;
use SM\Help\Api\QuestionRepositoryInterface;
use SM\Help\Model\Topic;

/**
 * Class Question
 * @package SM\Help\Block\Help
 */
class Question extends \SM\Help\Block\Help\View
{
    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $filterProvider;

    /**
     * @var QuestionRepositoryInterface
     */
    protected $questionRepository;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * Question constructor.
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param QuestionRepositoryInterface $questionRepository
     * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider
     * @param \SM\Help\Model\ResourceModel\Topic\CollectionFactory $topicCollectionFactory
     * @param \SM\Help\Model\ResourceModel\Question\CollectionFactory $questionCollectionFactory
     * @param \SM\Help\Api\TopicRepositoryInterface $topicRepository
     * @param \SM\Help\Model\Config $config
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \SM\Help\Api\QuestionRepositoryInterface $questionRepository,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \SM\Help\Model\ResourceModel\Topic\CollectionFactory $topicCollectionFactory,
        \SM\Help\Model\ResourceModel\Question\CollectionFactory $questionCollectionFactory,
        \SM\Help\Api\TopicRepositoryInterface $topicRepository,
        \SM\Help\Model\Config $config,
        Template\Context $context,
        array $data = []
    ) {
        parent::__construct(
            $topicCollectionFactory,
            $questionCollectionFactory,
            $topicRepository,
            $config,
            $context,
            $data
        );
        $this->filterProvider = $filterProvider;
        $this->questionRepository = $questionRepository;
        $this->storeManager = $storeManager;
    }

    /**
     * @return \SM\Help\Api\Data\QuestionInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCurrentQuestion()
    {
        $questionId = $this->getRequest()->getParam('question_id');
        $currentQuestion = $this->questionRepository->getById($questionId);
        $currentStore = $this->storeManager->getStore()->getStoreId();
        if (empty($currentQuestion->getData('store_id')[0]) || empty($currentStore)) {
            return $currentQuestion;
        }
        if ($currentQuestion->getData('store_id')[0] !== $currentStore && $currentQuestion->getData('question_id_link')) {
            return $this->questionRepository->getById($currentQuestion->getData('question_id_link'));
        }
        return $currentQuestion;
    }

    /**
     * @param \SM\Help\Model\Question $question
     * @return string
     * @throws \Exception
     */
    public function getQuestionContent($question)
    {
        return $this->filterProvider->getPageFilter()->filter($question->getContent());
    }

    /**
     * Prepare breadcrumbs
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    protected function _addBreadcrumbs()
    {
        $currentQuestion = $this->getCurrentQuestion();
        $currentTopic = $this->topicRepository->getById($currentQuestion->getTopicId());

        $this->pageConfig->getTitle()->set($currentQuestion->getTitle());

        /** @var Breadcrumbs $breadcrumbs */
        if ($breadcrumbs = $this->getLayout()->getBlock('breadcrumbs')) {
            $breadcrumbs->addCrumb(
                'home',
                [
                    'label' => __('Home'),
                    'title' => __('Go to Home Page'),
                    'link' => $this->getStore(),
                ]
            )->addCrumb(
                'help',
                [
                    'label' => __("Help Center"),
                    'title' => __("Help Center"),
                    'link'  => $this->config->getBaseUrl(),
                ]
            );

            $ids = $currentTopic->getParentIds();

            $ids[] = 0;
            $parents = $this->topicCollectionFactory->create()
                ->addFieldToFilter('main_table.topic_id', $ids)
                ->addStoreFilter()
                ->excludeRoot()
                ->addVisibilityFilter()
                ->setOrder('level', 'asc');

            /** @var Topic $item */
            foreach ($parents as $item) {
                $breadcrumbs->addCrumb($item->getId(), [
                    'label' => $item->getName(),
                    'title' => $item->getName(),
                    'link'  => $item->getUrl(),
                ]);
            }

            $breadcrumbs->addCrumb($currentTopic->getId(), [
                'label' => $currentTopic->getName(),
                'title' => $currentTopic->getName(),
                'link'  => $currentTopic->getUrl(),
            ])->addCrumb($currentQuestion->getTitle(), [
                'label' => $currentQuestion->getTitle(),
                'title' => $currentQuestion->getTitle(),
            ]);

        }
    }

    /**
     * @return \SM\Help\Api\Data\TopicInterface|Topic
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCurrentTopic()
    {
        $currentQuestion = $this->getCurrentQuestion();
        return $this->topicRepository->getById($currentQuestion->getTopicId());
    }
}
