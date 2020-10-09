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

use Trans\LocationCoverage\Api\Data\DistrictInterface;
use Trans\LocationCoverage\Model\ResourceModel\District as DistrictResourceModel;
use Trans\LocationCoverage\Api\DistrictRepositoryInterface;
use Magento\Framework\Exception\LocalizedException as Exception;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Trans\LocationCoverage\Model\ResourceModel\Collection\District\Collection;
use Trans\LocationCoverage\Model\ResourceModel\Collection\District\CollectionFactory;
use Trans\LocationCoverage\Api\Data\DistrictSearchResultInterfaceFactory;

/**
 * Class DistrictRepository
 * @package Trans\LocationCoverage\Model
 */
class DistrictRepository implements DistrictRepositoryInterface
{
    /**
     * @var array
     */
    private $instances = [];

    /**
     * @var DistrictResourceModel
     */
    private $districtResourceModel;

    /**
     * @var DistrictInterface
     */
    private $districtInterface;

    /**
     * @var DistrictFactory
     */
    private $districtFactory;

    private $districtSearchResultInterfaceFactory;

    private $collectionFactory;

    /**
     * @var ManagerInterface
     */
    private $messageManager;

    /**
     * DistrictRepository constructor.
     * @param DistrictResourceModel $districtResourceModel
     * @param DistrictInterface $districtInterface
     * @param DistrictFactory $districtFactory
     * @param Collectionactory $collectionFactory
     * @param DistrictSearchResultInterfaceFactory $districtSearchResultInterfaceFactory
     * @param ManagerInterface $messageManager
     */
    public function __construct(
        DistrictResourceModel $districtResourceModel,
        DistrictInterface $districtInterface,
        DistrictFactory $districtFactory,
        ManagerInterface $messageManager,
        CollectionFactory $collectionFactory,
        DistrictSearchResultInterfaceFactory $districtSearchResultInterfaceFactory
    ) {
        $this->districtSearchResultInterfaceFactory = $districtSearchResultInterfaceFactory;
        $this->districtResourceModel = $districtResourceModel;
        $this->collectionFactory = $collectionFactory;
        $this->districtInterface = $districtInterface;
        $this->districtFactory = $districtFactory;
        $this->messageManager = $messageManager;
    }

    /**
     * @param DistrictInterface $districtInterface
     * @return DistrictInterface
     * @throws \Exception
     */
    public function save(DistrictInterface $districtInterface)
    {
        try {
            $this->districtResourceModel->save($districtInterface);
        } catch (Exception $e) {
            $this->messageManager
                ->addExceptionMessage(
                    $e,
                    'There was a error while saving the district ' . $e->getMessage()
                );
        }

        return $districtInterface;
    }

    /**
     * @param $districtId
     * @return array
     */
    public function getById($districtId)
    {
        if (!isset($this->instances[$districtId])) {
            $district = $this->districtFactory->create();
            $this->districtResourceModel->load($district, $districtId);
            $this->instances[$districtId] = $district;
        }
        return $this->instances[$districtId];
    }

    /**
     * @param DistrictInterface $districtInterface
     * @return bool
     * @throws \Exception
     */
    public function delete(DistrictInterface $districtInterface)
    {
        $id = $districtInterface->getId();
        try {
            unset($this->instances[$id]);
            $this->districtResourceModel->delete($districtInterface);
        } catch (Exception $e) {
            $this->messageManager
                ->addExceptionMessage($e, 'There was a error while deleting the district');
        }
        unset($this->instances[$id]);
        return true;
    }

    /**
     * @param $districtId
     * @return bool
     * @throws \Exception
     */
    public function deleteById($districtId)
    {
        $district = $this->getById($districtId);
        return $this->delete($district);
    }

    /**
     * @param DistrictInterface $districtInterface
     * @return bool
     * @throws \Exception
     */
    public function saveAndDelete(DistrictInterface $districtInterface)
    {
        $districtInterface->setData(Data::ACTION, Data::DELETE);
        $this->save($districtInterface);
        return true;
    }

    /**
     * @param $districtId
     * @return bool
     * @throws \Exception
     */
    public function saveAndDeleteById($districtId)
    {
        $district = $this->getById($districtId);
        return $this->saveAndDelete($district);
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
        $searchResults = $this->districtSearchResultInterfaceFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }
}