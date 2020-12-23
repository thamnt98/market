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
use \Trans\IntegrationCatalogStock\Api\IntegrationDataValueRepositoryInterface;
use \Trans\IntegrationCatalogStock\Model\ResourceModel\IntegrationDataValue as ResourceModel;
use \Trans\IntegrationCatalogStock\Model\ResourceModel\IntegrationDataValue\Collection;
use \Trans\IntegrationCatalogStock\Model\ResourceModel\IntegrationDataValue\CollectionFactory;

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
     * @var ResourceConnection
     */
	protected $dbConnection;

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
		\Magento\Framework\App\ResourceConnection $resourceConnection
	) {
		$this->resource = $resource;

		$this->collectionFactory   = $collectionFactory;
		$this->searchResultFactory = $searchResultFactory;
		$this->interface           = $interface;

		$this->dbConnection = $resourceConnection->getConnection();
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
     * @param array $inserts
     * @return int
     */
    public function insertBulkUsingRawQuery($inserts) {

        try {

            if (!is_array($inserts) || empty($inserts)) {
                throw new StateException(__(
                    'Parameter Inserts are empty !'
                ));
            }

			$columns = array("id" => true, "jb_id" => true, "data_value" => true, "message" => true, "status" => true, "created_at" => true, "updated_at" => true);
			
			$bulk = array();

			foreach ($inserts as $insert) {

                $record = array();
            
                foreach($insert as $key => $value) {
                    if (isset($columns[$key])) {
                        $record[$key] = $value;                        
                    }
                }
    
                if (empty($record)) {
                    throw new StateException(__(
                        'Parameter Inserts are empty !'
                    ));
                }

                $bulk[] = $record;
                                
            }
            
            if (empty($bulk)) {
                throw new StateException(__(
                    'Parameter Inserts are empty !'
                ));
            }
    
            return $this->dbConnection->insertMultiple("integration_catalogstock_data", $bulk);

        }
        catch (\Exception $ex) {
            throw new StateException(
                __($ex->getMessage())
            );
        }

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
                throw new StateException(__(
                    'Parameter Job Id are empty !'
                ));
            }
    
            if (empty($status)) {
                throw new StateException(__(
                    'Parameter Status are empty !'
                ));
            }
            
            $str = "select `id`, `jb_id`, `data_value`, `message`, `status`, `created_at`, `updated_at` from `integration_catalogstock_data` where `jb_id` = %d and `status` = %d";
    
            $sql = sprintf($str, $jobId, $status);
            
            return $this->dbConnection->fetchAll($sql);

        } 
        catch (\Exception $ex) {
            throw new StateException(__(
                $ex->getMessage()
             ));
        }        

	}
	
}