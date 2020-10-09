<?php

namespace SM\FileManagement\Api\Data;

/**
 * Interface FileSearchResultsInterface
 *
 * @package SM\FileManagement\Api\Data
 */
interface FileSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get File list.
     * @return \SM\FileManagement\Api\Data\FileInterface[]
     */
    public function getItems();

    /**
     * Set file_id list.
     * @param \SM\FileManagement\Api\Data\FileInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
