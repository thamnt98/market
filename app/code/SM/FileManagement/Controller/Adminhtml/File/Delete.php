<?php
/**
 * Class Delete
 * @package SM\FileManagement\Controller\Adminhtml\File
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

declare(strict_types=1);

namespace SM\FileManagement\Controller\Adminhtml\File;

class Delete extends \SM\FileManagement\Controller\Adminhtml\File
{
    const ADMIN_RESOURCE = 'SM_FileManagement::File_delete';
    /**
     * @var \Magento\Framework\Filesystem\Driver\File
     */
    protected $file;

    /**
     * Delete constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Filesystem\Driver\File $file
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Filesystem\Driver\File $file
    ) {
        $this->file = $file;
        parent::__construct($context);
    }

    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('file_id');
        if ($id) {
            try {
                // init model and delete
                $model = $this->_objectManager->create(\SM\FileManagement\Model\File::class);
                $model->load($id);
                $model->delete();

                $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
                    ->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
                $mediaRootDir = $mediaDirectory->getAbsolutePath();

                if ($this->file->isExists($mediaRootDir . $model->getFilePath())) {
                    $this->file->deleteFile($mediaRootDir . $model->getFilePath());
                }

                if ($this->file->isExists($mediaRootDir . $model->getThumbnailPath())) {
                    $this->file->deleteFile($mediaRootDir . $model->getThumbnailPath());
                }

                // display success message
                $this->messageManager->addSuccessMessage(__('You deleted the File.'));
                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addErrorMessage($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['file_id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addErrorMessage(__('We can\'t find a File to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
}
