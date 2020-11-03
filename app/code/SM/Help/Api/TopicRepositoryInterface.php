<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\Help\Api;

/**
 * Interface TopicRepositoryInterface
 * @package SM\Help\Api
 */
interface TopicRepositoryInterface
{
    /**
     * Save topic.
     *
     * @param \SM\Help\Api\Data\TopicInterface $topic
     * @return \SM\Help\Api\Data\TopicInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\SM\Help\Api\Data\TopicInterface $topic);

    /**
     * Retrieve topic.
     *
     * @param int $topicId
     * @return \SM\Help\Api\Data\TopicInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($topicId);

    /**
     * Retrieve Topics matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \SM\Help\Api\Data\TopicSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Delete topic.
     *
     * @param \SM\Help\Api\Data\TopicInterface $topic
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(\SM\Help\Api\Data\TopicInterface $topic);

    /**
     * Delete topic by ID.
     *
     * @param int $topicId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($topicId);

    /**
     * Get Topics of current Store view
     *
     * @return \SM\Help\Api\Data\TopicInterface[]
     */
    public function getParentTopics();

    /**
     * Get Childes of Parent Topic with Store view
     *
     * @param int $parentId
     * @return \SM\Help\Api\Data\TopicInterface[]
     */
    public function getChildTopics($parentId);

    /**
     * Get Child Questions of current Topic with Store view
     *
     * @param int $topicId
     * @return \SM\Help\Api\Data\QuestionInterface[]
     */
    public function getChildQuestions($topicId);

    /**
     * Get Topics Category Contact us
     *
     * @return \SM\Help\Api\Data\TopicInterface[]
     */
    public function getListCategory();
}
