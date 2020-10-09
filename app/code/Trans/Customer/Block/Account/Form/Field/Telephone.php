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
namespace Trans\Customer\Block\Account\Form\Field;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\AccountManagement;

/**
 * Customer telephonr
 *
 * @api
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 * @since 100.0.2
 */
class Telephone extends \Magento\Customer\Block\Account\Dashboard
{
    /**
     * get customer telephone
     * 
     * @return string
     */
    public function getTelephone()
    {
        $phone = '';

        if($this->customerSession->isLoggedIn()) {
            $customer = $this->getCustomer();
            if($customer->getCustomAttribute('telephone')) {
                $phone = $customer->getCustomAttribute('telephone')->getValue();
            }
        }
        
        return $phone;
    }

    public function genRndString($length = 4, $chars = '0123456789')
    {
        if($length > 0)
        {
            $len_chars = (strlen($chars) - 1);
            $the_chars = $chars{rand(0, $len_chars)};
            for ($i = 1; $i < $length; $i = strlen($the_chars))
            {
                $r = $chars{rand(0, $len_chars)};
                if ($r != $the_chars{$i - 1}) $the_chars .=  $r;
            }

            return $the_chars;
        }
    }
}
