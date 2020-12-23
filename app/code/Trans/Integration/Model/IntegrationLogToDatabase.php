<?php
/**
 * @category Trans
 * @package  Trans_Integration
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author Hariadi Wicaksana <hariadi_wicaksana@transretail.co.id>
 *
 * Copyright © 2020 PT Trans Retail Indonesia. All rights reserved.
 * http://carrefour.co.id
 */

namespace Trans\Integration\Model;

use Trans\Integration\Api\IntegrationLogToDatabaseInterface;


class IntegrationLogToDatabase implements IntegrationLogToDatabaseInterface
{

    /**
     * @var ResourceConnection
     */
	public $dbConnection;

    /**
     * IntegrationLogToDatabase constructor.
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     */
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resourceConnection
    ) {
        $this->dbConnection = $resourceConnection->getConnection();
    }

    /**
     * @param string $cronType
     * @param string $cronTypeDetail
     * @param int $level
     * @param string $messageTopic
     * @param string $messageDetail
     * @return mixed
     */
    public function logCron($cronType, $cronTypeDetail, $level, $messageTopic, $messageDetail)
    {
     
        try {
            
            $str = "insert into `integration_log` (`cron_type`, `cron_type_detail`, `level`, `message_topic`, `message_detail`) values ('%s', '%s', %d, '%s', '%s')";
    
            $sql = sprintf($str, $cronType, $cronTypeDetail, $level, $messageTopic, $messageDetail);
            
            return $this->dbConnection->exec($sql);

        } 
        catch (\Exception $ex) {
        }

    }

    /**
     * @param string $cronType
     * @param string $cronTypeDetail
     * @param string $messageTopic
     * @param string $messageDetail
     * @return mixed
     */
    public function logCronInfo($cronType, $cronTypeDetail, $messageTopic, $messageDetail)
    {
     
        return $this->logCron($cronType, $cronTypeDetail, IntegrationLogToDatabaseInterface::LOG_CRON_LEVEL_INFO, $messageTopic, $messageDetail);

    }

    /**
     * @param string $cronType
     * @param string $cronTypeDetail
     * @param string $messageTopic
     * @param string $messageDetail
     * @return mixed
     */
    public function logCronWarning($cronType, $cronTypeDetail, $messageTopic, $messageDetail)
    {
     
        return $this->logCron($cronType, $cronTypeDetail, IntegrationLogToDatabaseInterface::LOG_CRON_LEVEL_WARNING, $messageTopic, $messageDetail);

    }
    
    /**
     * @param string $cronType
     * @param string $cronTypeDetail
     * @param string $messageTopic
     * @param string $messageDetail
     * @return mixed
     */
    public function logCronError($cronType, $cronTypeDetail, $messageTopic, $messageDetail)
    {
     
        return $this->logCron($cronType, $cronTypeDetail, IntegrationLogToDatabaseInterface::LOG_CRON_LEVEL_ERROR, $messageTopic, $messageDetail);

    }
    
    /**
     * @param string $cronType
     * @param string $cronTypeDetail
     * @param string $messageTopic
     * @param string $messageDetail
     * @return mixed
     */
    public function logCronErrorFatal($cronType, $cronTypeDetail, $messageTopic, $messageDetail)
    {
     
        return $this->logCron($cronType, $cronTypeDetail, IntegrationLogToDatabaseInterface::LOG_CRON_LEVEL_ERROR_FATAL, $messageTopic, $messageDetail);

    }    

}