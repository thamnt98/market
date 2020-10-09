<?php
declare(strict_types=1);

namespace SM\DigitalProduct\Api;

/**
 * Interface OperatorIconRepositoryInterface
 * @package SM\DigitalProduct\Api
 */
interface OperatorIconRepositoryInterface
{
    /**
     * Save operator_icon
     * @param \SM\DigitalProduct\Api\Data\OperatorIconInterface $operatorIcon
     * @return \SM\DigitalProduct\Api\Data\OperatorIconInterface
     */
    public function save(
        \SM\DigitalProduct\Api\Data\OperatorIconInterface $operatorIcon
    );

    /**
     * Retrieve operator_icon
     * @param int $operatorIconId
     * @return \SM\DigitalProduct\Api\Data\OperatorIconInterface
     */
    public function get($operatorIconId);
    /**
     * Retrieve operator_icon matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \SM\DigitalProduct\Api\Data\OperatorIconSearchResultsInterface
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete operator_icon
     * @param \SM\DigitalProduct\Api\Data\OperatorIconInterface $operatorIcon
     * @return bool true on success
     */
    public function delete(
        \SM\DigitalProduct\Api\Data\OperatorIconInterface $operatorIcon
    );

    /**
     * Delete operator_icon by ID
     * @param string $operatorIconId
     * @return bool true on success
     */
    public function deleteById($operatorIconId);
}

