<?php
declare(strict_types=1);

namespace SM\DigitalProduct\Api\Data;

interface OperatorIconSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get operator_icon list.
     * @return \SM\DigitalProduct\Api\Data\OperatorIconInterface[]
     */
    public function getItems();

    /**
     * Set operator_service list.
     * @param \SM\DigitalProduct\Api\Data\OperatorIconInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

