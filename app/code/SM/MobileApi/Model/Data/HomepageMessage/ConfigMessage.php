<?php

namespace SM\MobileApi\Model\Data\HomepageMessage;

use Magento\Framework\Api\AbstractExtensibleObject;
use SM\MobileApi\Api\Data\HomepageMessage\ConfigMessageInterface;

/**
 * Class ConfigMessage
 * @package SM\MobileApi\Model\Data\HomepageMessage
 */
class ConfigMessage extends AbstractExtensibleObject implements ConfigMessageInterface
{
    /**
     * @inheritDoc
     */
    public function getMessage()
    {
        return $this->_get(self::MESSAGE);
    }

    /**
     * @inheritDoc
     */
    public function setMessage($message)
    {
        return $this->setData(self::MESSAGE, $message);
    }

    /**
     * @inheritDoc
     */
    public function getType()
    {
        return $this->_get(self::TYPE);
    }

    /**
     * @inheritDoc
     */
    public function setType($type)
    {
        return $this->setData(self::TYPE, $type);
    }
}
