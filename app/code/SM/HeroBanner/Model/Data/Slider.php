<?php

namespace SM\HeroBanner\Model\Data;

use SM\HeroBanner\Api\Data\SliderInterface;

class Slider extends \Magento\Framework\Model\AbstractExtensibleModel implements SliderInterface
{
    public function setBanners($data)
    {
        return $this->setData('banners', $data);
    }

    public function getBanners()
    {
        return $this->getData('banners');
    }
}
