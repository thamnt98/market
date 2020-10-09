<?php
/**
 * @category Trans
 * @package  Trans_Sprint
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 *
 * Copyright © 2019 CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Sprint\Api\Data;

/**
 * interface SprintCustomerTokenizationInterface
 */
interface SprintCustomerTokenizationInterface
{
	/**
	 * Constants for keys of data array. Identical to the name of the getter in snake case
	 */
	const TABLE_NAME = 'sprint_customer_tokenization';
	const ID = 'id';
	const CUSTOMER_ID = 'customer_id';
	const MASKED_CARD_NO = 'masked_card_no';
	const CARD_TOKEN = 'card_token'; 
	const CREATED_AT = 'created_at';
	const UPDATED_AT = 'updated_at';
	
	/**
	 * @return int
	 */
	public function getId();

	/**
	 * @param int $id
	 * @return void
	 */
	public function setId($id);

	/**
	 * get customer id
	 * @return int
	 */
	public function getCustomerId();

	/**
	 * set customer id
	 * @param int $customerId
	 * @return void
	 */
	public function setCustomerId($customerId);

	/**
	 * get masked card number
	 * @return string
	 */
	public function getMaskedCard();

	/**
	 * set masked card number
	 * @param string $maskedCard
	 * @return void
	 */
	public function setMaskedCard($maskedCard);

	/**
	 * get card token
	 * @return string
	 */
	public function getCardToken();

	/**
	 * set card token
	 * @param string $cardToken
	 * @return void
	 */
	public function setCardToken($cardToken);

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
