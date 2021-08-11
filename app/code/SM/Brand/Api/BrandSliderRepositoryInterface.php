<?php

/**
 * @category SM
 * @package SM_Brand
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author      Chinhvd <chinhvd@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\Brand\Api;

interface BrandSliderRepositoryInterface
{
    /**
     * @param int $category_id
     * @return \Amasty\ShopbyBase\Api\Data\OptionSettingInterface[]
     */
    public function getBrandSlider($category_id);
}