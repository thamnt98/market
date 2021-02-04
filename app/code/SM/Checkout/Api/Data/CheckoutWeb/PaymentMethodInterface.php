<?php

namespace SM\Checkout\Api\Data\CheckoutWeb;

interface PaymentMethodInterface
{
    /**
     * @param string $title
     * @return $this
     */
    public function setTitle($title);

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId);

    /**
     * @return int
     */
    public function getStoreId();

    /**
     * @param bool $isActive
     * @return $this
     */
    public function setIsActive($isActive);

    /**
     * @return int
     */
    public function getIsActive();

    /**
     * @param string $description
     * @return $this
     */
    public function setDescription($description);

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @param string $tooltipDescription
     * @return $this
     */
    public function setTooltipDescription($tooltipDescription);

    /**
     * @return string
     */
    public function getTooltipDescription();

    /**
     * @param string $logo
     * @return $this
     */
    public function setLogo($logo);

    /**
     * @return string
     */
    public function getLogo();

    /**
     * @param string $method
     * @return $this
     */
    public function setMethod($method);

    /**
     * @return string
     */
    public function getMethod();
}
