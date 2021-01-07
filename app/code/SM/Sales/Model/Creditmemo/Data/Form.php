<?php
/**
 * Class Form
 * @package SM\Sales\Model\Creditmemo\Data
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */


namespace SM\Sales\Model\Creditmemo\Data;

use Magento\Framework\Api\AbstractSimpleObject;
use SM\Sales\Api\Data\Creditmemo\FormInterface;
use SM\Sales\Model\Creditmemo\RequestFormData;

class Form extends AbstractSimpleObject implements FormInterface
{

    public function getBank()
    {
        return $this->_get(RequestFormData::BANK_KEY);
    }

    public function getAccountNo()
    {
        return $this->_get(RequestFormData::ACCOUNT_KEY);
    }

    public function getAccountName()
    {
        return $this->_get(RequestFormData::ACCOUNT_NAME_KEY);
    }

    public function getTotalRefund()
    {
        return $this->_get(RequestFormData::TOTAL_REFUND_KEY);
    }

    public function getReferenceNumber()
    {
        return $this->_get(RequestFormData::ORDER_REFERENCE_NUMBER_KEY);
    }

    public function getPaymentMethod()
    {
        return $this->_get(RequestFormData::PAYMENT_METHOD_KEY);
    }

    public function getCreditmemoId()
    {
        return $this->_get(RequestFormData::CREDITMEMO_ID_KEY);
    }

    public function getPaymentNumber()
    {
        return $this->_get(RequestFormData::PAYMENT_NUMBER_KEY);
    }
}
