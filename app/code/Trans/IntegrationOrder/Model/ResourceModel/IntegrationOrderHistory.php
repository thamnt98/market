<?php
/**
 * @category Trans
 * @package  Trans_IntegrationOrder
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Randy <muhammad.randy@ctcorpdigital.com>
 *
 * Copyright © 2020 PT CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationOrder\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Trans\IntegrationOrder\Api\Data\IntegrationOrderHistoryInterface;

/**
 * Class IntegrationOrderHistory
 * @package Trans\IntegrationOrder\Model\ResourceModel
 */
class IntegrationOrderHistory extends AbstractDb
{
    /**
     * IntegrationOrderHistory constructor.
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
        $this->_init(IntegrationOrderHistoryInterface::TABLE_NAME, IntegrationOrderHistoryInterface::HISTORY_ID);
    }
}
