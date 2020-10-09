<?php

namespace SM\FileManagement\Api;

use Magento\Framework\Api\Data\ImageContentInterface;

interface UploadImageInterface
{
    /**
     * @param ImageContentInterface $imageContent
     * @param string $directory
     * @param string $path
     * @return bool
     */
    public function uploadImage(ImageContentInterface $imageContent, $directory, $path);
}
