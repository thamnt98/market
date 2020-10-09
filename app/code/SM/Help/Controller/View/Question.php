<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\Help\Controller\View;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NotFoundException;
use SM\Help\Api\Data\QuestionInterface;
use SM\Help\Api\QuestionRepositoryInterface;
use SM\Help\Controller\Help;

/**
 * Class Question
 * @package SM\Help\Controller\View
 */
class Question extends \Magento\Framework\App\Action\Action
{
    /**
     * @var QuestionRepositoryInterface
     */
    protected $questionRepository;

    /**
     * Question constructor.
     * @param QuestionRepositoryInterface $questionRepository
     * @param Context $context
     */
    public function __construct(
        \SM\Help\Api\QuestionRepositoryInterface $questionRepository,
        Context $context
    ) {
        parent::__construct($context);
        $this->questionRepository = $questionRepository;
    }

    /**
     * @inheritDoc
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        $question = $this->initQuestion();

        if (!$question) {
            throw new NotFoundException(__('Page not found'));
        }

        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $this->_eventManager->dispatch(
            'help_page_render',
            ['question' => $question, 'controller_action' => $this]
        );

        return $resultPage;
    }

    /**
     * @return bool|QuestionInterface
     */
    protected function initQuestion()
    {
        $id = $this->getRequest()->getParam(QuestionInterface::ID);
        if (!$id) {
            return false;
        }

        try {
            $question = $this->questionRepository->getById($id);
        } catch (LocalizedException $e) {
            return false;
        }

        if (!$question->getId()) {
            return false;
        }

        return $question;
    }
}
