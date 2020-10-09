<?php

declare(strict_types=1);

namespace SM\Category\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

/**
 * Class Config
 * @package SM\Customer\Helper
 */
class Config extends AbstractHelper
{
    const INCLUDE_IN_SEARCH_FORM_ATTRIBUTE_CODE = 'include_in_search_form';
    const ALLOW_ADD_TO_COMPARE_ATTRIBUTE_CODE = 'allow_compare';
    const MOST_POPULAR_ATTRIBUTE_CODE = 'most_popular';
    const MAIN_CATEGORY_COLOR = 'main_category_color';
    const SUB_CATEGORY_COLOR = 'sub_category_color';
    const FAVORITE_BRAND_COLOR = 'favorite_brand_color';
    const PRODUCT_CATEGORY_COLOR = 'product_color';
}
