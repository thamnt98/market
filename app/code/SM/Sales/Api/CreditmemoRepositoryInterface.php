<?php
/**
 * Class CreditmemoRepositoryInteface
 * @package SM\Sales\Api
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\Sales\Api;

use Magento\Tests\NamingConvention\true\mixed;

interface CreditmemoRepositoryInterface
{
    /**
     * @param int $customerId
     * @param int $creditmemoId
     * @return \SM\Sales\Api\Data\Creditmemo\FormInformationInterface
     */
    public function getFormInfo($customerId, $creditmemoId);

    /**
     * @param int $customerId
     * @param mixed $data
     * @return bool
     */
    public function submitRequest($customerId, $data);
}
