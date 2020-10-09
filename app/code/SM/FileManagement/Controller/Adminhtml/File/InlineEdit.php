<?php
/**
 * Class InlineEdit
 * @package SM\FileManagement\Controller\Adminhtml\File
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\FileManagement\Controller\Adminhtml\File;

class InlineEdit extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'SM_FileManagement::File_update';

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $jsonFactory;

    /**
     * @var \SM\FileManagement\Api\FileRepositoryInterface
     */
    protected $fileRepository;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
     * @param \SM\FileManagement\Api\FileRepositoryInterface $fileRepository
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \SM\FileManagement\Api\FileRepositoryInterface $fileRepository
    ) {
        $this->jsonFactory = $jsonFactory;
        $this->fileRepository = $fileRepository;
        parent::__construct($context);
    }

    /**
     * Inline edit action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];

        if ($this->getRequest()->getParam('isAjax')) {
            $postItems = $this->getRequest()->getParam('items', []);
            if (!count($postItems)) {
                $messages[] = __('Please correct the data sent.');
                $error = true;
            } else {
                foreach ($postItems as $id => $data) {
                    /** @var \SM\FileManagement\Model\Data\File $dataModel */
                    try {
                        $dataModel = $this->fileRepository->get($id);
                        foreach ($data as $key => $value) {
                            if ($value) {
                                $dataModel->setData($key, $value);
                            }
                        }
                        $this->fileRepository->save($dataModel);
                    } catch (\Exception $e) {
                        $messages[] = __("[File ID: %1]". $id) .  $e->getMessage();
                        $error = true;
                    }
                }
            }
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }
}

