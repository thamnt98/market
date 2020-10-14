<?php
/**
 * @category Trans
 * @package  Trans_Customer
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Imam Kusuma <imam.kusuma@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Customer\Helper;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Data
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * get customer phone number
     * 
     * @param array $customer
     * @return string|mixed
     */
    public function getCustomerPhone(array $customer)
    {
        if(isset($customer['custom_attributes']['telephone'])) {
            return $customer['custom_attributes']['telephone']['value'];
        }

        return null;
    }   
}