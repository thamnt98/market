<?php
/**
 * @category Magento
 * @package SM\Review\Api
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author Hung Viet Nguyen <hungnv6@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\Review\Api;

use Magento\Framework\Api\SearchCriteria;

/**
 * Interface ReviewRepositoryInterface
 * @package SM\Review\Api
 */
interface ReviewRepositoryInterface
{
    /**
     * @param \SM\Review\Api\Data\ReviewDataInterface $review
     * @param string[] $images
     * @param int $customerId
     * @return \SM\Review\Api\Data\ReviewDataInterface
     */
    public function create(\SM\Review\Api\Data\ReviewDataInterface $review, $images, $customerId);

    /**
     * @param \Magento\Framework\Api\SearchCriteria $searchCriteria
     * @return \SM\Review\Api\Data\ReviewSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteria $searchCriteria);

    /**
     * @param int $productId
     * @param int $storeId
     * @return float
     */
    public function getRatingSummary($productId, $storeId);
}
