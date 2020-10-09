<?php


namespace SM\HeroBanner\Api\Data;


interface SliderInterface  extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**
     * @param BannerInterface[] $data
     * @return $this
     */
    public function setBanners($data);

    /**
     * @return BannerInterface[]
     */
    public function getBanners();
}