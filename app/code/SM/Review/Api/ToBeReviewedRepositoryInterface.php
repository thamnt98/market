<?php
/**
 * @category SM
 * @package SM_Review
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 * @author      dungnm<dungnm@smartosc.com>
 *
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\Review\Api;

/**
 * Interface ToBeReviewedRepositoryInterface
 * @package SM\Review\Api
 */
interface ToBeReviewedRepositoryInterface
{

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @param int $customerId
     * @return \SM\Review\Api\Data\ToBeReviewedSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria, $customerId);

    /**
     * @param int $customerId
     * @param int $productId
     * @return \SM\Review\Api\Data\CheckResultDataInterface
     */
    public function isReviewAllowed($customerId, $productId);
}
