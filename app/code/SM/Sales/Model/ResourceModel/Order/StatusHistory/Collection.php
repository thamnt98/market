<?php

namespace SM\Sales\Model\ResourceModel\Order\StatusHistory;

use Zend_Db_Expr;

/**
 * Class Collection
 * @package SM\Sales\Model\ResourceModel\Order\StatusHistory
 */
class Collection extends \Magento\Sales\Model\ResourceModel\Order\Status\History\Collection
{
    const STATUSES = "pending,pending_payment,processing,in_process,in_delivery,delivered,complete,canceled,order_canceled";

    public function sortByStatus()
    {
        $this->getSelect()
            ->joinLeft(
                "sales_order_status",
                "sales_order_status.status = main_table.status"
            );
        $statuses = "'" . str_replace(
            ",",
            "','",
            self::STATUSES
        ) . "'";
        $this->getSelect()->order(new Zend_Db_Expr("FIELD(main_table.status,$statuses)"));
        return $this;
    }
}
