<?php

/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\Help\Block\Help;

use Magento\Cms\Api\BlockRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Template;
use Magento\Theme\Block\Html\Breadcrumbs;
use SM\Help\Api\Data\QuestionInterface;
use SM\Help\Api\Data\TopicInterface;
use SM\Help\Api\QuestionRepositoryInterface;
use SM\Help\Model\Topic;

/**
 * Class MainPage
 * @package SM\Help\Block\Help
 */
class MainPage extends \SM\Help\Block\Help
{
    /**
     * @var \SM\Help\Model\ResourceModel\Topic\Collection
     */
    protected $topicCollection;

    /**
     * @var BlockRepositoryInterface
     */
    protected $blockRepository;

    /**
     * @var QuestionRepositoryInterface
     */
    protected $questionRepository;

    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $filterProvider;

    /**
     * MainPage constructor.
     * @param BlockRepositoryInterface $blockRepository
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
        \Magento\Cms\Api\BlockRepositoryInterface $blockRepository,
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
        $this->blockRepository = $blockRepository;
        $this->questionRepository = $questionRepository;
        $this->filterProvider = $filterProvider;
    }

    /**
     * @return \Magento\Framework\View\Element\Template
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function _prepareLayout()
    {
        $this->_addBreadcrumbs();
        $this->pageConfig->getTitle()->set(__("Help"));
        return parent::_prepareLayout();
    }

    /**
     * Prepare breadcrumbs
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    protected function _addBreadcrumbs()
    {
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
                ]
            );
        }
    }

    /**
     * @return TopicInterface[]|\SM\Help\Model\ResourceModel\Topic\Collection
     */
    public function getFirstLevelTopics()
    {
        if (!$this->topicCollection) {
            $this->topicCollection = $this->topicRepository
                ->getChildTopics(Topic::TREE_ROOT_ID);
        }

        return $this->topicCollection;
    }

    /**
     * @return array|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getTopQuestion()
    {
        return $this->questionRepository->getTopQuestion();
    }

    /**
     * @return \Magento\Cms\Api\Data\BlockInterface|null
     */
    public function getContactUsBlock()
    {
        $blockId = $this->config->getContactUsBlock();
        try {
            return $this->blockRepository->getById($blockId);
        } catch (LocalizedException $e) {
            return null;
        }
    }

    /**
     * @param \Magento\Cms\Api\Data\BlockInterface $block
     * @return string
     * @throws \Exception
     */
    public function getBlockContent($block)
    {
        return $this->filterProvider->getPageFilter()->filter($block->getContent());
    }
}
