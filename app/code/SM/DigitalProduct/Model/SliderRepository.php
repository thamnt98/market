<?php
/**
 * Class SliderRepository
 * @package SM\DigitalProduct\Model
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */


namespace SM\DigitalProduct\Model;

use SM\DigitalProduct\Helper\Config;
use SM\HeroBanner\Model\Banner;

class SliderRepository implements \SM\DigitalProduct\Api\SliderRepositoryInterface
{
    /**
     * @var Banner
     */
    private $banner;

    /**
     * @var Config
     */
    private $configHelper;

    /**
     * SliderRepository constructor.
     * @param Banner $banner
     * @param Config $configHelper
     */
    public function __construct(
        Banner $banner,
        Config $configHelper
    ) {
        $this->banner = $banner;
        $this->configHelper = $configHelper;
    }

    /**
     * @inheritDoc
     */
    public function getSlider()
    {
        $catId = $this->configHelper->getC0CategoryId();
        return $this->banner->getBannersByCategoryId($catId);
    }
}
