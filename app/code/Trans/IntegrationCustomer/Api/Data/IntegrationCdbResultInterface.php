<?php
/**
 * @category Trans
 * @package  Trans_IntegrationCustomer
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 *
 * Copyright © 2019 CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationCustomer\Api\Data;

/**
 * @api
 */
interface IntegrationCdbResultInterface
{
    /**#@+
     * Constants for keys of data array
     */
    const STATUS = 'status';
    const MESSAGES = 'messages';
    const MESSAGES_ID = 'messages_id';

    /**
     * Constant for Message
     */
    const MSG_DATA_NOTAVAILABLE= 'Theres no data available';
    const MSG_DATA_SUCCESS= 'Update data success';

    /**#@-*/

    /**
     * Get status.
     *
     * @return int
     */
    public function getStatus();

    /**
     * Set status.
     *
     * @param int $status
     * @return $this
     */
    public function setStatus($status);

    /**
     * Get error messages id as string.
     *
     * @return int
     */
    public function getMessageId();

    /**
     * Set error message id.
     *
     * @param int $messageid
     * @return string
     */
    public function setMessageId($messageid);

    /**
     * Get error message as array in case of validation failure, else return empty array.
     *
     * @return string
     */
    public function getMessage();

    /**
     * Set error message as array in case of validation failure.
     *
     * @param string $message
     * @return string
     */
    public function setMessage(string $messages);

    /**
     * Generate message id.
     *
    * @return string
     */
    public function generateMessageId();
}
