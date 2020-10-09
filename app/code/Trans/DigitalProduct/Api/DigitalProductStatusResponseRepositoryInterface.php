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

namespace Trans\DigitalProduct\Api;

use Trans\DigitalProduct\Api\Data\DigitalProductStatusResponseInterface;

/**
 * @api
 */
interface DigitalProductStatusResponseRepositoryInterface
{

    /**
     * Save page.
     *
     * @param \Trans\DigitalProduct\Api\Data\DigitalProductStatusResponseInterface $statusResponse
     * @return \Trans\DigitalProduct\Api\Data\DigitalProductStatusResponseInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(DigitalProductStatusResponseInterface $statusResponse);

    /**
     * Retrieve StatusResponse.
     *
     * @param int $statusResponseId
     * @return \Trans\DigitalProduct\Api\Data\DigitalProductStatusResponseInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($statusResponseId);

    /**
     * Delete Digital Product status Response.
     *
     * @param \Trans\DigitalProduct\Api\Data\DigitalProductStatusResponseInterface $statusResponse
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(DigitalProductStatusResponseInterface $statusResponse);

    /**
     * Delete Digital Product Status Response by ID.
     *
     * @param int $statusResponseId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($statusResponseId);
}
