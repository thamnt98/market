<?php
/**
 * @category Trans
 * @package  Trans_IntegrationCustomer
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   J.P <jaka.pondan@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationCustomer\Cron\Centralize;

use Magento\Customer\Api\AccountManagementInterface;
use Trans\Integration\Api\IntegrationCommonInterface;
use Trans\IntegrationCustomer\Api\IntegrationCustomerInterface;
use Trans\IntegrationCustomer\Api\IntegrationCheckUpdatesInterface;

class RegisterNewCustomer {
    /**
     * @var \Trans\Integration\Logger\Logger
     */
    protected $logger;

    /**
     * @var IntegrationCommonInterface
     */
    protected $commonRepository;

    /**
     * @var IntegrationCustomerInterface
     */
    protected $customerRepository;

    /**
     * @var IntegrationCheckUpdatesInterface
     */
    protected $checkUpdates;

    /**
     * RegisterNewCustomer constructor.
     * @param \Trans\Integration\Logger\Logger $logger
     * @param IntegrationCommonInterface $commonRepository
     * @param IntegrationCustomerInterface $customerRepository
     */
    public function __construct(
        \Trans\Integration\Logger\Logger $logger
        ,IntegrationCommonInterface $commonRepository
        ,IntegrationCustomerInterface $customerRepository
        ,IntegrationCheckUpdatesInterface $checkUpdates
        ) {
        $this->logger = $logger;
        $this->commonRepository=$commonRepository;
        $this->customerRepository = $customerRepository;
        $this->checkUpdates = $checkUpdates;

    }

    /**
     * Write to system.log
     *
     * @return void
     */
    public function execute() {
        $lastUpdated = date('Y-m-d H:i:s');
        $class = str_replace(IntegrationCheckUpdatesInterface::CRON_DIRECTORY,"",get_class($this));

     
        try {
            $this->logger->info("=>". $class." Get Channel Data");
            $channel    = $this->commonRepository->prepareChannel('register-customers');
            
            $this->logger->info("=". $class." Check On Progress Jobs");
            $this->checkUpdates->checkOnProgressJobs($channel);

            $this->logger->info("=". $class." Get Waiting Jobs");
            $jobs    = $this->checkUpdates->getWaitingJobs($channel);
            
        } catch (\Exception $ex) {
            $this->logger->error($ex->getMessage());
            $this->logger->info("<=". $class);
            return false;
        }
        if(empty($jobs)){
            $this->logger->error("=". $class." Data Jobs Is Not Exist!");
            $this->logger->info("<=". $class);
            return false;
        }

        try {
            $this->logger->info("=". $class." Set Jobs On Progress");
            $this->customerRepository->setJobOnProgress($jobs);

            $this->logger->info("=". $class." Get Waiting Jobs");
            $customerData    = $this->customerRepository->getJobsData($jobs);

            $this->logger->info("=". $class." Set Auth Token");
            $data = $this->commonRepository->prepareAuth($customerData);

            $this->logger->info("=". $class." Prepare Request Data");
            $request = $this->commonRepository->prepareRequest($channel,$data);

            $this->logger->info("=". $class." Sending Request Data to API");
            $response = $this->commonRepository->post($request);

            $this->logger->info("=". $class." Save Customer Id & Centralize ID");
            $saveData = $this->customerRepository->saveCustomerCentralId($response);

            $this->logger->info("=". $class." Complete Jobs");
            $this->customerRepository->setJobComplete($jobs);

        } catch (\Exception $ex) {
            if($jobs){
                $this->customerRepository->setJobFailed($jobs);
            }
            $this->logger->error("=". $class." ".$ex->getMessage());
            return ;
        }
        $this->logger->info("<=". $class);
    }




}