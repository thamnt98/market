<?php
/**
 * @category Magento
 * @package SM\Sales\Model
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author Hung Viet Nguyen <hungnv6@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\Sales\Model\ResourceModel\Order\Item;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use SM\Sales\Model\ParentOrderRepository;
use Zend_Db_Expr;

/**
 * Class Collection
 * @package SM\Sales\Model
 */
class Collection extends \Magento\Sales\Model\ResourceModel\Order\Item\Collection
{
    /**
     * @return $this
     */
    public function selectToSearch()
    {
        $this->addFieldToSelect(["item_id"]);
        $this->getSelect()->joinLeft(
            ["sub_order" => "sales_order"],
            "main_table.order_id = sub_order.entity_id",
            [
                "parent_order",
                "sub_order_status" => "status",
                "customer_id"
            ]
        );
        $this->getSelect()->joinLeft(
            ["parent_order" => "sales_order"],
            "sub_order.parent_order = parent_order.entity_id",
            [
                "parent_entity_id" => new Zend_Db_Expr("IFNULL(parent_order.entity_id ,sub_order.entity_id)"),
            ]
        );
        $this->addFieldToFilter("main_table.parent_item_id", ["null" => true]);
        $this->addFieldToFilter("sub_order.status", ["in" => [
            ParentOrderRepository::STATUS_PENDING_PAYMENT,
            ParentOrderRepository::STATUS_IN_PROCESS,
            ParentOrderRepository::STATUS_IN_DELIVERY,
            ParentOrderRepository::STATUS_DELIVERED,
            ParentOrderRepository::STATUS_COMPLETE,
            ParentOrderRepository::STATUS_ORDER_CANCELED,
            ParentOrderRepository::IN_PROCESS_WAITING_FOR_PICKUP,
            ParentOrderRepository::PICK_UP_BY_CUSTOMER
        ]]);


        return $this;
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return Collection
     */
    public function itemFilter($searchCriteria)
    {
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() == "name") {
                    $this->addFieldToFilter(
                        [
                            "main_table.name",
                            "main_table.product_options",
                            "sub_order.reference_order_id",
                            "sub_order.reference_number",
                            "parent_order.reference_order_id",
                            "parent_order.reference_number"
                        ],
                        [
                            ["like" => '%' . $filter->getValue() . '%'],
                            ["like" => '%' . $filter->getValue() . '%'],
                            ["like" => '%' . $filter->getValue() . '%'],
                            ["like" => '%' . $filter->getValue() . '%'],
                            ["like" => '%' . $filter->getValue() . '%'],
                            ["like" => '%' . $filter->getValue() . '%']
                        ]
                    );
                }
                if ($filter->getField() == ParentOrderRepository::LIST_TYPE) {
                    if ($filter->getValue() == ParentOrderRepository::IN_PROGRESS) {
                        $this->addFieldToFilter(
                            new Zend_Db_Expr("IFNULL(parent_order.status ,sub_order.status)"),
                            ["neq" => ParentOrderRepository::STATUS_ORDER_CANCELED]
                        );
                        $this->addFieldToFilter(
                            new Zend_Db_Expr("IFNULL(parent_order.status ,sub_order.status)"),
                            ["neq" => ParentOrderRepository::STATUS_COMPLETE]
                        );
                    } elseif ($filter->getValue() == ParentOrderRepository::COMPLETED) {
                        $this->addFieldToFilter(
                            new Zend_Db_Expr("IFNULL(parent_order.status ,sub_order.status)"),
                            ["in" => [
                                ParentOrderRepository::STATUS_ORDER_CANCELED,
                                ParentOrderRepository::STATUS_COMPLETE]
                            ]
                        );
                    }
                }

                if ($filter->getField() == "created_at") {
                    if ($filter->getConditionType() == "lteq") {
                        $value = date("Y-m-d", strtotime($filter->getValue() . "+1 days"));
                    } else {
                        $value = date("Y-m-d", strtotime($filter->getValue()));
                    }
                    $this->addFieldToFilter(
                        "sub_order.created_at",
                        [$filter->getConditionType() => $value]
                    );
                }
            }
        }
        return $this;
    }
}
