<?php

declare(strict_types=1);

namespace SM\AndromedaApi\Model\Integration;

use GuzzleHttp\Client as GuzzleHttpClient;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Psr7\Request;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use SM\AndromedaApi\Helper\Config;
use SM\AndromedaSms\Api\Data\SmsVerification\VerifySms\TestMode\ResponseInterface as VerifySmsResponse;
use SM\AndromedaSms\Model\SmsVerification;

class Client
{
    const ANDROMEDA_API_VERSION = '1.0';
    const HTTP_GET = 'GET';
    const HTTP_POST = 'POST';
    const REQUEST_METHOD = 'request_method';
    const REQUEST_URI = 'request_uri';
    const REQUEST_BODY = 'request_body';
    const REQUEST_OPTIONS = 'request_options';
    const RESPONSE_CODE = 'response_code';
    const RESPONSE_BODY = 'response_body';

    /**
     * @var Preparator
     */
    protected $preparator;

    /**
     * @var JsonSerializer
     */
    protected $serializer;

    /**
     * @var GuzzleHttpClient
     */
    protected $client;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var \Trans\Integration\Helper\Curl
     */
    protected $apiCall;

    /**
     * @var \Trans\Integration\Helper\Config
     */
    protected $configApi;
    /**
     * @var \Trans\Customer\Model\AccountManagement
     */
    private $accountManagement;

    /**
     * Client constructor.
     * @param Preparator $preparator
     * @param JsonSerializer $serializer
     * @param LoggerInterface $logger
     * @param Config $config
     * @param \Trans\Integration\Helper\Curl $apiCall
     * @param \Trans\Integration\Helper\Config $configApi
     * @param \Trans\Customer\Model\AccountManagement $accountManagement
     */
    public function __construct(
        Preparator $preparator,
        JsonSerializer $serializer,
        LoggerInterface $logger,
        \SM\AndromedaApi\Helper\Config $config,
        \Trans\Integration\Helper\Curl $apiCall,
        \Trans\Integration\Helper\Config $configApi,
        \Trans\Customer\Model\AccountManagement $accountManagement
    ) {
        $this->preparator = $preparator;
        $this->serializer = $serializer;
        $this->logger = $logger;
        $this->config = $config;
        $this->apiCall = $apiCall;
        $this->configApi = $configApi;
        $this->client = new GuzzleHttpClient();
        $this->accountManagement = $accountManagement;
    }

    /**
     * @param string $returnType
     * @param string $apiPath
     * @param null|array $body
     * @param string $method
     * @param array $headers
     * @param array $options
     * @return DataObject
     * @throws \Exception
     */
    public function send(
        string $returnType,
        string $apiPath,
        string $method='GET',
        ?array $body=null,
        array $headers=[],
        array $options=[]
    ): DataObject {

        if ($this->config->isTestMode()) {
            //test mode
            try {
                $request = $this->createRequest($apiPath, $method, $headers, $body);
                $this->logRequest($request, $options);

                $response = $this->client->send($request, $options);

                // keep responseBody here, since we can not get body content any more after touch first time
                $responseBody = $response->getBody()->getContents();

                $this->logResponse($response);

                if ($response->getStatusCode() != 200) {
                    throw new LocalizedException(__('Error response code %1', $response->getStatusCode()));
                }

                /** @var DataObject $returnObject */
                $returnObject = ObjectManager::getInstance()->create($returnType);
                $returnObject->addData($this->serializer->unserialize($responseBody));

                return$returnObject;
            } catch (ClientException | ServerException | LocalizedException | \Exception $exception) {
                $this->logger->error($exception);

                throw new \Exception($exception->getMessage());
            }
        } else {
            // live mode
            try {

//                $body["auth_token"] = $this->apiCall->getCentralizeAuthToken();
//                if (is_array($body)) {
//                    $body = $this->serializer->serialize($body);
//                }
//
//                $response = $this->apiCall->post($this->preparator->getUri($apiPath), "", $body);

                /** @var DataObject $returnObject */
                $returnObject = ObjectManager::getInstance()->create($returnType);
                if ($returnType == VerifySmsResponse::class) { //Verify OTP
                    $verifyId = $body[SmsVerification::VERIFICATION_ID];
                    $verifyCode = $body[SmsVerification::VERIFICATION_CODE];
                    $response = $this->accountManagement->authVerification($verifyCode, $verifyId);

//                    $response = $this->serializer->unserialize($response);
//                    if ($response['is_verified'] == 0) {
//                        $verify = false;
//                    } else {
//                        $verify = true;
//                    }
                    $returnObject->addData(['is_verified' => $response]);
                } else { // Send OTP
                    $phone = $body[SmsVerification::PHONE_NUMBER];
                    $check = $body[SmsVerification::IS_CHECK_PHONE_NUMBER];
                    $response = $this->accountManagement->sendSmsVerification($phone, $check);
                    $response = $this->serializer->unserialize($response);
                    $returnObject->addData($response);
                }
                if ($returnObject->getCode() && $returnObject->getMessage()) {
                    throw new LocalizedException($returnObject->getMessage(), null, $returnObject->getCode());
                }
                return $returnObject;
            } catch (LocalizedException $err) {
                $this->logger->info('AndromedaApi Response Fail', ['code' => $err->getCode(), 'message' => $err->getMessage()]);
                throw new \Exception($err->getMessage());
            } catch (\Exception $err) {
                $this->logger->info('AndromedaApi Response Fail', ['code' => $err->getCode(), 'message' => $err->getMessage()]);
                throw new \Exception($err->getMessage());
            }
        }
    }

    /**
     * @param string $apiPath
     * @param string $method
     * @param array $headers
     * @param null|array $body
     * @return RequestInterface
     *
     * @throws LocalizedException
     *
     * @codeCoverageIgnore
     */
    protected function createRequest(
        string $apiPath,
        string $method,
        array $headers = [],
        ?array $body = null
    ): RequestInterface {
        $body = $this->preparator->attachAuthToken($body, $method);
        if (is_array($body)) {
            $body = $this->serializer->serialize($body);
        }
        $request = new Request(
            $method,
            $this->preparator->getUri($apiPath),
            $this->preparator->resolveHeaders($headers),
            $body,
            self::ANDROMEDA_API_VERSION
        );

        return $request;
    }

    /**
     * @param RequestInterface $request
     * @param array $options
     *
     * @codeCoverageIgnore
     */
    protected function logRequest(RequestInterface $request, array $options): void
    {
        $logData = [];
        $logData[self::REQUEST_METHOD] = $request->getMethod();
        $logData[self::REQUEST_URI] = $request->getUri();
        $logData[self::REQUEST_BODY] = \GuzzleHttp\Psr7\str($request);
        $logData[self::REQUEST_OPTIONS] = $options;

        $this->logger->info('AndromedaApi Request', $logData);
    }

    /**
     * @param ResponseInterface $response
     *
     * @codeCoverageIgnore
     */
    protected function logResponse(ResponseInterface $response): void
    {
        $logData = [];
        $logData[self::RESPONSE_CODE] = $response->getStatusCode();
        $logData[self::RESPONSE_BODY] = \GuzzleHttp\Psr7\str($response);

        $this->logger->info('AndromedaApi Response', $logData);
    }
}
