<?php


namespace SM\MobileApi\Api\Data\Order;


interface ListOrdersInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{

    /**
     * @return \SM\MobileApi\Api\Data\Order\OrderInterface[]
     */
    public function getOrders();

    /**
     * @param \SM\MobileApi\Api\Data\Order\OrderInterface[] $data
     * @return $this
     */
    public function setOrders($data);

    /**
     * @return integer
     */
    public function getPageSize();

    /**
     * @param $data
     * @return $this
     */
    public function setPageSize($data);

    /**
     * @return integer
     */
    public function getTotal();

    /**
     * @param $data
     * @return $this
     */
    public function setTotal($data);

}
