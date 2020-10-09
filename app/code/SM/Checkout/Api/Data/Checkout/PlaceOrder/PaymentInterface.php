<?php


namespace SM\Checkout\Api\Data\Checkout\PlaceOrder;

use SM\Checkout\Api\Data\Checkout\PaymentMethods\BankInterface;

interface PaymentInterface
{
    /**
     * @return string
     */
    public function getPaymentMethod();

    /**
     * @param $data
     * @return $this
     */
    public function setPaymentMethod($data);

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
     * @return string
     */
    public function getMessage();

    /**
     * @param $data
     * @return $this
     */
    public function setMessage($data);

    /**
     * @return string
     */
    public function getRedirectUrl();

    /**
     * @param $data
     * @return $this
     */
    public function setRedirectUrl($data);
    /**
     * @return string
     */
    public function getAccountNumber();

    /**
     * @param $data
     * @return $this
     */
    public function setAccountNumber($data);
    /**
     * @return string
     */
    public function getExpiredTime();

    /**
     * @param $data
     * @return $this
     */
    public function setExpiredTime($data);

    /**
     * @return string
     */
    public function getTotalAmount();

    /**
     * @param $data
     * @return $this
     */
    public function setTotalAmount($data);

    /**
     * @return string
     */
    public function getReferenceNumber();

    /**
     * @param $data
     * @return $this
     */
    public function setReferenceNumber($data);

    /**
     * @return string
     */
    public function getRelateUrl();

    /**
     * @param $data
     * @return $this
     */
    public function setRelateUrl($data);

    /**
     * @return \SM\Checkout\Api\Data\Checkout\PaymentMethods\BankInterface
     */
    public function getBank();

    /**
     * @param \SM\Checkout\Api\Data\Checkout\PaymentMethods\BankInterface $data
     * @return $this
     */
    public function setBank($data);

    /**
     * @return string
     */
    public function getHowToPay();

    /**
     * @param $data
     * @return $this
     */
    public function setHowToPay($data);

    /**
     * @return \SM\Checkout\Api\Data\Checkout\PaymentMethods\HowToPayInterface[]
     */
    public function getHowToPayObjects();

    /**
     * @param \SM\Checkout\Api\Data\Checkout\PaymentMethods\HowToPayInterface[] $value
     * @return $this
     */
    public function setHowToPayObjects($value);
}
