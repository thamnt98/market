<?php
/**
 * @category Trans
 * @package  Trans_IntegrationNotification
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 *
 * Copyright © 2019 CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationNotification\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Trans\IntegrationNotification\Api\Data\IntegrationNotificationLogInterface;

interface IntegrationNotificationLogRepositoryInterface
{
    /**
     * Save data.
     *
     * @param \Trans\IntegrationNotification\Api\Data\IntegrationNotificationLogInterface $data
     * @return \Trans\IntegrationNotification\Api\Data\IntegrationNotificationLogInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(IntegrationNotificationLogInterface $data);

    /**
     * Delete data.
     *
     * @param \Trans\IntegrationNotification\Api\Data\IntegrationNotificationLogInterface $data
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(IntegrationNotificationLogInterface $data);
}
