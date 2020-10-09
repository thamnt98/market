<?php

namespace SM\ShoppingList\Model\ResourceModel\Item;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Wishlist\Model\ResourceModel\Item\Collection as ItemCollection;

/**
 * Class Collection
 * @package SM\ShoppingList\Model\ResourceModel\Item
 */
class Collection extends ItemCollection
{
    /**
     * @return $this
     */
    public function getSelectProductName()
    {
        $this->getSelect()
            ->joinLeft(
                "catalog_product_entity",
                'main_table.product_id = catalog_product_entity.entity_id',
                ["sku"]
            )
            ->joinLeft(
                "catalog_product_entity_varchar",
                'catalog_product_entity.row_id = catalog_product_entity_varchar.row_id',
                [
                    "value"
                ]
            )
            ->joinLeft(
                "eav_attribute",
                'catalog_product_entity_varchar.attribute_id = eav_attribute.attribute_id',
                [
                    "attribute_code",
                    "entity_type_id"
                ]
            );
        $this->addFieldToFilter("eav_attribute.attribute_code", ["eq" => "name"]);
        $this->addFieldToFilter("eav_attribute.entity_type_id", ["eq" => 4]);
        return $this;
    }


    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return ItemCollection
     */
    public function itemFilter($searchCriteria)
    {
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            $fields = [];
            $conditions = [];
            foreach ($filterGroup->getFilters() as $filter) {
                $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
                if ($filter->getField() == "customer_id" || $filter->getField() == "name") {
                    $fields[] = 'wishlist.' . $filter->getField();
                } elseif ($filter->getField() == 'product_name') {
                    $fields[] = 'catalog_product_entity_varchar.value';
                } elseif ($filter->getField() == 'sku') {
                    $fields[] = 'catalog_product_entity.' . $filter->getField();
                } else {
                    $fields[] = 'main_table.' . $filter->getField();
                }
                if ($condition == 'like') {
                    $conditions[] = [$condition => '%' . $filter->getValue() . '%'];
                } else {
                    $conditions[] = [$condition => $filter->getValue()];
                }
            }
            if ($fields) {
                $this->addFieldToFilter($fields, $conditions);
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
            foreach ($sortOrders as $sortOrder) {
                $this->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        }
        return $this;
    }
}
