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

namespace Trans\IntegrationCustomer\Model;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Framework\Exception\StateException;
use Trans\Core\Helper\Customer;
use Trans\IntegrationCustomer\Api\Data\IntegrationCdbInterface;
use Trans\IntegrationCustomer\Api\Data\IntegrationCustomerCentralInterface;
use Trans\IntegrationCustomer\Api\Data\IntegrationDataValueInterface;
use Trans\IntegrationCustomer\Api\Data\IntegrationJobInterface;
use Trans\IntegrationCustomer\Api\IntegrationCustomerCentralRepositoryInterface;
use Trans\IntegrationCustomer\Api\IntegrationCustomerInterface;
use Trans\IntegrationCustomer\Api\IntegrationDataValueRepositoryInterface;
use Trans\IntegrationCustomer\Api\IntegrationJobRepositoryInterface;
use Trans\Integration\Api\Data\IntegrationChannelMethodInterface;
use Trans\Integration\Api\IntegrationCommonInterface;
use Trans\Integration\Api\IntegrationDatabaseInterface;

class IntegrationCustomer implements IntegrationCustomerInterface {
	/**
	 * @var Logger
	 */
	protected $logger;

	/**
	 * @var \Magento\Customer\Api\Data\CustomerInterfaceFactory
	 */
	protected $customerFactory;

	/**
	 * @var \Magento\Customer\Api\CustomerRepositoryInterface
	 */
	protected $customerRepository;

	/**
	 * @var \Magento\Customer\Model\Customer
	 */
	protected $customerModel;

	/**
	 * @var \Mageplaza\SocialLogin\Model\Social
	 */
	protected $socialModel;

	/**
	 * @var IntegrationDatabaseInterface
	 */
	protected $dbRepository;

	/**
	 * @var
	 */
	protected $customerData;

	/**
	 * @var Customer
	 */
	protected $customerHelper;

	/**
	 * @var IntegrationCommonInterface
	 */
	protected $commonRepository;

	/**
	 * @var IntegrationJobRepositoryInterface
	 */
	protected $jobRepository;

	/**
	 * @var IntegrationDataValueRepositoryInterface
	 */
	protected $datavalueRepository;

	/**
	 * @var \Magento\Customer\Api\AccountManagementInterface
	 */
	protected $accountManagement;

	/**
	 * @var IntegrationCustomerCentralRepositoryInterface
	 */
	protected $customerCentralRepository;

	/**
	 * @var \Trans\IntegrationCustomer\Api\Data\IntegrationCdbResultInterfaceFactory
	 */
	protected $resultInterface;

	/**
	 * @var \Mageplaza\SocialLogin\Model\ResourceModel\Social\CollectionFactory
	 */
	protected $socialCollection;

	/**
	 * @var \Mageplaza\SocialLogin\Model\ResourceModel\Social
	 */
	protected $socialResouce;

	/**
	 * @var \Trans\Integration\Helper\Curl
	 */
	protected $apiCall;

	/**
	 * @var \Trans\Integration\Helper\Config
	 */
	protected $configApi;

