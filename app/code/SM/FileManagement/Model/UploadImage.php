<?php

namespace SM\FileManagement\Model;

use Magento\Framework\Api\Data\ImageContentInterface;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;

class UploadImage implements \SM\FileManagement\Api\UploadImageInterface
{
    protected $_contentValidator;

    protected $_imageProcessor;

    protected $_fileSystem;

    protected $_transMyProfile;

    protected $_helperFile;

    protected $_file;

    public function __construct(
        \Magento\Framework\Api\ImageContentValidatorInterface $contentValidator,
        \Magento\Framework\Api\ImageProcessorInterface $imageProcessor,
        \Magento\Framework\Filesystem $filesystem,
        \Trans\CustomerMyProfile\ViewModel\MyProfile $myProfile,
        \Magento\Downloadable\Helper\File $helperFile,
        \Magento\Framework\Filesystem\Driver\File $file
    ) {
        $this->_contentValidator    = $contentValidator;
        $this->_imageProcessor      = $imageProcessor;
        $this->_fileSystem          = $filesystem;
        $this->_transMyProfile      = $myProfile;
        $this->_helperFile          = $helperFile;
        $this->_file                = $file;
    }

    /**
     * @param ImageContentInterface $imageContent
     * @param string $directory
     * @param string $path
     * @return bool|string
     * @throws FileSystemException
     * @throws InputException
     * @throws LocalizedException
     */
    public function uploadImage(ImageContentInterface $imageContent, $directory, $path)
    {
        //get path and directory
        if ($directory == "") {
            $directory = $this->_fileSystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        } else {
            $directory = $this->_fileSystem->getDirectoryRead($directory);
        }

        $path = $directory->getAbsolutePath($path);

        //get and convert base_64 code
        $base64code = $imageContent->getBase64EncodedData();
        $base64code = preg_replace('#^data:image/[^;]+;base64,#', '', $base64code);
        $imageContent->setBase64EncodedData($base64code);

        try {
            //validate content image
            $this->_contentValidator->isValid($imageContent);
            $configMaxSize = $this->_transMyProfile->getConfigMaxsize();

            //upload image and return path
            $imagePath = $this->_imageProcessor->processImageContent($path, $imageContent);
            $fileSize = $this->_helperFile->getFileSize($path . $imagePath);
        } catch (InputException $inputException) {
            throw $inputException;
        }
        //validate file size
        if ($fileSize >= $configMaxSize) {
            $this->_file->deleteFile($path . $imagePath);
            throw new LocalizedException(__('File is too big'));
        }

        return $imagePath;
    }
}
