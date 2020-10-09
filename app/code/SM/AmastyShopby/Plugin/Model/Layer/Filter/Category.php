<?php
/**
 * Class Category
 * @package SM\AmastyShopby\Plugin\Model\Layer\Filter
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

declare(strict_types=1);

namespace SM\AmastyShopby\Plugin\Model\Layer\Filter;

class Category
{
    public function afterGetName()
    {
        return __("Shop by Category");
    }
}
