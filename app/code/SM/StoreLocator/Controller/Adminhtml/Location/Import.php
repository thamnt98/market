<?php

namespace SM\StoreLocator\Controller\Adminhtml\Location;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use SM\StoreLocator\Controller\Adminhtml\AbstractLocationForm;
use SM\StoreLocator\Helper\Config;
use SM\StoreLocator\Model\Store\Location\Import as ImportModel;

/**
 * Import class
 */
class Import extends AbstractLocationForm
{
    /**
     * @var string
     */
    const FILE_FIELD_NAME = 'file_to_update';

    /**
     * @var string
     */
    const REMOVE_ALL_FIELD_NAME = 'remove_all';

    /**
     * @var string
     */
    const FIELD_GROUP_NAME = 'import_form';

    /**
     * @var ImportModel
     */
    protected $importProcessor;

    /**
     * @var Filesystem
     */
    protected $fileSystem;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @param Context $context
     * @param ImportModel $locationImport
     * @param Filesystem $filesystem
     * @param Config $config
     */
    public function __construct(
        Context $context,
        ImportModel $locationImport,
        Filesystem $filesystem,
        Config $config
    ) {
        $this->importProcessor = $locationImport;
        $this->fileSystem = $filesystem;
        $this->config = $config;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        try {
            $updateForm = $this->_request->getParam(self::FIELD_GROUP_NAME, false);
            if (empty($updateForm[self::FILE_FIELD_NAME])) {
                throw new LocalizedException(__('File is not uploaded.'));
            }

            $file = $updateForm[self::FILE_FIELD_NAME][0];
            $this->importProcessor->import(
                $file['path'] . $file['file'],
                filter_var($updateForm[self::REMOVE_ALL_FIELD_NAME] ?? null, FILTER_VALIDATE_BOOLEAN)
            );

            if ($this->config->getDeleteUploadedFile()) {
                $directory = $this->fileSystem->getDirectoryWrite(
                    DirectoryList::TMP
                );
                $directory->delete($file['path'] . $file['file']);
            }

            $this->messageManager->addSuccessMessage(__('Update successfully finished. File #' . $file['file']));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $this->resultRedirectFactory->create()->setPath('*/*/index');
    }
}
