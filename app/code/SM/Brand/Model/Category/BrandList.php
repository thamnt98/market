<?php

/**
 * @category SM
 * @package SM_Brand
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author      Chinhvd <chinhvd@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\Brand\Model\Category;

class BrandList extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     */
    public function _construct()
    {
        $this->_init(\SM\Brand\Model\ResourceModel\Category\BrandList::class);
    }
}
