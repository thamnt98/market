<?php

namespace SM\Checkout\Api\Data;

use Magento\Tests\NamingConvention\true\mixed;

/**
 * Interface for managing customers accounts.
 * @api
 */
interface AccountManagementResponseInterface
{
    /**
     * @return bool
     */
    public function getStatus();

    /**
     * @return mixed
     */
    public function getResult();

    /**
     * @param bool $status
     * @return $this
     */
    public function setStatus($status);

    /**
     * @param string $result
     * @return $this
     */
    public function setResult($result);
}
