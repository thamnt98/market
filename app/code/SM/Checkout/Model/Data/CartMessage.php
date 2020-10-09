<?php

namespace SM\Checkout\Model\Data;

class CartMessage extends \Magento\Framework\Model\AbstractExtensibleModel implements \SM\Checkout\Api\Data\CartMessage
{
    /**
     * @inheritDoc
     */
    public function getMessage()
    {
        return $this->getData(self::MESSAGE);
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
    public function getMessageType()
    {
        return $this->getData(self::MESSAGE_TYPE);
    }

    /**
     * @inheritDoc
     */
    public function setMessageType($type)
    {
        return $this->setData(self::MESSAGE_TYPE, $type);
    }
}
