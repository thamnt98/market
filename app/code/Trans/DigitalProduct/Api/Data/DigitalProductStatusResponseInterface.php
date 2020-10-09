<?php
/**
 * @category Trans
 * @package  Trans_DigitalProduct
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   hadi <ashadi.sejati@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\DigitalProduct\Api\Data;

/**
 * @api
 */
interface DigitalProductStatusResponseInterface
{

    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case
     *
     */

    const DEFAULT_EVENT = 'trans_digitalproduct_transaction_status';
    const TABLE_NAME    = 'digitalproduct_transaction_status';

    /**
     * Constant Field name
     */
    const ID          = 'id';
    const CUSTOMER_ID = 'customer_id';
    const ORDER_ID = 'order_id';
    const REQUEST     = 'request';
    const RESPONSE    = 'response';
    const STATUS      = 'status';
    const MESSAGE     = 'message';
    const UPDATED_AT  = 'updated_at';
    const CREATED_AT  = 'created_at';

    /**
     * get id
     *
     * @return int
     */
    public function getId();

    /**
     * Get Brand Id
     *
     * @param string
     */
    public function getCustomerId();

    /**
     * Set Brand Id
     *
     * @param string $customerId
     * @return void
     */
    public function setCustomerId($customerId);

    /**
     * Get Brand Id
     *
     * @param string
     */
    public function getRequest();

    /**
     * Set Brand Id
     *
     * @param string $request
     * @return void
     */
    public function setRequest($request);

    /**
     * Get Brand Id
     *
     * @param string
     */
    public function getResponse();

    /**
     * Set Brand Id
     *
     * @param string $response
     * @return void
     */
    public function setResponse($response);

    /**
     * Get Brand Id
     *
     * @param string
     */
    public function getStatus();

    /**
     * Set Brand Id
     *
     * @param string $status
     * @return void
     */
    public function setStatus($status);

    /**
     * Get Brand Id
     *
     * @param string
     */
    public function getMessage();

    /**
     * Set Brand Id
     *
     * @param string $message
     * @return void
     */
    public function setMessage($message);

    /**
     * @return string
     */
    public function getCreatedAt();

    /**
     * @param string $createdAt
     * @return void
     */
    public function setCreatedAt($createdAt);

    /**
     * @return string
     */
    public function getUpdatedAt();

    /**
     * @param string $updatedAt
     * @return void
     */
    public function setUpdatedAt($updatedAt);
}
