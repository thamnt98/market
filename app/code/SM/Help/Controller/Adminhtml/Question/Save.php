<?php


namespace SM\Help\Controller\Adminhtml\Question;

use Magento\Cms\Model\Template\FilterProvider;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
use SM\Help\Api\QuestionRepositoryInterface;
use SM\Help\Model\QuestionFactory;
use SM\Help\Model\Question;
use Magento\Backend\App\Action;
use SM\Help\Model\ResourceModel\Question\CollectionFactory;
use Magento\Framework\Filter\FilterManager;

class Save extends \Magento\Backend\App\Action implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'SM_Help_Question::save';

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var QuestionFactory
     */
    private $questionFactory;

    /**
     * @var QuestionRepositoryInterface
     */
    private $questionRepository;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var FilterManager
     */
    protected $filter;

    /**
     * Save constructor.
     * @param Action\Context $context
     * @param DataPersistorInterface $dataPersistor
     * @param QuestionFactory|NULL $questionFactory
     * @param QuestionRepositoryInterface|NULL $questionRepository
     * @param CollectionFactory $collectionFactory
     * @param FilterManager $filterProvider
     */
    public function __construct(
        Action\Context $context,
        DataPersistorInterface $dataPersistor,
        QuestionFactory $questionFactory = null,
        QuestionRepositoryInterface $questionRepository = null,
        CollectionFactory $collectionFactory,
        FilterManager $filterProvider
    ) {
        $this->dataPersistor  = $dataPersistor;
        $this->questionFactory    = $questionFactory ?: ObjectManager::getInstance()->get(QuestionFactory::class);
        $this->questionRepository = $questionRepository
            ?: ObjectManager::getInstance()->get(QuestionRepositoryInterface::class);
        $this->collectionFactory = $collectionFactory;
        $this->filter = $filterProvider;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            if (isset($data['status']) && $data['status'] === 'true') {
                $data['status'] = Question::STATUS_ENABLED;
            }
            if (empty($data['question_id'])) {
                $data['question_id'] = null;
            }

            /** @var \SM\Help\Model\Question $model */
            $model = $this->questionFactory->create();

            $id = $this->getRequest()->getParam('question_id');
            $urlKey = '';
            if ($id > 0 || $id != '') {
                try {
                    $model = $this->questionRepository->getById($id);
                } catch (LocalizedException $e) {
                    $this->messageManager->addErrorMessage(__('This question no longer exists.'));

                    return $resultRedirect->setPath('*/*/');
                }
                $urlKey = $model->getUrlKey();
            } else {
                if ($data['url_key']) {
                    $urlKey = $data['url_key'];
                } else {
                    $urlKey = $this->filter->translitUrl($data['title']);
                }
            }

            $model->setData($data);

            if ($this->isUrlKeyExist($this->collectionFactory->create(), $urlKey, $id)) {
                $this->messageManager->addErrorMessage(__('The question URL Key already exists.'));
                return $resultRedirect->setPath(
                    '*/*/edit',
                    ['question_id' => $this->getRequest()->getParam('question_id')]
                );
            }

            try {
                $this->questionRepository->save($model);
                $this->messageManager->addSuccessMessage(__('You saved the question.'));
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['question_id' => $model->getId(), '_current' => true]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addExceptionMessage($e->getPrevious() ?: $e);
            } catch (\Throwable $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the question.'));
            }

            $this->dataPersistor->set('sm_help_question', $data);
            return $resultRedirect->setPath(
                '*/*/edit',
                ['question_id' => $this->getRequest()->getParam('question_id')]
            );
        }
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * @param $collection
     * @param $identifier
     * @param $postId
     * @return bool
     */
    private function isUrlKeyExist($collection, $identifier, $postId)
    {
        //case edit record
        if ($postId > 0 || $postId != '') {
            $result = $collection->addFieldToFilter('url_key', $identifier)
                ->addFieldToFilter('main_table.question_id', ['neq' => $postId]);
            if ($result->getSize() >= 1) {
                return true;
            } else {
                return false;
            }
        } else {
            //case add new record
            $result = $collection->addFieldToFilter('url_key', $identifier);
            if ($result->getSize() >= 1) {
                return true;
            } else {
                return false;
            }
        }
    }
}
