<?php
/**
 * @category Trans
 * @package  Trans_IntegrationCustomer
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   J.P <jaka.pondan@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationCustomer\Api\Data;

/**
 * @api
 */
interface IntegrationCustomerCentralInterface
{
	/**
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
	const TABLE_NAME = 'integration_customer_central';
	const ID = 'id';
	const CUST_ID = 'integration_id'; // method id
    const CENTRAL_ID = 'central_id'; // Batch id are unique id base on chunk jobs
	const STATUS = 'status'; // waiting , progress , close


    /**
     * Constant for Message
     */
    const MSG_DATA_NOTAVAILABLE= 'Theres no data available';
	
	/**
	 * Get id
	 * 
	 * @return int
	 */
	public function getId();

	/**
	 * Set id
	 * 
	 * @param string $id
	 * @return void
	 */
	public function setId($id);

	/**
	 * Get Customer Id
	 * 
	 * @return string
	 */
	public function getMagentoCustomerId();

	/**
	 * Set Customer Id
	 * 
	 * @param string $custId
	 * @return void
	 */
	public function setMagentoCustomerId($custId);

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

	/**
	 * Get created at
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
	 * Get updated at
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