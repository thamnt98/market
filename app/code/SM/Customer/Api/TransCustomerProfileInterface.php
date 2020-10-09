<?php
/**
 * @category    SM
 * @package     SM_Customer
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 * @author      Nam Nguyen <namnd2@smartosc.com>
 *
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\Customer\Api;

/**
 * TranSmart Customer CRUD interface.
 * @api
 * @since 100.0.2
 */
interface TransCustomerProfileInterface
{
    /**
     * @param string $user
     * @param string $type
     * @return bool|mixed
     */
    public function isExistUser($user, $type);

    /**
     * @param string $email
     * @return bool
     */
    public function sendVerificationLink($email);

    /**
     * @param string $email
     * @param string $currentPassword
     * @param string $newPassword
     * @param string $os
     * @return mixed
     */
    public function changePassword($email, $currentPassword, $newPassword, $os='mobile');

    /**
     * @param integer $customerId
     * @param \Magento\Framework\Api\Data\ImageContentInterface $imageContent
     * @return boolean
     */
    public function uploadCustomerAvatar(int $customerId, \Magento\Framework\Api\Data\ImageContentInterface $imageContent);

    /**
     * @param int $customerId
     * @return boolean
     */
    public function limitChangeDob($customerId);

    /**
     * @param int $customerId
     * @param string $currentPassword
     * @param string $newPassword
     * @return \SM\Customer\Api\Data\CustomerChangePasswordResultInterface
     * @throws \Magento\Framework\Webapi\Exception
     */
    public function changePasswordMobile($customerId, $currentPassword, $newPassword);
}
