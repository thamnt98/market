<?php
/**
 * @category Trans
 * @package  Trans_CatalogStock
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   J.P <jaka.pondan@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationCatalogStock\Api;

use \Trans\IntegrationCatalogStock\Api\Data\IntegrationDataValueInterface;

interface IntegrationDataValueRepositoryInterface {
	/**
	 * Retrieve data by id
	 *
	 * @param int $id
	 * @return \Trans\IntegrationCatalogStock\Api\Data\IntegrationDataValueInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function getById($id);

	/**
	 * Save data.
	 *
	 * @param \Trans\IntegrationCatalogStock\Api\Data\IntegrationDataValueInterface $data
	 * @return \Trans\IntegrationCatalogStock\Api\Data\IntegrationDataValueInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function save(IntegrationDataValueInterface $data);

	/**
	 * Delete data.
	 *
	 * @param \Trans\IntegrationCatalogStock\Api\Data\IntegrationDataValueInterface $data
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

	/**
	 * @param $data
	 * @param $status
	 * @return mixed
	 */
	public function getByDataValueWithStatus($data,$status);

    /**
     * @param array $inserts
     * @return int
     */
    public function insertBulkUsingRawQuery($inserts);

    /**
     * @param int $jobId
     * @param int $status
     * @return mixed
     */
	public function getAllByJobIdStatusUsingRawQuery($jobId, $status);	

}