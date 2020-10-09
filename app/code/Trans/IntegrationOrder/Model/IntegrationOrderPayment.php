<?php
/**
 * @category Trans
 * @package  Trans_IntegrationOrder
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Randy <muhammad.randy@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationOrder\Model;

use \Trans\IntegrationOrder\Api\Data\IntegrationOrderPaymentInterface;
use \Trans\IntegrationOrder\Model\ResourceModel\IntegrationOrderPayment as ResourceModel;

class IntegrationOrderPayment extends \Magento\Framework\Model\AbstractModel implements
    IntegrationOrderPaymentInterface
{
    protected function _construct()
    {
        $this->_init(ResourceModel::class);
    }

    /**
     * @inheritdoc
     */
    public function getOmsIdOrderPayment()
    {
        return $this->getData(IntegrationOrderPaymentInterface::OMS_ID_ORDER_PAYMENT);
    }

    /**
     * @inheritdoc
     */
    public function setOmsIdOrderPayment($omsIdOrderPayment)
    {
        return $this->setData(IntegrationOrderPaymentInterface::OMS_ID_ORDER_PAYMENT, $omsIdOrderPayment);
    }

    /**
     * @inheritdoc
     */
    public function getReferenceNumber()
    {
        return $this->getData(IntegrationOrderPaymentInterface::REFERENCE_NUMBER);
    }

    /**
     * @inheritdoc
     */
    public function setReferenceNumber($referenceNumber)
    {

        return $this->setData(IntegrationOrderPaymentInterface::REFERENCE_NUMBER, $referenceNumber);
    }

    /**
     * @inheritdoc
     */
    public function getOrderId()
    {
        return $this->getData(IntegrationOrderPaymentInterface::ORDER_ID);
    }

    /**
     * @inheritdoc
     */
    public function setOrderId($orderId)
    {

        return $this->setData(IntegrationOrderPaymentInterface::ORDER_ID, $orderId);
    }

    /**
     * @inheritdoc
     */
    public function getPaymentRefNumber1()
    {
        return $this->getData(IntegrationOrderPaymentInterface::PAYMENT_REF_NUMBER_1);
    }

    /**
     * @inheritdoc
     */
    public function setPaymentRefNumber1($paymentRefNumber1)
    {
        return $this->setData(IntegrationOrderPaymentInterface::PAYMENT_REF_NUMBER_1, $paymentRefNumber1);
    }

    /**
     * @inheritdoc
     */
    public function getPaymentRefNumber2()
    {
        return $this->getData(IntegrationOrderPaymentInterface::PAYMENT_REF_NUMBER_2);
    }

    /**
     * @inheritdoc
     */
    public function setPaymentRefNumber2($paymentRefNumber2)
    {
        return $this->setData(IntegrationOrderPaymentInterface::PAYMENT_REF_NUMBER_2, $paymentRefNumber2);
    }

    /**
     * @inheritdoc
     */
    public function getOrderPaidDateTime()
    {
        return $this->getData(IntegrationOrderPaymentInterface::ORDER_PAID_DATE_TIME);
    }

    /**
     * @inheritdoc
     */
    public function setOrderPaidDateTime($orderPaidDateTime)
    {
        return $this->setData(IntegrationOrderPaymentInterface::ORDER_PAID_DATE_TIME, $orderPaidDateTime);
    }

    /**
     * @inheritdoc
     */
    public function getCreateOrderDateTime()
    {
        return $this->getData(IntegrationOrderPaymentInterface::CREATE_ORDER_DATE_TIME);
    }

    /**
     * @inheritdoc
     */
    public function setCreateOrderDateTime($createOrderDateTime)
    {
        return $this->setData(IntegrationOrderPaymentInterface::CREATE_ORDER_DATE_TIME, $createOrderDateTime);
    }

    /**
     * @inheritdoc
     */
    public function getSplitPayment()
    {
        return $this->getData(IntegrationOrderPaymentInterface::SPLIT_PAYMENT);
    }

    /**
     * @inheritdoc
     */
    public function setSplitPayment($splitPayment)
    {
        return $this->setData(IntegrationOrderPaymentInterface::SPLIT_PAYMENT, $splitPayment);
    }

    /**
     * @inheritdoc
     */
    public function getPaymentType1()
    {
        return $this->getData(IntegrationOrderPaymentInterface::PAYMENT_TYPE_1);
    }

    /**
     * @inheritdoc
     */
    public function setPaymentType1($paymentType1)
    {
        return $this->setData(IntegrationOrderPaymentInterface::PAYMENT_TYPE_1, $paymentType1);
    }

    /**
     * @inheritdoc
     */
    public function getPaymentType2()
    {
        return $this->getData(IntegrationOrderPaymentInterface::AMOUNT_OF_PAYMENT_2);
    }

    /**
     * @inheritdoc
     */
    public function setPaymentType2($paymentType2)
    {
        return $this->setData(IntegrationOrderPaymentInterface::AMOUNT_OF_PAYMENT_2, $paymentType2);
    }

    /**
     * @inheritdoc
     */
    public function getAmountOfPayment1()
    {
        return $this->getData(IntegrationOrderPaymentInterface::AMOUNT_OF_PAYMENT_1);
    }

    /**
     * @inheritdoc
     */
    public function setAmountOfPayment1($amountOfPayment1)
    {
        return $this->setData(IntegrationOrderPaymentInterface::AMOUNT_OF_PAYMENT_1, $amountOfPayment1);
    }

    /**
     * @inheritdoc
     */
    public function getAmountOfPayment2()
    {
        return $this->getData(IntegrationOrderPaymentInterface::AMOUNT_OF_PAYMENT_2);
    }

    /**
     * @inheritdoc
     */
    public function setAmountOfPayment2($amountOfPayment2)
    {
        return $this->setData(IntegrationOrderPaymentInterface::AMOUNT_OF_PAYMENT_2, $amountOfPayment2);
    }

    /**
     * @inheritdoc
     */
    public function getTotalAmountPaid()
    {
        return $this->getData(IntegrationOrderPaymentInterface::TOTAL_AMOUNT_PAID);
    }

    /**
     * @inheritdoc
     */
    public function setTotalAmountPaid($totalAmountPaid)
    {
        return $this->setData(IntegrationOrderPaymentInterface::TOTAL_AMOUNT_PAID, $totalAmountPaid);
    }

    /**
     * @inheritdoc
     */
    public function getPaymentStatus()
    {
        return $this->getData(IntegrationOrderPaymentInterface::PAYMENT_STATUS);
    }

    /**
     * @inheritdoc
     */
    public function setPaymentStatus($paymentStatus)
    {
        return $this->setData(IntegrationOrderPaymentInterface::PAYMENT_STATUS, $paymentStatus);
    }
}
