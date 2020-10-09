<?php

namespace SM\StoreLocator\Api;

use SM\StoreLocator\Api\Data\Request\StoreSearchCriteriaInterface;

/**
 * @api
 */
interface StoreLocationRepositoryInterface
{
    /**
     * @param int $id
     * @return \SM\StoreLocator\Api\Data\StoreInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($id);

    /**
     * @param \SM\StoreLocator\Api\Data\Request\StoreSearchCriteriaInterface $searchCriteria
     * @return \SM\StoreLocator\Api\Data\Response\StoreSearchResultsInterface
     */
    public function getList(StoreSearchCriteriaInterface $searchCriteria);

    /**
     * @param \SM\StoreLocator\Api\Data\Request\StoreSearchCriteriaInterface $searchCriteria
     * @return \SM\StoreLocator\Api\Data\Response\StoreSearchLittleInfoResultsInterface
     */
    public function getListStores(StoreSearchCriteriaInterface $searchCriteria);
}
