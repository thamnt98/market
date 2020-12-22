<?php
/**
 * @category Trans
 * @package  Trans_Integration
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   J.P <jaka.pondan@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Integration\Api;


interface IntegrationCommonInterface
{
    /**
     * Error Message
     */
    const MSG_UPDATE_ONPROGRESS= 'Cron Update Data still on progress';
    const MSG_CHANNEL_NOTAVAILABLE='Channel is not available';
    const MSG_METHOD_NOTAVAILABLE='Method is not available';
    const MSG_JOB_NOTAVAILABLE='Jobs is not available';
    const MSG_DATA_NOTAVAILABLE='DATA is not available';
    /**
     * @param $tag
     * @return mixed
     */
    public function prepareChannel($tag);

    /**
     * @param $data
     * @return mixed
     */
    public function prepareAuth($data);

    /**
     * @param $data
     * @return mixed
     */
    public function prepareRequest($channel,$data);

    /**
     * @param $data
     * @return mixed
     */
    public function get($data);

    /**
     * @param $data
     * @return mixed
     */
    public function post($data);

    /**
     * @param $tag
     * @return mixed
     */
    public function prepareChannelMultiTag($tag);

    /**
     * @param string $tag
     * @return array
     */
    public function prepareChannelUsingRawQuery($tag);

    /**
     * @param array $data
     * @return array
     */
    public function doCallUsingRawQuery($data);

}