<?php
/**
 * @category Magento
 * @package SM\Sales\Model\ResourceModel\Order
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author Hung Viet Nguyen <hungnv6@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\Sales\Model\ResourceModel\Order;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use SM\Sales\Model\ParentOrderRepository;
use Zend_Db_Expr;

/**
 * Class Collection
 * @package SM\Sales\Model\ResourceModel\Order
 */
class Collection extends \Magento\Sales\Model\ResourceModel\Order\Collection
{
    const STATUSES = "pending,pending_payment,processing,in_process,in_delivery,delivered,complete,canceled,order_canceled";

    /**
     * @return Collection
     */
    public function sortByStatus()
    {
        $statuses = "'" . str_replace(
            ",",
            "','",
            self::STATUSES
        ) . "'";
        $this->getSelect()->order(new Zend_Db_Expr("FIELD(main_table.status, $statuses)"));
        return $this;
    }

    /**
     * @return $this
     */
    public function selectParent()
    {
        $this->addFieldToSelect([
            "entity_id",
            "status",
            "grand_total",
            "subtotal",
            "created_at",
            "shipping_amount",
            "reference_number",
            "reference_order_id",
            "reference_invoice_number",
            "is_virtual",
            "voucher_detail",
            "discount_amount"
        ]);

        $this->addFieldToFilter("is_parent", ["eq" => 1]);
        return $this;
    }

    /**
     * @return $this
     */
    public function selectSub()
    {
        $this->addFieldToSelect([
            "entity_id",
            "status",
            "increment_id",
            "grand_total",
            "subtotal",
            "shipping_amount",
            "shipping_description",
            "shipping_method",
            "created_at",
            "reference_number",
            "reference_order_id",
            "parent_order",
            "customer_id",
            "is_virtual",
            "is_parent",
            "store_pick_up",
            "store_pick_up_delivery",
            "store_pick_up_time",
            "date",
            "time",
            "reference_invoice_number"
        ]);

        $this->getSelect()->joinLeft(
            "sales_order_address",
            "sales_order_address.parent_id = main_table.entity_id AND sales_order_address.address_type = 'shipping'",
            [
                "street",
                "address_tag",
                "lastname",
                "firstname",
                "postcode",
                "telephone",
                "region"
            ]
        );

        $this->getSelect()->joinLeft(
            "districts",
            "districts.district_id = sales_order_address.district",
            [
                "district"
            ]
        );

        $this->getSelect()->joinLeft(
            "regency",
            "regency.entity_id = districts.entity_id",
            [
                "region_id" => "entity_id",
                "city"
            ]
        );
        $this->getSelect()->group("main_table.entity_id");

        $this->addFieldToFilter("is_parent", ["eq" => 0]);
        return $this;
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return $this
     */
    public function itemFilter($searchCriteria)
    {
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() == "created_at") {
                    if ($filter->getConditionType() == "lteq") {
                        $value = date("Y-m-d", strtotime($filter->getValue() . "+1 days"));
                    } else {
                        $value = date("Y-m-d", strtotime($filter->getValue()));
                    }
                    $this->addFieldToFilter(
                        "main_table.created_at",
                        [$filter->getConditionType() => $value]
                    );
                }
            }
        }
        return $this;
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return Collection
     */
    public function itemSort($searchCriteria)
    {
        $sortOrders = $searchCriteria->getSortOrders();
        if ($sortOrders) {
            /** @var SortOrder $sortOrder */
            foreach ($sortOrders as $sortOrder) {
                if ($sortOrder->getField() == ParentOrderRepository::SORT_STATUS) {
                    $statuses = "'" . str_replace(
                        ",",
                        "','",
                        self::STATUSES
                    ) . "'";
                    $this->getSelect()->order(new Zend_Db_Expr("FIELD(main_table.status, $statuses)"));
                    $this->getSelect()->order("main_table.created_at " . SortOrder::SORT_DESC);
                }

                if ($sortOrder->getField() == ParentOrderRepository::SORT_LATEST) {
                    $this->getSelect()->order("main_table.created_at " . SortOrder::SORT_DESC);
                }
            }
        }
        return $this;
    }

    public function selectReorderQuickly()
    {
        $this->addFieldToSelect([
            "entity_id",
            "status",
            "grand_total",
            "created_at",
            "is_virtual",
            "customer_id"
        ]);
        $this->addFieldToFilter("is_parent", ["eq" => 1]);
        return $this;
    }
}
