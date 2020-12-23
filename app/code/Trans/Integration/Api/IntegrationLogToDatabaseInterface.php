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

namespace Trans\IntegrationCatalogStock\Api;

interface IntegrationLogToDatabaseInterface {
	
    /**
     * logcron level
     */
    const LOG_CRON_LEVEL_INFO = 1;
    const LOG_CRON_LEVEL_WARNING = 2;
    const LOG_CRON_LEVEL_ERROR = 3;
    const LOG_CRON_LEVEL_ERROR_FATAL = 4;

    /**
     * @param string $cronType
     * @param string $cronTypeDetail
     * @param int $level
     * @param string $messageTopic
     * @param string $messageDetail
     * @return mixed
     */
    public function logCron($cronType, $cronTypeDetail, $level, $messageTopic, $messageDetail);

    /**
     * @param string $cronType
     * @param string $cronTypeDetail
     * @param string $messageTopic
     * @param string $messageDetail
     * @return mixed
     */
    public function logCronInfo($cronType, $cronTypeDetail, $messageTopic, $messageDetail);

    /**
     * @param string $cronType
     * @param string $cronTypeDetail
     * @param string $messageTopic
     * @param string $messageDetail
     * @return mixed
     */
    public function logCronWarning($cronType, $cronTypeDetail, $messageTopic, $messageDetail);    

    /**
     * @param string $cronType
     * @param string $cronTypeDetail
     * @param string $messageTopic
     * @param string $messageDetail
     * @return mixed
     */
    public function logCronError($cronType, $cronTypeDetail, $messageTopic, $messageDetail);
    
    /**
     * @param string $cronType
     * @param string $cronTypeDetail
     * @param string $messageTopic
     * @param string $messageDetail
     * @return mixed
     */
    public function logCronErrorFatal($cronType, $cronTypeDetail, $messageTopic, $messageDetail);
	
}