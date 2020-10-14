<?php
/**
 * @category Trans
 * @package  Trans_Customer
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   J.P <jaka.pondan@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Customer\Api\Data;

/**
 * @api
 */
interface CustomerIntegrationDataInterface
{
    const ID = 'id';

    const CENTRAL_ID = 'central_id';
    const CUST_NAME='customer_name';
    const CUST_PHONE='customer_phone';
    const CUST_EMAIL='customer_email';
    const CUST_PASSHASH='customer_password_hash';
    const CUST_PASS='customer_password';
    const STATUS = 'status'; // waiting , progress , close


    /**
     * Get Central Id
     *
     * @return string
     */
    public function getCentralId();

    /**
     * Set Central Id
     *
     * @param string $cenId
     * @return void
     */
    public function setCentralId($cenId);

    /**
     * Get Customer Name
     *
     * @return string
     */
    public function getCustomerName();

    /**
     * Set Customer Name
     *
     * @param string $name
     * @return void
     */
    public function setCustomerName($name);

    /**
     * Get Customer Phone
     *
     * @return string
     */
    public function getCustomerPhone();

    /**
     * Set Customer Phone
     *
     * @param string $phone
     * @return void
     */
    public function setCustomerPhone($phone);

    /**
     * Get Customer Email
     *
     * @return string
     */
    public function getCustomerEmail();

    /**
     * Set Customer Email
     *
     * @param string $email
     * @return void
     */
    public function setCustomerEmail($email);

    /**
     * Get Customer Passoword Hash
     *
     * @return string
     */
    public function getCustomerPasswordHash();

    /**
     * Set Customer Passoword Hash
     *
     * @param string $pass
     * @return void
     */
    public function setCustomerPasswordHash($pass);

    /**
     * Get Customer Passoword
     *
     * @return string
     */
    public function getCustomerPassword();

    /**
     * Set Customer Passoword
     *
     * @param string $pass
     * @return void
     */
    public function setCustomerPassword($pass);

	/**
	 * Get Status
	 * 
	 * @return int
	 */
	public function getStatus();

	/**
	 * Set Status	
	 * 
	 * @param int $status
	 * @return void
	 */
	public function setStatus($status);




	
}