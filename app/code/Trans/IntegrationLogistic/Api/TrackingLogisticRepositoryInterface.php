<?php
/**
 * @category Trans
 * @package  Trans_IntegrationLogistic
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Randy <muhammad.randy@ctcorpdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationLogistic\Api;

use Trans\IntegrationLogistic\Api\Data\TrackingLogisticInterface;

interface TrackingLogisticRepositoryInterface
{
    /**
     * Save data.
     *
     * @param \Trans\IntegrationLogistic\Api\Data\TrackingLogisticInterface $data
     * @return \Trans\IntegrationLogistic\Api\Data\TrackingLogisticInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(TrackingLogisticInterface $data);

    /**
     * Retrieve data by id
     *
     * @param int $trackingId
     * @return \Trans\IntegrationLogistic\Api\Data\TrackingLogisticInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($trackingId);
}
