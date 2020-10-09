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

namespace Trans\Customer\Preference\SocialLogin\Block\Form;

/**
 * Class Login
 */
class Login extends \Mageplaza\SocialLogin\Block\Form\Login
{
    /**
     * get create account url
     *
     * @return string
     */
    public function getCreateAccountUrl()
    {
        return $this->getUrl('customer/account/create');
    }

    /**
     * get forgot password url
     *
     * @return string
     */
    public function getForgotPasswordUrl()
    {
        return $this->getUrl('customer/account/forgotpassword');
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
