<?php
/**
 * Class UploadFile
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
use Magento\Framework\Controller\ResultFactory;

class Upload extends \Magento\Backend\App\Action implements HttpPostActionInterface
{
    const ADMIN_RESOURCE = 'SM_FileManagement::File_save';

    /**
     * @var \SM\FileManagement\Model\ImageUploader
     */
    public $imageUploader;

    /**
     * Upload constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \SM\FileManagement\Model\ImageUploader $imageUploader
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \SM\FileManagement\Model\ImageUploader $imageUploader
    ) {
        parent::__construct($context);
        $this->imageUploader = $imageUploader;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        if ($name = $this->getRequest()->getParam('param_name')) {
            try {
                if ($name == 'file') {
                    $this->setImageUploaderConfig();
                }
                $result = $this->imageUploader->saveFileToTmpDir($name);
                $result['cookie'] = [
                    'name' => $this->_getSession()->getName(),
                    'value' => $this->_getSession()->getSessionId(),
                    'lifetime' => $this->_getSession()->getCookieLifetime(),
                    'path' => $this->_getSession()->getCookiePath(),
                    'domain' => $this->_getSession()->getCookieDomain(),
                ];
            } catch (\Exception $e) {
                $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
            }
            return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
        }

        return $this->resultFactory->create(ResultFactory::TYPE_JSON);
    }

    /**
     * Set Image Uploader config
     */
    private function setImageUploaderConfig()
    {
        $this->imageUploader->setAllowedExtensions($this->getAllowedFileExtension());
        $this->imageUploader->setBasePath('transmart/file');
        $this->imageUploader->setBaseTmpPath('transmart/tmp/file');
    }

    /**
     * @return array
     */
    private function getAllowedFileExtension()
    {
        return [
            'flv',
            'mp4',
            'avi',
            'mov',
            'rm',
            'wmv',
        ];
    }
}
