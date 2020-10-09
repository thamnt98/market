<?php

namespace SM\Checkout\Api\Data\Checkout;

interface SupportShippingInterface
{
    /**
     * @param bool $use
     * @return $this
     */
    public function setUse($use);

    /**
     * @return bool
     */
    public function getUse();

    /**
     * @param string $message
     * @return $this
     */
    public function setMessage($message);

    /**
     * @return string
     */
    public function getMessage();

    /**
     * @param string $addressMassage
     * @return $this
     */
    public function setAddressMessage($addressMassage);

    /**
     * @return string
     */
    public function getAddressMessage();

    /**
     * @param string $addressSupport
     * @return $this
     */
    public function setAddressSupport($addressSupport);

    /**
     * @return string
     */
    public function getAddressSupport();

}
