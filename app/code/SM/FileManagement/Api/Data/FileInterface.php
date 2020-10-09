<?php


namespace SM\FileManagement\Api\Data;

/**
 * Interface FileInterface
 *
 * @package SM\FileManagement\Api\Data
 */
interface FileInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{

    const FILE_NAME= 'file_name';
    const FILE_PATH = 'file_path';
    const FILE_SIZE = 'file_size';
    const THUMBNAIL_NAME = 'thumbnail_name';
    const THUMBNAIL_PATH = 'thumbnail_path';
    const THUMBNAIL_SIZE = 'thumbnail_size';
    const TITLE = 'title';
    const FILE_ID = 'file_id';

    /**
     * Get file_id
     * @return string|null
     */
    public function getFileId();

    /**
     * Set file_id
     * @param string $fileId
     * @return \SM\FileManagement\Api\Data\FileInterface
     */
    public function setFileId($fileId);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \SM\FileManagement\Api\Data\FileExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \SM\FileManagement\Api\Data\FileExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \SM\FileManagement\Api\Data\FileExtensionInterface $extensionAttributes
    );

    /**
     * Get title
     * @return string|null
     */
    public function getTitle();

    /**
     * Set title
     * @param string $title
     * @return \SM\FileManagement\Api\Data\FileInterface
     */
    public function setTitle($title);

    /**
     * Get file_name
     * @return string|null
     */
    public function getFileName();

    /**
     * Set file_name
     * @param $fileName
     * @return \SM\FileManagement\Api\Data\FileInterface
     */
    public function setFileName($fileName);

    /**
     * Get file_path
     * @return string|null
     */
    public function getFilePath();

    /**
     * Set file_path
     * @param string $filePath
     * @return \SM\FileManagement\Api\Data\FileInterface
     */
    public function setFilePath($filePath);

    /**
     * Get file_size
     * @return string|null
     */
    public function getFileSize();

    /**
     * Set file_path
     * @param $fileSize
     * @return \SM\FileManagement\Api\Data\FileInterface
     */
    public function setFileSize($fileSize);

    /**
     * Get thumbnail_name
     * @return string|null
     */
    public function getThumbnailName();

    /**
     * Set thumbnail_name
     * @param $thumbnailName
     * @return \SM\FileManagement\Api\Data\FileInterface
     */
    public function setThumbnailName($thumbnailName);

    /**
     * Get thumbnail_path
     * @return string|null
     */
    public function getThumbnailPath();

    /**
     * Set thumbnail_path
     * @param string $thumbnailPath
     * @return \SM\FileManagement\Api\Data\FileInterface
     */
    public function setThumbnailPath($thumbnailPath);

    /**
     * Get thumbnail_path
     * @return string|null
     */
    public function getThumbnailSize();

    /**
     * Set thumbnail_path
     * @param $thumbnailSize
     * @return \SM\FileManagement\Api\Data\FileInterface
     */
    public function setThumbnailSize($thumbnailSize);
}
