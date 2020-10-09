<?php

namespace SM\Customer\Api\Data;

/**
 * Interface ResultInterface
 * @package SM\Customer\Api\Data
 */
interface ResultInterface
{
    const MESSAGE = 'message';
    const ARGUMENT = 'argument';

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
    public function getArgument();

    /**
     * @param string $argument
     * @return $this
     */
    public function setArgument($argument);
}
