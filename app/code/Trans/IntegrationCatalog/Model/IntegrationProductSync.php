<?php
/**
 * @category Trans
 * @package  Trans_IntegrationCatalog
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   J.P <jaka.pondan@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationCatalog\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\RuntimeException;
use Magento\Framework\Exception\StateException;

use Trans\IntegrationCatalog\Api\Data\IntegrationJobInterface;
use Trans\IntegrationCatalog\Api\Data\IntegrationProductInterface;
use Trans\IntegrationCatalog\Api\Data\IntegrationDataValueInterface;
use Trans\IntegrationCatalog\Api\IntegrationDataValueRepositoryInterface;
use Trans\IntegrationCatalog\Api\IntegrationJobRepositoryInterface;
use Trans\IntegrationCatalog\Model\ResourceModel\IntegrationDataValue;

use Trans\IntegrationEntity\Api\IntegrationProductAttributeRepositoryInterface;
use Trans\IntegrationEntity\Api\IntegrationProductAttributeInterface;

/**
 * Class integrationProductSync
 */
class IntegrationProductSync
{
	/**
	 * @var IntegrationJobRepositoryInterface
	 */
	protected $integrationJobRepositoryInterface;

	/**
	 * @var IntegrationDataValueRepositoryInterface
	 */
	protected $integrationDataValueRepositoryInterface;

	/**
	 * @var Logger
	 */
	protected $logger;

	/**
	 * @var IntegrationProductAttributeRepositoryInterface
	 */
	protected $integrationAttributeRepository;

	/**
	 * @var Attribute Group For General Information
	 */
	protected $attrGroupGeneralInfoId;

	/**
	 * @var \Trans\IntegrationCatalog\Model\ProductImport
	 */
	protected $productImport;

	/**
	 * @var \Trans\IntegrationCatalog\Model\ResourceModel\IntegrationDataValue
	 */
	protected $dataValueResource;

	/**
	 * @param IntegrationJobRepositoryInterface $integrationJobRepositoryInterface
	 * @param IntegrationDataValueRepositoryInterface $integrationDataValueRepositoryInterface
	 * @param IntegrationProductAttributeRepositoryInterface
	 * @param \Trans\IntegrationCatalog\Model\ProductImport $productImport
	 * @param \Trans\IntegrationCatalog\Model\ResourceModel\IntegrationDataValue $dataValueResource
	 */
	public function __construct (
		IntegrationJobRepositoryInterface $integrationJobRepositoryInterface,
		IntegrationDataValueRepositoryInterface $integrationDataValueRepositoryInterface,
		IntegrationProductAttributeRepositoryInterface $integrationAttributeRepository,
		IntegrationDataValue $dataValueResource,
		\Trans\IntegrationCatalog\Model\ProductImport $productImport
	) {
		$this->integrationJobRepositoryInterface = $integrationJobRepositoryInterface;
		$this->integrationDataValueRepositoryInterface = $integrationDataValueRepositoryInterface;
		$this->integrationAttributeRepository = $integrationAttributeRepository;
		$this->dataValueResource = $dataValueResource;
		$this->productImport = $productImport;
		
		$this->attrGroupGeneralInfoId = IntegrationProductInterface::ATTRIBUTE_SET_ID;
		$this->attrGroupProductDetailId = $this->integrationAttributeRepository->getAttributeGroupId(IntegrationProductAttributeInterface::ATTRIBUTE_GROUP_CODE_PRODUCT);

		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/integration_product_import.log');
        $logger = new \Zend\Log\Logger();
        $this->logger = $logger->addWriter($writer);
	}

	/**
	 * Update Job data
	 * @param object $datas
	 * @param int $status
	 * @param string $msg
	 * @throw error 
	 */
	protected function updateJobData($jobId = 0, $status = "", $msg = "", $startJob = "", $endJob = "")
	{
		if ($jobId < 1) {
			throw new StateException(
				__(IntegrationJobInterface::MSG_DATA_NOTAVAILABLE)
			);
		}

		try {
			$dataJobs = $this->integrationJobRepositoryInterface->getById($jobId);
			$dataJobs->setStatus($status);
			
			if($startJob) {
				$dataJobs->setStartJob($startJob);
			}

			if($endJob) {
				$dataJobs->setEndJob($endJob);
			}

			$dataJobs->setMessages($msg);
			
			$this->integrationJobRepositoryInterface->save($dataJobs);
		} catch (\Exception $exception) {
			$this->logger->info(__FUNCTION__."------ ERROR ".$exception->getMessage());
			throw new CouldNotSaveException(__("Error : Cannot Update Job data - ".$exception->getMessage()));
		}
	}

