<?php


namespace SM\Sales\Api\Data\Invoice;

/**
 * Interface InvoiceItemInterface
 * @package SM\Sales\Api\Data\Invoice
 */
interface SubInvoiceItemInterface
{
    const NAME = "name";
    const QTY = "qty";
    const ROW_TOTAL = "row_total";

    /**
     * @return string
     */
    public function getName();

    /**
     * @return int
     */
    public function getQty();

    /**
     * @return string
     */
    public function getRowTotal();

    /**
     * @param string $value
     * @return $this
     */
    public function setName($value);

    /**
     * @param int $value
     * @return $this
     */
    public function setQty($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setRowTotal($value);

}
