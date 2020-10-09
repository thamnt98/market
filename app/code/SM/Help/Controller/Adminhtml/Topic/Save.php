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
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
use SM\Help\Api\Data\TopicInterface;
use SM\Help\Controller\Adminhtml\Topic;

/**
 * Class Save
 * @package SM\Help\Controller\Adminhtml\Topic
 */
class Save extends Topic
{
    /**
     * @var \SM\Help\Api\TopicRepositoryInterface
     */
    protected $topicRepository;

    /**
     * @var \SM\Help\Model\TopicFactory
     */
    protected $topicFactory;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * Save constructor.
     * @param Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param DataPersistorInterface $dataPersistor
     * @param \SM\Help\Api\TopicRepositoryInterface $topicRepository
     * @param \SM\Help\Model\TopicFactory $topicFactory
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        DataPersistorInterface $dataPersistor,
        \SM\Help\Api\TopicRepositoryInterface $topicRepository,
        \SM\Help\Model\TopicFactory $topicFactory
    ) {
        parent::__construct($context, $coreRegistry);
        $this->topicRepository = $topicRepository;
        $this->topicFactory = $topicFactory;
        $this->dataPersistor = $dataPersistor;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            if (empty($data['topic_id'])) {
                $data['topic_id'] = null;
            }
            /** @var \SM\Help\Model\Topic $model */
            $model = $this->topicFactory->create();

            $id = $this->getRequest()->getParam('topic_id');
            $storeId = isset($data['store_id']) ? $data['store_id'] : 0;

            if ($id) {
                try {
                    $model = $this->topicRepository->getById($id);
                } catch (LocalizedException $e) {
                    $this->messageManager->addErrorMessage(__('This topic no longer exists.'));
                    return $resultRedirect->setPath('*/*/');
                }
                try {
                    $this->validateData($data, $id);
                } catch (LocalizedException $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                    return $storeId ?
                        $resultRedirect->setPath('*/*/edit',['topic_id' => $id, 'store' => $storeId]) :
                        $resultRedirect->setPath('*/*/edit',['topic_id' => $id]);
                }
            }

            try {
                $data = $this->filterData($data);
                $model->setData($data);
                $this->topicRepository->save($model);
                $this->messageManager->addSuccessMessage(__('You saved the topic.'));
                $this->dataPersistor->clear('sm_help_topic_persistor');
                return $this->processTopicReturn($model, $data, $resultRedirect, $storeId);
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the topic.'));
            }

            $this->dataPersistor->set('sm_help_topic_persistor', $data);
            return $storeId ?
                $resultRedirect->setPath('*/*/edit',['topic_id' => $id, 'store' => $storeId]) :
                $resultRedirect->setPath('*/*/edit',['topic_id' => $id]);
        }
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * @param array $data
     * @return array
     * @throws LocalizedException
     */
    private function filterData($data)
    {
        if (isset($data[TopicInterface::IMAGE]) && is_array($data[TopicInterface::IMAGE])) {
            if (!empty($data[TopicInterface::IMAGE]['delete'])) {
                $data[TopicInterface::IMAGE] = null;
            } else {
                if (isset($data[TopicInterface::IMAGE][0]['name'])) {
                    $data[TopicInterface::IMAGE] = $data[TopicInterface::IMAGE][0]['name'];
                }
            }
        }

        if (!isset($data[TopicInterface::IMAGE])) {
            $data[TopicInterface::IMAGE] = '';
        }

        return $data;
    }

    /**
     * @param array $data
     * @param int $topicId
     * @throws LocalizedException
     */
    private function validateData($data, $topicId)
    {
        $newParentId = $data[TopicInterface::PARENT_ID];

        if ($newParentId === $topicId) {
            throw new LocalizedException(__('Parent topic can\'t be itself.'));
        }

        try {
            /** @var \SM\Help\Model\Topic $newParent */
            $newParent = $this->topicRepository->getById($newParentId);
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage(__('Parent topic no longer exists. Please reload the page.'));
        }

        $path = explode('/', $newParent->getPath());
        foreach ($path as $item) {
            if ((int)$item == $topicId) {
                throw new LocalizedException(__('Parent topic can\'t be its children.'));
            }
        }
    }

    /**
     * Process and set the topic return
     *
     * @param \SM\Help\Model\Topic $model
     * @param array $data
     * @param \Magento\Framework\Controller\ResultInterface $resultRedirect
     * @param int $storeId
     * @return \Magento\Framework\Controller\ResultInterface
     * @throws LocalizedException
     */
    private function processTopicReturn($model, $data, $resultRedirect, $storeId)
    {
        $redirect = $data['back'] ?? 'close';

        if ($redirect === 'continue') {
            $storeId ?
                $resultRedirect->setPath('*/*/edit',['topic_id' => $model->getId(), 'store' => $storeId]) :
                $resultRedirect->setPath('*/*/edit',['topic_id' => $model->getId()]);
        } else if ($redirect === 'close') {
            $resultRedirect->setPath('*/*/');
        } else if ($redirect === 'duplicate') {
            $duplicateModel = $this->topicFactory->create(['data' => $data]);
            $duplicateModel->setId(null);
            $duplicateModel->setStatus(0);
            $this->topicRepository->save($duplicateModel);
            $id = $duplicateModel->getId();
            $this->messageManager->addSuccessMessage(__('You duplicated the topic.'));
            $this->dataPersistor->set('sm_help_topic_persistor', $data);
            $resultRedirect->setPath('*/*/edit', ['topic_id' => $id]);
        }
        return $resultRedirect;
    }
}
