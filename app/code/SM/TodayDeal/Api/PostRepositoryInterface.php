<?php

/**
 * @category SM
 * @package SM_TodayDeal
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 * @author      Chinhvd <chinhvd@smartosc.com>
 *
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\TodayDeal\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface PostRepositoryInterface
{
    /**
     * Save post.
     *
     * @param \SM\TodayDeal\Api\Data\PostInterface $post
     * @return \SM\TodayDeal\Api\Data\PostInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\SM\TodayDeal\Api\Data\PostInterface $post);

    /**
     * Retrieve post.
     *
     * @param int $postId
     * @return \SM\TodayDeal\Api\Data\CampaignDetailsMobileInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($postId);

    /**
     * Retrieve post.
     *
     * @param int $postId
     * @return \SM\TodayDeal\Api\Data\CampaignDetailsMobileInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByIdMobile($postId);

    /**
     * Retrieve pages matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \SM\TodayDeal\Api\Data\PostSearchResultInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Delete post.
     *
     * @param \SM\TodayDeal\Api\Data\PostInterface $post
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(\SM\TodayDeal\Api\Data\PostInterface $post);

    /**
     * Delete post by ID.
     *
     * @param int $postId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($postId);

    /**
     * Retrieve pages matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \SM\TodayDeal\Api\Data\ProductsSearchResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getProducts($searchCriteria);

    /**
     * @return \SM\TodayDeal\Api\Data\MenuDealListingMobileInterface[]
     */
    public function getListDeals();

    /**
     * Retrieve FlashSale.
     *
     * @param int $limit
     * @param int $p
     * @return \SM\TodayDeal\Api\Data\FlashSaleDetailsMobileInterface
     */
    public function getFlashSaleDetail($limit = 12, $p = 1);
}
