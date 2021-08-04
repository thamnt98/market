<?php

/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Category
 *
 * Date: June, 09 2021
 * Time: 4:39 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Category\Model\Entity\Attribute\Source;

class CategoryType extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    const TYPE_DEFAULT = 'category';
    const TYPE_BRAND   = 'brand';
    const TYPE_PARTNER = 'partner';

    public function getAllOptions()
    {
        return [
            ['value' => self::TYPE_DEFAULT, 'label' => __('Category')],
            ['value' => self::TYPE_BRAND, 'label' => __('Brand')],
            ['value' => self::TYPE_PARTNER, 'label' => __('Dropship partner')],
        ];
    }
}
