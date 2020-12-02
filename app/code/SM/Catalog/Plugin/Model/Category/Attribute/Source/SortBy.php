<?php
/**
 * Class Sortby
 * @package SM\Catalog\Plugin\Model\Category\Attribute\Source
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

declare(strict_types=1);

namespace SM\Catalog\Plugin\Model\Category\Attribute\Source;

/**
 * Class Sortby
 * @package SM\Catalog\Plugin\Model\Category\Attribute\Source
 */
class SortBy
{
    /**
     * @param \Magento\Catalog\Model\Category\Attribute\Source\Sortby $subject
     * @param $options
     * @return array
     */
    public function afterGetAllOptions(
        \Magento\Catalog\Model\Category\Attribute\Source\Sortby $subject,
        $options
    ) {
        $tmpOptions = [];
        $newOptions = \SM\Catalog\Helper\ProductList\Toolbar::getAdditionalOptions();
        foreach ($newOptions as $key => $label) {
            $tmpOptions[] = [
                'label' => $label,
                'value' => $key
            ];
        }

        $columns = array_keys($newOptions);
        foreach ($options as $key => $option) {
            if (empty($option['value'])) {
                continue;
            } elseif ($option['value'] === 'name') {
                $options[$key]['label'] = __('A to Z');
            } elseif (in_array($option['value'], $columns) || $option['value'] === 'price') {
                unset($options[$key]);
            }
        }

        return array_merge($tmpOptions, $options);
    }
}