	/**
	 * IntegrationCustomer constructor.
	 * @param \Magento\Customer\Api\Data\CustomerInterfaceFactory $customerFactory
	 * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
	 * @param \Magento\Customer\Model\Customer $customerModel
	 * @param \Mageplaza\SocialLogin\Model\Social $socialModel
	 * @param IntegrationDatabaseInterface $dbRepository
	 * @param Customer $customerHelper
	 * @param IntegrationCommonInterface $commonRepository
	 * @param IntegrationJobRepositoryInterface $jobRepository
	 * @param IntegrationDataValueRepositoryInterface $datavalueRepository
	 * @param AccountManagementInterface $accountManagement
	 * @param IntegrationCustomerCentralRepositoryInterface $customerCentralRepository
	 * @param \Trans\IntegrationCustomer\Api\Data\IntegrationCdbResultInterfaceFactory $resultInterface
	 * @param \Mageplaza\SocialLogin\Model\ResourceModel\Social\CollectionFactory $socialCollection
	 * @param \Mageplaza\SocialLogin\Model\ResourceModel\Social $socialResouce
	 * @param \Trans\Integration\Helper\Curl $apiCall
	 * @param \Trans\Integration\Helper\Config $configApi
	 */
	public function __construct(
		\Magento\Customer\Api\Data\CustomerInterfaceFactory $customerFactory,
		\Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
		\Magento\Customer\Model\Customer $customerModel,
		\Mageplaza\SocialLogin\Model\Social $socialModel,
		IntegrationDatabaseInterface $dbRepository,
		Customer $customerHelper,
		IntegrationCommonInterface $commonRepository,
		IntegrationJobRepositoryInterface $jobRepository,
		IntegrationDataValueRepositoryInterface $datavalueRepository,
		AccountManagementInterface $accountManagement,
		IntegrationCustomerCentralRepositoryInterface $customerCentralRepository,
		\Trans\IntegrationCustomer\Api\Data\IntegrationCdbResultInterfaceFactory $resultInterface,
		\Mageplaza\SocialLogin\Model\ResourceModel\Social\CollectionFactory $socialCollection,
		\Mageplaza\SocialLogin\Model\ResourceModel\Social $socialResouce,
		\Trans\Integration\Helper\Curl $apiCall,
		\Trans\Integration\Helper\Config $configApi
	) {
		$this->customerFactory           = $customerFactory;
		$this->customerRepository        = $customerRepository;
		$this->customerModel             = $customerModel;
		$this->socialModel               = $socialModel;
		$this->dbRepository              = $dbRepository;
		$this->customerHelper            = $customerHelper;
		$this->commonRepository          = $commonRepository;
		$this->jobRepository             = $jobRepository;
		$this->datavalueRepository       = $datavalueRepository;
		$this->accountManagement         = $accountManagement;
		$this->customerCentralRepository = $customerCentralRepository;
		$this->resultInterface           = $resultInterface;
		$this->socialCollection          = $socialCollection;
		$this->socialResouce             = $socialResouce;
		$this->apiCall                   = $apiCall;
		$this->configApi                 = $configApi;

		$writer       = new \Zend\Log\Writer\Stream(BP . '/var/log/integration_customer.log');
		$logger       = new \Zend\Log\Logger();
		$this->logger = $logger->addWriter($writer);
	}

	/**
	 * {{@inheritdoc}}
	 */
	public function newCustomerIntegration($customerId) {
		$customer = $this->customerRepository->getById($customerId);
		$body     = $this->prepareCustomer($customer);
		$this->logger->info($body);

		try {
			$url = $this->configApi->getSendNewCustomerUrl();
			$hit = $this->apiCall->post($url, "", $body);

			$this->logger->info($hit);
			$response = $hit;
			$data     = json_decode($response, true);
			$this->saveCustomerCentralId($data);
		} catch (StateException $err) {
			$response = '{"error": true, "message": ' . $err->getMessage() . '}';
			$this->logger->info($err->getMessage());
		} catch (\Exception $err) {
			$response = '{"error": true, "message": ' . $err->getMessage() . '}';
			$this->logger->info($err->getMessage());
		}

		return $response;
	}

	/**
	 * {{@inheritdoc}}
	 */
	public function updateCustomerIntegration($customer) {
		$body = $this->prepareCustomer($customer);
		$this->logger->info($body);

		try {
			$url = $this->configApi->getSendUpdateCustomerUrl();
			$hit = $this->apiCall->post($url, "", $body);

			$this->logger->info($hit);
			$response = $hit;
		} catch (StateException $err) {
			$response = '{"error": true, "message": ' . $err->getMessage() . '}';
			$this->logger->info($err->getMessage());
		} catch (\Exception $err) {
			$response = '{"error": true, "message": ' . $err->getMessage() . '}';
			$this->logger->info($err->getMessage());
		}

		return $response;
	}

