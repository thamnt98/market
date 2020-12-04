<?php

namespace SM\MobileApi\Api\Data\HomepageMessage;

/**
 * Interface ConfigMessageInterface
 * @package SM\MobileApi\Api\Data\HomepageMessage
 */
interface ConfigMessageInterface
{
    const MESSAGE = 'message';
    const TYPE = 'type';

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
    public function getType();

    /**
     * @param string $type
     * @return $this
     */
    public function setType($type);
}
