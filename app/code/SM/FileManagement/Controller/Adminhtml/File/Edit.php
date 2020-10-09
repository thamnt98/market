<?php
/**
 * Class Edit
 * @package SM\FileManagement\Controller\Adminhtml\File
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\FileManagement\Controller\Adminhtml\File;

class Edit extends \SM\FileManagement\Controller\Adminhtml\File
{
    const ADMIN_RESOURCE = 'SM_FileManagement::File_update';

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \SM\FileManagement\Api\FileRepositoryInterface
     */
    protected $fileRepository;

    /**
     * @var \SM\FileManagement\Model\File
     */
    protected $file;

    /**
     * @var \SM\FileManagement\Model\FileFactory
     */
    protected $fileFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \SM\FileManagement\Model\FileFactory $fileFactory
     * @param \SM\FileManagement\Api\FileRepositoryInterface $fileRepository
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \SM\FileManagement\Model\FileFactory $fileFactory,
        \SM\FileManagement\Api\FileRepositoryInterface $fileRepository
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->fileFactory = $fileFactory;
        $this->fileRepository = $fileRepository;
        parent::__construct($context);
    }

    /**
     * Edit action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('file_id');

        // 2. Initial checking
        if ($id) {
            try {
                $model = $this->fileRepository->get($id);
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__($e->getMessage()));
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        } else {
            $model = $this->fileFactory->create();
        }
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $this->initPage($resultPage)->addBreadcrumb(
            $id ? __('Edit File') : __('New File'),
            $id ? __('Edit File') : __('New File')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Files'));
        $resultPage->getConfig()->getTitle()->prepend($model->getFileId() ? __(
            'Edit %1',
            $model->getTitle()
        ) : __('New File'));

        return $resultPage;
    }
}