	/**
	 * Prepare Customer
	 * @param $customer
	 * @return array
	 */
	public function prepareCustomer($customer) {
		$this->customerData = $customer;
		$password           = null;

		$body["auth_token"]           = $this->apiCall->getCentralizeAuthToken();
		$body["request_from_service"] = "magento";

		$telephone        = $customer->getCustomAttribute('telephone') ? $customer->getCustomAttribute('telephone')->getValue() : null;
		$nik              = $customer->getCustomAttribute('nik') ? $customer->getCustomAttribute('nik')->getValue() : null;
		$maritalStatus    = $customer->getCustomAttribute('marital_status') ? $customer->getCustomAttribute('marital_status')->getValue() : null;
		$maritalStatusCdb = $maritalStatus ? $this->customerHelper->getMaritalStatusValueForCdb($maritalStatus) : null;
		$gender           = $customer->getGender() ? $this->customerHelper->getGenderValueForCdb($customer->getGender()) : null;

		$data = [
			"integration_id" => (string) $customer->getId(),
			"email_address" => $customer->getEmail(),
			"full_name" => $this->customerHelper->generateFullnameByCustomer($customer),
			"first_name" => $customer->getFirstname(),
			"last_name" => $customer->getLastname(),
			"password" => $password,
			"is_email_verified" => $this->getEmailConfirmed($customer),
			"gender" => $gender,
			"date_of_birth" => $customer->getDob(),
			"marital_status" => $maritalStatusCdb,
			"phone_number" => $telephone,
			"ktp_id" => $nik,
			"updated_at" => $customer->getUpdatedAt() . '.000001',
		];

		$body['data'][] = $data;

		return json_encode($body);
	}

	/**
	 * Get Email COnfirmasi By int
	 * @param $customer
	 * @return int
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	protected function getEmailConfirmed($customer) {
		$confirmationStatus = $this->accountManagement->getConfirmationStatus($customer->getId());
		switch ($confirmationStatus) {
		case AccountManagementInterface::ACCOUNT_CONFIRMED:
			return 1;
			break;
		default:
			return 0;
			break;
		}
	}

	/**
	 * Get Password From DB
	 * @return mixed
	 */
	protected function getCustomerPassword() {
		$raw   = 'SELECT entity_id , password_hash FROM %s WHERE entity_id = %d';
		$query = sprintf($raw, SELF::TABLE_CUSTOMER, $this->customerData->getId());
		return $this->dbRepository->getRow(SELF::TABLE_CUSTOMER, $query);
	}

	/**
	 * Prepare Job
	 * @param array $channel
	 * @return array
	 * @throws StateException
	 */
	public function prepareJob($channel = []) {
		$result = [];
		if (!isset($channel['method'])) {
			throw new StateException(
				__(IntegrationCustomerInterface::MSG_CHANNEL_NOTAVAILABLE)
			);
		}
		$method  = $channel['method'];
		$batchId = uniqid();

		$result[IntegrationJobInterface::TOTAL_DATA]   = 1;
		$result[IntegrationJobInterface::METHOD_ID]    = $method->getId();
		$result[IntegrationJobInterface::LIMIT]        = (!empty($method->getLimits())) ? $method->getLimits() : IntegrationChannelMethodInterface::VAL_LIMIT;
		$result[IntegrationJobInterface::LAST_UPDATED] = NULL;
		$result[IntegrationJobInterface::BATCH_ID]     = $batchId;

		$result[IntegrationJobInterface::OFFSET] = null;
		return $result;
	}

