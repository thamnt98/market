<?php

namespace SM\Customer\Api\Data;

/**
 * Interface CustomerChangePasswordResultInterface
 * @package SM\Customer\Api\Data
 */
interface CustomerChangePasswordResultInterface
{
    const STATUS = 'status';
    const MESSAGE = 'message';
    const LOCATION_APPEARS = 'location_appears';

    /**
     * @return boolean
     */
    public function getStatus();

    /**
     * @param bool $status
     * @return $this
     */
    public function setStatus($status);

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
    public function getLocationAppears();

    /**
     * @param string $location
     * @return $this
     */
    public function setLocationAppears($location);
}
