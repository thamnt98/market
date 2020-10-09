<?php
/**
 * @category Magento
 * @package SM\ShoppingList\Model\Data
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author Hung Viet Nguyen <hungnv6@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\ShoppingList\Model\Data;

use Magento\Framework\DataObject;
use SM\ShoppingList\Api\Data\ResultDataInterface;
use SM\ShoppingList\Api\Data\ShoppingListDataInterface;

/**
 * Class ResultData
 * @package SM\ShoppingList\Model\Data
 */
class ResultData extends DataObject implements ResultDataInterface
{

    /**
     * @inheritDoc
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * @return string
     */
    public function getResult()
    {
        return $this->getData(self::RESULT);
    }

    /**
     * @inheritDoc
     */
    public function setStatus($value)
    {
        return $this->setData(self::STATUS, $value);
    }

    /**
     * @inheritDoc
     */
    public function setResult($value)
    {
        return $this->setData(self::RESULT, $value);
    }

    /**
     * @inheritDoc
     */
    public function setMessage($value)
    {
        return $this->setData(self::MESSAGE, $value);
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->getData(self::MESSAGE);
    }
}
