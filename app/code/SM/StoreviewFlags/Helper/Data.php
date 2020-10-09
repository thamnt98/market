<?php

/**
 * @category SM
 * @package SM_Theme
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 * @author      Chinhvd <chinhvd@smartosc.com>
 *
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\StoreviewFlags\Helper;

use Magento\Framework\UrlInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Data
 *
 * @package SM\StoreviewFlags\Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * GetUpload
     *
     * @param number $storeId
     * @return mixed
     */
    public function getFlagUpload($storeId = null)
    {
        return $this->scopeConfig->getValue(
            'sm_store_flag/general/upload_flag',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * GetUpload
     *
     * @param number $storeId
     * @return mixed
     */
    public function getAbbreviationName($storeId = null)
    {
        return $this->scopeConfig->getValue(
            'sm_store_flag/general/abbreviation_name',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }


}
