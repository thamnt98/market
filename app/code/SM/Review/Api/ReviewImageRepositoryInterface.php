<?php

namespace SM\Review\Api;

/**
 * Interface ReviewImageRepositoryInterface
 * @package SM\Review\Api
 */
interface ReviewImageRepositoryInterface
{
    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \SM\Review\Api\Data\ReviewImageSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * @param \Magento\Framework\Api\ImageContent $imageContent
     * @return bool
     */
    public function uploadImage(\Magento\Framework\Api\ImageContent $imageContent);
}
