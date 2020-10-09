<?php

namespace SM\Checkout\Api\Data\Checkout\Estimate;

interface EstimateDataInterface
{
    /**
     * @param \SM\Checkout\Api\Data\Checkout\Estimate\ItemInterface[] $items
     * @return $this
     */
    public function setItems($items);

    /**
     * @return \SM\Checkout\Api\Data\Checkout\Estimate\ItemInterface[]
     */
    public function getItems();

    /**
     * @param \SM\Checkout\Api\Data\Checkout\Estimate\AdditionalInfoInterface $additionalInfo
     * @return $this
     */
    public function setAdditionalInfo($additionalInfo);

    /**
     * @return \SM\Checkout\Api\Data\Checkout\Estimate\AdditionalInfoInterface
     */
    public function getAdditionalInfo();
}
