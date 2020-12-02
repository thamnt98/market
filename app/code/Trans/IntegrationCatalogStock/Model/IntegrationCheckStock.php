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

use Trans\Integration\Api\IntegrationCommonInterface;
use Trans\IntegrationCatalogStock\Api\IntegrationCheckUpdatesInterface;
use Trans\IntegrationCatalogStock\Api\IntegrationGetUpdatesInterface;
use Trans\IntegrationCatalogStock\Api\IntegrationCheckStockInterface;
use Trans\IntegrationCatalogStock\Api\Data\IntegrationCheckStockResponseInterfaceFactory;

use \Magento\Framework\Serialize\Serializer\Json;
use \Magento\Framework\App\RequestInterface;
use \Trans\Core\Helper\ValidateRequest;

/**
 * @inheritdoc
 */
class IntegrationCheckStock implements IntegrationCheckStockInterface
{
    /**
     * @var \Trans\IntegrationCatalogStock\Logger\Logger
     */
    protected $logger;

    /**
     * @var IntegrationCommonInterface
     */
    protected $commonRepository;


    /**
     * @var IntegrationGetUpdatesInterface
     */
    protected $getUpdates;

    /**
	 * @var \Magento\Framework\Serialize\Serializer\Json
	 */
    protected $json;
    
    /**
	 * @var \Magento\Framework\App\RequestInterface
	 */
    protected $request;
    
    /**
     * @var ValidateRequest
     */
    protected $validate;

    /**
     * @var IntegrationCheckStockResponseInterfaceFactory
     */
    protected $response;

    


    public function __construct(
        \Trans\IntegrationCatalogStock\Logger\Logger $logger
        ,IntegrationCommonInterface $commonRepository
        ,IntegrationCheckUpdatesInterface $checkUpdates
        ,IntegrationGetUpdatesInterface $getUpdates
        ,Json $json
        ,RequestInterface $request
        ,IntegrationCheckStockResponseInterfaceFactory $response
        ,ValidateRequest $validate
        ) {
        $this->logger = $logger;
        $this->commonRepository=$commonRepository;
        $this->checkUpdates=$checkUpdates;
        $this->getUpdates=$getUpdates;
        $this->request = $request;
        $this->validate=$validate;
        $this->response=$response;

    }

   /**
    * Write to system.log
    *
    * @return void
    */
    public function execute() {

        $this->logger->info("=>".__CLASS__);
        try {
            $this->logger->info("=Validate Request Parameter");
            $request = $this->request->getContent();

            // $request_data = $this->json->unserialize($request);

            $request_data = json_decode($request);
            $request_data = (array) $request_data;
             if(isset($request_data['request'])){
                $request_data = (array) $request_data['request'];
            }
            $this->logger->info(print_r($request_data,true));


            $this->validate->one('product_sku',$request_data);
            $this->validate->one('location_code',$request_data);

           
            $this->logger->info("=Get Channel Data");
            $channel    = $this->commonRepository->prepareChannel('product-stock-direct-check');

            $this->logger->info("=Check Waiting Jobs");
            $this->checkUpdates->checkWaitingJobs($channel);

            $this->logger->info("=Get Last Complete Jobs");
            $channel    = $this->checkUpdates->checkCompleteJobs($channel);

            $this->logger->info(print_r($request_data ,true));
            $this->logger->info("=Set Parameter Request Data");
            $data       = $this->getUpdates->preparePostCall($channel,json_encode($request_data));
            
            $this->logger->info(print_r($data ,true));
            $this->logger->info("=Sending Request Data to API");
            $response    = $this->commonRepository->post($data);
          
            $this->logger->info("=Set Response to Job data");
            $dataResp = $this->getUpdates->setResponseData($channel,$response);
            $this->logger->info(print_r($dataResp,true));


        } catch (\Exception $ex) {
            $msg = $ex->getMessage();
            $this->logger->error(  $msg);
            return $this->getResponse(["message"=> $msg]);
        }
        $this->logger->info("<=End ".__CLASS__);
        return $this->getResponse($dataResp);
    }

    /**
     * @param null $message
     * @param array $data
     * @return mixed
     */
    protected function getResponse($response = null) {
        /** \Trans\IntegrationCatalogStock\Api\Data\IntegrationCheckStockResponseInterfaceFactory */
        if(!$response){
            return false;
        }
        $result = $this->response->create();
        $message = (isset($response['message']))?$response['message']:"";
        $data = (isset($response['data']))?$response['data']:"";
        $result->setMessage(__(($message)));
        $result->setDatas($data);

        return $result;
    }

}