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

use Magento\Framework\Api\SearchCriteriaInterface;
use \Trans\IntegrationCustomer\Api\Data\IntegrationJobInterface;

interface IntegrationJobRepositoryInterface
{
    /**
     * Retrieve data by id
     *
     * @param int $id
     * @return \Trans\IntegrationCustomer\Api\Data\IntegrationJobInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($id);

    /**
     * Save data.
     *
     * @param \Trans\IntegrationCustomer\Api\Data\IntegrationJobInterface $data
     * @return \Trans\IntegrationCustomer\Api\Data\IntegrationJobInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(IntegrationJobInterface $data);

     /**
     * Delete data.
     *
     * @param \Trans\IntegrationCustomer\Api\Data\IntegrationJobInterface $data
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(IntegrationJobInterface $data);

    /**
     * Get By Method Id
     * @param $mdId
     * @return mixed
     */
    public function getByMdIdWithStatus($mdId,$status);


    /**
     * Get By Method Id First Item
     * @param $mdId
     * @return mixed
     */
    public function getByMdIdFirstItem($mdId,$status);

    /**
     * @param $data
     * @return mixed
     */
    public function saveJobs($data);

    /**
     * Get MD First Item FIlter by Multi Md Id
     * @param $mdId
     * @param $status
     * @return mixed
     */
    public function getByMdIdFirstItemInMdId($mdId,$status);

    /**
     * Get Md FIlter by Multi Md Id With status
     * @param $mdId
     * @param $status
     * @return mixed
     */
    public function getByMdIdMultiWithStatus($mdId,$status);
}