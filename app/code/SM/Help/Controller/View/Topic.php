<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\Help\Controller\View;

use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NotFoundException;
use SM\Help\Api\Data\TopicInterface;
use SM\Help\Controller\Help;

/**
 * Class Topic
 * @package SM\Help\Controller\View
 */
class Topic extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \SM\Help\Api\TopicRepositoryInterface
     */
    protected $topicRepository;

    /**
     * Index constructor.
     * @param \SM\Help\Api\TopicRepositoryInterface $topicRepository
     * @param Context $context
     */
    public function __construct(
        \SM\Help\Api\TopicRepositoryInterface $topicRepository,
        Context $context
    ) {
        parent::__construct($context);
        $this->topicRepository = $topicRepository;
    }

    /**
     * @inheritDoc
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        $topic = $this->initTopic();

        if (!$topic) {
            throw new NotFoundException(__('Page not found'));
        }

        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $this->_eventManager->dispatch(
            'help_page_render',
            ['topic' => $topic, 'controller_action' => $this]
        );

        return $resultPage;
    }

    /**
     * @return bool|TopicInterface
     */
    protected function initTopic()
    {
        $id = $this->getRequest()->getParam(TopicInterface::ID);
        if (!$id) {
            return false;
        }

        try {
            $topic = $this->topicRepository->getById($id);
        } catch (LocalizedException $e) {
            return false;
        }

        if (!$topic->getId()) {
            return false;
        }

        return $topic;
    }
}
