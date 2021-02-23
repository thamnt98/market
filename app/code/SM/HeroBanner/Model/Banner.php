<?php

namespace SM\HeroBanner\Model;

use Magento\Config\Model\Config\Source\Enabledisable;
use Mageplaza\BannerSlider\Model\ResourceModel\Banner\CollectionFactory as BannerFactory;
use Mageplaza\BannerSlider\Model\ResourceModel\Slider\CollectionFactory;
use SM\HeroBanner\Api\Data\BannerInterfaceFactory;
use SM\HeroBanner\Api\Data\SliderInterfaceFactory;

class Banner
{
    /**
     * @var CollectionFactory
     */
    protected $sliderFactory;
    /**
     * @var BannerFactory
     */
    protected $bannerFactory;
    /**
     * @var BannerInterfaceFactory
     */
    protected $bannerInterfaceFactory;
    /**
     * @var SliderInterfaceFactory
     */
    protected $sliderInterfaceFactory;

    public function __construct(
        CollectionFactory $sliderFactory,
        BannerFactory $bannerFactory,
        BannerInterfaceFactory $bannerInterfaceFactory,
        SliderInterfaceFactory $sliderInterfaceFactory
    ) {
        $this->sliderFactory          = $sliderFactory;
        $this->bannerFactory          = $bannerFactory;
        $this->bannerInterfaceFactory = $bannerInterfaceFactory;
        $this->sliderInterfaceFactory = $sliderInterfaceFactory;
    }

    public function getBannersByCategoryId($catId)
    {
        $sliderCollection = $this->sliderFactory->create();
        $sliderCollection->addFieldToFilter("category", $catId)->addFieldToFilter(
            "status",
            Enabledisable::ENABLE_VALUE
        )->setOrder('priority', 'asc')->getLastItem();
        $ids = $sliderCollection->getAllIds();
        if (empty($ids[0])) {
            return [];
        }
        $bannerCollection = $this->getBannerBySliderId($ids[0]);
        $bannerCollection->setOrder('position','asc');
        $data             = [];
        foreach ($bannerCollection as $banner) {
            /** @var \SM\HeroBanner\Api\Data\BannerInterface $bannerResp */
            $bannerResp = $this->bannerInterfaceFactory->create();
            $bannerResp->setCategoryId($catId);
            $bannerResp->setName($banner->getName());
            $bannerResp->setImage($banner->getImage());
            $bannerResp->setUrl($banner->getUrlBanner());
            $bannerResp->setNewtab($banner->getNewtab());
            $bannerResp->setTitle($banner->getTitle());
            $bannerResp->setSubTitle($banner->getSubTitle());
            $bannerResp->setContent($banner->getDescription());
            $bannerResp->setPromoId($banner->getPromoId());
            $bannerResp->setPromoName($banner->getPromoName());
            $bannerResp->setPromoCreative($banner->getPromoCreative());
            $bannerResp->setPromoPosition($banner->getPosition() ?? 0);
            $bannerResp
                ->setLinkType($banner->getLinkType())
                ->setLinkTypeValue($banner->getLinkTypeValue());
            $data[] = $bannerResp;
        }

        return $data;
    }

    /**
     * @param $sliderId
     * @return \Mageplaza\BannerSlider\Model\ResourceModel\Banner\Collection
     */
    public function getBannerBySliderId($sliderId)
    {
        $bannerCollection  = $this->bannerFactory->create();
        $sliderBannerTable = $bannerCollection->getTable('mageplaza_bannerslider_banner_slider');
        $mainTable         = 'main_table';
        $bannerCollection->getSelect()->joinInner(
            $sliderBannerTable,
            "$mainTable.banner_id=$sliderBannerTable.banner_id"
        )->where("$sliderBannerTable.slider_id=$sliderId");
        return $bannerCollection;
    }
}
