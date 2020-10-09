<?php
/**
 * Class SliderRepositoryInterface
 * @package SM\DigitalProduct\Api
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\DigitalProduct\Api;

interface SliderRepositoryInterface
{
    /**
     * @return \SM\HeroBanner\Api\Data\BannerInterface[]
     */
    public function getSlider();
}
