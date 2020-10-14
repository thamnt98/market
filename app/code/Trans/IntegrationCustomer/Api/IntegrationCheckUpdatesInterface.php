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

namespace Trans\IntegrationCustomer\Api;

interface IntegrationCheckUpdatesInterface
{
    /**
     * Error Message
     */
    const MSG_DATA_NOTAVAILABLE= 'No Data available for update';
    const MSG_RESP_NOTAVAILABLE= 'Response Count key is not available';
    const MSG_CHANNEL_NOTAVAILABLE= 'Channel Data method is undefine';
    const CRON_DIRECTORY = "Trans\IntegrationCustomer\Cron";

    /**
     * @param $tag
     * @return mixed
     */
    public function prepareCall($tag);


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
    public function checkOnProgressJobs($channel);


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
    public function getWaitingJobs($channel);
}