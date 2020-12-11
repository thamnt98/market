<?php
/**
 * Class ImageFactory
 * @package SM\LazyLoad\Plugin\Block\Product
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\LazyLoad\Plugin\Block\Product;

class ImageFactory
{
    /**
     * @param $subject
     * @param $result
     * @return mixed
     */
    public function afterCreate(
        $subject,
        $result
    ) {
        $result->setTemplate('SM_LazyLoad::product/image_with_borders.phtml');
        return $result;
    }
}
