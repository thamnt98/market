<?php
/**
 * interface JiraRepositoryInterface
 * @package SM\JiraService\Api
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Dung Nguyen My <dungnm@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\JiraService\Api;

interface JiraRepositoryInterface
{
    /**
     * @param int $customerId
     * @param string $typeName
     * @param array $data
     * @return \SM\JiraService\Api\Data\ResponseInterface
     */
    public function createTicket($customerId, $typeName, $data);
}
