<?php


namespace SM\Review\Model\ResourceModel\Vote;


class Collection extends \Magento\Review\Model\ResourceModel\Rating\Option\Vote\Collection
{

    public function getSelectQuality()
    {
        $this->getSelect()->joinLeft("rating", "main_table.rating_id = rating.rating_id AND rating.rating_code='Quality'");
        return $this;
    }
}
