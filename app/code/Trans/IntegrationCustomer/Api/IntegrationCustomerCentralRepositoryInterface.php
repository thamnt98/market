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

namespace Trans\IntegrationCustomer\Api;

use \Trans\IntegrationCustomer\Api\Data\IntegrationCustomerCentralInterface;

interface IntegrationCustomerCentralRepositoryInterface {
	/**
	 * Retrieve data by id
	 *
	 * @param int $id
	 * @return \Trans\IntegrationCustomer\Api\Data\IntegrationCustomerCentralInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function getById($id);

	/**
	 * Save data.
	 *
	 * @param \Trans\IntegrationCustomer\Api\Data\IntegrationCustomerCentralInterface $data
	 * @return \Trans\IntegrationCustomer\Api\Data\IntegrationJobInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function save(IntegrationCustomerCentralInterface $data);

	/**
	 * Delete data.
	 *
	 * @param \Trans\Integration\Api\Data\IntegrationCustomerCentralInterface $data
	 * @return bool true on success
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function delete(IntegrationCustomerCentralInterface $data);

	/**
	 * Save Data By Array Param
	 * @param $param
	 * @return mixed
	 */
	public function saveData($param);

	/**
	 * Get Customer by Email
	 * @param string $email
	 * @return string
	 */
	public function getCustomerByEmail($email);

}