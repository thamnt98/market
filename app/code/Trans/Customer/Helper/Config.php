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

use Magento\Store\Model\ScopeInterface;

/**
 * Class Config
 */
class Config extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Constant config path
     */
    const ENABLE_LOGIN_PHONE = 'customer/startup/enabled_phone_login';
    const ENABLE_ACCOUNT_RECOVERY = 'customer/account_recovery/enabled';
    const REDIRECT_RECOVERY_PAGE = 'customer/account_recovery/redirect_recovery_page';
    const RECOVERY_EMAIL_TEMPLATE = 'customer/account_recovery/recovery_email_template';

    /**
     * Get config value by path
     *
     * @param string $path
     * @return mixed
     */
    public function getConfigValue($path)
    {
        return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE);
    }

    /**
     * is redirect to recovery page
     *
     * @return bool
     */
    public function isRedirectRecoveryPage()
    {
        return $this->getConfigValue(self::REDIRECT_RECOVERY_PAGE);
    }

    /**
     * is enable account recovery
     *
     * @return bool
     */
    public function isEnableAccountRecovery()
    {
        return $this->getConfigValue(self::ENABLE_ACCOUNT_RECOVERY);
    }

    /**
     * get email template id
     *
     * @return int
     */
    public function getEmailTemplateId()
    {
        return $this->getConfigValue(self::RECOVERY_EMAIL_TEMPLATE);
    }
}
