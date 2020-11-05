<?php
/**
 * @category Trans
 * @package  Trans_IntegrationLogistic
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Randy <muhammad.randy@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationLogistic\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Trans\IntegrationLogistic\Api\Data\TrackingLogisticInterface;

/**
 * Class TrackingLogistic
 * @package Trans\IntegrationLogistic\Model\ResourceModel
 */
class TrackingLogistic extends AbstractDb
{
    /**
     * TrackingLogistic constructor.
     * @param Context $context
     * @param string|null $connectionName
     */
    public function __construct(
        Context $context,
        string $connectionName = null
    ) {
        parent::__construct(
            $context,
            $connectionName
        );
    }

    public function _construct()
    {
        $this->_init(TrackingLogisticInterface::TABLE_NAME, TrackingLogisticInterface::TRACKING_ID);
    }
}
