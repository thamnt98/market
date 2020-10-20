<?php

namespace SM\Review\Model\ResourceModel\Order;

/**
 * Class Collection
 * @package SM\Review\Model\ResourceModel\Order
 */
class Collection extends \Magento\Sales\Model\ResourceModel\Order\Collection
{
    /**
     * @return $this
     */
    public function getSelectWithItem()
    {
        $this->getSelect()->joinLeft(["second" => "sales_order_item"], "main_table.entity_id = second.order_id");
        return $this;
    }
}
