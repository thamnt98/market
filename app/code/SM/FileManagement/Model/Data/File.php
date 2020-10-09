<?php
/**
 * Class File
 * @package SM\FileManagement\Model\Data
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

declare(strict_types=1);

namespace SM\FileManagement\Model\Data;

use SM\FileManagement\Api\Data\FileInterface;

class File extends \Magento\Framework\Api\AbstractExtensibleObject implements FileInterface
{

    /**
     * Get file_id
     * @return string|null
     */
    public function getFileId()
    {
        return $this->_get(self::FILE_ID);
    }

    /**
     * Set file_id
     * @param string $fileId
     * @return \SM\FileManagement\Api\Data\FileInterface
     */
    public function setFileId($fileId)
    {
        return $this->setData(self::FILE_ID, $fileId);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \SM\FileManagement\Api\Data\FileExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     * @param \SM\FileManagement\Api\Data\FileExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \SM\FileManagement\Api\Data\FileExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }

    /**
     * Get title
     * @return string|null
     */
    public function getTitle()
    {
        return $this->_get(self::TITLE);
    }

    /**
     * Set title
     * @param string $title
     * @return \SM\FileManagement\Api\Data\FileInterface
     */
    public function setTitle($title)
    {
        return $this->setData(self::TITLE, $title);
    }

    /**
     * Get file_name
     * @return string|null
     */
    public function getFileName()
    {
        return $this->_get(self::FILE_NAME);
    }

    /**
     * Set file_name
     * @param $fileName
     * @return \SM\FileManagement\Api\Data\FileInterface
     */
    public function setFileName($fileName)
    {
        return $this->setData(self::FILE_NAME, $fileName);
    }

    /**
     * Get file_path
     * @return string|null
     */
    public function getFilePath()
    {
        return $this->_get(self::FILE_PATH);
    }

    /**
     * Set file_path
     * @param string $filePath
     * @return \SM\FileManagement\Api\Data\FileInterface
     */
    public function setFilePath($filePath)
    {
        return $this->setData(self::FILE_PATH, $filePath);
    }

    /**
     * Set file_size
     * @param $fileSize
     * @return \SM\FileManagement\Api\Data\FileInterface
     */
    public function setFileSize($fileSize)
    {
        return $this->setData(self::FILE_SIZE, $fileSize);
    }

    /**
     * Get file_size
     * @return string|null
     */
    public function getFileSize()
    {
        return $this->_get(self::FILE_SIZE);
    }

    /**
     * Get file_name
     * @return string|null
     */
    public function getThumbnailName()
    {
        return $this->_get(self::THUMBNAIL_NAME);
    }

    /**
     * Set file_name
     * @param string $thumbnailName
     * @return \SM\FileManagement\Api\Data\FileInterface
     */
    public function setThumbnailName($thumbnailName)
    {
        return $this->setData(self::THUMBNAIL_NAME, $thumbnailName);
    }

    /**
     * Get file_path
     * @return string|null
     */
    public function getThumbnailPath()
    {
        return $this->_get(self::THUMBNAIL_PATH);
    }

    /**
     * Set file_path
     * @param string $thumbnailPath
     * @return \SM\FileManagement\Api\Data\FileInterface
     */
    public function setThumbnailPath($thumbnailPath)
    {
        return $this->setData(self::THUMBNAIL_PATH, $thumbnailPath);
    }

    /**
     * Get file_size
     * @return string|null
     */
    public function getThumbnailSize()
    {
        return $this->_get(self::THUMBNAIL_SIZE);
    }

    /**
     * Set file_size
     * @param string $thumbnailSize
     * @return \SM\FileManagement\Api\Data\FileInterface
     */
    public function setThumbnailSize($thumbnailSize)
    {
        return $this->setData(self::THUMBNAIL_SIZE, $thumbnailSize);
    }
}
