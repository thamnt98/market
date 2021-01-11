<?php
/**
 * @category Trans
 * @package  Trans_CatalogStock
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   J.P <jaka.pondan@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationCatalogStock\Model;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Exception\CouldNotSaveException;
use \Trans\IntegrationCatalogStock\Api\Data\IntegrationDataValueInterface;
use \Trans\IntegrationCatalogStock\Api\Data\IntegrationDataValueInterfaceFactory;
use \Trans\IntegrationCatalogStock\Api\Data\IntegrationDataValueSearchResultInterfaceFactory;
use \Trans\IntegrationCatalogStock\Api\Data\IntegrationJobInterface;
use \Trans\IntegrationCatalogStock\Api\IntegrationDataValueRepositoryInterface;
use \Trans\IntegrationCatalogStock\Model\ResourceModel\IntegrationDataValue as ResourceModel;
use \Trans\IntegrationCatalogStock\Model\ResourceModel\IntegrationDataValue\Collection;
use \Trans\IntegrationCatalogStock\Model\ResourceModel\IntegrationDataValue\CollectionFactory;

use Trans\Integration\Exception\WarningException;
use Trans\Integration\Exception\ErrorException;
use Trans\Integration\Exception\FatalException;


class IntegrationDataValueRepository implements IntegrationDataValueRepositoryInterface {
	/**
	 * @var array
	 */
	protected $instances = [];

	/**
	 * @var ResourceModel
	 */
	protected $resource;

	/**
	 * @var IntegrationDataValueCollectionFactory
	 */
	private $collectionFactory;

	/**
	 * @var IntegrationJobSearchResultInterfaceFactory
	 */
	private $searchResultFactory;

	/**
	 * @var IntegrationDataValueInterface
	 */
	protected $interface;

     /**
     * @var TimezoneInterface
     */
    protected $timezone;

    /**
     * @var ResourceConnection
     */
	protected $dbConnection;

	/**
	 * @var \Trans\IntegrationCatalogStock\Logger\Logger
	 */
	protected $loggerfile;
	

	/**
	 * IntegrationDataValueRepository constructor.
	 * @param ResourceModel $resource
	 * @param CollectionFactory $collectionFactory
	 * @param IntegrationDataValueSearchResultInterfaceFactory $searchResultFactory
	 * @param IntegrationDataValueInterfaceFactory $interface
	 */
	public function __construct(
		ResourceModel $resource,
		CollectionFactory $collectionFactory,
		IntegrationDataValueSearchResultInterfaceFactory $searchResultFactory,
		IntegrationDataValueInterfaceFactory $interface,		
		\Magento\Framework\App\ResourceConnection $resourceConnection,		
		\Trans\IntegrationCatalogStock\Logger\Logger $loggerfile,
		\Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
	) {
		$this->resource = $resource;
		$this->collectionFactory = $collectionFactory;
		$this->searchResultFactory = $searchResultFactory;
		$this->interface = $interface;
		$this->timezone = $timezone;
		$this->dbConnection = $resourceConnection->getConnection();
		$this->loggerfile = $loggerfile;		
	}

	/**
	 * {@inheritdoc}
	 */
	public function getById($id) {

		if (!isset($this->instances[$id])) {
			/** @var IntegrationDataValueInterface|\Magento\Framework\Model\AbstractModel $data */
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
	public function save(IntegrationDataValueInterface $data) {
		/** @var IntegrationDataValueInterface|\Magento\Framework\Model\AbstractModel $data */
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
	public function delete(IntegrationDataValueInterface $data) {
		/** @var IntegrationDataValueInterface|\Magento\Framework\Model\AbstractModel $data */
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

	public function saveDataValue($param = []) {
		$data = $this->interface->create();
		try {

			$data->setJbId($param[IntegrationDataValueInterface::JOB_ID]);
			$data->setDataValue($param[IntegrationDataValueInterface::DATA_VALUE]);
			$this->save($data);
		} catch (\Exception $ex) {
			throw new StateException(
				__($ex->getMessage())
			);
		}
		return $data;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getByJobIdWithStatus($jobId = 0, $status = 1) {
		$data = NULL;
		if (empty($jobId)) {
			throw new StateException(__(
				'Parameter MD are empty !'
			));
		}
		$collection = $this->interface->create()->getCollection();
		$collection->addFieldToFilter(IntegrationDataValueInterface::JOB_ID, $jobId);
		$collection->addFieldToFilter(IntegrationDataValueInterface::STATUS, $status);

		return $collection;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getByDataValueWithStatus($data = 0, $status = 1) {
		
		if (empty($data)) {
			throw new StateException(__(
				'Parameter Data are empty !'
			));
		}
		$collection = $this->interface->create()->getCollection();
		$collection->addFieldToFilter(IntegrationDataValueInterface::DATA_VALUE, $data);
		$collection->addFieldToFilter(IntegrationDataValueInterface::STATUS, $status);

		return $collection;
	}

    /**
     * @param array $data
     * @return int
     */
    public function insertBulkUsingRawQuery($data) {		

		$label = "upsert-stock-candidates --> ";

		$this->loggerfile->info($label . "start");


		if (!is_array($data) || empty($data)) {

			$err = "parameter data in IntegrationDataValueRepository.insertBulkUsingRawQuery empty !";			
			$this->loggerfile->info($label . "error = " . $err);
			$this->loggerfile->info($label . "finish in = " . (microtime(true) - $startTime) . " second");
			
			throw new ErrorException($err);

		}

		
		if (!is_array($data['stock_data']) || empty($data['stock_data'])) {
			
			$err = "stock-data in IntegrationDataValueRepository.insertBulkUsingRawQuery empty !";
			$this->loggerfile->info($label . "error = " . $err);
			$this->loggerfile->info($label . "finish in = " . (microtime(true) - $startTime) . " second");
			
			throw new ErrorException($err);

		}


		if (empty($data['stock_data_job_id'])) {
			
			$err = "stock-data-job-id in IntegrationDataValueRepository.insertBulkUsingRawQuery empty !";
			$this->loggerfile->info($label . "error = " . $err);
			$this->loggerfile->info($label . "finish in = " . (microtime(true) - $startTime) . " second");
			
			throw new ErrorException($err);

		}
		

		if (empty($data['stock_data_last_updated'])) {

			$err = "stock-data-last-updated in IntegrationDataValueRepository.insertBulkUsingRawQuery empty !";
			$this->loggerfile->info($label . "error = " . $err);
			$this->loggerfile->info($label . "finish in = " . (microtime(true) - $startTime) . " second");
			
			throw new ErrorException($err);

		}
		

		$dataNumber = -1;


        try {

			$columns = array("id" => true, "jb_id" => true, "data_value" => true, "message" => true, "status" => true, "created_at" => true, "updated_at" => true);
			
			$bulk = [];

			foreach ($data['stock_data'] as $row) {

                $rec = [];
            
                foreach($row as $key => $value) {
                    if (isset($columns[$key])) {
                        $rec[$key] = $value;                        
                    }
                }
    
                if (empty($rec)) {

					$err = "stock-data in IntegrationDataValueRepository.insertBulkUsingRawQuery empty !";
					$this->loggerfile->info($label . "error = " . $err);
					$this->loggerfile->info($label . "finish in = " . (microtime(true) - $startTime) . " second");
					
					throw new ErrorException($err);
		
				}

                $bulk[] = $rec;
                                
            }
            
            if (empty($bulk)) {

				$err = "stock-data in IntegrationDataValueRepository.insertBulkUsingRawQuery empty !";
				$this->loggerfile->info($label . "error = " . $err);
				$this->loggerfile->info($label . "finish in = " . (microtime(true) - $startTime) . " second");
				
				throw new ErrorException($err);
	
			}

			$endJobDateTime = $this->timezone->date(new \DateTime())->format('Y-m-d H:i:s.u');

            $datetimeLastUpdated = new \DateTime($data['stock_data_last_updated']);
			$lastRetrieved = $datetimeLastUpdated->format('Y-m-d H:i:s.u');
			$this->loggerfile->info($label . "stock-data-last-updated = " . $lastRetrieved);


			$this->dbConnection->beginTransaction();
			
			$dataNumber = $this->dbConnection->insertMultiple("integration_catalogstock_data", $bulk);
			
			$sql = "update `integration_catalogstock_job` set `status` = " . IntegrationJobInterface::STATUS_READY . ", `last_updated` = '" .  $lastRetrieved . "', `end_job` = '" . $endJobDateTime . "' where `id` = " . $data['stock_data_job_id'];
			$res = $this->dbConnection->exec($sql);
			
			$this->dbConnection->commit();

			$this->loggerfile->info($label . "insert-multiple stock-candidate result = " . $dataNumber);
			$this->loggerfile->info($label . "update stock-candidate-job result = " . $res);
        }
        catch (WarningException $ex) {

			$this->loggerfile->info($label . "warning-exception = " . $ex->getMessage());
			$this->loggerfile->info($label . "finish in = " . (microtime(true) - $startTime) . " second");

			throw $ex;

        }
        catch (ErrorException $ex) {

			$this->loggerfile->info($label . "error-exception = " . $ex->getMessage());
			$this->loggerfile->info($label . "finish in = " . (microtime(true) - $startTime) . " second");

			throw $ex;

        }
        catch (FatalException $ex) {

			$this->loggerfile->info($label . "fatal-exception = " . $ex->getMessage());
			$this->loggerfile->info($label . "finish in = " . (microtime(true) - $startTime) . " second");
			throw $ex;
			
        }
        catch (\Exception $ex) {

			$this->loggerfile->info($label . "generic-exception = " . $ex->getMessage());
			$this->loggerfile->info($label . "finish in = " . (microtime(true) - $startTime) . " second");

			throw $ex;

		}
		
		
		$startTimeMonstock = microtime(true);
		
		try {					

			$this->loggerfile->info($label . "monitoring-stock start");

			if (is_array($data['monitoring_stock_sql']) && !empty($data['monitoring_stock_sql'])) {
				foreach ($data['monitoring_stock_sql'] as $idx => $qry) {
					if (!empty($qry)) {
						$res = $this->dbConnection->exec($qry);
						$this->loggerfile->info($label . "monitoring-stock query-" . ($idx + 1) . " result = " . $res);
					}
				}
			}

			$this->loggerfile->info($label . "monitoring-stock finish in = " . (microtime(true) - $startTimeMonstock) . " second");

		}
		catch (\Exception $ex) {

			$this->loggerfile->info($label . "monitoring-stock exception = " . $ex->getMessage());
			$this->loggerfile->info($label . "monitoring-stock finish in = " . (microtime(true) - $startTimeMonstock) . " second");

		}


		return $dataNumber;

    }


    /**
     * @param int $jobId
     * @param int $status
     * @return mixed
     */
    public function getAllByJobIdStatusUsingRawQuery($jobId, $status)
    {
     
        try {            

            if (empty($jobId)) {
				throw new ErrorException('parameter job-id in IntegrationDataValueRepository.getAllByJobIdStatusUsingRawQuery empty !');
            }
    
            if (empty($status)) {
				throw new ErrorException('parameter status in IntegrationDataValueRepository.getAllByJobIdStatusUsingRawQuery empty !');
            }
            
            $str = "select `id`, `jb_id`, `data_value`, `message`, `status`, `created_at`, `updated_at` from `integration_catalogstock_data` where `jb_id` = %d and `status` = %d";
    
            $sql = sprintf($str, $jobId, $status);
            
            return $this->dbConnection->fetchAll($sql);

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