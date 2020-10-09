<?php
/**
 * Class Save
 * @package SM\FileManagement\Controller\Adminhtml\File
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

declare(strict_types=1);

namespace SM\FileManagement\Controller\Adminhtml\File;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Exception\LocalizedException;

class Save extends \Magento\Backend\App\Action implements HttpPostActionInterface
{
    const ADMIN_RESOURCE = 'SM_FileManagement::File_save';

    /**
     * @var \Magento\Framework\App\Request\DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
    ) {
        $this->dataPersistor = $dataPersistor;
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            $id = $this->getRequest()->getParam('file_id');

            $model = $this->_objectManager->create(\SM\FileManagement\Model\File::class)->load($id);
            if (!$model->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This File no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }

            if (isset($data['file'][0])) {
                $data['file_name'] = $data['file'][0]['file'];
                $data['file_path'] = $data['file'][0]['path'];
                $data['file_size'] = $data['file'][0]['size'];
                unset($data['file']);
            }

            if (isset($data['thumbnail'][0])) {
                if (isset($data['thumbnail'][0]['file'])) {
                    $data['thumbnail_name'] = $data['thumbnail'][0]['file'];
                } elseif (isset($data['thumbnail'][0]['name'])) {
                    $data['thumbnail_name'] = $data['thumbnail'][0]['name'];
                }

                if (isset($data['thumbnail'][0]['path'])) {
                    $data['thumbnail_path'] = $data['thumbnail'][0]['path'];
                } elseif (isset($data['thumbnail'][0]['url'])) {
                    $path = explode('/', $data['thumbnail'][0]['url']);
                    unset($path[0]);
                    unset($path[1]);
                    $data['thumbnail_path'] = implode('/', $path);
                }

                $data['thumbnail_size'] = $data['thumbnail'][0]['size'];
                unset($data['thumbnail']);
            }

            $model->setData($data);

            try {
                $model->save();
                $this->messageManager->addSuccessMessage(__('You saved the File.'));
                $this->dataPersistor->clear('sm_filemanagement_file');

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['file_id' => $model->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the File.'));
            }

            $this->dataPersistor->set('sm_filemanagement_file', $data);
            return $resultRedirect->setPath('*/*/edit', ['file_id' => $this->getRequest()->getParam('file_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
