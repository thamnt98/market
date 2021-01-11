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


    public function __construct(
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Trans\IntegrationCatalogStock\Logger\Logger $loggerfile,
        \Trans\Integration\Model\IntegrationCronLogToDatabase $loggerdb
    ) {
        $this->timezone = $timezone;
        $this->loggerfile = $loggerfile;
        $this->loggerdb = $loggerdb;
    }
    

    public function execute() {

        $startTime = microtime(true);
        
        $cronType = "stock";
        $cronTypeDetail = "sync";
        $cronLabel = $cronType . "-" . $cronTypeDetail;

        $logMessageTopic = "start";
        $logMessage = "start";
        $this->loggerfile->info($cronLabel . $logMessageTopic . " = " . $logMessage);
        $this->loggerdb->info($cronType, $cronTypeDetail, $logMessageTopic, $logMessage);

        try {
            
       
        }
        catch (WarningException $ex) {
            $logMessageTopic = "warning-exception";
            $logMessage = $ex->getMessage();
            $this->loggerfile->info($cronLabel . $logMessageTopic . " = " . $logMessage);
            $this->loggerdb->warn($this->cronType, $this->cronTypeDetail, $logMessageTopic, $logMessage);
        }
        catch (ErrorException $ex) {
            $logMessageTopic = "error-exception";
            $logMessage = $ex->getMessage();
            $this->loggerfile->info($cronLabel . $logMessageTopic . " = " . $logMessage);
            $this->loggerdb->error($this->cronType, $this->cronTypeDetail, $logMessageTopic, $logMessage);
        }
        catch (FatalException $ex) {
            $logMessageTopic = "fatal-exception";
            $logMessage = $ex->getMessage();
            $this->loggerfile->info($cronLabel . $logMessageTopic . " = " . $logMessage);
            $this->loggerdb->fatal($this->cronType, $this->cronTypeDetail, $logMessageTopic, $logMessage);
        }
        catch (\Exception $ex) {
            $logMessageTopic = "generic-exception";
            $logMessage = $ex->getMessage();
            $this->loggerfile->info($cronLabel . $logMessageTopic . " = " . $logMessage);
            $this->loggerdb->fatal($this->cronType, $this->cronTypeDetail, $logMessageTopic, $logMessage);
        }

        $logMessageTopic = "finish";
        $logMessage = "finish in " . (microtime(true) - $startTime) . " second";
        $this->loggerfile->info($cronLabel . $logMessageTopic . " = " . $logMessage);
        $this->loggerdb->info($cronType, $cronTypeDetail, $logMessageTopic, $logMessage);

    }

}