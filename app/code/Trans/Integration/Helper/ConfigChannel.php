<?php
/**
 * @category Trans
 * @package  Trans_Integration
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.co.id>
 *
 * Copyright © 2019 CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Integration\Helper;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Config
 */
class ConfigChannel extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Constant config path
     * CHannel URL
     */
    const CENTRALIZE_API_ENV = 'integration_channel/environment';
    const CHANNEL_URL_DEV = "integration_channel/%s/base_url/develop";
    const CHANNEL_URL_PROD = "integration_channel/%s/base_url/production";
    const CHANNEL_DEFAULT_STATUS = "integration_channel/default_status";
    const CHANNEL_DEFAULT_AUTHOR = "integration_channel/default_author";
    const CHANNEL_DEFAULT_LIMIT = "integration_channel/default_limit";
    const CHANNEL_DEFAULT_CHANNEL_NAME = "integration_channel/%s/name";
    const CHANNEL_DEFAULT_CODE = "integration_channel/%s/code";
    

    /**
     * MEthod
     */
    const CHANNEL_DEFAULT_METHOD_CHANNEL_ID = "integration_channel/%s/channel_method_%s/channel_id";
    const CHANNEL_DEFAULT_CHANNEL_DESC = "integration_channel/%s/channel_method_%s/description";
    const CHANNEL_DEFAULT_METHOD_TAG = "integration_channel/%s/channel_method_%s/tag";
    const CHANNEL_DEFAULT_METHOD_METHOD = "integration_channel/%s/channel_method_%s/method";
    const CHANNEL_DEFAULT_METHOD_HEADERS = "integration_channel/%s/channel_method_%s/headers";
    const CHANNEL_DEFAULT_METHOD_QUERY = "integration_channel/%s/channel_method_%s/query_params";
    const CHANNEL_DEFAULT_METHOD_BODY = "integration_channel/%s/channel_method_%s/body";
    const CHANNEL_DEFAULT_METHOD_PATH = "integration_channel/%s/channel_method_%s/path";




    /**
     * Get config value by path
     * 
     * @param string $path
     * @return mixed
     */
    public function getConfigValue($path)
    {
        return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE);
    }

    /**
     * is env production
     * 
     * @return bool
     */
    public function isProduction()
    {
        return $this->getConfigValue(self::CENTRALIZE_API_ENV);
    }

    /**
     * @param $path
     * @param $channel
     * @return string
     */
    public function getValueChannel($path , $channel)
    {
        if(!empty($path) && !empty($channel)) {
            return sprintf($path, $channel);
        }

        return "";
    }

    /**
     * @param $path
     * @param $channel
     * @param int $sequence
     * @return string
     */
    public function getValueChannelSeq($path , $channel,$sequence=1)
    {
        if(!empty($path) && !empty($channel)) {
            return sprintf($path, $channel,$sequence);
        }
        return "";
    }

    /**
     * Define Production
     *
     * @return bool
     */
    public function getChannelEnv()
    {
        if($this->isProduction()) {
            return "production";
        }
        return "development";
    }

    /**
     * Define Default CHannel Name
     *
     * @return value
     */
    public function getDefaultChannelName($channel)
    {

        $value = $this->getValueChannel(self::CHANNEL_DEFAULT_CHANNEL_NAME,$channel);
        if(empty($value)){
            return "";
        }
        return $this->getConfigValue($value);
    }

    /**
     * Define Default CHannel Name
     *
     * @return value
     */
    public function getDefaultCode($channel)
    {

        $value = $this->getValueChannel(self::CHANNEL_DEFAULT_CODE,$channel);
        if(empty($value)){
            return "";
        }
        return $this->getConfigValue($value);
    }

    /**
     * get centralize api url
     *
     * @return bool
     */
    public function getDefaultBaseUrl($channel)
    {
        $prod = $this->getValueChannel(self::CHANNEL_URL_PROD,$channel);
        $dev = $this->getValueChannel(self::CHANNEL_URL_DEV,$channel);
        if(empty($prod) && empty($dev)){
            return "";
        }
        if($this->isProduction()) {
            return $this->getConfigValue($prod);
        }
        return $this->getConfigValue($dev);
    }

    /**
     * Define Default Status
     *
     * @return status
     */
    public function getDefaultStatus()
    {
        return $this->getConfigValue(self::CHANNEL_DEFAULT_STATUS);
    }


    /**
     * Define Default Author
     *
     * @return author
     */
    public function getDefaultAuthor()
    {
        return $this->getConfigValue(self::CHANNEL_DEFAULT_AUTHOR);
    }

    /**
     * Define Default Author
     *
     * @return author
     */
    public function getDefaultLimit()
    {
        return $this->getConfigValue(self::CHANNEL_DEFAULT_LIMIT);
    }

    /**
     * Define Default CHannel Name
     *
     * @return value
     */
    public function getDefaultMethodChannelDesc($channel,$sequence=1)
    {
        $value = $this->getValueChannelSeq(self::CHANNEL_DEFAULT_CHANNEL_DESC,$channel,$sequence);
        if(empty($value)){
            return "";
        }
        return $this->getConfigValue($value);
    }

    /**
     * Define Default Method CHannel Id
     *
     * @return value
     */
    public function getDefaultMethodChannelId($channel,$sequence=1)
    {
        $value =  $this->getValueChannelSeq(self::CHANNEL_DEFAULT_METHOD_CHANNEL_ID,$channel,$sequence);

        if(empty($value)){
            return "";
        }
        return $this->getConfigValue($value);
    }

    /**
     * Define Default Method Tag
     *
     * @return value
     */
    public function getDefaultMethodTag($channel,$sequence=1)
    {
        $value =  $this->getValueChannelSeq(self::CHANNEL_DEFAULT_METHOD_TAG,$channel,$sequence);

        if(empty($value)){
            return "";
        }
        return $this->getConfigValue($value);
    }

    /**
     * Define Default Method Tag
     *
     * @return value
     */
    public function getDefaultMethod($channel,$sequence=1)
    {
        $value =  $this->getValueChannelSeq(self::CHANNEL_DEFAULT_METHOD_METHOD,$channel,$sequence);

        if(empty($value)){
            return "";
        }
        return $this->getConfigValue($value);
    }

    /**
     * Define Default Method Headers
     *
     * @return value
     */
    public function getDefaultMethodHeaders($channel,$sequence=1)
    {
        $value =  $this->getValueChannelSeq(self::CHANNEL_DEFAULT_METHOD_HEADERS,$channel,$sequence);

        if(empty($value)){
            return "";
        }
        return $this->getConfigValue($value);
    }

    /**
     * Define Default Method Headers
     *
     * @return value
     */
    public function getDefaultMethodQuery($channel,$sequence=1)
    {
        $value =  $this->getValueChannelSeq(self::CHANNEL_DEFAULT_METHOD_QUERY,$channel,$sequence);

        if(empty($value)){
            return "";
        }
        return $this->getConfigValue($value);
    }

    /**
     * Define Default Method Headers
     *
     * @return value
     */
    public function getDefaultMethodBody($channel,$sequence=1)
    {
        $value =  $this->getValueChannelSeq(self::CHANNEL_DEFAULT_METHOD_BODY,$channel,$sequence);

        if(empty($value)){
            return "";
        }
        return $this->getConfigValue($value);
    }

    /**
     * Define Default Method Headers
     *
     * @return value
     */
    public function getDefaultMethodPath($channel,$sequence=1)
    {
        $value =  $this->getValueChannelSeq(self::CHANNEL_DEFAULT_METHOD_PATH,$channel,$sequence);

        if(empty($value)){
            return "";
        }
        return $this->getConfigValue($value);
    }
}