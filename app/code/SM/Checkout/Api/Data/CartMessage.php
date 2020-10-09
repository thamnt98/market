<?php

namespace SM\Checkout\Api\Data;

interface CartMessage extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const MESSAGE = 'message';
    const MESSAGE_TYPE = 'type';

    /**
     * @return string
     */
    public function getMessage();

    /**
     * @param string $message
     * @return $this
     */
    public function setMessage($message);

    /**
     * @return string
     */
    public function getMessageType();

    /**
     * @param string $type
     * @return $this
     */
    public function setMessageType($type);
}
