<?php


namespace SM\Review\Api;

/**
 * Interface ReviewEditRepositoryInterface
 * @package SM\Review\Api
 */
interface ReviewEditRepositoryInterface
{
    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \SM\Review\Api\Data\ReviewEditSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * @param \SM\Review\Api\Data\ReviewEditInterface $reviewEdit
     * @param string[] $images
     * @return \SM\Review\Api\Data\ReviewEditInterface
     */
    public function create(\SM\Review\Api\Data\ReviewEditInterface $reviewEdit, $images);

    /**
     * @param int $entityId
     * @return \SM\Review\Api\Data\ReviewEditInterface
     */
    public function getById($entityId);

    /**
     * @param \SM\Review\Api\Data\ReviewEditInterface $reviewEdit
     * @return bool
     */
    public function apply(\SM\Review\Api\Data\ReviewEditInterface $reviewEdit);

    /**
     * @param \SM\Review\Api\Data\ReviewEditInterface $reviewEdit
     * @return bool
     */
    public function reject(\SM\Review\Api\Data\ReviewEditInterface $reviewEdit);
    /**
     * @param \SM\Review\Api\Data\ReviewEditInterface $reviewEdit
     * @return bool
     */
    public function delete(\SM\Review\Api\Data\ReviewEditInterface $reviewEdit);

    /**
     * @param int $entityId
     * @return bool
     */
    public function deleteById($entityId);

    /**
     * @param int $reviewId
     * @return \SM\Review\Api\Data\ReviewEditInterface
     */
    public function getByReviewId($reviewId);
}
