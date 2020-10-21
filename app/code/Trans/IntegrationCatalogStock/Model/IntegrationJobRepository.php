<?php
/**
 * @category Trans
 * @package  Trans_CatalogStock
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   J.P <jaka.pondan@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationCatalogStock\Model;
 
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;

use \Trans\Integration\Api\Data\IntegrationChannelInterface;
use \Trans\Integration\Api\Data\IntegrationChannelInterfaceFactory;

use \Trans\IntegrationCatalogStock\Api\Data\IntegrationJobInterface;
use \Trans\IntegrationCatalogStock\Api\Data\IntegrationJobInterfaceFactory ;
use \Trans\IntegrationCatalogStock\Api\Data\IntegrationJobSearchResultInterface;
use \Trans\IntegrationCatalogStock\Api\Data\IntegrationJobSearchResultInterfaceFactory;
use \Trans\IntegrationCatalogStock\Api\IntegrationJobRepositoryInterface;

use \Trans\IntegrationCatalogStock\Model\ResourceModel\IntegrationJob\CollectionFactory;
use \Trans\IntegrationCatalogStock\Model\ResourceModel\IntegrationJob\Collection;
use \Trans\IntegrationCatalogStock\Model\ResourceModel\IntegrationJob as ResourceModel;

class IntegrationJobRepository implements IntegrationJobRepositoryInterface
{
    /**
     * @var array
     */
    protected $instances = [];

    /**
     * @var ResourceModel
     */
    protected $resource;
 
    /**
     * @var IntegrationChannelCollectionFactory
     */
    private $collectionFactory;
 
    /**
     * @var IntegrationJobSearchResultInterfaceFactory
     */
    private $searchResultFactory;

     /**
     * @var integrationJobInterface
     */
    protected $interface;
    
    /**
     * @param integrationJobInterfaceFactory $integrationJobInterface
     */
    public function __construct(
        ResourceModel $resource,
        CollectionFactory $collectionFactory,
        IntegrationJobSearchResultInterfaceFactory $searchResultFactory,
        IntegrationJobInterfaceFactory $interface
    ) {
        $this->resource = $resource;

        $this->collectionFactory = $collectionFactory;
        $this->searchResultFactory = $searchResultFactory;
        $this->interface = $interface;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($id)
    {
        if (!isset($this->instances[$id])) {
            /** @var integrationJobInterface|\Magento\Framework\Model\AbstractModel $data */
            $data = $this->interface->create();
            $this->resource->load($data, $id);
            if (!$data->getId()) {
                throw new NoSuchEntityException(__('Requested Data Reservation Response doesn\'t exist'));
            }
            $this->instances[$id] = $data;
        }
        return $this->instances[$id];
    }

    /**
     * {@inheritdoc}
     */
    public function save(IntegrationJobInterface $data)
    {
    	/** @var IntegrationJobInterfaceFactory|\Magento\Framework\Model\AbstractModel $data */
        try {
            $this->resource->save($data);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the data: %1',
                $exception->getMessage()
            ));
        }
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(IntegrationJobInterface $data)
    {
        /** @var IntegrationJobInterfaceFactory|\Magento\Framework\Model\AbstractModel $data */
        $id = $data->getId();
       
        try {
            unset($this->instances[$id]);
            $this->resource->delete($data);
        } catch (ValidatorException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new StateException(
                __('Unable to remove data %1', $id)
            );
        }
        unset($this->instances[$id]);
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getByMdIdWithStatus($mdId,$status=IntegrationJobInterface::STATUS_WAITING)
    {
        $data=NULL;
        if(empty($mdId)){
            throw new StateException(__(
                'Parameter MD are empty !'
            ));
        }
        $collection = $this->interface->create()->getCollection();
        $collection->addFieldToFilter(IntegrationJobInterface::METHOD_ID,$mdId);
        $collection->addFieldToFilter(IntegrationJobInterface::STATUS,$status);


        return $collection;
    }

    /**
     * {@inheritdoc}
     */
    public function getByMdIdMultiWithStatus($mdId=[],$status=IntegrationJobInterface::STATUS_WAITING)
    {
        $data=NULL;
        if(empty($mdId)){
            throw new StateException(__(
                'Parameter MD are empty !'
            ));
        }
        $collection = $this->interface->create()->getCollection();
        $collection->addFieldToFilter(IntegrationJobInterface::METHOD_ID,['in'=>$mdId]);
        $collection->addFieldToFilter(IntegrationJobInterface::STATUS,$status);


        return $collection;
    }

    /**
     * {@inheritdoc}
     */
    public function getByMdIdFirstItem($mdId,$status=IntegrationJobInterface::STATUS_WAITING)
    {
        $data=NULL;
        if(empty($mdId)){
            throw new StateException(__(
                'Parameter MD are empty !'
            ));
        }
        $collection = $this->interface->create()->getCollection();
        $collection->addFieldToFilter(IntegrationJobInterface::METHOD_ID,$mdId);
        $collection->addFieldToFilter(IntegrationJobInterface::STATUS,$status);

        if($collection->getSize()){
            $getLastCollection = $collection->getFirstItem();
            try {
                $data = $this->getById($getLastCollection->getId());
            } catch (NoSuchEntityException $ex) {
                throw new StateException(__(
                    $ex->getMessage()
                ));
            }
        }

        return $data;

    }

    /**
     * @param array $param
     * @return mixed|IntegrationJobInterface
     * @throws StateException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function saveJobs($param=[]){
        try {
            $this->validateParamArray($param);

        } catch (\Exception $ex) {
            throw new StateException(
                __($ex->getMessage())
            );
        }

        $data = $this->interface->create();
        try {
            if(isset($param[IntegrationJobInterface::METHOD_ID]) ){
                $data->setMdId($param[IntegrationJobInterface::METHOD_ID]);
            }
            if(isset($param[IntegrationJobInterface::BATCH_ID]) ){
                $data->setBatchId($param[IntegrationJobInterface::BATCH_ID]);
            }

            if(isset($param[IntegrationJobInterface::LAST_UPDATED]) ){
                $data->setLastUpdated($param[IntegrationJobInterface::LAST_UPDATED]);
            }
            if(isset($param[IntegrationJobInterface::TOTAL_DATA]) ){
                $data->setTotalData($param[IntegrationJobInterface::TOTAL_DATA]);
            }

            if(isset($param[IntegrationJobInterface::LIMIT]) ){
                $data->setLimits($param[IntegrationJobInterface::LIMIT]);
            }

            if(isset($param[IntegrationJobInterface::OFFSET]) ){
                $data->setOffset($param[IntegrationJobInterface::OFFSET]);
            }

            if(isset($param[IntegrationJobInterface::STATUS]) ){
                $data->setStatus($param[IntegrationJobInterface::STATUS]);
            }

            if(isset($param[IntegrationJobInterface::HIT]) ){
                $data->setHit($param[IntegrationJobInterface::HIT]);
            }

            if(isset($param[IntegrationJobInterface::LAST_JB_ID]) ){
                $data->setLastJbId($param[IntegrationJobInterface::LAST_JB_ID]);
            }

        } catch (\Exception $ex) {
            throw new StateException(
                __($ex->getMessage())
            );
        }
        $this->save($data);
        return $data;

    }

    protected function validateParamArray($param=[]){
        if(!is_array($param)){
            throw new StateException(__(
                'Parameter Jobs are empty !'
            ));
        }
        $check = array_filter($param);
        if(empty($check)){
            throw new StateException(__(
                'Parameter Jobs are empty !'
            ));
        }
    }


    /**
     * {@inheritdoc}
     */
    public function getByMdIdFirstItemInMdId($mdId=[],$status=IntegrationJobInterface::STATUS_WAITING)
    {
        $data=NULL;

        $check = array_filter($mdId);
        if(empty($check)){
            throw new StateException(__(
                'Parameter MD are empty !'
            ));
        }
        $collection = $this->interface->create()->getCollection();
        $collection->addFieldToFilter(IntegrationJobInterface::METHOD_ID,['in'=>$mdId]);
        $collection->addFieldToFilter(IntegrationJobInterface::STATUS,$status);

        if($collection->getSize()){

            $getLastCollection = $collection->getFirstItem();
            try {
                $data = $this->getById($getLastCollection->getId());
            } catch (NoSuchEntityException $ex) {
                throw new StateException(__(
                    $ex->getMessage()
                ));
            }
        }

        return $data;

    }

    /**
     * {@inheritdoc}
     */
    public function getJobByMultiStatus($mdId="",$status=[])
    {
        $data=NULL;
        if(empty($mdId)){
            throw new StateException(__(
                'Parameter MD are empty !'
            ));
        }
        $arrayStatus = array_filter($status); 
        if(empty($arrayStatus)){
            throw new StateException(__(
                'Parameter Status are empty !'
            ));
        }
        $collection = $this->interface->create()->getCollection();
        $collection->addFieldToFilter(IntegrationJobInterface::METHOD_ID,$mdId);
        $collection->addFieldToFilter(IntegrationJobInterface::STATUS,['in'=>$status]);


        return $collection;
    }

    /**
     * {@inheritdoc}
     */
    public function getByIdMdIdlastItem($id=null,$mdId,$status=IntegrationJobInterface::STATUS_COMPLETE)
    {
        $data=NULL;
        if(empty($mdId)){
            throw new StateException(__(
                'Parameter MD are empty !'
            ));
        }
        if(empty($id)){
            throw new StateException(__(
                'Parameter Id are empty !'
            ));
        }
        $collection = $this->interface->create()->getCollection();
        $collection->addFieldToFilter(IntegrationJobInterface::ID,$id);
        $collection->addFieldToFilter(IntegrationJobInterface::METHOD_ID,$mdId);
        $collection->addFieldToFilter(IntegrationJobInterface::STATUS,$status);

        if($collection->getSize()){
            $getLastCollection = $collection->getLastItem();
            try {
                $data = $this->getById($getLastCollection->getId());
            } catch (NoSuchEntityException $ex) {
                throw new StateException(__(
                    $ex->getMessage()
                ));
            }
        }

        return $data;

    }

}