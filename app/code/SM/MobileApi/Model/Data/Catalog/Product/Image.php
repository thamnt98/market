<?php

namespace SM\MobileApi\Model\Data\Catalog\Product;

/**
 * Class for storing image information
 */
class Image extends \Magento\Framework\Model\AbstractExtensibleModel implements \SM\MobileApi\Api\Data\Catalog\Product\ImageInterface
{
    public function getUrl()
    {
        return $this->getData(self::URL);
    }

    public function setUrl($url)
    {
        return $this->setData(self::URL, $url);
    }

    public function getLabel()
    {
        return $this->getData(self::LABEL);
    }

    public function setLabel($lable)
    {
        return $this->setData(self::LABEL, $lable);
    }

    public function getType()
    {
        return $this->getData(self::TYPE);
    }

    public function setType($type)
    {
        return $this->setData(self::TYPE, $type);
    }

    public function getVideoUrl()
    {
        return $this->getData(self::VIDEO_URL);
    }

    public function setVideoUrl($videoUrl)
    {
        return $this->setData(self::VIDEO_URL, $videoUrl);
    }

    public function get360Url()
    {
        return $this->getData(self::IMAGES_360_URL);
    }

    public function set360Url($url360Image)
    {
        return $this->setData(self::IMAGES_360_URL, $url360Image);
    }
}
