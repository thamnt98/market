<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\InspireMe\Api;

use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Interface PostRepositoryInterface
 * @package SM\InspireMe\Api
 */
interface PostRepositoryInterface
{
    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \SM\InspireMe\Api\Data\ArticleFilterInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * @return \SM\InspireMe\Api\Data\PostListingInterface[]
     */
    public function getPost();

    /**
     * Get 3 Most Popular Posts (Admin can preset)
     *
     * @return \SM\InspireMe\Api\Data\PostListingInterface[]
     */
    public function getMostPopular();

    /**
     * Get 5 Articles on Homepage (Admin can preset)
     *
     * @return \SM\InspireMe\Api\Data\PostListingInterface[]
     */
    public function getHomeArticles();

    /**
     * Get Articles by ID
     *
     * @param int $postId
     * @return \SM\InspireMe\Api\Data\PostDetailInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($postId);

    /**
     * @param int $postId
     * @return boolean
     */
    public function updateViewsCount($postId);

    /**
     * @param int $postId
     * @return boolean
     */
    public function isShopIngredient($postId);

    /**
     * @return int
     */
    public function getPagingConfig();

    /**
     * @param int $postId
     * @return \SM\InspireMe\Api\Data\RelatedProductResultInterface
     */
    public function getProducts($postId);
}
