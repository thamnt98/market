<?php
/**
 * @category Trans
 * @package  Trans_IntegrationEntity
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   J.P <jaka.pondan@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationEntity\Api;

use \Trans\IntegrationEntity\Api\Data\IntegrationDataValueInterface;

interface IntegrationDataValueRepositoryInterface {
	/**
	 * Retrieve data by id
	 *
	 * @param int $id
	 * @return \Trans\IntegrationEntity\Api\Data\IntegrationDataValueInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function getById($id);

	/**
	 * Save data.
	 *
	 * @param \Trans\IntegrationEntity\Api\Data\IntegrationDataValueInterface $data
	 * @return \Trans\IntegrationEntity\Api\Data\IntegrationDataValueInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function save(IntegrationDataValueInterface $data);

	/**
	 * Delete data.
	 *
	 * @param \Trans\IntegrationEntity\Api\Data\IntegrationDataValueInterface $data
	 * @return bool true on success
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function delete(IntegrationDataValueInterface $data);

	/**
	 * @param $param
	 * @return mixed
	 */
	public function saveDataValue($param);

	/**
	 * @param $jobId
	 * @param $status
	 * @return mixed
	 */
	public function getByJobIdWithStatus($jobId, $status);

}