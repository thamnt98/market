<?php
/**
 * @category Trans
 * @package  Trans_IntegrationCatalog
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   J.P <jaka.pondan@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationCatalog\Api;

interface IntegrationGetUpdatesInterface
{
    /**
     * Error Messsage
     */
    const MSG_DATA_NOTAVAILABLE= 'No Data available for update';
    const MSG_RESP_NOTAVAILABLE= 'Response is not available';
    const MSG_CHANNEL_NOTAVAILABLE= 'Channel Data method is undefine';
    const MSG_JOBS_NOTAVAILABLE = 'Jobs is not available';
    const MSG_DATAVALUE_NOTAVAILABLE = 'Data value is not available';
    const CRON_DIRECTORY = "Trans\IntegrationCatalog\Cron";


    /**
     * @param $tag
     * @return mixed
     */
    public function prepareCall($tag);


    /**
     * @param $channel
     * @return mixed
     */
    public function checkWaitingJobs($channel);

    /**
     * @param $channel
     * @param $response
     * @return mixed
     */
    public function prepareData($channel,$response);

    /**
     * @param $channel
     * @param $response
     * @return mixed
     */
    public function save($channel,$data,$date);

    /**
     * @param $channel
     * @param $response
     * @return mixed
     */
    public function prepareDataProperResp($channel,$response);

    
    /**
     * @param $channel
     * @return mixed
     */
    public function getCompleteJobs($channel);

    /**
     * @param $channel
     * @param $status IntegrationJobInterface::Status
     * @param $message
     * @return mixed
     */
    public function setJobStatus($channel,$status,$message);

    
}