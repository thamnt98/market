<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\Help\Controller\Adminhtml\Question;

use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;

/**
 * Class MassDelete
 * @package SM\Help\Controller\Adminhtml\Question
 */
class MassDelete extends Action
{
    /**
     * @var \Magento\Ui\Component\MassAction\Filter
     */
    protected $filter;

    /**
     * @var \SM\Help\Model\ResourceModel\Question\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \SM\Help\Api\QuestionRepositoryInterface
     */
    protected $questionRepository;

    /**
     * MassDelete constructor.
     * @param Action\Context $context
     * @param \Magento\Ui\Component\MassAction\Filter $filter
     * @param \SM\Help\Model\ResourceModel\Question\CollectionFactory $collectionFactory
     * @param \SM\Help\Api\QuestionRepositoryInterface $questionRepository
     */
    public function __construct(
        Action\Context $context,
        \Magento\Ui\Component\MassAction\Filter $filter,
        \SM\Help\Model\ResourceModel\Question\CollectionFactory $collectionFactory,
        \SM\Help\Api\QuestionRepositoryInterface $questionRepository
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->questionRepository = $questionRepository;
    }

    /**
     * @return Redirect|ResponseInterface|ResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        /** @var \SM\Help\Model\ResourceModel\Question\Collection $collection */
        $collection     = $this->filter->getCollection($this->collectionFactory->create());
        $collectionSize = $collection->getSize();

        /** @var \SM\Help\Model\Question $item */
        foreach ($collection as $item) {
            $this->questionRepository->delete($item);
        }

        $this->messageManager->addSuccessMessage(__('A total of %1 question(s) have been deleted.', $collectionSize));

        /**
         * @var Redirect $resultRedirect
         */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath('*/*/');
    }
}
