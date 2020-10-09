<?php
/**
 * Class SearchResults
 * @package SM\Help\Model
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\Help\Api;

interface SearchRepositoryInterface
{
    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \SM\Help\Api\Data\SearchResultsInterface
     */
    public function searchQuestions(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \SM\Help\Api\Data\SearchResultsFullDataInterface
     */
    public function searchQuestionsFullData(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}
