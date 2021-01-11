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

use Trans\Integration\Exception\WarningException;
use Trans\Integration\Exception\ErrorException;
use Trans\Integration\Exception\FatalException;


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
     * @var ResourceConnection
     */
    protected $dbConnection;
    
    /**
     * @param integrationJobInterfaceFactory $integrationJobInterface
     */
    public function __construct(
        ResourceModel $resource,
        CollectionFactory $collectionFactory,
        IntegrationJobSearchResultInterfaceFactory $searchResultFactory,
        IntegrationJobInterfaceFactory $interface,
        \Magento\Framework\App\ResourceConnection $resourceConnection
    ) {
        $this->resource = $resource;

        $this->collectionFactory = $collectionFactory;
        $this->searchResultFactory = $searchResultFactory;
        $this->interface = $interface;

        $this->dbConnection = $resourceConnection->getConnection();
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

    /**
     * @param $mdId int
     * @param $status array
     * @return mixed
     */
    public function getAnyByMdIdMultiStatusUsingRawQuery($mdId, $status)
    {

        try {

            if (empty($mdId)) {
                throw new ErrorException('parameter md-id in IntegrationJobRepository.getAnyByMdIdMultiStatusUsingRawQuery is empty !');
            }
    
            if (!is_array($status) || empty($status)) {
                throw new ErrorException('parameter status in IntegrationJobRepository.getAnyByMdIdMultiStatusUsingRawQuery is empty !');
            }


            $str = "select `id`, `md_id`, `batch_id`, `last_updated`, `total_data`, `limit`, `offset`, `start_job`, `end_job`, `message`, `hit`, `last_jb_id`, `status`, `created_at`, `updated_at`, `created_by`, `updated_by`
            from `integration_catalogstock_job` where `md_id` = %d";
    
            $sql = sprintf($str, $mdId);

            $strStatus = "";

            foreach ($status as $st) {
                $strStatus .= sprintf(",%d", $st);
            }

            if ($strStatus == "") {
                throw new ErrorException('parameter status in IntegrationJobRepository.getAnyByMdIdMultiStatusUsingRawQuery is empty !');
            }

            $sql .= " and `status` in (" . substr($strStatus, 1) . ") limit 1";    

            return $this->dbConnection->fetchRow($sql);

        } 
        catch (WarningException $ex) {
            throw $ex;
        }
        catch (ErrorException $ex) {
            throw $ex;
        }
        catch (FatalException $ex) {
            throw $ex;
        }        
        catch (\Exception $ex) {
            throw $ex;
        }
        
    }

    /**
     * @param int $mdId
     * @param int $status
     * @return mixed
     */
    public function getFirstByMdIdStatusUsingRawQuery($mdId, $status)
    {

        try  {

            if (empty($mdId)) {
                throw new ErrorException('parameter md-id in IntegrationJobRepository.getFirstByMdIdStatusUsingRawQuery is empty !');
            }

            if (empty($status)) {
                throw new ErrorException('parameter status in IntegrationJobRepository.getFirstByMdIdStatusUsingRawQuery is empty !');
            }
            
            $str = "select `id`, `md_id`, `batch_id`, `last_updated`, `total_data`, `limit`, `offset`, `start_job`, `end_job`, `message`, `hit`, `last_jb_id`, `status`, `created_at`, `updated_at`, `created_by`, `updated_by`
            from `integration_catalogstock_job` where `id` = (select min(`id`) from `integration_catalogstock_job` where `md_id` = %d and `status` = %d) limit 1";
    
            $sql = sprintf($str, $mdId, $status);
    
            return $this->dbConnection->fetchRow($sql);

        } 
        catch (WarningException $ex) {
            throw $ex;
        }
        catch (ErrorException $ex) {
            throw $ex;
        }
        catch (FatalException $ex) {
            throw $ex;
        }        
        catch (\Exception $ex) {
            throw $ex;
        }
        
    }    

    /**
     * @param int $mdId
     * @param int $status
     * @return mixed
     */
    public function getLastByMdIdStatusUsingRawQuery($mdId, $status)
    {
     
        try {            

            if (empty($mdId)) {
                throw new ErrorException('parameter md-id in IntegrationJobRepository.getLastByMdIdStatusUsingRawQuery is empty !');
            }

            if (empty($status)) {
                throw new ErrorException('parameter status in IntegrationJobRepository.getLastByMdIdStatusUsingRawQuery is empty !');
            }
            
            $str = "select `id`, `md_id`, `batch_id`, `last_updated`, `total_data`, `limit`, `offset`, `start_job`, `end_job`, `message`, `hit`, `last_jb_id`, `status`, `created_at`, `updated_at`, `created_by`, `updated_by`
            from `integration_catalogstock_job` where `id` = (select max(`id`) from `integration_catalogstock_job` where `md_id` = %d and `status` = %d) limit 1";
    
            $sql = sprintf($str, $mdId, $status);

            return $this->dbConnection->fetchRow($sql);

        } 
        catch (WarningException $ex) {
            throw $ex;
        }
        catch (ErrorException $ex) {
            throw $ex;
        }
        catch (FatalException $ex) {
            throw $ex;
        }        
        catch (\Exception $ex) {
            throw $ex;
        }

    }    

    /**
     * @param array $inserts
     * @return int
     */
    public function insertBulkUsingRawQuery($inserts) {

        try {

            if (!is_array($inserts) || empty($inserts)) {
                throw new ErrorException('parameter insert in IntegrationJobRepository.insertBulkUsingRawQuery is empty !');
            }

            $columns = array("id" => true, "md_id" => true, "batch_id" => true, "last_updated" => true, "total_data" => true, "limit" => true, "offset" => true, "start_job" => true, "end_job" => true, "message" => true, "hit" => true, "last_jb_id" => true, "status" => true, "created_at" => true, "updated_at" => true, "created_by" => true, "updated_by" => true);

            $bulk = array();

            foreach ($inserts as $insert) {

                $record = array();

                foreach($insert as $key => $value) {
                    if (isset($columns[$key])) {
                        $record[$key] = $value;                        
                    }
                }
    
                if (empty($record)) {
                    throw new ErrorException('parameter insert in IntegrationJobRepository.insertBulkUsingRawQuery is empty !');
                }

                $bulk[] = $record;
                                
            }
            
            if (empty($bulk)) {
                throw new ErrorException('parameter insert in IntegrationJobRepository.insertBulkUsingRawQuery is empty !');
            }
    
            return $this->dbConnection->insertMultiple("integration_catalogstock_job", $bulk);

        }
        catch (WarningException $ex) {
            throw $ex;
        }
        catch (ErrorException $ex) {
            throw $ex;
        }
        catch (FatalException $ex) {
            throw $ex;
        }        
        catch (\Exception $ex) {
            throw $ex;
        }

    }


    /**
     * @param int $id
     * @param int $mdId
     * @param int $status
     * @return mixed
     */
    public function getByIdMdIdStatusUsingRawQuery($id, $mdId, $status)
    {
     
        try {            

            if (empty($id)) {
                throw new ErrorException('parameter id in IntegrationJobRepository.getByIdMdIdStatusUsingRawQuery is empty !');
            }
    
            if (empty($mdId)) {
                throw new ErrorException('parameter md-id in IntegrationJobRepository.getByIdMdIdStatusUsingRawQuery is empty !');
            }

            if (empty($status)) {
                throw new ErrorException('parameter status in IntegrationJobRepository.getByIdMdIdStatusUsingRawQuery is empty !');
            }
            
            $str = "select `id`, `md_id`, `batch_id`, `last_updated`, `total_data`, `limit`, `offset`, `start_job`, `end_job`, `message`, `hit`, `last_jb_id`, `status`, `created_at`, `updated_at`, `created_by`, `updated_by`
            from `integration_catalogstock_job` where `id` = %d and `md_id` = %d and `status` = %d limit 1";
    
            $sql = sprintf($str, $id, $mdId, $status);
            
            return $this->dbConnection->fetchRow($sql);

        } 
        catch (WarningException $ex) {
            throw $ex;
        }
        catch (ErrorException $ex) {
            throw $ex;
        }
        catch (FatalException $ex) {
            throw $ex;
        }        
        catch (\Exception $ex) {
            throw $ex;
        }

    }

    /**
     * @param int $id
     * @param array $updates
     * @return int
     */
    public function updateUsingRawQuery($id, $updates) {

        try {

            if (empty($id)) {
                throw new ErrorException('parameter id in IntegrationJobRepository.updateUsingRawQuery is empty !');
            }

            if (!is_array($updates) || empty(($updates))) {
                throw new ErrorException('parameter update in IntegrationJobRepository.updateUsingRawQuery is empty !');
            }

            $columns = array("md_id" => true, "batch_id" => true, "last_updated" => true, "total_data" => true, "limit" => true, "offset" => true, "start_job" => true, "end_job" => true, "message" => true, "hit" => true, "last_jb_id" => true, "status" => true, "created_at" => true, "updated_at" => true, "created_by" => true, "updated_by" => true);

            $clause = "";
            
            foreach($updates as $key => $value) {

                if (isset($columns[$key])) {                    

                    if ($value === null) {
                        $clause .= ",`{$key}` = null";
                    }
                    else {
                        $clause .= ",`{$key}` = '{$value}'";
                    }

                }

            }

            if ($clause == "") {
                throw new ErrorException('parameter update in IntegrationJobRepository.updateUsingRawQuery is empty !');
            }
            
            $sql = sprintf("update `integration_catalogstock_job` set " . substr($clause, 1) . " where `id` = %d", $id);

            return $this->dbConnection->exec($sql);

        }
        catch (WarningException $ex) {
            throw $ex;
        }
        catch (ErrorException $ex) {
            throw $ex;
        }
        catch (FatalException $ex) {
            throw $ex;
        }        
        catch (\Exception $ex) {
            throw $ex;
        }

    }    

}