	/**
	 * @param array $channel
	 * @return mixed
	 * @throws NoSuchEntityException
	 * @throws StateException
	 */
	public function prepareData($channel = [])
	{
		try{
			if (empty($channel)) {
				throw new StateException(__(
					'Parameter Channel are empty !'
				));
			}

			$job = $channel['jobs'];
			$jobId = $job->getId();

			//$this->logger->info("channel: ".json_encode($channel));
        	$this->logger->info("job_id: $jobId");
			// $jobId     = 26053;
		
			//try{	
			$status    = IntegrationProductInterface::STATUS_JOB;
			
			$result = $this->integrationDataValueRepositoryInterface->getByJobIdWithStatus($jobId, $status);
			if ($result->getSize() < 1) {
				throw new NoSuchEntityException(__('Result Data Value Are Empty!!'));
			}
			
		} catch (\Exception $exception) {
			$msg = __FUNCTION__ . "------ " . $exception->getMessage() . ' $jobId: ' . $jobId;
			$this->logger->info($msg);
			$this->logger->info($exception->getTraceAsString());

			if($job->getStatus() == IntegrationJobInterface::STATUS_READY) {
				$this->updateJobData($jobId, IntegrationJobInterface::STATUS_PROGRES_UPDATE_FAIL, $msg);
			}

			throw new StateException(__($exception->getMessage()));
		}

		return $result;
	}

	/**
	 * @param Object Data Value Product
	 */
	public function saveProduct($datas, $jobData = null)
	{	
		if (!$datas->getSize()) {
			throw new StateException(
				__(IntegrationJobInterface::MSG_DATA_NOTAVAILABLE)
			);
		}
		
		$jobId = $jobData->getId();
		// $jobId = 26053;
		
		$startJob = date('H:i:s');
		$this->updateJobData($jobId, IntegrationJobInterface::STATUS_PROGRESS_CATEGORY, null, $startJob);

		try {
			$i = 0;
			$objStatusMessage = [];
			$dataProduct = [];
			$saveMagento = [];
			$deleted = [];
			$resultDataConfigurable = [];

			$this->productImport->syncProduct($datas);

			$this->updateDataStatusByJobId($jobId, IntegrationDataValueInterface::STATUS_DATA_SUCCESS);

		} catch (\Exception $exception) {
			$msg = __FUNCTION__." ERROR : ".$exception->getMessage();
			$this->logger->info($msg);
			$this->logger->info($exception->getTraceAsString());
			$this->updateJobData($jobId,IntegrationJobInterface::STATUS_PROGRES_UPDATE_FAIL);
			
			throw new StateException(__($exception->getMessage()));
		}

		$endJob = date('H:i:s');
		$this->updateJobData($jobId, IntegrationJobInterface::STATUS_COMPLETE, '', null, $endJob);
		return true;
	}

	/**
	 * update data value status
	 *
	 * @param int $jobId
	 * @param string $statusData
	 * @return void
	 */
	protected function updateDataStatusByJobId($jobId, $statusData)
	{
		$connection = $this->dataValueResource->getConnection();
		$tableName = $connection->getTableName(IntegrationDataValue::TABLE_DATA_VALUE);

		$data['status'] = $statusData;

		$connection->update(
			$tableName,
			$data,
			['jb_id = ?' => (int)$jobId]
		);
	}	

	/**
	 * Save Status and Message to Integration Data Value
	 *
	 * @param IntegrationDataValueInterface $objStatusMessage
	 * @param string $message
	 * @param int $status
	 */
	protected function saveStatusMessage($objStatusMessage, $message, $status) {
		$objStatusMessage->setMessage($message);
		$objStatusMessage->setStatus($status);
		$this->integrationDataValueRepositoryInterface->save($objStatusMessage);
	}
}
