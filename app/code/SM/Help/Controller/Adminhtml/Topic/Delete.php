<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\Help\Controller\Adminhtml\Topic;

use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\ResponseInterface;
use SM\Help\Controller\Adminhtml\Topic;

/**
 * Class Delete
 * @package SM\Help\Controller\Adminhtml\Topic
 */
class Delete extends Topic
{
    /**
     * @var \SM\Help\Api\TopicRepositoryInterface
     */
    protected $topicRepository;

    /**
     * Delete constructor.
     * @param Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \SM\Help\Api\TopicRepositoryInterface $topicRepository
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \SM\Help\Api\TopicRepositoryInterface $topicRepository
    ) {
        parent::__construct($context, $coreRegistry);
        $this->topicRepository = $topicRepository;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('topic_id');
        if ($id) {
            try {
                $this->topicRepository->deleteById($id);
                $this->messageManager->addSuccessMessage(__('You deleted the topic.'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['topic_id' => $id]);
            }
        }
        $this->messageManager->addErrorMessage(__('We can\'t find a topic to delete.'));
        return $resultRedirect->setPath('*/*/');
    }
}