	/**
	 * Save Job
	 * @param array $channel
	 * @param array $data
	 * @return mixed
	 * @throws StateException
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function saveJob($channel = [], $data = []) {
		if (!isset($channel['method'])) {
			throw new StateException(
				__(IntegrationCustomerInterface::MSG_CHANNEL_NOTAVAILABLE)
			);
		}
		try {
			$collection = $this->jobRepository->getByMdIdFirstItem($channel['method']->getId(), IntegrationJobInterface::STATUS_WAITING);

			if ($collection) {

				$job = $collection;
				$job->setTotalData($job->getTotalData() + $data[IntegrationJobInterface::TOTAL_DATA]);
				$this->jobRepository->save($job);

			} else {

				$job = $this->jobRepository->saveJobs($data);
			}

		} catch (\Exception $ex) {
			throw new StateException(
				__($ex->getMessage())
			);
		}
		return $job;
	}

	/**
	 * Prepare Data
	 * @param $jobs
	 * @param $data
	 * @return mixed
	 * @throws StateException
	 */
	public function prepareData($jobs, $data) {
		if (!$jobs->getId()) {
			throw new StateException(
				__(IntegrationCustomerInterface::MSG_JOB_NOTAVAILABLE)
			);
		}
		$result[IntegrationDataValueInterface::JOB_ID]     = $jobs->getId();
		$result[IntegrationDataValueInterface::DATA_VALUE] = json_encode($data);
		return $result;
	}

	/**
	 * Get Jobs & Data value
	 * @param $jobs
	 * @throws StateException
	 */
	public function getJobsData($jobs) {
		$result = [];
		if (!$jobs->getId()) {
			throw new StateException(
				__(IntegrationCustomerInterface::MSG_JOB_NOTAVAILABLE)
			);
		}
		try {
			$collection = $this->datavalueRepository->getByJobIdWithStatus($jobs->getId(), 1);
			if ($collection->getSize()) {
				// generate data value
				$i = 0;
				foreach ($collection as $row) {
					$result[$i] = json_decode($row->getDataValue(), true);
					$result[$i] = $this->customerHelper->setMagentoCustomerId($result[$i], $row->getId());
					$i++;
				}

			}
		} catch (\Exception $ex) {
			throw new StateException(
				__($ex->getMessage())
			);
		}

		return $result;
	}

	/**
	 * Set Job on Progress
	 * @param \Trans\IntegrationCustomer\Api\Data\IntegrationJobInterface $jobs
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function setJobOnProgress($jobs) {
		if ($jobs instanceof \Trans\IntegrationCustomer\Api\Data\IntegrationJobInterface) {
			$jobs->setStatus(IntegrationJobInterface::STATUS_PROGRESS_POST);
			$jobs->setStartJob(date('Y-m-d H:i:s'));
			$this->jobRepository->save($jobs);
		}
	}

	/**
	 * Set Job Failed
	 * @param \Trans\IntegrationCustomer\Api\Data\IntegrationJobInterface $jobs
	 * @param $msg
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function setJobFailed($jobs, $msg) {
		if ($jobs instanceof \Trans\IntegrationCustomer\Api\Data\IntegrationJobInterface) {
			$jobs->setStatus(IntegrationJobInterface::STATUS_PROGRESS_FAIL);
			$jobs->setEndJob(date('Y-m-d H:i:s'));
			$jobs->setMessages($msg);
			$this->jobRepository->save($jobs);
		}
	}

	/**
	 * Save Map To Customer Central Id
	 * @param $response
	 * @return array|mixed
	 * @throws StateException
	 */
	public function saveCustomerCentralId($response) {
		if (!isset($response['data'])) {
			throw new StateException(
				__(IntegrationCustomerInterface::MSG_RESP_NOTAVAILABLE)
			);
		}
		if (!is_array($response['data'])) {
			throw new StateException(
				__(IntegrationCustomerInterface::MSG_RESP_NOTAVAILABLE)
			);
		}

		$check = array_filter($response['data']);
		if (empty($check)) {
			throw new StateException(
				__(IntegrationCustomerInterface::MSG_RESP_NOTAVAILABLE)
			);
		}

		$param            = [];
		$dataValueArrayId = [];
		$custArrayId      = [];

		try {
			$data = $response['data'];
			if (is_array($response['data'])) {
				$data = $response['data'][0];
			}

			$dataValueArrayId = $this->customerHelper->getCustValueId($data[IntegrationCustomerCentralInterface::CUST_ID], 1);
			$this->saveResponseDataValue($dataValueArrayId, $data);

			$custArrayId                                            = $this->customerHelper->getCustValueId($data[IntegrationCustomerCentralInterface::CUST_ID], 0);
			$param[IntegrationCustomerCentralInterface::CUST_ID]    = $custArrayId;
			$param[IntegrationCustomerCentralInterface::CENTRAL_ID] = $data[IntegrationCustomerCentralInterface::CENTRAL_ID];

			// Save to customer central map
			if ($data[IntegrationCustomerCentralInterface::STATUS] == 1) {
				$this->customerCentralRepository->saveData($param);
			}
		} catch (\Exception $ex) {
			throw new StateException(
				__($ex->getMessage())
			);
		}

		return $param;
	}

