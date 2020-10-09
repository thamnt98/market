<?php
/**
 * Class Config
 * @package SM\Catalog\Plugin\Model
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

declare(strict_types=1);

namespace SM\Catalog\Plugin\Model;

class Config
{
    /**
     * @param \Magento\Catalog\Model\Config $catalogConfig
     * @param $options
     * @return mixed
     */
    public function afterGetAttributeUsedForSortByArray(
        \Magento\Catalog\Model\Config $catalogConfig,
        $options
    ) {
        if (isset($options['price'])) {
            unset($options['price']);
        }

        if (isset($options['position'])) {
            unset($options['position']);
        }

        return array_merge(\SM\Catalog\Helper\ProductList\Toolbar::getAdditionalOptions(), $options);
    }
}
