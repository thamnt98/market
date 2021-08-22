<?php
/**
 * Class PlaceOrderInterface
 * @package SM\Checkout\Api\Data\Checkout
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\Checkout\Api\Data\Checkout;


interface PlaceOrderInterface
{
    const BASKET_ID = 'basket_id';
    const BASKET_QTY = 'basket_qty';
    const BASKET_VALUE = 'basket_value';
    const TRANSACTION_ID = 'transaction_id';
    const TOTAL_PAYMENT = 'total_payment';

    const PAYMENT_METHOD = 'payment_method';
    const BANK_TYPE = 'bank_type';
    const SHIPPING_METHOD = 'shipping_method';

    const SHIPPING_DATE = 'shipping_date';
    const SHIPPING_TIME = 'shipping_time';

    /**
     * @return bool
     */
    public function getError();

    /**
     * @param $data
     * @return $this
     */
    public function setError($data);

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
    public function getOrderIds();

    /**
     * @param $data
     * @return $this
     */
    public function setOrderIds($data);

    /**
     * @return \SM\Checkout\Api\Data\Checkout\PlaceOrder\PaymentInterface
     */
    public function getPayment();

    /**
     * @param \SM\Checkout\Api\Data\Checkout\PlaceOrder\PaymentInterface $data
     * @return $this
     */
    public function setPayment($data);

    /**
     * @return \SM\MobileApi\Api\Data\GTM\GTMCheckoutInterface[]
     */
    public function getGtmData();

    /**
     * @param \SM\MobileApi\Api\Data\GTM\GTMCheckoutInterface[] $data
     * @return $this
     */
    public function setGtmData($data);

    /**
     * @return int
     */
    public function getBasketID();

    /**
     * @param int $value
     * @return $this
     */
    public function setBasketID($value);

    /**
     * @return int
     */
    public function getBasketQty();

    /**
     * @param int $value
     * @return $this
     */
    public function setBasketQty($value);

    /**
     * @return float
     */
    public function getBasketValue();

    /**
     * @param float $value
     * @return $this
     */
    public function setBasketValue($value);

    /**
     * @return string
     */
    public function getTransactionId();

    /**
     * @param string $data
     * @return $this
     */
    public function setTransactionId($data);

    /**
     * @return string
     */
    public function getTotalPayment();

    /**
     * @param string $data
     * @return $this
     */
    public function setTotalPayment($data);

    /**
     * @return string
     */
    public function getPaymentMethod();

    /**
     * @param string $data
     * @return $this
     */
    public function setPaymentMethod($data);

    /**
     * @return string
     */
    public function getBankType();

    /**
     * @param string $data
     * @return $this
     */
    public function setBankType($data);

    /**
     * @return string
     */
    public function getShippingDate();

    /**
     * @param string $data
     * @return $this
     */
    public function setShippingDate($data);

    /**
     * @return string
     */
    public function getShippingTime();

    /**
     * @param string $data
     * @return $this
     */
    public function setShippingTime($data);
}
