<?php
/**
 * Class ListSort
 * @package SM\Catalog\Plugin\Model\Config\Source
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

declare(strict_types=1);

namespace SM\Catalog\Plugin\Model\Config\Source;

use SM\Catalog\Plugin\Model\Config;

class ListSort
{
    /**
     * @param \Magento\Catalog\Model\Config\Source\ListSort $subject
     * @param $options
     * @return array
     */


    public function afterToOptionArray(
        \Magento\Catalog\Model\Config\Source\ListSort $subject,
        $options
    ) {
        $newOptions = \SM\Catalog\Helper\ProductList\Toolbar::getAdditionalOptions();
        $columns = array_keys($newOptions);
        foreach ($options as $key => $option) {
            if (in_array($option['value'], $columns) || $option['value'] == 'price') {
                unset($options[$key]);
            }
        }
        return array_merge($newOptions, $options);
    }
}
