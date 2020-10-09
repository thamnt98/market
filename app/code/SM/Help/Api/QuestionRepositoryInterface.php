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

namespace SM\Help\Api;

interface QuestionRepositoryInterface
{
    /**
     * Save question.
     *
     * @param \SM\Help\Api\Data\QuestionInterface $question
     * @return \SM\Help\Api\Data\QuestionInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\SM\Help\Api\Data\QuestionInterface $question);

    /**
     * Retrieve question.
     *
     * @param int $questionId
     * @return \SM\Help\Api\Data\QuestionInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($questionId);

    /**
     * Retrieve blocks matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magento\Cms\Api\Data\BlockSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Delete question.
     *
     * @param \SM\Help\Api\Data\QuestionInterface $question
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(\SM\Help\Api\Data\QuestionInterface $question);

    /**
     * Delete question by ID.
     *
     * @param int $questionId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($questionId);

    /**
     * Get Top Questions On Main Help Page
     *
     * @return \SM\Help\Api\Data\QuestionInterface[]|null
     */
    public function getTopQuestion();
}
