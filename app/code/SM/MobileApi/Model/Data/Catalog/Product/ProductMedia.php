<?php

namespace SM\MobileApi\Model\Data\Catalog\Product;

/**
 * Class for storing Product's media information
 */
class ProductMedia extends \Magento\Framework\Model\AbstractExtensibleModel implements \SM\MobileApi\Api\Data\Catalog\Product\ProductMediaInterface
{
    public function getImage()
    {
        return $this->getData(self::IMAGE);
    }

    public function setImage($data)
    {
        return $this->setData(self::IMAGE, $data);
    }

    public function getSmallImage()
    {
        return $this->getData(self::SMALL_IMAGE);
    }

    public function setSmallImage($data)
    {
        return $this->setData(self::SMALL_IMAGE, $data);
    }

    public function getThumbnail()
    {
        return $this->getData(self::THUMBNAIL);
    }

    public function setThumbnail($data)
    {
        return $this->setData(self::THUMBNAIL, $data);
    }

    public function getImage360()
    {
        return $this->getData(self::IMAGE_360);
    }

    public function setImage360($data)
    {
        return $this->setData(self::IMAGE_360, $data);
    }
}
