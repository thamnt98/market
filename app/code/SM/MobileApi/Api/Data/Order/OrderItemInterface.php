<?php


namespace SM\MobileApi\Api\Data\Order;


interface OrderItemInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**
     * @return string
     */
    public function getSku();

    /**
     * @param $data
     * @return $this
     */
    public function setSku($data);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param $data
     * @return $this
     */
    public function setName($data);

    /**
     * @return integer
     */
    public function getQty();

    /**
     * @param $data
     * @return $this
     */
    public function setQty($data);
}
