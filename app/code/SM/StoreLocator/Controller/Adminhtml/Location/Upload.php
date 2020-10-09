<?php

namespace SM\StoreLocator\Controller\Adminhtml\Location;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use SM\StoreLocator\Controller\Adminhtml\AbstractLocationForm;

class Upload extends AbstractLocationForm
{
    /**
     * @var string
     */
    const FIELD_FILE_NAME = 'import_form[file_to_update]';

    /**
     * @var string
     */
    const IMPORT_DIRECTORY_NAME = 'transmart_store_location_import';

    /**
     * @var WriteInterface
     */
    protected $mediaDirectory;

    /**
     * @var UploaderFactory
     */
    protected $fileUploaderFactory;

    /**
     * @param Context $context
     * @param Filesystem $filesystem
     * @param UploaderFactory $fileUploaderFactory
     * @throws FileSystemException
     */
    public function __construct(
        Context $context,
        Filesystem $filesystem,
        UploaderFactory $fileUploaderFactory
    ) {
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::TMP);
        $this->fileUploaderFactory = $fileUploaderFactory;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        try {
            $target = $this->mediaDirectory->getAbsolutePath(self::IMPORT_DIRECTORY_NAME . DIRECTORY_SEPARATOR);
            $uploader = $this->fileUploaderFactory->create(['fileId' => self::FIELD_FILE_NAME]);
            $uploader->setAllowedExtensions(['csv']);
            $uploader->setAllowRenameFiles(true);
            $result = $uploader->save($target);
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
        }

        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }
}
