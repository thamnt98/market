<?php
/**
 * @category Trans
 * @package  Trans_Customer
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 *
 * Copyright © 2019 CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Customer\Preference\Customer\Block\Form;

/**
 * Class Login
 */
class Login extends \Mageplaza\SocialLogin\Block\Form\Login
{
    public function getVerificationBlock()
    {
    	return $this->getLayout()->createBlock('\Trans\Reservation\Block\Form\Verification');
    }

    /**
     * get send sms verification url
     * 
     * @return string
     */
    public function getSendSmsVerificationUrl()
    {
    	return $this->getVerificationBlock()->getSendSmsVerificationUrl();
    }

    /**
     * get login post url
     * 
     * @return string
     */
    public function getLoginPostUrl()
    {
    	return $this->getUrl('customer/account/loginPost');
    }
}
