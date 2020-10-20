<?php


namespace SM\Checkout\Api\Data\Checkout\PaymentMethods;


interface MethodInterface
{

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @param $data
     * @return $this
     */
    public function setTitle($data);

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @param $data
     * @return $this
     */
    public function setDescription($data);

    /**
     * @return string
     */
    public function getMinimumAmount();

    /**
     * @param $data
     * @return $this
     */
    public function setMinimumAmount($data);

    /**
     * @return string
     */
    public function getType();

    /**
     * @param $data
     * @return $this
     */
    public function setType($data);

    /**
     * @return \SM\Checkout\Api\Data\Checkout\PaymentMethods\BankInterface[]
     */
    public function getBanks();

    /**
     * @param \SM\Checkout\Api\Data\Checkout\PaymentMethods\BankInterface[] $data
     * @return $this
     */
    public function setBanks($data);


}