	/**
	 * Save Response to Data Value Table
	 * @param int $id
	 * @param array $data
	 * @return bool
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	protected function saveResponseDataValue($id = 0, $data = []) {
		if (empty($id)) {
			return false;
		}
		if (!is_array($data)) {
			return false;
		}
		$check = array_filter($data);
		if (empty($check)) {
			return false;
		}
		$query = $this->datavalueRepository->getById($id);
		$query->setMessages(json_encode($data));
		if (isset($data['status'])) {
			$query->setMessages($data['status']);
		}

		$this->datavalueRepository->save($query);
		return true;
	}

	/**
	 * Set Job Failed
	 * @param \Trans\IntegrationCustomer\Api\Data\IntegrationJobInterface $jobs
	 * @param $msg
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function setJobComplete($jobs) {
		if ($jobs instanceof \Trans\IntegrationCustomer\Api\Data\IntegrationJobInterface) {
			$date = date('Y-m-d H:i:s');
			$jobs->setStatus(IntegrationJobInterface::STATUS_COMPLETE);
			$jobs->setEndJob($date);
			$jobs->setLastUpdated($date);
			$this->jobRepository->save($jobs);
		}
	}

	/**
	 * {{@inheritdoc}}
	 */
	public function updateCdbCustomerProfile($customer) {
		try {
			$this->customerHelper->validatePhoneNumber($customer->getTelephone());

			$data = $this->customerRepository->getById($customer->getMagentoCustomerId());

			$data->setData('firstname', $customer->getFirstname());
			$data->setData('lastname', $customer->getLastname());
			$data->setData('website_id', 1);

			$gender        = $this->getGender($customer->getGender());
			$maritalStatus = $this->customerHelper->getMaritalStatusValueByLabel($customer->getMaritalStatus());

			$data->setData('gender', $gender);
			$data->setData('dob', $customer->getDateOfBirth());
			$data->setCustomAttribute('telephone', $customer->getTelephone());
			$data->setCustomAttribute('marital_status', $maritalStatus);
			$data->setCustomAttribute('nik', $customer->getIdCard());
			// $data->setCustomAttribute('profile_picture', $customer->getProfilePicture());

			$newEmail       = $customer->getEmail();
			$confirmedEmail = $customer->getVerifiedEmail();

			if ($newEmail) {
				$data->setData('email', $newEmail);

				switch ($newEmail) {
				case $confirmedEmail:
					$data->setConfirmation(null); //confirmed
					break;

				default:
					if (!$data->getConfirmation()) {
						$data->setConfirmation($this->customerModel->getRandomConfirmationKey()); //not confirmed yet
					}
					break;
				}
			}

			$savedCustomer = $this->customerRepository->save($data);

			$social  = $this->socialCustomer($customer, $savedCustomer);
			$status  = 200;
			$message = 'Update sucess';
		} catch (\Exception $e) {
			$status  = 400;
			$message = 'Update failed. ' . trim($e->getMessage());
		}

		$result    = $this->resultInterface->create();
		$messageId = $result->generateMessageId();

		$result->setStatus($status);
		$result->setMessageId($messageId);
		$result->setMessage($message);

		$this->logger->info("message_id = " . $messageId . " - message = " . $message . " - status = " . $status . " - post_data = " . json_encode($customer->getData(), true));

		return $result;
	}

