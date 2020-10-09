<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\Help\Block\Help;

use Magento\Framework\Exception\LocalizedException;
use Magento\Theme\Block\Html\Breadcrumbs;
use SM\Help\Api\Data\QuestionInterface;
use SM\Help\Api\Data\TopicInterface;
use SM\Help\Model\Topic;

/**
 * Class View
 * @package SM\Help\Block\Help
 */
class View extends \SM\Help\Block\Help
{
    /**
     * @return \Magento\Framework\View\Element\Template
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function _prepareLayout()
    {
        $this->_addBreadcrumbs();
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
        $currentTopic = $this->getCurrentTopic();
        $this->pageConfig->getTitle()->set($currentTopic->getName());

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
                    'label' => __($item->getName()),
                    'title' => __($item->getName()),
                    'link'  => $item->getUrl(),
                ]);
            }

            $breadcrumbs->addCrumb($currentTopic->getId(), [
                'label' => __($currentTopic->getName()),
                'title' => __($currentTopic->getName()),
            ]);
        }
    }

    /**
     * @return TopicInterface[]
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getChildTopics()
    {
        return $this->topicRepository->getChildTopics($this->getCurrentTopic()->getId());
    }

    /**
     * @return TopicInterface[]
     * @throws LocalizedException
     */
    public function getSameParentTopics()
    {
        return $this->topicRepository->getChildTopics($this->getCurrentTopic()->getParentId());
    }

    /**
     * @return QuestionInterface[]
     * @throws LocalizedException
     */
    public function getChildQuestions()
    {
        return $this->topicRepository->getChildQuestions($this->getCurrentTopic()->getId());
    }

    /**
     * @return string
     * @throws LocalizedException
     */
    public function getBackUrl()
    {
        /** @var Topic $currentTopic */
        $currentTopic = $this->getCurrentTopic();
        if ($currentTopic->getParentId() === Topic::TREE_ROOT_ID) {
            return $this->config->getBaseUrl();
        }

        try {
            /** @var Topic $parent */
            $parent = $this->getParentTopic($currentTopic);
            return $parent->getUrl();
        } catch (LocalizedException $e) {
            return $this->config->getBaseUrl();
        }
    }

    /**
     * @param Topic $topic
     * @return TopicInterface
     * @throws LocalizedException
     */
    public function getParentTopic($topic)
    {
        $parentId = $topic->getParentId();
        return $this->topicRepository->getById($parentId);
    }
}
