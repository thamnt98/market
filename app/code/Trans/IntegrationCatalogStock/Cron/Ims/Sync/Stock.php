<?php

/**
 * @category Trans
 * @package  Trans_Integration_Exception
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Hariadi <hariadi_wicaksana@transretail.co.id>
 *
 * Copyright © 2020 PT Trans Retail Indonesia. All rights reserved.
 * http://carrefour.co.id
 */

namespace Trans\IntegrationCatalogStock\Cron\Ims\Sync;


use Trans\Integration\Exception\WarningException;
use Trans\Integration\Exception\ErrorException;
use Trans\Integration\Exception\FatalException;


class Stock {

    public const CURL_ERRNO = [
        1  => "CURL_UNSUPPORTED_PROTOCOL",
        2  => "CURL_FAILED_INIT",
        3  => "CURL_URL_MALFORMAT",
        4  => "CURL_URL_MALFORMAT_USER",
        5  => "CURL_COULDNT_RESOLVE_PROXY",
        6  => "CURL_COULDNT_RESOLVE_HOST",
        7  => "CURL_COULDNT_CONNECT",
        8  => "CURL_FTP_WEIRD_SERVER_REPLY",
        9  => "CURL_FTP_ACCESS_DENIED",
        10 => "CURL_FTP_USER_PASSWORD_INCORRECT",
        11 => "CURL_FTP_WEIRD_PASS_REPLY",
        12 => "CURL_FTP_WEIRD_USER_REPLY",
        13 => "CURL_FTP_WEIRD_PASV_REPLY",
        14 => "CURL_FTP_WEIRD_227_FORMAT",
        15 => "CURL_FTP_CANT_GET_HOST",
        16 => "CURL_FTP_CANT_RECONNECT",
        17 => "CURL_FTP_COULDNT_SET_BINARY",
        18 => "CURL_FTP_PARTIAL_FILE or CURL_PARTIAL_FILE",
        19 => "CURL_FTP_COULDNT_RETR_FILE",
        20 => "CURL_FTP_WRITE_ERROR",
        21 => "CURL_FTP_QUOTE_ERROR",
        22 => "CURL_HTTP_NOT_FOUND or CURL_HTTP_RETURNED_ERROR",
        23 => "CURL_WRITE_ERROR",
        24 => "CURL_MALFORMAT_USER",
        25 => "CURL_FTP_COULDNT_STOR_FILE",
        26 => "CURL_READ_ERROR",
        27 => "CURL_OUT_OF_MEMORY",
        28 => "CURL_OPERATION_TIMEDOUT or CURL_OPERATION_TIMEOUTED",
        29 => "CURL_FTP_COULDNT_SET_ASCII",
        30 => "CURL_FTP_PORT_FAILED",
        31 => "CURL_FTP_COULDNT_USE_REST",
        32 => "CURL_FTP_COULDNT_GET_SIZE",
        33 => "CURL_HTTP_RANGE_ERROR",
        34 => "CURL_HTTP_POST_ERROR",
        35 => "CURL_SSL_CONNECT_ERROR",
        36 => "CURL_BAD_DOWNLOAD_RESUME or CURL_FTP_BAD_DOWNLOAD_RESUME",
        37 => "CURL_FILE_COULDNT_READ_FILE",
        38 => "CURL_LDAP_CANNOT_BIND",
        39 => "CURL_LDAP_SEARCH_FAILED",
        40 => "CURL_LIBRARY_NOT_FOUND",
        41 => "CURL_FUNCTION_NOT_FOUND",
        42 => "CURL_ABORTED_BY_CALLBACK",
        43 => "CURL_BAD_FUNCTION_ARGUMENT",
        44 => "CURL_BAD_CALLING_ORDER",
        45 => "CURL_HTTP_PORT_FAILED",
        46 => "CURL_BAD_PASSWORD_ENTERED",
        47 => "CURL_TOO_MANY_REDIRECTS",
        48 => "CURL_UNKNOWN_TELNET_OPTION",
        49 => "CURL_TELNET_OPTION_SYNTAX",
        50 => "CURL_OBSOLETE",
        51 => "CURL_SSL_PEER_CERTIFICATE",
        52 => "CURL_GOT_NOTHING",
        53 => "CURL_SSL_ENGINE_NOTFOUND",
        54 => "CURL_SSL_ENGINE_SETFAILED",
        55 => "CURL_SEND_ERROR",
        56 => "CURL_RECV_ERROR",
        57 => "CURL_SHARE_IN_USE",
        58 => "CURL_SSL_CERTPROBLEM",
        59 => "CURL_SSL_CIPHER",
        60 => "CURL_SSL_CACERT",
        61 => "CURL_BAD_CONTENT_ENCODING",
        62 => "CURL_LDAP_INVALID_URL",
        63 => "CURL_FILESIZE_EXCEEDED",
        64 => "CURL_FTP_SSL_FAILED",
        79 => "CURL_SSH"
    ];
    
