<?php
/**
 * @category Trans
 * @package  Trans_IntegrationCustomer
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Dwi Septha Kurniawan <septha.kurniawan@transdigital.co.id>
 *
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationCustomer\Cron\Centralize;

use Magento\Customer\Api\AccountManagementInterface;
use Trans\Integration\Api\IntegrationCommonInterface;
use Trans\IntegrationCustomer\Api\IntegrationCustomerUpdateInterface;

class EditCustomerAccount {
    /**
     * @var \Trans\Integration\Logger\Logger
     */
    protected $logger;

    /**
     * @var IntegrationCommonInterface
     */
    protected $commonRepository;

    /**
     * @var IntegrationCustomerUpdateInterface
     */
    protected $customerRepository;

    /**
     * RegisterNewCustomer constructor.
     * @param \Trans\Integration\Logger\Logger $logger
     * @param IntegrationCommonInterface $commonRepository
     * @param IntegrationCustomerUpdateInterface $customerRepository
     */
    public function __construct(
        \Trans\Integration\Logger\Logger $logger
        ,IntegrationCommonInterface $commonRepository
        ,IntegrationCustomerUpdateInterface $customerRepository
        ) {
        $this->logger = $logger;
        $this->commonRepository=$commonRepository;
        $this->customerRepository = $customerRepository;

    }

    /**
     * Write to system.log
     *
     * @return void
     */
    public function execute() {
        $lastUpdated = date('Y-m-d H:i:s');
        $this->logger->info("===>".__CLASS__);

        $getJob = $this->getJobUpdateCustomer();

        $this->processJob($getJob['channel'], $getJob['jobs']);

        $this->logger->info("<===".__CLASS__);
    }

    /**
     * get job update customer
     * @return mixed
     */
    protected function getJobUpdateCustomer(){
        $channel = NULL;
        $jobs    = NULL;

        try {
            $this->logger->info("Get Channel Data");
            $channel = $this->commonRepository->prepareChannel('update-customers');

            $this->logger->info("Check Waiting Jobs");
            $jobs = $this->customerRepository->checkWaitingJobs($channel);

            return array('channel' => $channel, 'jobs' => $jobs); 

        } catch (\Exception $ex) {
            if($jobs){
                $this->customerRepository->setJobFailed($jobs);
            }
            $this->logger->error($ex->getMessage());
            return;
        }
       
    }

    /**
     * process job
     * @param array $channel
     * @param array $jobs
     * @return mixed
     */
    protected function processJob($channel, $jobs){
        try {

            if($channel!=NULL || $jobs!=NULL){
                $this->logger->info("Set Jobs On Progress");
                $this->customerRepository->setJobOnProgress($jobs);

                $this->logger->info("Get Waiting Jobs");
                $updateCustomerData = $this->customerRepository->getJobsDataCustomerUpdate($jobs);
                // $this->logger->info(print_r($updateCustomerData,true));

                $this->logger->info("Post to Centrallize");
                $responseStatus = $this->postToCentrallize($updateCustomerData, $channel);
                
                if ($responseStatus > 0){
                   $this->logger->info("Complete Jobs");
                   $this->customerRepository->setJobComplete($jobs);
                } else {
                   $this->logger->info("Complete Jobs with Error");
                   $this->customerRepository->setJobCompleteWithError($jobs,'There is a response Failed');    
                }
            }

        } catch (\Exception $ex) {
            if($jobs){
                $this->customerRepository->setJobFailed($jobs);
            }
            $this->logger->error($ex->getMessage());
            return;
        }        
    }

    /**
     * post to centrallize
     * @param array $updateCustomerData
     * @return mixed
     */
    protected function postToCentrallize($updateCustomerData, $channel){
        $responseStatus = 1;
        foreach ($updateCustomerData as $updateCustomerDatas) {
            $dataRequest = array(json_decode($updateCustomerDatas->getDataValue(), true));
            $data = $this->commonRepository->prepareAuth($dataRequest);
            $request = $this->commonRepository->prepareRequest($channel,$data);
            // $this->logger->info(print_r($request,true));

            $response = $this->commonRepository->post($request);
            // $this->logger->info(print_r($response,true)); 

            $this->customerRepository->setStatusMessage($response, $updateCustomerDatas);

            if ($this->responseStatus($response) != 1){
                $responseStatus = 0;
            }
        }
        
        return $responseStatus;
    }

    /**
     * Response Status
     * @param array $response
     * @return mixed
     */
    public function responseStatus($response)
    {
        $responseStatus = 0;

        if (isset($response['data'])){
            foreach ($response['data'] as $key => $value) {
                $responseStatus = $value['status'];
            }
        }

        return $responseStatus;
    }    

}