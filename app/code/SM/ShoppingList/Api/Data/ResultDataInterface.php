<?php
/**
 * @category Magento
 * @package SM\ShoppingList\Api\Data
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author Hung Viet Nguyen <hungnv6@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\ShoppingList\Api\Data;

/**
 * Interface ResultDataInterface
 * @package SM\ShoppingList\Api\Data
 */
interface ResultDataInterface
{
    const STATUS = "status";
    const RESULT = "result";
    const MESSAGE = "message";

    /**
     * @param string $value
     * @return $this
     */
    public function setMessage($value);

    /**
     * @return string
     */
    public function getMessage();

    /**
     * @return int
     */
    public function getStatus();

    /**
     * @return \SM\ShoppingList\Api\Data\ShoppingListDataInterface[]
     */
    public function getResult();

    /**
     * @param int $value
     * @return $this
     */
    public function setStatus($value);

    /**
     * @param \SM\ShoppingList\Api\Data\ShoppingListDataInterface[] $value
     * @return $this
     */
    public function setResult($value);
}
