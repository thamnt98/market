<?php
namespace SM\MobileApi\Api\Data\Order;

interface OrderInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**
     * @return string
     */
    public function getIncrementId();

    /**
     * @param $data
     * @return $this
     */
    public function setIncrementId($data);

    /**
     * @return string
     */
    public function getCreatedAt();

    /**
     * @param $data
     * @return $this
     */
    public function setCreatedAt($data);

    /**
     * @return float
     */
    public function getBaseGrandTotal();

    /**
     * @param $data
     * @return $this
     */
    public function setBaseGrandTotal($data);

    /**
     * @return string
     */
    public function getStatus();

    /**
     * @param $data
     * @return $this
     */
    public function setStatus($data);


    /**
     * @return \SM\MobileApi\Api\Data\Order\OrderItemInterface[]
     */
    public function getOrderItems();

    /**
     * @param \SM\MobileApi\Api\Data\Order\OrderItemInterface[] $data
     * @return  $this
     */
    public function setOrderItems($data);


}
