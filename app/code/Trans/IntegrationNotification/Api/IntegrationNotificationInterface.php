<?php
/**
 * @category Trans
 * @package  Trans_IntegrationNotification
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationNotification\Api;

/**
 * interface IntegrationNotificationInterface
 */
interface IntegrationNotificationInterface
{
    /**
     * @param string $telephone
     * @param string $body
     * @param bool $isOtp
     * @return mixed
     */
    public function sendSms(string $telephone, string $body, bool $isOtp = false);

    /**
     * @param string $emailTo
     * @param string $subject
     * @param string $body
     * @return mixed
     */
    public function sendEmail(string $emailTo, string $subject, string $body);

    /**
     * @param string[] $deviceIds array
     * @param string $title
     * @param string $body
     * @return mixed
     */
    public function pushNotif($deviceIds, string $title, string $body);
}
