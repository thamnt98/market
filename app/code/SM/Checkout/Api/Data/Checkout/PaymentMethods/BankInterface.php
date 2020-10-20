<?php


namespace SM\Checkout\Api\Data\Checkout\PaymentMethods;


interface BankInterface
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
    public function getCode();

    /**
     * @param $data
     * @return $this
     */
    public function setCode($data);

    /**
     * @return string
     */
    public function getLogo();

    /**
     * @param string $data
     * @return $this
     */
    public function setLogo($data);


    /**
     * @return string
     */
    public function getMinimumAmount();

    /**
     * @param string $data
     * @return $this
     */
    public function setMinimumAmount($data);

    /**
     * @return string[]
     */
    public function getContent();

    /**
     * @return \SM\Checkout\Api\Data\Checkout\PaymentMethods\HowToPayInterface[]
     */
    public function getContentObjects();

    /**
     * @param \SM\Checkout\Api\Data\Checkout\PaymentMethods\HowToPayInterface[] $value
     * @return $this
     */
    public function setContentObjects($value);

    /**
     * @param string[] $data
     * @return $this
     */
    public function setContent($data);

    /**
     * @return \SM\Checkout\Api\Data\Checkout\PaymentMethods\InstallmentTermInterface[]
     */
    public function getTerms();

    /**
     * @param \SM\Checkout\Api\Data\Checkout\PaymentMethods\InstallmentTermInterface[] $data
     * @return $this
     */
    public function setTerms($data);

}