	/**
	 * update social login data
	 *
	 * @param \Trans\IntegrationCustomer\Api\Data\IntegrationCdbInterface $cdbCustomer
	 * @param \Magento\Customer\Api\Data\CustomerInterface
	 * @return void
	 */
	protected function socialCustomer($cdbCustomer, $customerData) {
		$cdbCustomerArray = $cdbCustomer->getData();

		try {
			$facebook = $cdbCustomer->getFacebookId();
			$google   = $cdbCustomer->getGoogleId();
			$apple    = $cdbCustomer->getAppleId();

			$socialFb = $this->getSocialData('facebook', $cdbCustomer->getMagentoCustomerId());
			if (!empty($facebook)) {
				if (!$socialFb) {
					$this->socialModel->setAuthorCustomer($facebook, $customerData->getId(), 'facebook');
				} else {
					$socialFb->setData('social_id', $facebook);
					$this->socialResouce->save($socialFb);
				}
			} else {
				$issetData = isset($cdbCustomerArray[IntegrationCdbInterface::FACEBOOK_ID]);
				$this->deleteSocialId($socialFb, $issetData);
			}

			$socialGoogle = $this->getSocialData('google', $cdbCustomer->getMagentoCustomerId());
			if (!empty($google)) {
				if (!$socialGoogle) {
					$this->socialModel->setAuthorCustomer($google, $customerData->getId(), 'google');
				} else {
					$socialGoogle->setData('social_id', $google);
					$this->socialResouce->save($socialGoogle);
				}
			} else {
				$issetData = isset($cdbCustomerArray[IntegrationCdbInterface::GOOGLE_ID]);
				$this->deleteSocialId($socialGoogle, $issetData);
			}

			$socialApple = $this->getSocialData('apple', $cdbCustomer->getMagentoCustomerId());
			if (!empty($apple)) {
				if (!$socialApple) {
					$this->socialModel->setAuthorCustomer($apple, $customerData->getId(), 'apple');
				} else {
					$socialApple->setData('social_id', $apple);
					$this->socialResouce->save($socialApple);
				}
			} else {
				$issetData = isset($cdbCustomerArray[IntegrationCdbInterface::APPLE_ID]);
				$this->deleteSocialId($socialApple, $issetData);
			}
		} catch (\Exception $e) {
			throw new Exception("Update social id failed", 1);
		}
	}

	/**
	 * get social id data
	 *
	 * @param string $type
	 * @param int $customerId
	 * @return object|bool
	 */
	protected function getSocialData(string $type, int $customerId) {
		try {
			$collection = $this->socialCollection->create();
			$collection->addFieldToFilter('customer_id', $customerId);
			$collection->addFieldToFilter('type', $type);

			if ($collection->getSize()) {
				return $data = $collection->getFirstItem();
			}
		} catch (\Exception $e) {
		}

		return false;
	}

	/**
	 * remove social id
	 *
	 * @param object $data
	 * @param bool $delete
	 * @return void
	 */
	protected function deleteSocialId($data, bool $delete = true) {
		if ($delete && $data) {
			try {
				$this->socialResouce->delete($data);
			} catch (\Exception $e) {
				throw new Exception("Delete social id failed", 1);
			}
		}
	}

	/**
	 * get gender id
	 * @param string $genderChar
	 * @return int
	 */
	protected function getGender(string $genderChar = null) {
		if ($genderChar) {
			switch ($genderChar) {
			case IntegrationCdbInterface::CDB_GENDER_MALE:
				$label = 'Male';
				break;

			case IntegrationCdbInterface::CDB_GENDER_FEMALE:
				$label = 'Female';
				break;

			default:
				$label = 'Not Specified';
				break;
			}

			return $this->customerHelper->getGenderValueByLabel($label);
		}
	}
}
