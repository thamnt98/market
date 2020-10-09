<?php
/**
 * Interface CartRepositoryInterface
 * @package SM\DigitalProduct\
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

declare(strict_types=1);

namespace SM\DigitalProduct\Api;

interface OrderRepositoryInterface
{
    /**
     * Gets collection items.
     * @param int $customerId
     * @param int $limit
     * @return \SM\DigitalProduct\Api\Data\Order\OrderItemInterface[] Array of collection items.
     */
    public function getList($customerId, $limit = 10);
}
