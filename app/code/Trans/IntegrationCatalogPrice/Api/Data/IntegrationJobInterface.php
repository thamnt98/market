<?php
/**
 * @category Trans
 * @package  Trans_IntegrationCatalogPrice
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   J.P <jaka.pondan@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationCatalogPrice\Api\Data;

/**
 * @api
 */
interface IntegrationJobInterface {
	/**
	 * Constants for keys of data array. Identical to the name of the getter in snake case
	 */
	const TABLE_NAME   = 'integration_catalogprice_job';
	const ID           = 'id';
	const METHOD_ID    = 'md_id'; // method id
	const BATCH_ID     = 'batch_id'; // Batch id are unique id base on chunk jobs
	const TOTAL_DATA   = 'total_data'; // total all data (count)
	const LIMIT        = 'limit'; // limit of data request
	const OFFSET       = 'offset'; // offset
	const LAST_UPDATED = 'last_updated'; // last job updated
	const START_JOB    = 'start_job'; // job start
	const END_JOB      = "end_job"; // job end
	const MESSAGE      = "message"; // message response
	const STATUS       = 'status'; // waiting , progress , close
	const LAST_JB_ID   = 'last_jb_id'; // Last update base on jb_id 
	const HIT		   = 'hit';// How many request from cron
	/**


	/**
	 * Constant for attribute
	 */
	const DEFAULT_MD_ID        = 1;
	const STATUS_WAITING       = 1; // waiting to get data from API
	const STATUS_PROGRESS_GET  = 10; // On progress save data from api
	const STATUS_PROGRESS_SYNC = 11; // On Progress save / sync data original magento table entity
	const STATUS_PROGRESS_POST = 12; // On Progress post data to

	const STATUS_PROGRESS_FAIL = 20; // Fail

	const STATUS_READY             = 30; // Data ready for sync to original magento table entity
	const STATUS_PROGRESS_CATEGORY = 31; // On Progres Sync job data category to magento table

	const STATUS_PROGRES_UPDATE_FAIL = 35;

	const STATUS_COMPLETE = 50; // Data successfully saved sync
	const STATUS_COMPLETE_WITH_ERROR = 53; // Data successfully saved sync With ERROR

	const STATUS_PROGRESS_IMAGE = 51; // Progress Image
	const STATUS_COMPLETE_IMAGE = 60; // COMPLETE AFTER IMAGE PROCESS

	// Maximal Job Progress Will Produce Retry. 
	// Note 1 hit = 1 minute 
	const MAX_HIT_ONPROGRESS = 60; // After 1 hour
	const MAX_HIT_RETRY = 120; // After 2 Hour (1 hour on retry) Set Fail Status 37
	
	const STATUS_UPDATE_RETRY_FAIL = 37;// To Many Retry (error Not Found) Check Paramater


	/**
	 * Constant for Message
	 */
	const MSG_DATA_NOTAVAILABLE = 'Theres no data available';

	/**
	 * Get id
	 *
	 * @return int
	 */
	public function getId();

	/**
	 * Set id
	 *
	 * @param string $idData
	 * @return void
	 */
	public function setId($idData);

	/**
	 * Get Method Id
	 *
	 * @return string
	 */
	public function getMdId();

	/**
	 * Set Method Id
	 *
	 * @param string $mdid
	 * @return void
	 */
	public function setMdId($mdid);

	/**
	 * Get Batch Id
	 *
	 * @return string
	 */
	public function getBatchId();

	/**
	 * Set Batch Id
	 *
	 * @param string $batchId
	 * @return void
	 */
	public function setBatchId($batchId);

	/**
	 * Get Message
	 *
	 * @return string
	 */
	public function getMessages();

	/**
	 * Set Message
	 *
	 * @param string $msg
	 * @return void
	 */
	public function setMessages($msg);

	/**
	 * Get last updated
	 *
	 * @return datetime
	 */
	public function getLastUpdated();

	/**
	 * Set last updated
	 *
	 * @param datetime $lastupdated
	 * @return void
	 */
	public function setLastUpdated($lastupdated);

	/**
	 * Get Total Data
	 *
	 * @return int
	 */
	public function getTotalData();

	/**
	 * Set Total Data
	 *
	 * @param int $total
	 * @return void
	 */
	public function setTotalData($total);

	/**
	 * Get Limit
	 *
	 * @return int
	 */
	public function getLimits();

	/**
	 * Set Limit
	 *
	 * @param int $limit
	 * @return void
	 */
	public function setLimits($limit);

	/**
	 * Get Offset
	 *
	 * @return int $offset
	 */
	public function getOffset();

	/**
	 * Set Offset
	 *
	 * @param int $offset
	 * @return void
	 */
	public function setOffset($offset);

	/**
	 * Get Start Job
	 *
	 * @return datetime
	 */
	public function getStartJob();

	/**
	 * Set Start Job
	 *
	 * @param datetime $startJob
	 * @return void
	 */
	public function setStartJob($startJob);

	/**
	 * Get End Job
	 *
	 * @return datetime
	 */
	public function getEndJob();

	/**
	 * Set  End Job
	 *
	 * @param datetime $endJob
	 * @return void
	 */
	public function setEndJob($endJob);

	/**
	 * Get Status
	 *
	 * @return int
	 */
	public function getStatus();

	/**
	 * Set Status
	 *
	 * @param int $status
	 * @return void
	 */
	public function setStatus($status);

	/**
	 * Get created at
	 *
	 * @return string
	 */
	public function getCreatedAt();

	/**
	 * Set created at
	 *
	 * @param string $createdAt
	 * @return void
	 */
	public function setCreatedAt($createdAt);

	/**
	 * Get updated at
	 *
	 * @return string
	 */
	public function getUpdatedAt();

	/**
	 * Set updated at
	 *
	 * @param string $updatedAt
	 * @return void
	 */
	public function setUpdatedAt($updatedAt);

	/**
	 * Get created at
	 *
	 * @return string
	 */
	public function getCreatedBy();

	/**
	 * Set created at
	 *
	 * @param string $createdAt
	 * @return void
	 */
	public function setCreatedBy($createdBy);

	/**
	 * Get updated at
	 *
	 * @return string
	 */
	public function getUpdatedBy();

	/**
	 * Set updated at
	 *
	 * @param string $updatedAt
	 * @return void
	 */
	public function setUpdatedBy($updatedBy);

	/**
	 * Get Hit
	 *
	 * @return int
	 */
	public function getHit();

	/**
	 * Set Hit
	 *
	 * @param int $hit
	 * @return void
	 */
	public function setHit($hit);

	/**
	 * Get Last JB ID
	 *
	 * @return int
	 */
	public function getLastJbId();

	/**
	 * Set Last JB ID
	 *
	 * @param int $jbid
	 * @return void
	 */
	public function setLastJbId($jbid);


}