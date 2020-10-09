<?php
/**
 * Class PlaceOrder
 * @package SM\Checkout\Model\Api\CheckoutData
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\Checkout\Model\Api\CheckoutData;

use SM\Checkout\Api\Data\Checkout\PlaceOrderInterface;

class PlaceOrder extends \Magento\Framework\Model\AbstractExtensibleModel implements PlaceOrderInterface
{

    public function getError()
    {
        return $this->getData('error');
    }

    public function setError($data)
    {
        return $this->setData('error', $data);
    }

    public function getMessage()
    {
        return $this->getData('message');
    }

    public function setMessage($data)
    {
        return $this->setData('message', $data);
    }

    public function getOrderIds()
    {
        return $this->getData('orderIds');
    }

    public function setOrderIds($data)
    {
        return $this->setData('orderIds', $data);
    }

    public function getPayment()
    {
        return $this->getData('payment');
    }

    public function setPayment($data)
    {
        return $this->setData('payment', $data);
    }

    public function getGtmData(){
        return $this->getData('gtm_data');
    }

    public function setGtmData($data){
        return $this->setData('gtm_data',$data);
    }

    /**
     * @inheritdoc
     */
    public function getBasketID()
    {
        return $this->getData(self::BASKET_ID);
    }

    /**
     * @inheritdoc
     */
    public function setBasketID($value)
    {
        return $this->setData(self::BASKET_ID, $value);
    }

    /**
     * @inheritdoc
     */
    public function getBasketQty()
    {
        return $this->getData(self::BASKET_QTY);
    }

    /**
     * @inheritdoc
     */
    public function setBasketQty($value)
    {
        return $this->setData(self::BASKET_QTY, $value);
    }

    /**
     * @inheritdoc
     */
    public function getBasketValue()
    {
        return $this->getData(self::BASKET_VALUE);
    }

    /**
     * @inheritdoc
     */
    public function setBasketValue($value)
    {
        return $this->setData(self::BASKET_VALUE, $value);
    }

    /**
     * @inheritdoc
     */
    public function getTransactionId(){
        return $this->getData(self::TRANSACTION_ID);
    }

    /**
     * @inheritdoc
     */
    public function setTransactionId($data){
        return $this->setData(self::TRANSACTION_ID,$data);
    }

    /**
     * @inheritdoc
     */
    public function getTotalPayment(){
        return $this->getData(self::TOTAL_PAYMENT);
    }

    /**
     * @inheritdoc
     */
    public function setTotalPayment($data){
        return $this->setData(self::TOTAL_PAYMENT,$data);
    }

    /**
     * @inheritdoc
     */
    public function getPaymentMethod(){
        return $this->getData(self::PAYMENT_METHOD);
    }

    /**
     * @inheritdoc
     */
    public function setPaymentMethod($data){
        return $this->setData(self::PAYMENT_METHOD,$data);
    }
    /**
     * @inheritdoc
     */
    public function getBankType(){
        return $this->getData(self::BANK_TYPE);
    }

    /**
     * @inheritdoc
     */
    public function setBankType($data){
        return $this->setData(self::BANK_TYPE,$data);
    }

    /**
     * @inheritdoc
     */
    public function getShippingDate(){
        return $this->getData(self::SHIPPING_DATE);
    }

    /**
     * @inheritdoc
     */
    public function setShippingDate($data){
        return $this->setData(self::SHIPPING_DATE,$data);
    }

    /**
     * @inheritdoc
     */
    public function getShippingTime(){
        return $this->getData(self::SHIPPING_TIME);
    }

    /**
     * @inheritdoc
     */
    public function setShippingTime($data){
        return $this->setData(self::SHIPPING_TIME,$data);
    }
}
