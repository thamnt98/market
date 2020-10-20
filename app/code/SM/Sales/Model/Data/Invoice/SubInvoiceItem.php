<?php
/**
 * SM\Sales\Model\Data\Invoice
 *
 * @copyright Copyright © 2020 SmartOSC. All rights reserved.
 * @author    hungnv6@smartosc.com
 */

namespace SM\Sales\Model\Data\Invoice;

use Magento\Framework\DataObject;
use SM\Sales\Api\Data\Invoice\SubInvoiceItemInterface;

/**
 * Class SubInvoiceItem
 * @package SM\Sales\Model\Data\Invoice
 */
class SubInvoiceItem extends DataObject implements SubInvoiceItemInterface
{

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * @inheritDoc
     */
    public function getQty()
    {
        return $this->getData(self::QTY);
    }

    /**
     * @inheritDoc
     */
    public function getRowTotal()
    {
        return $this->getData(self::ROW_TOTAL);
    }

    /**
     * @inheritDoc
     */
    public function setName($value)
    {
        return $this->setData(self::NAME, $value);
    }

    /**
     * @inheritDoc
     */
    public function setQty($value)
    {
        return $this->setData(self::QTY, $value);
    }

    /**
     * @inheritDoc
     */
    public function setRowTotal($value)
    {
        return $this->setData(self::ROW_TOTAL, $value);
    }
}
