<?php

namespace SM\Checkout\Api\Data\Checkout\Estimate\ItemAdditionalInfo;

interface AdditionalInfoInterface
{
    /**
     * @param \SM\Checkout\Api\Data\Checkout\Estimate\ItemAdditionalInfo\Delivery\DeliveryInterface $data
     * @return $this
     */
    public function setDelivery($data);

    /**
     * @return \SM\Checkout\Api\Data\Checkout\Estimate\ItemAdditionalInfo\Delivery\DeliveryInterface
     */
    public function getDelivery();

    /**
     * @return \SM\Checkout\Api\Data\CartItem\InstallationInterface
     */
    public function getInstallationInfo();

    /**
     * @param \SM\Checkout\Api\Data\CartItem\InstallationInterface $info
     * @return $this
     */
    public function setInstallationInfo($info);

}
