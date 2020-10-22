<?php
/**
 * @category Trans
 * @package  Trans_FavoriteProduct
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   J.P <jaka.pondan@ctcorpdigital.com>
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 *
 * Copyright © 2019 CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Integration\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\StateException;
use Zend\Http\Client;

class Curl extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var  Context
     */
    protected $context;

    /**
     * @var Client
     */
    protected $zendClient;

    /**
     * @var \Magento\Framework\HTTP\Client\Curl
     */
    protected $curl;

    /**
     * @var Trans\Integration\Helper\Config
     */
    protected $config;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTimeFactory
     */
    protected $dateTimeFactory;

    /**
     * @param Zend\Http\Client $zenClient
     * @param \Magento\Framework\HTTP\Client\Curl $curl
     * @param Magento\Framework\App\Helper\Context $context
     * @param Trans\Integration\Helper\Config $config
     * @param
     */
    public function __construct(
        Context $context,
        Client $zendClient,
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Magento\Framework\Stdlib\DateTime\DateTimeFactory $dateTimeFactory,
        \Trans\Integration\Helper\Config $config
    ) {
        parent::__construct($context);
        $this->zendClient      = $zendClient;
        $this->curl            = $curl;
        $this->dateTimeFactory = $dateTimeFactory;
        $this->config          = $config;
    }

    /**
     * generate auth token
     *
     * @return string
     */
    public function getCentralizeAuthToken()
    {
        $key       = $this->config->getCentralizeApiKey();
        $dateModel = $this->dateTimeFactory->create();
        $datetime  = $dateModel->gmtDate('YmdHi');
        var_dump($datetime);
        $authToken = strtolower(md5($key . $datetime));

        return $authToken;
    }

    /**
     * Get Reponse In Json
     * @param string $url
     * @param string $headers
     * @param string $queryParam
     * @return mixed
     * @throws StateException
     */
    public function get($url = "", $headers = "", $queryParam = "")
    {

        if (empty($url)) {
            throw new StateException(__(
                'Url Cannot be empty'
            ));
        }
        try {
            $this->prepare($url, $headers, $queryParam);
            $result = $this->call();
        } catch (\Exception $ex) {
            throw new StateException(__(
                $ex->getMessage()
            ));
        }

        return $result;
    }

    /**
     * Get Reponse In Json
     * @param string $url
     * @param string $headers
     * @param string $body
     * @return mixed
     * @throws StateException
     */
    public function getNew($url = "", $headers = "", $body = "")
    {

        if (empty($url)) {
            throw new StateException(__(
                'Url Cannot be empty'
            ));
        }
        try {
            $this->prepareGet($url, $headers, $body);
            $result = $this->call();
        } catch (\Exception $ex) {
            throw new StateException(__(
                $ex->getMessage()
            ));
        }

        return $result;
    }

    /**
     * Get Reponse In Json from TPL
     * @param string $url
     * @param string $headers
     * @return mixed
     * @throws StateException
     */
    public function getTpl($url = "", $headers = "")
    {

        if (empty($url)) {
            throw new StateException(__(
                'Url Cannot be empty'
            ));
        }
        try {
            $this->prepareDataTpl($url, $headers);
            $result = $this->call();
        } catch (\Exception $ex) {
            throw new StateException(__(
                $ex->getMessage()
            ));
        }

        return $result;
    }

    /**
     * @param string $url
     * @param string $headers
     * @param string $body
     * @return mixed|string
     * @throws StateException
     */
    public function post($url = "", $headers = "", $body = "")
    {

        if (empty($url)) {
            throw new StateException(__(
                'Url Cannot be empty'
            ));
        }
        try {
            /**new version
            **/
            $headers = empty($headers) ? [] : $headers;
            $result  = $this->curlPost($url, $headers, $body);

            /** old version
            $this->preparePost($url, $headers, $body);
            $result = $this->call();
             **/
        } catch (\Exception $ex) {
            throw new StateException(__(
                $ex->getMessage()
            ));
        }

        return $result;
    }

    /**
     * Curl POST
     *
     * @param string $url
     * @param array $headers
     * @param array|string $params
     */
    protected function curlPost($url = "", $headers = "", $params = "")
    {
        try {
            $this->curl->setOption(CURLOPT_RETURNTRANSFER, true);
            $this->curl->setOption(CURLOPT_TIMEOUT, 30);
            $this->curl->setOption(CURLOPT_SSL_VERIFYHOST, false);
            $this->curl->setOption(CURLOPT_SSL_VERIFYPEER, false);
            $this->curl->setHeaders($headers);
            $this->curl->post($url, $params);

            if ($this->curl->getStatus() != \Zend\Http\Response::STATUS_CODE_200) {
                throw new StateException(__(
                    $this->curl->getBody()
                ));
            }

            return $response = $this->curl->getBody();
        } catch (\Zend\Http\Exception\RuntimeException $runtimeException) {
            throw new StateException(__(
                $runtimeException->getMessage()
            ));
        }
    }

    /**
     * @return mixed|string
     * @throws StateException
     */
    protected function call()
    {
        try {
            $this->zendClient->send();
            $response = $this->zendClient->getResponse();
            if ($response->getStatusCode() != \Zend\Http\Response::STATUS_CODE_200) {
                throw new StateException(__(
                    $response->getBody()
                ));
            }
        } catch (\Zend\Http\Exception\RuntimeException $runtimeException) {
            throw new StateException(__(
                $runtimeException->getMessage()
            ));
        }
        return $response->getBody();
    }

    /**
     * @param string $url
     * @param array $headers
     * @param array $queryParam
     * @throws StateException
     */
    protected function prepare($url = "", $headers = [], $queryParam = [])
    {

        try {
            $this->zendClient->reset();
            $this->zendClient->setUri($url);
            $this->zendClient->setMethod(\Zend\Http\Request::METHOD_GET);

            if (!empty($headers)) {
                $headers = $this->setParams($headers);
                $this->zendClient->setHeaders($headers);
            }

            $options = ['maxredirects' => 0, 'timeout' => 30, 'sslverifypeer' => false];
            if (!empty($options)) {
                $this->zendClient->setOptions($options);
            }

            if (!empty($queryParam)) {
                $this->zendClient->setParameterGet($queryParam);
            }
        } catch (\Zend\Http\Exception\InvalidArgumentException $argumentException) {
            throw new StateException(__(
                $argumentException->getMessage()
            ));
        }
    }

    /**
     * @param string $url
     * @param string $headers
     * @param string $body
     * @throws StateException
     */
    protected function prepareGet($url = "", $headers = "", $body = "")
    {

        try {
            $this->zendClient->reset();
            $this->zendClient->setUri($url);
            $this->zendClient->setMethod(\Zend\Http\Request::METHOD_GET);

            if (!empty($headers)) {
                $headers = $this->setParams($headers);
                $this->zendClient->setHeaders($headers);
            }

            $options = ['maxredirects' => 0, 'timeout' => 30, 'sslverifypeer' => false];
            if (!empty($options)) {
                $this->zendClient->setOptions($options);
            }

            if (!empty($body)) {
                $this->zendClient->setRawBody($body);
            }
        } catch (\Zend\Http\Exception\InvalidArgumentException $argumentException) {
            throw new StateException(__(
                $argumentException->getMessage()
            ));
        }
    }

    /**
     * @param string $url
     * @param string $headers
     * @throws StateException
     */
    protected function prepareDataTpl($url = "", $headers = "")
    {

        try {
            $this->zendClient->reset();
            $this->zendClient->setUri($url);
            $this->zendClient->setMethod(\Zend\Http\Request::METHOD_GET);

            if (!empty($headers)) {
                $headers = $this->setParams($headers);
                $this->zendClient->setHeaders($headers);
            }

            $options = ['maxredirects' => 0, 'timeout' => 30, 'sslverifypeer' => false];
            if (!empty($options)) {
                $this->zendClient->setOptions($options);
            }
        } catch (\Zend\Http\Exception\InvalidArgumentException $argumentException) {
            throw new StateException(__(
                $argumentException->getMessage()
            ));
        }
    }

    /**
     * @param string $url
     * @param array $headers
     * @param array $queryParam
     * @throws StateException
     */
    protected function preparePost($url = "", $headers = [], $body = [], $formdata = false)
    {

        try {
            $this->zendClient->reset();
            $this->zendClient->setUri($url);
            $this->zendClient->setMethod(\Zend\Http\Request::METHOD_POST);

            if (!empty($headers)) {
                $headers = $this->setParams($headers);
                $this->zendClient->setHeaders($headers);
            }

            $options = ['maxredirects' => 0, 'timeout' => 30, 'sslverifypeer' => false];
            if (!empty($options)) {
                $this->zendClient->setOptions($options);
            }

            if (!empty($body)) {
                if ($formdata) {
// set form data
                    $this->zendClient->setParameterPost($body);
                } else {
                    //Raw Query
                    $this->zendClient->setRawBody($body);
                }
            }
        } catch (\Zend\Http\Exception\InvalidArgumentException $argumentException) {
            throw new StateException(__(
                $argumentException->getMessage()
            ));
        }
    }

    /**
     * @param $param
     * @return mixed|string
     */
    public function jsonToArray($param)
    {

        try {
            if (empty($param)) {
                return [];
            }
            $result = json_decode($param, true);
        } catch (\Exception $ex) {
            throw new StateException(
                __($ex->getMessage())
            );
        }
        return $result;
    }

    /**
     * @param array $param
     * @throws StateException
     */
    private function setParams($param = array())
    {
        if (!is_array($param)) {
            return $param;
        }
        $check = array_filter($param);
        if (empty($check)) {
            return $param;
        }

        return $this->generateJsonToArray($param);
    }

    /**
     * Different Output array with number / no string key.
     * @param $param
     * @return array
     */
    private function generateJsonToArray($param)
    {
        $query = [];
        $i     = 0;
        foreach ($param as $key => $row) {
            $query[] = $key . ':' . $row;
            $i++;
        }
        return $query;
    }
}
