<?php
/**
 * @category Trans
 * @package  Trans_IntegrationNotification
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationNotification\Model;

use \Trans\IntegrationNotification\Api\IntegrationNotificationInterface;

/**
 * Class IntegrationNotification
 */
class IntegrationNotification implements IntegrationNotificationInterface
{
    /**
     * @var \Trans\IntegrationNotification\Api\Data\IntegrationNotificationLogInterfaceFactory
     */
    protected $notificationLog;

    /**
     * @var \Trans\IntegrationNotification\Api\IntegrationNotificationLogRepositoryInterface
     */
    protected $logRepository;

    /**
     * @var \Trans\IntegrationNotification\Helper\Config
     */
    protected $config;

    /**
     * @var \Trans\Integration\Helper\Curl
     */
    protected $curlHelper;

    /**
     * @var \Trans\Core\Helper\Data
     */
    protected $dataHelper;

    /**
     * @param \Trans\IntegrationNotification\Api\Data\IntegrationNotificationLogInterfaceFactory $notificationLog
     * @param \Trans\IntegrationNotification\Api\IntegrationNotificationLogRepositoryInterface $LogRepository
     * @param \Trans\IntegrationNotification\Helper\Config $configHelper
     * @param \Trans\Integration\Helper\Curl $curlHelper
     * @param \Trans\Core\Helper\Data $dataHelper
     */
    public function __construct(
        \Trans\IntegrationNotification\Api\Data\IntegrationNotificationLogInterfaceFactory $notificationLog,
        \Trans\IntegrationNotification\Api\IntegrationNotificationLogRepositoryInterface $logRepository,
        \Trans\IntegrationNotification\Helper\Config $config,
        \Trans\Integration\Helper\Curl $curlHelper,
        \Trans\Core\Helper\Data $dataHelper
    ) {
        $this->notificationLog = $notificationLog;
        $this->logRepository = $logRepository;
        $this->config = $config;
        $this->curlHelper = $curlHelper;
        $this->dataHelper = $dataHelper;
    }

    /**
     * @param string $telephone
     * @param string $body
     * @param bool $isOtp
     * @return mixed
     */
    public function sendSms(string $telephone, string $body, bool $isOtp = false)
    {
        $channel = 'sms';
        $url = $this->config->getApiPublishUrl();
        $key = $this->config->getAppKey();

        $param['telephone'] = $telephone;
        $param['content'] = $body;

        $body = $this->prepareBody($param, $channel, $isOtp);
        $message = $this->createMessage($body);
        $bearer = base64_encode($key . $message . $key);

        $header = [
            'Content-Type' => 'application/json',
            'Authorization' => $bearer
        ];

        $result = $this->curlHelper->post($url, $header, $message);
        
        $this->saveLog($channel, $param, $message, $header, $result);
        
        return $result;
    }

    /**
     * @param string $emailTo
     * @param string $subject
     * @param string $body
     * @return mixed
     */
    public function sendEmail(string $emailTo, string $subject, string $body)
    {
        $channel = 'email';
        $url = $this->config->getApiPublishUrl();
        $key = $this->config->getAppKey();

        $param['emailTo'] = $emailTo;
        $param['subject'] = $subject;
        $param['content'] = $body;

        $body = $this->prepareBody($param, $channel);
        $message = $this->createMessage($body);
        $bearer = base64_encode($key . $message . $key);

        $header = [
            'Content-Type' => 'application/json',
            'Authorization' => $bearer
        ];

        $result = $this->curlHelper->post($url, $header, $message);
        
        $this->saveLog($channel, $param, $message, $header, $result);

        return $result;
    }

    /**
     * @param string[] $deviceIds array
     * @param string $title
     * @param string $body
     * @return mixed
     */
    public function pushNotif($deviceIds, string $title, string $body)
    {
        $channel = 'pushnotif';
        $url = $this->config->getApiPublishUrl();
        $key = $this->config->getAppKey();
        
        $param['device_ids'] = $deviceIds;
        $param['title'] = $title;
        $param['content'] = $body;

        $body = $this->prepareBody($param, $channel);
        $message = $this->createMessage($body);
        $bearer = base64_encode($key . $message . $key);

        $header = [
            'Content-Type' => 'application/json',
            'Authorization' => $bearer
        ];

        $result = $this->curlHelper->post($url, $header, $message);
        
        $this->saveLog($channel, $param, $message, $header, $result);

        return $result;
    }

    /**
     * save notification log
     *
     * @param string $channel
     * @param array $param
     * @param string $body
     * @param string $header
     * @param string $result
     * @return void
     */
    protected function saveLog(string $channel, array $param, string $body, array $header, string $result)
    {
        try {
            $param['header'] = json_encode($header);

            $data = $this->notificationLog->create();
            $data->setChannel($channel);
            $data->setParam(json_encode($param));
            $data->setParamEncrypt($body);
            $data->setResponse($result);
            $this->logRepository->save($data);
        } catch (\Exception $e) {
            //nothing
        }
    }

    /**
     * create message
     *
     * @param string $body
     * @return string
     */
    protected function createMessage(string $body)
    {
        $message['messages'][] = ['data' => $body];

        return json_encode($message);
    }

    /**
     * prepare data body
     *
     * @param array $param
     * @param string $channel
     * @param mixed $isOtp
     * @return string
     */
    protected function prepareBody(array $param, string $channel, $isOtp = '')
    {
        $data['channel'] = $channel;
        $data['origin'] = 'magento-andromeda';

        switch ($channel) {
            case 'sms':
                $data['sms'] = array(
                  'phone' => $param['telephone'],
                  'message' => $param['content'],
                  'channel' => (string)"1"
                );

                if ($isOtp) {
                    $data['sms']['channel']  = (string)"2";
                }
                break;
            
            case 'email':
                $data['email'] = array(
                    'address' => $param['emailTo'],
                    'subject' => $param['subject'],
                    'type' => 'html',
                    'body' => $param['content']
                );
                break;

            case 'pushnotif':
                $data['pushnotif'] = array(
                    'devices' => $param['device_ids'],
                    "title" => $param['title'],
                    "body" => $param['content']
                );
                break;
        }
        
        $json = $this->getJson()->serialize($data);
        
        $request = base64_encode($json);
        return $request;
    }

    /**
     * get json
     *
     * @return \Magento\Framework\Serialize\Serializer\Json
     */
    protected function getJson()
    {
        return $this->dataHelper->getJson();
    }
}
