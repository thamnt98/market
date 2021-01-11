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


class IntegrationCronLogToDatabase
{

    /**
     * log level
     */
    public const LEVEL_INFO = 1;
    public const LEVEL_WARNING = 2;
    public const LEVEL_ERROR = 3;
    public const LEVEL_ERROR_FATAL = 4;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
	private $dbConnection;

    /**
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
    * @param int $logLevel
    * @param string $messageTopic
    * @param string $messageDetail
    * @return int
     */
    protected function log($cronType, $cronTypeDetail, $logLevel, $messageTopic, $messageDetail)
    {     
        try {
            
            $cols = "";
            $vals = "";

            if (!empty($cronType)) {
                $cols .= ",`cron_type`";
                $vals .= ",'{$cronType}'";
            }

            if (!empty($cronTypeDetail)) {
                $cols .= ",`cron_type_detail`";
                $vals .= ",'{$cronTypeDetail}'";
            }

            if (!empty($logLevel)) {
                $cols .= ",`level`";
                $vals .= "," . (int) $logLevel;
            }

            if (!empty($messageTopic)) {
                $cols .= ",`message_topic`";
                $vals .= ",'{$messageTopic}'";
            }

            if (!empty($messageDetail)) {
                $cols .= ",`message_detail`";
                $vals .= ",'{$messageDetail}'";
            }

            if ($cols != "") {
                $sql = "insert ignore into `integration_log` (`created_at`," . substr($cols, 1) . ") values (sysdate(6)," . substr($vals, 1) . ")";
                return $this->dbConnection->exec($sql);
            }
            
            return 0;

        } 
        catch (\Exception $ex) {
            return -1;
        }
    }

    /**
    * @param string $cronType
    * @param string $cronTypeDetail
    * @param string $messageTopic
    * @param string $messageDetail
    * @return int
     */
    public function info($cronType, $cronTypeDetail, $messageTopic, $messageDetail)
    {     
        return $this->log($cronType, $cronTypeDetail, $this::LEVEL_INFO, $messageTopic, $messageDetail);
    }

    /**
    * @param string $cronType
    * @param string $cronTypeDetail
    * @param string $messageTopic
    * @param string $messageDetail
    * @return int
     */
    public function warn($cronType, $cronTypeDetail, $messageTopic, $messageDetail)
    {     
        return $this->log($cronType, $cronTypeDetail, $this::LEVEL_WARNING, $messageTopic, $messageDetail);
    }
    
    /**
    * @param string $cronType
    * @param string $cronTypeDetail
    * @param string $messageTopic
    * @param string $messageDetail
    * @return int
     */
    public function error($cronType, $cronTypeDetail, $messageTopic, $messageDetail)
    {     
        return $this->log($cronType, $cronTypeDetail, $this::LEVEL_ERROR, $messageTopic, $messageDetail);
    }
    
    /**
    * @param string $cronType
    * @param string $cronTypeDetail
    * @param string $messageTopic
    * @param string $messageDetail
    * @return int
     */
    public function fatal($cronType, $cronTypeDetail, $messageTopic, $messageDetail)
    {     
        return $this->log($cronType, $cronTypeDetail, $this::LEVEL_ERROR_FATAL, $messageTopic, $messageDetail);
    }

}