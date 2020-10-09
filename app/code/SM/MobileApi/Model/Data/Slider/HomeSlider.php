<?php

namespace SM\MobileApi\Model\Data\Slider;

use Magento\Framework\Api\AbstractExtensibleObject;
use SM\MobileApi\Api\Data\Slider\HomeSliderInterface;

class HomeSlider extends AbstractExtensibleObject implements HomeSliderInterface
{
    public function getImageUrl()
    {
        return $this->_get(self::IMAGE_URL);
    }

    public function setImageUrl($imageUrl)
    {
        return $this->setData(self::IMAGE_URL, $imageUrl);
    }

    public function getDescription()
    {
        return $this->_get(self::DESCRIPTION);
    }

    public function setDescription($description)
    {
        return $this->setData(self::DESCRIPTION, $description);
    }
}
