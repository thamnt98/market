<?php
/**
 * @category Trans
 * @package  Trans_Integration
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   J.P <jaka.pondan@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Integration\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use \Trans\Integration\Api\Data\IntegrationChannelInterface;

interface IntegrationChannelRepositoryInterface
{
    /**
     * Retrieve data by id
     *
     * @param int $id
     * @return \Trans\Integration\Api\Data\IntegrationChannelInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($id);

    /**
     * Retrieve data by code
     *
     * @param string $code
     * @return \Trans\Integration\Api\Data\IntegrationChannelInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByCode($code);

    /**
     * Save data.
     *
     * @param \Trans\IntegrationChannelInterface\Api\Data\IntegrationChannelInterface $data
     * @return \Trans\IntegrationChannelInterface\Api\Data\IntegrationChannelInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(IntegrationChannelInterface $data);

     /**
     * Delete data.
     *
     * @param \Trans\IntegrationChannelInterface\Api\Data\IntegrationChannelInterface $data
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(IntegrationChannelInterface $data);
}