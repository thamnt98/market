<?php
/**
 * @category Trans
 * @package  Trans_Customer
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Imam Kusuma <imam.kusuma@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Customer\Model;

use Trans\Customer\Api\AccountManagementInterface;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Exception\InputException;
use Magento\Customer\Model\AccountManagement as MageAccountManagement;

/**
 * @api
 */
class AccountManagement implements AccountManagementInterface
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Framework\Math\Random
     */
    protected $mathRandom;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $json;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory
     */
    protected $customerFactory;

    /**
     * @var \Magento\Customer\Model\AccountManagement
     */
    protected $accountManagement;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Trans\Integration\Logger\Logger
     */
    protected $logger;

    /**
     * @var \Trans\Integration\Helper\Curl
     */
    protected $apiCall;

    /**
     * @var \Trans\Integration\Helper\Config
     */
    protected $configApi;

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\Math\Random $mathRandom
     * @param \Magento\Framework\Serialize\Serializer\Json $json
     * @param \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param MageAccountManagement $accountManagement
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Trans\Integration\Logger\Logger $logger
     * @param \Trans\Integration\Helper\Curl $apiCall
     * @param \Trans\Integration\Helper\Config $configApi
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Math\Random $mathRandom,
        \Magento\Framework\Serialize\Serializer\Json $json,
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerFactory,
        \Magento\Framework\Registry $coreRegistry,
        MageAccountManagement $accountManagement,
        \Magento\Customer\Model\Session $customerSession,
        \Trans\Integration\Logger\Logger $logger,
        \Trans\Integration\Helper\Curl $apiCall,
        \Trans\Integration\Helper\Config $configApi
    ) {
        $this->request = $request;
        $this->mathRandom = $mathRandom;
        $this->accountManagement = $accountManagement;
        $this->json = $json;
        $this->coreRegistry = $coreRegistry;
        $this->customerSession = $customerSession;
        $this->customerFactory = $customerFactory;
        $this->logger = $logger;
        $this->apiCall = $apiCall;
        $this->configApi = $configApi;
    }

    /**
     * {@inheritdoc}
     */
    public function isTelephoneAvailable($telephone, $websiteId = null)
    {
        if (!strpos($telephone, '@') !== false) {
            /* Get email id based on mobile number and login*/
            $customereCollection = $this->customerFactory->create();
            $customereCollection->addFieldToFilter("telephone", $telephone);
            
            if ($customereCollection->getSize() > 0) {
                foreach ($customereCollection as $customerdata) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function authVerification($code, $verificationId)
    {
        $this->logger->info('----Start ' . __CLASS__ . ' ' . __FUNCTION__);
        if ($this->configApi->isEnableStaticOtp()) {
            $staticCode = $this->configApi->getStaticOtp();
            if ($staticCode) {
                if (in_array($code, $staticCode)) {
                    return true;
                }
            }
            
            return false;
        }

        $url = $this->configApi->getVerifyVerificationUrl();
        $body = $this->prepareVerify($code, $verificationId);

        try {
            $hit = $this->apiCall->post($url, "", $body);
            
            $this->logger->info($body);
            $this->logger->info($hit);
            
            $response = json_decode($hit, true);
            $result['status'] = $response['status'];
            if ($response['status'] === 1) {
                $this->customerSession->setVerified(true);
                $this->logger->info('----End ' . __CLASS__ . ' ' . __FUNCTION__);
                return true;
            }
            $result['message'] = __('Verification code is wrong');
        } catch (StateException $err) {
            $result['is_verified'] = 0;
            $result['message'] = __('Something wrong with the system. Please contact web administrator');
            $this->logger->info($err->getMessage());
        } catch (\Exception $err) {
            $result['is_verified'] = 0;
            $result['message'] = $err->getMessage();
            $this->logger->info($err->getMessage());
        }
        
        $this->logger->info('----End ' . __CLASS__ . ' ' . __FUNCTION__);
        $this->customerSession->setVerified(false);
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function sendSmsVerification($telephone, $isNeedCheck, $language = "id")
    {
        $isNeedCheck = 0;

        if ($this->configApi->isEnableStaticOtp()) {
            $staticCode = $this->configApi->getStaticOtp();
            if ($staticCode) {
                $response['verification_id'] = 12345678;
                return json_encode($response);
            }
            
            $response['error'] = true;
            $response['message'] =  __('Your phone number is not registered yet.');
            
            return json_encode($response);
        }

        $this->logger->info('----Start ' . __CLASS__ . ' ' . __FUNCTION__);
        try {
            if (!$this->isTelephoneAvailable($telephone) && $isNeedCheck) {
                // $checkPhone = $this->checkCustomerRegister($telephone);
                // $checkPhone = json_decode($checkPhone, true);
                
                // if ($checkPhone['customer_phone'] == 0) {
                    $response['error'] = true;
                    $response['message'] =  __('Your phone number is not registered yet.');
                    
                    return json_encode($response);
                // }
            }

            $url = $this->configApi->getSendVerificationUrl();
            $body = $this->prepareVerificationBody($telephone, $isNeedCheck, $language);
            $hit = $this->apiCall->post($url, "", $body);
            
            $this->logger->info($body);
            $this->logger->info($hit);
            $response = $hit;
        } catch (StateException $err) {
            $response = '{"error": true, "message": ' . $err->getMessage() . '}';
            $this->logger->info($err->getMessage());
        } catch (\Exception $err) {
            $response = '{"error": true, "message": ' . $err->getMessage() . '}';
            $this->logger->info($err->getMessage());
        }

        $this->logger->info('----End ' . __CLASS__ . ' ' . __FUNCTION__);
        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function checkCustomerRegister($telephone = null, $email = null)
    {
        $this->logger->info('----Start ' . __CLASS__ . ' ' . __FUNCTION__);
        try {
            $url = $this->configApi->getCheckDataCustomerUrl();
            $body = $this->prepareCustomerApi($telephone, $email);
            $hit = $this->apiCall->post($url, "", $body);
            
            $this->logger->info($body);
            $this->logger->info($hit);
            $response = $hit;
        } catch (StateException $err) {
            $response = '{"error": true, "message": ' . $err->getMessage() . '}';
            $this->logger->info($err->getMessage());
        } catch (\Exception $err) {
            $response = '{"error": true, "message": ' . $err->getMessage() . '}';
            $this->logger->info($err->getMessage());
        }
        
        $this->logger->info('----End ' . __CLASS__ . ' ' . __FUNCTION__);
        
        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function getCentralCustomerData($telephone = null, $customerCdbId = null)
    {
        $this->logger->info('----Start ' . __CLASS__ . ' ' . __FUNCTION__);
        try {
            $url = $this->configApi->getDataCustomerUrl();
            $body = $this->prepareGetCustomerApi($telephone, $customerCdbId);
            $hit = $this->apiCall->post($url, "", $body);
            
            $this->logger->info($body);
            $this->logger->info($hit);
            $response = $hit;
        } catch (StateException $err) {
            $response = '{"error": true, "message": ' . $err->getMessage() . '}';
            $this->logger->info($err->getMessage());
        } catch (\Exception $err) {
            $response = '{"error": true, "message": ' . $err->getMessage() . '}';
            $this->logger->info($err->getMessage());
        }
        
        $this->logger->info('----End ' . __CLASS__ . ' ' . __FUNCTION__);
        
        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function initateResetPassword(\Magento\Customer\Api\Data\CustomerInterface $customer, $websiteId = null)
    {
        $this->logger->info('----Start ' . __CLASS__ . ' ' . __FUNCTION__);
        try {
            $newPasswordToken = $this->mathRandom->getUniqueHash();
            $this->accountManagement->changeResetPasswordLinkToken($customer, $newPasswordToken);
            $this->logger->info($newPasswordToken);
            $result = true;
        } catch (InputException $err) {
            $this->logger->info($err->getMessage());
            $result = false;
        } catch (\Exception $err) {
            $this->logger->info($err->getMessage());
            $result = false;
        }
        
        $this->logger->info('----End ' . __CLASS__ . ' ' . __FUNCTION__);
        
        return $result;
    }

    /**
     * prepare raw body
     *
     * @param string $code
     * @param string $verificationId
     * @return string
     */
    protected function prepareVerify(string $code, string $verificationId)
    {
        $token = $this->apiCall->getCentralizeAuthToken();
        $body["auth_token"] = $token;
        $body["request_from_service"] = 'magento';
        $body["verification_id"] = $verificationId;
        $body["verification_code"] = $code;

        return json_encode($body);
    }

    /**
     * prepare raw body
     *
     * @param string $telephone
     * @param bool $isNeedCheck
     * @return string
     */
    protected function prepareVerificationBody(string $telephone, $isNeedCheck, string $language)
    {
        $token = $this->apiCall->getCentralizeAuthToken();
        $body["auth_token"] = $token;
        $body["request_from_service"] = 'magento';
        $body["customer_phone_number"] = $telephone;
        $body["check_user_existence"] = $isNeedCheck;
        $body["language"] = $language;

        return json_encode($body);
    }

    /**
     * prepare raw body
     *
     * @param string $telephone
     * @param string $email
     * @return string
     */
    protected function prepareCustomerApi(string $telephone = null, string $email = null)
    {
        $token = $this->apiCall->getCentralizeAuthToken();
        $body["auth_token"] = $token;
        $body["request_from_service"] = 'magento';
        
        if (!empty($telephone)) {
            $data["phone_number"] = $telephone;
        }

        if ($email) {
            $data["email_address"] = $email;
        }

        $body['details'][] = $data;

        return json_encode($body);
    }

    /**
     * prepare raw body
     *
     * @param string $telephone
     * @param string $cdbCustomerId
     * @return string
     */
    protected function prepareGetCustomerApi(string $telephone = null, string $cdbCustomerId = null)
    {
        $token = $this->apiCall->getCentralizeAuthToken();
        $body["auth_token"] = $token;
        $body["request_from_service"] = 'magento';
        
        if (!empty($telephone)) {
            $body["customer_phone_number"] = $telephone;
        }

        if (!empty($cdbCustomerId)) {
            $body["central_id"] = $cdbCustomerId;
        }

        return json_encode($body);
    }
}
