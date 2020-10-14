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

namespace Trans\IntegrationNotification\Api\Data;

/**
 * @api
 */
interface IntegrationNotificationLogInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const TABLE_NAME = 'integration_notification_log';
    const ID = 'id';
    const CHANNEL = 'channel';
    const PARAM = 'param';
    const PARAM_ENCRYPT = 'param_encrypt';
    const RESPONSE = 'response';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_ad';
    
    /**
     * Get id
     *
     * @return int
     */
    public function getId();

    /**
     * Set id
     *
     * @param string $idData
     * @return void
     */
    public function setId($idData);

    /**
     * get channel
     *
     * @return string
     */
    public function getChannel();

    /**
     * Set channel
     *
     * @param string $channel
     * @return void
     */
    public function setChannel($channel);

    /**
     * get param
     *
     * @return string
     */
    public function getParam();

    /**
     * Set param
     *
     * @param string $param
     * @return void
     */
    public function setParam($param);

    /**
     * get param encrypted
     *
     * @return string
     */
    public function getParamEncrypt();

    /**
     * Set param encrypt
     *
     * @param string $param
     * @return void
     */
    public function setParamEncrypt($param);

    /**
     * get response
     *
     * @return string
     */
    public function getResponse();

    /**
     * Set response
     *
     * @param string $response
     * @return void
     */
    public function setResponse($response);

    /**
     * get created at
     *
     * @return string
     */
    public function getCreatedAt();

    /**
     * Set created at
     *
     * @param string $createdAt
     * @return void
     */
    public function setCreatedAt($createdAt);

    /**
     * get updated at
     *
     * @return string
     */
    public function getUpdatedAt();

    /**
     * Set updated at
     *
     * @param string $updatedAt
     * @return void
     */
    public function setUpdatedAt($updatedAt);
}
