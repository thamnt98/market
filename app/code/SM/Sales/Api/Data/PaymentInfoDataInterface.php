<?php
/**
 * @category Magento
 * @package SM\Sales\Api\Data
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author Hung Viet Nguyen <hungnv6@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\Sales\Api\Data;

/**
 * Interface PaymentInfoDataInterface
 * @package SM\Sales\Api\Data
 */
interface PaymentInfoDataInterface
{
    const TRANSACTION_ID = "transaction_id";
    const METHOD = "method";
    const BANK_ISSUER = "bank_issuer";
    const CARD_BRAND = "card_brand";
    const CARD_TYPE = "card_type";
    const CARD_NO = "card_no";
    const EXPIRE_DATE = "expire_date";
    const VIRTUAL_ACCOUNT = "virtual_account";
    const REDIRECT_URL = "redirect_url";
    const HOW_TO_PAY = "how_to_pay";
    const HOW_TO_PAY_OBJECTS = "how_to_pay_objects";
    const LOGO = "logo";

    /**
     * @param string $value
     * @return $this
     */
    public function setTransactionId($value);

    /**
     * @return string|null
     */
    public function getTransactionId();

    /**
     * @param string $value
     * @return $this
     */
    public function setMethod($value);

    /**
     * @return string
     */
    public function getMethod();

    /**
     * @return string|null
     */
    public function getLogo();

    /**
     * @param string $value
     * @return $this
     */
    public function setLogo($value);

    /**
     * @return string|null
     */
    public function getRedirectUrl();

    /**
     * @param string $value
     * @return $this
     */
    public function setRedirectUrl($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setBankIssuer($value);

    /**
     * @return string|null
     */
    public function getBankIssuer();

    /**
     * @param string $value
     * @return $this
     */
    public function setCardBrand($value);

    /**
     * @return string|null
     */
    public function getCardBrand();

    /**
     * @param string $value
     * @return $this
     */
    public function setCardType($value);

    /**
     * @return string|null
     */
    public function getCardType();

    /**
     * @param string $value
     * @return $this
     */
    public function setCardNo($value);

    /**
     * @return string|null
     */
    public function getCardNo();

    /**
     * @return string
     */
    public function getExpireDate();

    /**
     * @param string $value
     * @return $this
     */
    public function setExpireDate($value);

    /**
     * @return string
     */
    public function getVirtualAccount();

    /**
     * @param string $value
     * @return $this
     */
    public function setVirtualAccount($value);

    /**
     * @return string
     */
    public function getHowToPay();

    /**
     * @param string $value
     * @return $this
     */
    public function setHowToPay($value);

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
