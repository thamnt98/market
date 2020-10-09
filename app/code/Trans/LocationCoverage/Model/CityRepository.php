<?php
/**
 * @category Trans
 * @package  Trans_LocationCoverage
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Randy <muhammad.randy@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\LocationCoverage\Model;

use Trans\LocationCoverage\Api\Data\CityInterface;
use Trans\LocationCoverage\Model\ResourceModel\City as CityResourceModel;
use Trans\LocationCoverage\Api\CityRepositoryInterface;
use Magento\Framework\Exception\LocalizedException as Exception;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Trans\LocationCoverage\Model\ResourceModel\Collection\City\Collection;
use Trans\LocationCoverage\Model\ResourceModel\Collection\City\CollectionFactory;
use Trans\LocationCoverage\Api\Data\CitySearchResultInterfaceFactory;

/**
 * Class CityRepository
 * @package Trans\LocationCoverage\Model
 */
class CityRepository implements CityRepositoryInterface
{
    /**
     * @var array
     */
    private $instances = [];

    /**
     * @var CityResourceModel
     */
    private $cityResourceModel;

    /**
     * @var CityInterface
     */
    private $cityInterface;

    /**
     * @var CityFactory
     */
    private $cityFactory;

    private $citySearchResultInterfaceFactory;

    private $collectionFactory;

    /**
     * @var ManagerInterface
     */
    private $messageManager;

    /**
     * CityRepository constructor.
     * @param CityResourceModel $cityResourceModel
     * @param CityInterface $cityInterface
     * @param CityFactory $cityFactory
     * @param CollectionFactory $collectionFactory
     * @param CitySearchResultInterfaceFactory $citySearchResultInterfaceFactory
     * @param ManagerInterface $messageManager
     */
    public function __construct(
        CityResourceModel $cityResourceModel,
        CityInterface $cityInterface,
        CityFactory $cityFactory,
        ManagerInterface $messageManager,
        CollectionFactory $collectionFactory,
        CitySearchResultInterfaceFactory $citySearchResultInterfaceFactory
    ) {
        $this->citySearchResultInterfaceFactory = $citySearchResultInterfaceFactory;
        $this->cityResourceModel = $cityResourceModel;
        $this->collectionFactory = $collectionFactory;
        $this->cityInterface = $cityInterface;
        $this->cityFactory = $cityFactory;
        $this->messageManager = $messageManager;
    }

    /**
     * @param CityInterface $cityInterface
     * @return CityInterface
     * @throws \Exception
     */
    public function save(CityInterface $cityInterface)
    {
        try {
            $this->cityResourceModel->save($cityInterface);
        } catch (Exception $e) {
            $this->messageManager
                ->addExceptionMessage(
                    $e,
                    'There was a error while saving the city ' . $e->getMessage()
                );
        }

        return $cityInterface;
    }

    /**
     * @param $cityId
     * @return array
     */
    public function getById($cityId)
    {
        if (!isset($this->instances[$cityId])) {
            $city = $this->cityFactory->create();
            $this->cityResourceModel->load($city, $cityId);
            $this->instances[$cityId] = $city;
        }
        return $this->instances[$cityId];
    }

    /**
     * @param CityInterface $cityInterface
     * @return bool
     * @throws \Exception
     */
    public function delete(CityInterface $cityInterface)
    {
        $id = $cityInterface->getId();
        try {
            unset($this->instances[$id]);
            $this->cityResourceModel->delete($cityInterface);
        } catch (Exception $e) {
            $this->messageManager
                ->addExceptionMessage($e, 'There was a error while deleting the city');
        }
        unset($this->instances[$id]);
        return true;
    }

    /**
     * @param $cityId
     * @return bool
     * @throws \Exception
     */
    public function deleteById($cityId)
    {
        $city = $this->getById($cityId);
        return $this->delete($city);
    }

    /**
     * @param CityInterface $cityInterface
     * @return bool
     * @throws \Exception
     */
    public function saveAndDelete(CityInterface $cityInterface)
    {
        $cityInterface->setData(Data::ACTION, Data::DELETE);
        $this->save($cityInterface);
        return true;
    }

    /**
     * @param $cityId
     * @return bool
     * @throws \Exception
     */
    public function saveAndDeleteById($cityId)
    {
        $city = $this->getById($cityId);
        return $this->saveAndDelete($city);
    }


    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $collection = $this->collectionFactory->create();
        $this->addFiltersToCollection($searchCriteria, $collection);
        $this->addSortOrdersToCollection($searchCriteria, $collection);
        $this->addPagingToCollection($searchCriteria, $collection);
        $collection->load();
        return $this->buildSearchResult($searchCriteria, $collection);
    }

    private function addFiltersToCollection(SearchCriteriaInterface $searchCriteria, Collection $collection)
    {
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            $fields = $conditions = [];
            foreach ($filterGroup->getFilters() as $filter) {
                $fields[] = $filter->getField();
                $conditions[] = [$filter->getConditionType() => $filter->getValue()];
            }
            $collection->addFieldToFilter($fields, $conditions);
        }
    }

    private function addSortOrdersToCollection(SearchCriteriaInterface $searchCriteria, Collection $collection)
    {
        foreach ((array)$searchCriteria->getSortOrders() as $sortOrder) {
            $direction = $sortOrder->getDirection() == SortOrder::SORT_ASC ? 'asc' : 'desc';
            $collection->addOrder($sortOrder->getField(), $direction);
        }
    }

    private function addPagingToCollection(SearchCriteriaInterface $searchCriteria, Collection $collection)
    {
        $collection->setPageSize($searchCriteria->getPageSize());
        $collection->setCurPage($searchCriteria->getCurrentPage());
    }

    private function buildSearchResult(SearchCriteriaInterface $searchCriteria, Collection $collection)
    {
        $searchResults = $this->citySearchResultInterfaceFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }
}