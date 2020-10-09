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
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;

/**
 * Class MassStatus
 * @package SM\Help\Controller\Adminhtml\Topic
 */
class MassStatus extends Action
{
    /**
     * @var \Magento\Ui\Component\MassAction\Filter
     */
    protected $filter;

    /**
     * @var \SM\Help\Model\ResourceModel\Topic\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \SM\Help\Api\TopicRepositoryInterface
     */
    protected $topicRepository;

    /**
     * MassDelete constructor.
     * @param Action\Context $context
     * @param \Magento\Ui\Component\MassAction\Filter $filter
     * @param \SM\Help\Model\ResourceModel\Topic\CollectionFactory $collectionFactory
     * @param \SM\Help\Api\TopicRepositoryInterface $topicRepository
     */
    public function __construct(
        Action\Context $context,
        \Magento\Ui\Component\MassAction\Filter $filter,
        \SM\Help\Model\ResourceModel\Topic\CollectionFactory $collectionFactory,
        \SM\Help\Api\TopicRepositoryInterface $topicRepository
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->topicRepository = $topicRepository;
    }

    /**
     * @return Redirect|ResponseInterface|ResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        /** @var \SM\Help\Model\ResourceModel\Topic\Collection $collection */
        $collection  = $this->filter->getCollection($this->collectionFactory->create());
        $statusValue = $this->getRequest()->getParam('status');

        /** @var \SM\Help\Model\Topic $item */
        foreach ($collection as $item) {
            $item->setStatus($statusValue);
            $this->topicRepository->save($item);
        }

        $this->messageManager->addSuccessMessage(
            __('A total of %1 record(s) have been modified.', $collection->getSize())
        );

        /**
         * @var Redirect $resultRedirect
         */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath('*/*/');
    }
}
