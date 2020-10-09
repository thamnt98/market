<?php
/**
 * @category Trans
 * @package  Trans_IntegrationCustomer
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   J.P <jaka.pondan@ctcorpdigital.com>
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 *
 * Copyright © 2019 CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationCustomer\Api;

interface IntegrationCustomerInterface
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

    /**
     * Send new customer magento to CDB
     * @param int $customerId
     * @return string
     */
    public function newCustomerIntegration($customerId);

    /**
     * Send update customer magento to CDB
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @return string
     */
    public function updateCustomerIntegration($customer);

    /**
     * Prepare Data Customer
     * @param $data
     * @return mixed
     */
    public function prepareCustomer($data);

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
    public function saveJob($channel, $data);

    /**
     * Prepare Data Value
     * @param $jobs
     * @param $data
     * @return mixed
     */
    public function prepareData($jobs, $data);

    /**
     * Set / Prepare Job & Data Value
     * @param $jobs
     * @return mixed
     */
    public function getJobsData($jobs);

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
    public function setJobFailed($jobs, $msg);

    /**
     * Save CUstomer Id & Central Id
     * @param $data
     * @return mixed
     */
    public function saveCustomerCentralId($data);

    /**
     * Set Status Job Completed
     * @param $jobs
     * @return mixed
     */
    public function setJobComplete($jobs);

    /**
     * Update customer profile by CDB
     * @param \Trans\IntegrationCustomer\Api\Data\IntegrationCdbInterface $customer
     * @return \Trans\IntegrationCustomer\Api\Data\IntegrationCdbResultInterface
     */
    public function updateCdbCustomerProfile($customer);
}
