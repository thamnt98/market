<?php
/**
 * @category Trans
 * @package  Trans_IntegrationCustomer
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Dwi Septha Kurniawan <septha.kurniawan@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationCustomer\Api;


interface IntegrationCustomerUpdateInterface
{
    /**
     * Error Message
     */
    const MSG_METHOD_NOTAVAILABLE='Method is not available';
    const TABLE_CUSTOMER = 'customer_entity';
    const MSG_CHANNEL_NOTAVAILABLE= 'Channel Data method is undefine';
    const MSG_JOB_NOTAVAILABLE= 'Jobs Id is undefine';
    const MSG_DATA_NOTAVAILABLE= 'No Data available for update';
    const MSG_RESP_NOTAVAILABLE= 'Response is not available';

    const MSG_RESPONSE_ERROR = 'response error';    

    /**
     * Prepare Data Update Customer
     * @param $data
     * @return mixed
     */
    public function prepareDataUpdateCustomer($data);

    /**
     * Prepare Job
     * @param $channel
     * @return mixed
     */
    public function prepareJob($channel);

    /**
     * Save job list
     * @param $channel
     * @param $data
     * @return mixed
     */
    public function saveJob($channel,$data);

    /**
     * Prepare Data Value
     * @param $jobs
     * @param $data
     * @return mixed
     */
    public function prepareData($jobs,$data);

    /**
     * Set / Prepare Job & Data Value
     * @param $jobs
     * @return mixed
     */
    public function getJobsDataCustomerUpdate($jobs);

    /**
     * Set Status Job On Progress
     * @param $jobs
     * @return mixed
     */
    public function setJobOnProgress($jobs);

    /**
     * Set Status Job Failed
     * @param $jobs
     * @param $msg
     * @return mixed
     */
    public function setJobFailed($jobs,$msg);

    /**
     * Set Status Job Completed
     * @param $jobs
     * @return mixed
     */
    public function setJobComplete($jobs);

    /**
     * Set Status Job Complete With Error
     * @param $jobs
     * @return mixed
     */
    public function setJobCompleteWithError($jobs,$response);

    /**
     * @param $channel
     * @return mixed
     */
    public function checkWaitingJobs($channel);   

    /**
     * @param array $response
     * @param array $data
     * @return mixed
     */
    public function setStatusMessage($response, $data);
}