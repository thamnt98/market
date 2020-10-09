<?php
/**
 * @category Trans
 * @package  Trans_Customer
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 *
 * Copyright © 2019 CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Customer\Api;

use Magento\Framework\Exception\InputException;

/**
 * Interface for managing customers accounts.
 * @api
 */
interface AccountManagementInterface
{
    /**
     * Check if given telephone is associated with a customer account in given website.
     *
     * @param string $telephone
     * @param int $websiteId If not set, will use the current websiteId
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function isTelephoneAvailable($telephone, $websiteId = null);

    /**
     * Verify sms verification.
     *
     * @param string $code
     * @param string $verificationId
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function authVerification($code, $verificationId);

    /**
     * Request to send SMS Verification.
     *
     * @param string $telephone
     * @param bool $isNeedCheck
     * @param string $language
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function sendSmsVerification($telephone, $isNeedCheck, $language = "id");

    /**
     * Check customer data for registrasi.
     *
     * @param string $telephone
     * @param string $email
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function checkCustomerRegister($telephone = null, $email = null);

    /**
     * get customer detail from centralize.
     *
     * @param string $telephone
     * @param string $customerCdbId
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCentralCustomerData($telephone = null, $customerCdbId = null);

    /**
     * Initiate reset password without send email.
     *
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @param int $websiteId If not set, will use the current websiteId
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function initateResetPassword(\Magento\Customer\Api\Data\CustomerInterface $customer, $websiteId = null);
}