    /**
     * @var ResourceConnection
     */
	protected $dbConnection;

	/**
	 * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
	 */
    protected $timezone;
    
	/**
	 * @var \Trans\IntegrationCatalogStock\Logger\Logger
	 */
	protected $loggerfile;

    /**
     * @var \Trans\Integration\Model\IntegrationCronLogToDatabase
     */
    protected $loggerdb;

    /**
     * @var string
     */
    protected $cronType;

    /**
     * @var string
     */
    protected $cronTypeDetail;    


    public function __construct(
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Trans\IntegrationCatalogStock\Logger\Logger $loggerfile,
        \Trans\Integration\Model\IntegrationCronLogToDatabase $loggerdb
    ) {
        $this->dbConnection = $resourceConnection->getConnection();
        $this->timezone = $timezone;
        $this->loggerfile = $loggerfile;
        $this->loggerdb = $loggerdb;

        $this->adjustCronLogger();
    }
    

    protected function adjustCronLogger() {
        $this->cronType = "stock";
        $this->cronTypeDetail = "sync";
        $this->cronFileLabel = $this->cronType . "-" . $this->cronTypeDetail . " --> ";
    }


	public function execute() {

        $startTime = microtime(true);


        $tagChannel = 'v2-product-stock-update';        
        
        $apiTimeout = 60;
        $apiCallDuration = -1;

		$limitDataToApi = 0;
		$totalDataFromApiReceived = 0;
		$totalDataFromApiReceivedInvalid = 0;
		$totalDataFromApiUpdatedToMagentoStock = 0;
        $totalDataFromApiSaved = 0;

        $monitoringStockJobId = null;
        

        $exceptionFound = false;
        $logMessageTopic = "start";
        $logMessage = "memory usage = " . round(memory_get_usage() / 1048576, 2) . " megabytes";
        $logLevel = IntegrationCronLogToDatabase::LEVEL_INFO;
        $this->loggerfile->info($this->cronFileLabel . $logMessageTopic . " = " . $logMessage);
        $this->loggerdb->log($this->cronType, $this->cronTypeDetail, $logLevel, $logMessageTopic, $logMessage);


		try {

			$this->dbConnection->beginTransaction();								
		
			$sql = "set @saved_lock_wait = @@session.innodb_lock_wait_timeout";
			$this->dbConnection->exec($sql);
		
			$sql = "set session innodb_lock_wait_timeout = 1";
			$this->dbConnection->exec($sql);    
            

            $meta = [];

			$sql = "select `last_id` from `v2_monitoring_stock_last_retrieved` limit 1 for update";
			$meta['last_id'] = $this->dbConnection->fetchOne($sql);
			if (empty($meta['last_id'])) {
				$meta['last_id'] = "";
			}
			$this->loggerfile->info($this->cronFileLabel . "last-retrieved-stock-id = '" . $meta['last_id'] . "'");
		
			$sql = "select `ch_id`, `headers`, `query_params`, `path`, `limit` from `integration_channel_method` where `tag` = '" . $tagChannel . "' and `status` = 1 limit 1";
			$meta['method'] = $this->dbConnection->fetchRow($sql);    
			if (empty($meta['method'])) {
				throw new ErrorException('integration-channel-method not found');
			}
				
			$sql = "select `url` from `integration_channel` where `id` = " . $meta['method']['ch_id'] . " limit 1";
			$meta['channel'] = $this->dbConnection->fetchRow($sql);    
			if (empty($meta['channel'])) {
				throw new ErrorException('integration-channel not found');
			}
            

            $attr = [];

			$sql = "select (select `attribute_id` from `eav_attribute` where `attribute_code` = 'is_fresh' and `frontend_input` = 'boolean' and `entity_type_id` = e.`entity_type_id`) as `is_fresh`, (select `attribute_id` from `eav_attribute` where `attribute_code` = 'sold_in' and `entity_type_id` = e.`entity_type_id`) as `sold_in`, (select `attribute_id` from `eav_attribute` where `attribute_code` = 'weight' and `entity_type_id` = e.`entity_type_id`) as `weight` from `eav_entity_type` e where `entity_type_code` = 'catalog_product' limit 1";
			$attr = $this->dbConnection->fetchRow($sql);
			unset($sql);
			if (empty($attr)) {
				throw new ErrorException('attribute-product not found');
			}
        

			$limitDataToApi = $meta['method']['limit'];
			$apiPayload = json_decode($meta['method']['query_params'], true);
			$apiPayload['_limit'] = $limitDataToApi;
			$apiPayload['_offset'] = 0;
			$apiPayload['_modified_at'] = $meta['last_id'];
			$apiPath = $meta['channel']['url'] . $meta['method']['path'];
			$apiPathFull = sprintf("%s?%s", $apiPath, http_build_query($apiPayload));    
			$apiHeader = json_decode($meta['method']['headers'], true);
			$this->loggerfile->info($this->cronFileLabel . "api-path = " . $apiPath);
			$this->loggerfile->info($this->cronFileLabel . "api-payload = " . print_r($apiPayload, true));
			$this->loggerfile->info($this->cronFileLabel . "api-header = " . print_r($apiHeader, true));
			
			$curlHolder = curl_init();
			curl_setopt($curlHolder, CURLOPT_URL, $apiPathFull);
			curl_setopt($curlHolder, CURLOPT_HTTPHEADER, $apiHeader);
			curl_setopt($curlHolder, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curlHolder, CURLOPT_HEADER, false);
			curl_setopt($curlHolder, CURLOPT_TIMEOUT, $apiTimeout);
			$curlOutput = curl_exec($curlHolder);
			$curlInfo = curl_getinfo($curlHolder);
			$curlErrNo = curl_errno($curlHolder);
			curl_close($curlHolder);
			
			$apiCallDuration = $curlInfo['total_time'];
			
			if (!empty($curlErrNo)) {
				throw new ErrorException("api-call error = " . $this::CURL_ERRNO[(int) $curlErrNo]);
			}    
			
			$curlResponse = json_decode($curlOutput, true);
			unset($curlOutput);
		
			if ($curlInfo['http_code'] != 200) {
				$err = "api-call error";
				if (isset($curlResponse['message'])) {
					$err .= " = " . $curlResponse['message'];
				}
				throw new ErrorException($err);
			}        
		
            if (
                empty($curlResponse['data']) || 
                !is_array($curlResponse['data']) || 
                ($totalDataFromApiReceived = count($curlResponse['data'])) == 0) {
				throw new WarningException("stock-data from api-call empty");
			}
		
			$this->loggerfile->info($this->cronFileLabel . "total-data-from-api received = " . $totalDataFromApiReceived);


			$stockCandidateIndex = -1;
			$stockCandidateList = [];    
			$stockCandidatePointerList = [];
			$stockCandidateQuantityFloatList = [];
			$stockValidStr = "";    
			$skuList = [];
			$skuStr = "";
			$locationCodeList = [];
			$locationCodeStr = "";
			$lastStockId = "";
			$monitoringStockList = [];
        
			foreach ($curlResponse['data'] as $data) {
				if (!empty($data['stock_id'])) {
					$lastStockId = $data['stock_id'];
				}
				else {
					$totalDataFromApiReceivedInvalid++;
					continue;
				}
		
				if (!empty($data['location_code'])) {
					$locationCode = $data['location_code'];
				}
				else {
					$totalDataFromApiReceivedInvalid++;
					continue;
				}
		
				if (!empty($data['product_sku'])) {
					$productSku = $data['product_sku'];
				}
				else {
					$totalDataFromApiReceivedInvalid++;
					continue;
				}
		
				$quantityFloat = 0;
				$quantity = 0;				
				if (!empty($data['quantity'])) {
					$quantityFloat = (float) $data['quantity'];
					if ($quantityFloat < 0) {
						$quantityFloat = 0;
					}
					$quantity = (int) floor($quantityFloat);
				}        
		
				if (!isset($locationCodeList[$locationCode])) {
					$locationCodeList[$locationCode] = true;
					$locationCodeStr .= ",('{$locationCode}','{$locationCode}',1,'ID','00000',1)";
				}
			
				if (!isset($stockCandidatePointerList[$productSku])) {
					$stockCandidatePointerList[$productSku] = [];
					$skuList[] = $productSku;
					$skuStr .= ",'" . $productSku . "'";
				}
				
				$stockCandidate = array(
					"source_code" => $locationCode,
					"sku" => $productSku,
					"quantity" => $quantity,
					"status" => ($quantity > 0 ? 1 : 0)
				);
		
				$stockCandidateIndex++;
				$stockCandidateList[$stockCandidateIndex] = $stockCandidate;
				$stockCandidateQuantityFloatList[$stockCandidateIndex] = $quantityFloat;
		
				$stockCandidatePointerList[$productSku][] = $stockCandidateIndex;
		
				$stockValidStr .= ",'" . $lastStockId . "'";
				
				if (!empty($data['stock_name'])) {
					$stockName = ",'" . $data['stock_name'] . "'";
				}
				else {
					$stockName = ",null";
				}
		
				if (!empty($data['stock_filename'])) {
					$stockFilename = ",'" . $data['stock_filename'] . "'";
				}
				else {
					$stockFilename = ",null";
				}
		
				if (!empty($data['stock_action'])) {
					$stockAction = ",'" . $data['stock_action'] . "'";
				}
				else {
					$stockAction = ",null";
				}
				
				$monitoringStockList[] = "(sysdate(6),sysdate(6),'" . $locationCode . "','" . $productSku . "','" . $lastStockId . "'" . $stockName . $stockFilename . $stockAction;		
			}
		
			if ($stockCandidateIndex > -1) {
				$skuStr = substr($skuStr, 1);
		
				$sql = "select c.`sku`, (select `value` from `catalog_product_entity_int` where `row_id` = c.`row_id` and `attribute_id` = {$attr['is_fresh']}) as `is_fresh`, (select `value` from `catalog_product_entity_varchar` where `row_id` = c.`row_id` and `attribute_id` = {$attr['sold_in']}) as `sold_in`, (select `value` from `catalog_product_entity_decimal` where `row_id` = c.`row_id` and `attribute_id` = {$attr['weight']}) as `weight`
				from `catalog_product_entity` c where `sku` in (" . $skuStr . ")";
				$collections = $this->dbConnection->fetchAll($sql);
                unset($sql);

				if (!empty($collections)) {
					foreach ($collections as $item) {
						if (!isset($stockCandidatePointerList[$item['sku']])) {
							$item['sku'] = strtoupper($item['sku']);
							if (!isset($stockCandidatePointerList[$item['sku']])) {
								$item['sku'] = strtolower($item['sku']);
								if (!isset($stockCandidatePointerList[$item['sku']])) {
									continue;
								}
							}
						}                
						foreach ($stockCandidatePointerList[$item['sku']] as $idx) {
							if ($item['is_fresh'] == 1) {
								if ($item['sold_in'] == 'kg' || $item['sold_in'] == 'Kg' || $item['sold_in'] == 'KG') {
									if (!empty($item['weight'])) {
										$weight = (float) $item['weight'];
										if ($weight > 0) {
											$newQuantity = (int) floor(($stockCandidateQuantityFloatList[$idx] * 1000) / $weight);
											$stockCandidateList[$idx]['quantity'] = $newQuantity;
											$stockCandidateList[$idx]['status'] = ($newQuantity > 0 ? 1 : 0);    
										}
									}
								}
							}	
						}
					}
				}        
		
				$mainSql = "";
				foreach ($stockCandidateList as $item) {
					$line = "";
					foreach ($item as $key => $value) {                
						$line .= ",'" . $value . "'";                
					}
					$mainSql .= ",(" . substr($line, 1) . ")";
				}
				unset($stockCandidateList);
		
				if ($locationCodeStr != "") {
					$sql = "insert ignore into `inventory_source` (`source_code`, `name`, `enabled`, `country_id`, `postcode`, `use_default_carrier_config`) values " . substr($locationCodeStr, 1);
					$res = $this->dbConnection->exec($sql);
					unset($locationCodeList);
					unset($locationCodeStr);
					unset($sql);
					$this->loggerfile->info($this->cronFileLabel . "insert-ignore inventory_source result = " . $res);
				}        
		
				$mainSql = "insert into `inventory_source_item` (`source_code`, `sku`, `quantity`, `status`) values " . substr($mainSql, 1) . " on duplicate key update `quantity` = values(`quantity`), `status` = values(`status`)";        
				$totalDataFromApiUpdatedToMagentoStock = $this->dbConnection->exec($mainSql);
				unset($mainSql);
				$this->loggerfile->info($this->cronFileLabel . "total-data-from-api updated to magento-stock = " . $totalDataFromApiUpdatedToMagentoStock);
				
				$sql =
				" insert into `cataloginventory_stock_item` "
				. " ( "
				. " `product_id`, "
				. " `stock_id`, "
				. " `qty`, "
				. " `min_qty`, "
				. " `use_config_min_qty`, "
				. " `is_qty_decimal`, "
				. " `backorders`, "
				. " `use_config_backorders`, "
				. " `min_sale_qty`, "
				. " `use_config_min_sale_qty`, "
				. " `max_sale_qty`, "
				. " `use_config_max_sale_qty`, "
				. " `is_in_stock`, "
				. " `low_stock_date`, "
				. " `notify_stock_qty`, "
				. " `use_config_notify_stock_qty`, "
				. " `manage_stock`, "
				. " `use_config_manage_stock`, "
				. " `stock_status_changed_auto`, "
				. " `use_config_qty_increments`, "
				. " `qty_increments`, "
				. " `use_config_enable_qty_inc`, "
				. " `enable_qty_increments`, "
				. " `is_decimal_divided`, "
				. " `website_id`, "
				. " `deferred_stock_update`, "
				. " `use_config_deferred_stock_update` "
				. " ) "
				. " select "
					. " `entity_id` as `product_id`, "
					. " 1 as `stock_id`, "
					. " 1.0000 as `qty`, "
					. " 0.0000 as `min_qty`, "
					. " 1 as `use_config_min_qty`, "
					. " 0 as `is_qty_decimal`, "
					. " 0 as `backorders`, "
					. " 1 as `use_config_backorders`, "
					. " 1.0000 as `min_sale_qty`, "
					. " 1 as `use_config_min_sale_qty`, "
					. " 99.0000 as `max_sale_qty`, "
					. " 1 as `use_config_max_sale_qty`, "
					. " 1 as `is_in_stock`, "
					. " null as `low_stock_date`, "
					. " 1.0000 as `notify_stock_qty`, "
					. " 1 as `use_config_notify_stock_qty`, "
					. " 1 as `manage_stock`, "
					. " 1 as `use_config_manage_stock`, "
					. " 0 as `stock_status_changed_auto`, "
					. " 1 as `use_config_qty_increments`, "
					. " 1.0000 as `qty_increments`, "
					. " 1 as `use_config_enable_qty_inc`, "
					. " 0 as `enable_qty_increments`, "
					. " 0 as `is_decimal_divided`, "
					. " 0 as `website_id`, "
					. " 0 as `deferred_stock_update`, "
					. " 1 as `use_config_deferred_stock_update` "
				. " from `catalog_product_entity` "
				. " where `sku` in ( " . $skuStr . " ) "
				. " on duplicate key update `manage_stock` = 1, `use_config_manage_stock` = 1 "
				;
				$res = $this->dbConnection->exec($sql);
				unset($skuStr);
				unset($sql);
				$this->loggerfile->info($this->cronFileLabel . "insert-on-duplicate-update cataloginventory_stock_item result = " . $res);
		
				$sql = "update `v2_monitoring_stock_last_retrieved` set `last_id` = '" . $lastStockId . "'";
				$res = $this->dbConnection->exec($sql);
				$this->loggerfile->info($this->cronFileLabel . "update last-retrieved-stock-id result = " . $res);
				$this->loggerfile->info($this->cronFileLabel . "next last-retrieved-stock-id = '" . $lastStockId . "'");
		
				$sql = "select sysdate(6) as t";
				$monitoringStockJobId = $this->dbConnection->fetchOne($sql);
				$this->loggerfile->info($this->cronFileLabel . "monitoring-stock-job-id = '" . $monitoringStockJobId . "'");
		
				$sql = "insert ignore into `v2_monitoring_stock` (`retrieved_at`, `processed_at`, `store_code`, `sku`, `stock_id`, `stock_name`, `stock_filename`, `stock_action`, `job_id`) values " . implode(",'{$monitoringStockJobId}'),", $monitoringStockList) . ",'{$monitoringStockJobId}')";
				$totalDataFromApiSaved = $this->dbConnection->exec($sql);
				unset($monitoringStockList);
				$this->loggerfile->info($this->cronFileLabel . "total-data-from-api saved = " . $totalDataFromApiSaved);
		
				$stockValidStr = substr($stockValidStr, 1);
		
				$sql = "insert ignore into `v2_monitoring_stock_when_watcher_temp` (`id`, `when_at`, `when_type`, `store_code`, `sku`, `stock_id`) select sysdate(6), `retrieved_at`, 'retrieved', `store_code`, `sku`, `stock_id` from `v2_monitoring_stock` where `stock_id` in (" . $stockValidStr . ")";
				$res = $this->dbConnection->exec($sql);
				unset($sql);
				$this->loggerfile->info($this->cronFileLabel . "insert-ignore retrieved-status result = " . $res);
		
				$sql = "insert ignore into `v2_monitoring_stock_when_watcher_temp` (`id`, `when_at`, `when_type`, `store_code`, `sku`, `stock_id`) select sysdate(6), `processed_at`, 'processed', `store_code`, `sku`, `stock_id` from `v2_monitoring_stock` where `stock_id` in (" . $stockValidStr . ")";
				$res = $this->dbConnection->exec($sql);
				unset($stockValidStr);
				unset($sql);
				$this->loggerfile->info($this->cronFileLabel . "insert-ignore processed-status result = " . $res);
		
				$sql = "insert ignore into `v2_monitoring_stock_job` (`id`, `memory_usage_megabytes`, `process_duration_seconds`, `api_call_duration_seconds`, `limit_data_to_api`, `total_data_from_api_received`, `total_data_from_api_received_valid`, `total_data_from_api_received_invalid`, `total_data_from_api_saved`, `total_data_from_api_updated_to_magento_stock`, `status`) values (sysdate(6), " . round(memory_get_usage() / 1048576, 2) . "," . (microtime(true) - $startTime) . ", {$apiCallDuration}, {$limitDataToApi}, {$totalDataFromApiReceived}, " . ($totalDataFromApiReceived - $totalDataFromApiReceivedInvalid) . ", {$totalDataFromApiReceivedInvalid}, {$totalDataFromApiSaved}, {$totalDataFromApiUpdatedToMagentoStock}, 'success')";
				$res = $this->dbConnection->exec($sql);
				$this->loggerfile->info($this->cronFileLabel . "insert-ignore monitoring-stock-job result = " . $res);		
			}
		
			$sql = "set session innodb_lock_wait_timeout = @saved_lock_wait";
            $this->dbConnection->exec($sql);
			
            $this->dbConnection->commit();

		}
		catch (WarningException $ex) {       
            $exceptionFound = true;     
            $logMessageTopic = "warning";
            $logMessage = $ex->getMessage();
            $logLevel = IntegrationCronLogToDatabase::LEVEL_WARNING;
		}
		catch (ErrorException $ex) {        
            $exceptionFound = true;    
            $logMessageTopic = "error";
            $logMessage = $ex->getMessage();
            $logLevel = IntegrationCronLogToDatabase::LEVEL_ERROR;
		}
		catch (FatalException $ex) {
            $exceptionFound = true;	        
            $logMessageTopic = "fatal-error";
            $logMessage = $ex->getMessage();
            $logLevel = IntegrationCronLogToDatabase::LEVEL_ERROR_FATAL;
		}		
		catch (\Exception $ex) {
            $exceptionFound = true;    
            $logMessageTopic = "generic-error";
            $logMessage = $ex->getMessage();
            $logLevel = IntegrationCronLogToDatabase::LEVEL_ERROR_GENERIC;
        }
        finally {
            if ($exceptionFound == true) {
                $this->dbConnection->rollback();

                try {
                    $sql = "set session innodb_lock_wait_timeout = @saved_lock_wait";
                    $this->dbConnection->exec($sql);

                    $this->loggerfile->info($this->cronFileLabel . $logMessageTopic . " = " . $logMessage);
                    $this->loggerdb->log($this->cronType, $this->cronTypeDetail, $logLevel, $logMessageTopic, $logMessage);    
                }
                catch (\Exception $exInner) {
                    $this->loggerfile->info($this->cronFileLabel . $logMessageTopic . " = " . $logMessage);
                    $this->loggerdb->log($this->cronType, $this->cronTypeDetail, $logLevel, $logMessageTopic, $logMessage);    

                    $logMessageTopicInner = "generic-error";
                    $logMessageInner = $exInner->getMessage();
                    $logLevelInner = IntegrationCronLogToDatabase::LEVEL_ERROR_GENERIC;
                    $this->loggerfile->info($this->cronFileLabel . $logMessageTopicInner . " = " . $logMessageInner);
                    $this->loggerdb->log($this->cronType, $this->cronTypeDetail, $logLevelInner, $logMessageTopicInner, $logMessageInner);
                }

                try {					
                    $sql = "insert ignore into `v2_monitoring_stock_job` (`id`, `memory_usage_megabytes`, `process_duration_seconds`, `api_call_duration_seconds`, `limit_data_to_api`, `total_data_from_api_received`, `total_data_from_api_received_valid`, `total_data_from_api_received_invalid`, `total_data_from_api_saved`, `total_data_from_api_updated_to_magento_stock`, `status`, `message`) values (sysdate(6), " . round(memory_get_usage() / 1048576, 2) . "," . (microtime(true) - $startTime) . ", {$apiCallDuration}, {$limitDataToApi}, {$totalDataFromApiReceived}, " . ($totalDataFromApiReceived - $totalDataFromApiReceivedInvalid) . ", {$totalDataFromApiReceivedInvalid}, {$totalDataFromApiSaved}, {$totalDataFromApiUpdatedToMagentoStock}, '{$logMessageTopic}', '" . addslashes($ex->getMessage()) . "')";
                    $res = $this->dbConnection->exec($sql);
                    
                    $this->loggerfile->info($this->cronFileLabel . "insert-ignore monitoring-stock-job result = " . $res);
                }
                catch (\Exception $exInner) {
                    $logMessageTopicInner = "generic-error";
                    $logMessageInner = $exInner->getMessage();
                    $logLevelInner = IntegrationCronLogToDatabase::LEVEL_ERROR_GENERIC;
                    $this->loggerfile->info($this->cronFileLabel . $logMessageTopicInner . " = " . $logMessageInner);
                    $this->loggerdb->log($this->cronType, $this->cronTypeDetail, $logLevelInner, $logMessageTopicInner, $logMessageInner);
                }
            }            
        }


        if ($apiCallDuration >= 0) {
            $this->loggerfile->info($this->cronFileLabel . "api-call duration = " . $apiCallDuration . " seconds");
        }

        $logMessageTopic = "finish";
        $logMessage = "memory usage = " . round(memory_get_usage() / 1048576, 2) . " megabytes - duration = " . (microtime(true) - $startTime) . " seconds";
        $this->loggerfile->info($this->cronFileLabel . $logMessageTopic . " = " . $logMessage);
        $this->loggerdb->info($this->cronType, $this->cronTypeDetail, $logMessageTopic, $logMessage);

    }

}