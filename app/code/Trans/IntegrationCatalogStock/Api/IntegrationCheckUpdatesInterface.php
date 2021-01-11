<?php
/**
 * @category Trans
 * @package  Trans_CatalogStock
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   J.P <jaka.pondan@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationCatalogStock\Api;

interface IntegrationCheckUpdatesInterface
{
    /**
     * Error Message
     */
    const MSG_DATA_NOTAVAILABLE= 'No Data available for update';
    const MSG_RESP_NOTAVAILABLE= 'Response Count key is not available';
    const MSG_CHANNEL_NOTAVAILABLE= 'Channel Data method is undefine';
    const CRON_DIRECTORY = "Trans\IntegrationCatalogStock\Cron";

    /**
     * @param $tag
     * @return mixed
     */
    public function prepareCall($tag);

    /**
     * @param $tag
     * @return mixed
     */
    public function prepareCallWithoutCount($tag);

    /**
     * @param $channel
     * @param $response
     * @param $date
     * @return mixed
     */
    public function prepareJobsData($channel,$response);

    /**
     * @param $data
     * @return mixed
     */
    public function save($data);

    /**
     * @param $channel
     * @return mixed
     */
    public function checkWaitingJobs($channel);

    /**
     * @param $channel
     * @return mixed
     */
    public function checkOnProgressJobs($channel);

    /**
     * @param $channel
     * @return mixed
     */
    public function checkSaveOnProgressJob($channel);


    /**
     * @param $channel
     * @param $response
     * @return mixed
     */
    public function prepareJobsDataProperResp($channel,$response);

    /**
     * @param $data
     * @return mixed
     */
    public function saveProperOffset($data);

    /**
     * @param $channel
     * @return mixed
     */
    public function checkCompleteJobs($channel);

    /**
     * @param $channel
     * @return mixed
     */
    public function checkMultiCompleteJobs($channel);

    /**
     * @param $channel
     * @return mixed
     */
    public function checkReadyJobs($channel);

    /**
     * @param $channel
     * @param $response
     * @return mixed
     */
    public function prepareJobsDataImsStockUpdate($channel,$response);

    /**
     * @param $dataValue
     * @return mixed
     */
    public function checkDataValue($dataValue);

    /**
     * @param $channel
     * @param $response
     * @return mixed
     */
    public function prepareJobsDataProperRespWithoutCount($channel,$response);


    /**
     * @param array $channel
     * @return mixed
     */
    public function checkOnProgressJobUsingRawQuery($channel);

    /**
     * @param array $channel
     * @return mixed
     */
    public function getLastCompleteJobUsingRawQuery($channel);

    /**
     * @param array $channel
     * @return array
     */
    public function prepareCallUsingRawQuery($channel);

     /**
     * @param array $channel
     * @param array $response
     * @return array
     */
    public function prepareJobCandidatesUsingRawQuery($channel, $response);

    /**
     * @param array $data
     * @return array
     */
    public function insertJobCandidatesUsingRawQuery($data);

    /**
     * @param array $channel
     * @return mixed
     */
    public function checkOnProgressDataSavingJobUsingRawQuery($channel);

    /**
     * @param array $channel
     * @return mixed
     */
    public function getFirstDataReadyJobUsingRawQuery($channel);     
}