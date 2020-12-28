<?php
/**
 * Class FormInformation
 * @package SM\Sales\Model\Creditmemo\Data
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\Sales\Model\Creditmemo\Data;

use Magento\Framework\Api\AbstractSimpleObject;
use SM\Sales\Api\Data\Creditmemo\FormInformationInterface;

class FormInformation extends AbstractSimpleObject implements FormInformationInterface
{
    /**
     * @inheridoc
     */
    public function getBanks()
    {
        return $this->_get(self::BANKS);
    }

    /**
     * @inheridoc
     */
    public function getIsSubmitted()
    {
        return $this->_get(self::IS_SUBMITTED);
    }

    /**
     * @inheridoc
     */
    public function getTotalRefund()
    {
        return $this->_get(self::TOTAL_REFUND);
    }

    /**
     * @inheridoc
     */
    public function getReferenceNumber()
    {
        return $this->_get(self::REFERENCE_NUMBER);
    }

    /**
     * @inheridoc
     */
    public function getOrderId()
    {
        return $this->_get(self::ORDER_ID);
    }

    /**
     * @inheridoc
     */
    public function getParentOrderId()
    {
        return $this->_get(self::PARENT_ORDER_ID);
    }
}
