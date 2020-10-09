<?php


namespace SM\FileManagement\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Interface FileRepositoryInterface
 *
 * @package SM\FileManagement\Api
 */
interface FileRepositoryInterface
{

    /**
     * Save File
     * @param \SM\FileManagement\Api\Data\FileInterface $file
     * @return \SM\FileManagement\Api\Data\FileInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \SM\FileManagement\Api\Data\FileInterface $file
    );

    /**
     * Retrieve File
     * @param string $fileId
     * @return \SM\FileManagement\Api\Data\FileInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($fileId);

    /**
     * Retrieve File matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \SM\FileManagement\Api\Data\FileSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete File
     * @param \SM\FileManagement\Api\Data\FileInterface $file
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \SM\FileManagement\Api\Data\FileInterface $file
    );

    /**
     * Delete File by ID
     * @param string $fileId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($fileId);
}

