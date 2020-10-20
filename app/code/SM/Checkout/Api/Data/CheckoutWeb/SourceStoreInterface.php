<?php

namespace SM\Checkout\Api\Data\CheckoutWeb;

interface SourceStoreInterface
{
    /**
     * @param string $sourceCode
     * @return $this
     */
    public function setSourceCode($sourceCode);

    /**
     * @return string
     */
    public function getSourceCode();

    /**
     * @param float $distance
     * @return $this
     */
    public function setDistance($distance);

    /**
     * @return float
     */
    public function getDistance();
}
