<?php
/**
 * @category    SM
 * @package     SM_Customer
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 * @author      Nam Nguyen <namnd2@smartosc.com>
 *
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\Customer\Model;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory as CustomerCollectionFactory;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Api\ImageContentValidatorInterface;
use Magento\Framework\Api\ImageProcessorInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\State\UserLockedException;
use Magento\Framework\Filesystem;
use Magento\Integration\Model\Oauth\TokenFactory as TokenModelFactory;
use Magento\Integration\Model\ResourceModel\Oauth\Token\CollectionFactory;
use Magento\Setup\Exception;
use SM\Customer\Model\Data\CustomerChangePasswordResultFactory;
use SM\Customer\Model\Email\Sender as EmailSender;

/**
 * Class TransCustomerProfile
 * @package SM\Customer\Model
 */
class TransCustomerProfile implements \SM\Customer\Api\TransCustomerProfileInterface
{
    /**
     * Const for location of message in change password section
     */
    const NEW_PASSWORD = 'new_password';
    const CURRENT_PASSWORD = 'current_password';
    const POP_UP = 'pop_up';

    /**
     * @var \Magento\Framework\Serialize\Serializer\Serialize
     */
    protected $serialize;

    /**
     * @var \Magento\Customer\Model\AuthenticationInterface
     */
    protected $authentication;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var \Magento\Customer\Model\CustomerRegistry
     */
    protected $customerRegistry;

    /**
     * @var \Magento\Framework\Encryption\EncryptorInterface
     */
    protected $encryptor;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

    /**
     * @var CustomerCollectionFactory
     */
    protected $customerCollectionFactory;

    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * @var EmailSender
     */
    protected $emailSender;

    /**
     * @var \SM\FileManagement\Api\UploadImageInterface
     */
    protected $fileSystem;

    /**
     * @var ImageProcessorInterface
     */
    protected $imageProcessor;

    /**
     * @var TokenModelFactory
     */
    protected $tokenFactory;

    /**
     * @var CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var \SM\Customer\Helper\Customer
     */
    protected $customerHelper;

    /**
     * @var \SM\FileManagement\Api\UploadImageInterface
     */
    protected $imageUpload;

    /**
     * @var ImageContentValidatorInterface
     */
    protected $contentValidator;

    /**
     * @var \Trans\CustomerMyProfile\Helper\Data
     */
    protected $customerMyProfileHelper;
    /**
     * @var ValidateHash
     */
    protected $validateHash;

    /**
     * @var AccountManagementInterface
     */
    protected $customerAccountManagement;

    /**
     * @var CustomerChangePasswordResultFactory
     */
    protected $changePasswordResultFactory;

    /**
     * TransCustomerProfile constructor.
     * @param \Trans\CustomerMyProfile\Helper\Data $customerMyProfileHelper
     * @param \Magento\Framework\Serialize\SerializerInterface $serialize
     * @param \Magento\Customer\Model\AuthenticationInterface $authentication
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Customer\Model\CustomerRegistry $customerRegistry
     * @param \Magento\Framework\Encryption\EncryptorInterface $encryptor
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param CustomerCollectionFactory $customerCollectionFactory
     * @param CustomerSession $customerSession
     * @param EmailSender $emailSender
     * @param ImageContentValidatorInterface $contentValidator
     * @param Filesystem $fileSystem
     * @param ImageProcessorInterface $imageProcessor
     * @param CollectionFactory $tokenFactory
     * @param CustomerFactory $customerFactory
     * @param \SM\Customer\Helper\Customer $customerHelper
     * @param \SM\FileManagement\Api\UploadImageInterface $uploadImage
     * @param ValidateHash $validateHash
     * @param AccountManagementInterface $accountManagement
     * @param CustomerChangePasswordResultFactory $changePasswordResultFactory
     */
    public function __construct(
        \Trans\CustomerMyProfile\Helper\Data $customerMyProfileHelper,
        \Magento\Framework\Serialize\SerializerInterface $serialize,
        \Magento\Customer\Model\AuthenticationInterface $authentication,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Model\CustomerRegistry $customerRegistry,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        CustomerCollectionFactory $customerCollectionFactory,
        CustomerSession $customerSession,
        EmailSender $emailSender,
        ImageContentValidatorInterface $contentValidator,
        FileSystem $fileSystem,
        ImageProcessorInterface $imageProcessor,
        CollectionFactory $tokenFactory,
        CustomerFactory $customerFactory,
        \SM\Customer\Helper\Customer $customerHelper,
        \SM\FileManagement\Api\UploadImageInterface $uploadImage,
        ValidateHash $validateHash,
        AccountManagementInterface $accountManagement,
        CustomerChangePasswordResultFactory $changePasswordResultFactory
    ) {
        $this->validateHash = $validateHash;
        $this->serialize = $serialize;
        $this->authentication = $authentication;
        $this->customerRepository = $customerRepository;
        $this->customerRegistry = $customerRegistry;
        $this->encryptor = $encryptor;
        $this->messageManager = $messageManager;
        $this->eventManager = $eventManager;
        $this->customerCollectionFactory = $customerCollectionFactory;
        $this->customerSession = $customerSession;
        $this->emailSender = $emailSender;
        $this->contentValidator = $contentValidator;
        $this->fileSystem = $fileSystem;
        $this->imageProcessor = $imageProcessor;
        $this->tokenFactory = $tokenFactory;
        $this->imageUpload = $uploadImage;
        $this->customerFactory = $customerFactory;
        $this->customerHelper = $customerHelper;
        $this->customerMyProfileHelper = $customerMyProfileHelper;
        $this->customerAccountManagement = $accountManagement;
        $this->changePasswordResultFactory = $changePasswordResultFactory;
    }

    /**
     * @param string $user
     * @param string $type
     * @return bool|mixed
     */
    public function isExistUser($user, $type)
    {
        $customerCollection = $this->customerCollectionFactory->create();
        if ($type == 'email') {
            $customerCollection->addFieldToFilter("email", $user);
        } else {
            $user = '628' . preg_replace("/^(^\+628|^628|^08|^8)/", '', $user);
            $customerCollection->addFieldToFilter("telephone", $user);
        }
        if ($this->customerSession->isLoggedIn()) {
            $customerId = $this->customerSession->getCustomerId();
            $customerCollection->addFieldToFilter('entity_id', ['neq' => $customerId]);
        }

        return ($customerCollection->getSize() == 0) ? false : true;
    }

    /**
     * @param string $email
     * @return bool
     */
    public function sendVerificationLink($email)
    {
        if (!$this->isEmail($email)) {
            return false;
        }
        try {
            $customer = $this->customerRepository->get($email);
            if (!$customer->getCustomAttribute('is_verified_email')) {
                return false;
            }
            $value = $customer->getCustomAttribute('is_verified_email')->getValue();
            if ($value == 1) {
                return false;
            }
            $this->emailSender->sendVerifyEmail($customer);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @param string $email
     * @param string $currentPassword
     * @param string $newPassword
     * @return mixed
     */
    public function changePassword($email, $currentPassword, $newPassword, $os='mobile')
    {
        try {
            $customer = $this->customerRepository->get($email);
            $customerId = $customer->getId();
        } catch (\Exception $e) {
            return $this->serialize->serialize(['status' => false, 'message' => $e->getMessage()]);
        }

        if (!($this->customerSession->getIsSocial() == true)) {
            try {
                $this->authentication->authenticate($customerId, $currentPassword);
            } catch (UserLockedException $e) {
                return $this->serialize->serialize(['status' => false, 'message' => $e->getMessage()]);
            } catch (\Exception $e) {
                return $this->serialize->serialize(['status' => false, 'message' => __('This is not your current password'), 'curPassErr' => true]);
            }
        }

        try {
            $customerSecure = $this->customerRegistry->retrieveSecureData($customerId);
            $this->validateHash->validate($customerSecure, $newPassword);

            $customerSecure->setRpToken(null);
            $customerSecure->setRpTokenCreatedAt(null);
            $customerSecure->setPasswordHash($this->encryptor->getHash($newPassword, true));
            $this->customerRepository->save($customer);

            $this->customerSession->setIsSocial(false);

            $this->eventManager->dispatch(
                'trans_customer_change_password_after',
                ['customer' => $customer]
            );
//            $this->eventManager->dispatch(
//                'customer_account_edited',
//                ['email' => $customer->getEmail()]
//            );

            $this->customerHelper->logout($customerId);
            $this->messageManager->addSuccessMessage(__('Your password has been changed.'));
            return $this->serialize->serialize(['status' => true, 'message' => __('success')]);
        } catch (InputException $e) {
            return $this->serialize->serialize(['status' => false, 'message' => $e->getMessage(), 'newPassErr' => true]);
        } catch (\Exception $e) {
            return $this->serialize->serialize(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * @inheritDoc
     */
    public function changePasswordMobile($customerId, $currentPassword, $newPassword)
    {
        $result = $this->changePasswordResultFactory->create();

        try {
            $this->customerAccountManagement->changePasswordById($customerId, $currentPassword, $newPassword);
            $result->setStatus(true);
            $result->setMessage(__('Your password change successfully'));
            $result->setLocationAppears(self::POP_UP);
        } catch (NoSuchEntityException $emailOrPasswordException) {
            throw new \Magento\Framework\Webapi\Exception(__($emailOrPasswordException->getMessage()), 401, 401);
        } catch (InputException $inputException) {
            $result->setStatus(false);
            $result->setMessage($inputException->getMessage());
            $result->setLocationAppears(self::NEW_PASSWORD);
        } catch (UserLockedException $userLockedException) {
            $result->setStatus(false);
            $result->setMessage(__('Your account is locked'));
            $result->setLocationAppears(self::POP_UP);
        } catch (LocalizedException $localizedException) {
            $result->setStatus(false);
            $result->setMessage(__('This is not your current password'));
            $result->setLocationAppears(self::CURRENT_PASSWORD);
        }

        return $result;
    }

    /**
     * Check is email
     * @param string $mail
     * @return bool
     */
    protected function isEmail($mail)
    {
        return (!preg_match("/[-0-9a-zA-Z.+_]+@[-0-9a-zA-Z.+_]+.[a-zA-Z]{2,4}/", $mail)) ? false : true;
    }

    /**
     * @param int $phone
     * @return bool
     */
    protected function isPhone($phone)
    {
        return (!preg_match("/^\+?(62|08|8)[0-9]+$/", $phone)) ? false : true;
    }

    /**
     * @param int $customerId
     * @param \Magento\Framework\Api\Data\ImageContentInterface $imageContent
     * @return bool
     * @throws Exception
     */
    public function uploadCustomerAvatar(int $customerId, \Magento\Framework\Api\Data\ImageContentInterface $imageContent)
    {
        $directory = \Magento\Framework\App\Filesystem\DirectoryList::MEDIA;

        $imagePath = $this->imageUpload->uploadImage($imageContent, $directory, 'customer');
        $this->setImageCustomer($customerId, $imagePath);

        return true;
    }

    /**
     * @param integer $customerId
     * @param string $imagePath
     * @throws Exception
     */
    protected function setImageCustomer($customerId, $imagePath)
    {
        try {
            $customer = $this->customerRepository->getById($customerId);
            $profilePictureAttribute = $customer->getCustomAttribute('profile_picture');
            if ($profilePictureAttribute != null) {
                $customer->getCustomAttribute('profile_picture')->setValue($imagePath);
            }

            if ($profilePictureAttribute == null) {
                $customer->setCustomAttribute('profile_picture', $imagePath);
            }

            $customer->setData('ignore_validation_flag', true);
            $this->customerRepository->save($customer);
        } catch (\Exception $e) {
            throw new Exception(__('Something wrong while saving image, pls try again later!'));
        }
    }

    /**
     * @inheritDoc
     */
    public function limitChangeDob($customerId)
    {
        try {
            $customer = $this->customerRepository->getById($customerId);
            $dobChangeCurrent = (int)$customer->getCustomAttribute('dob_change_number')->getValue();
            $dobChangeLimit = (int)$this->customerMyProfileHelper->getDobChangeLimit();
            if ($dobChangeCurrent >= $dobChangeLimit) {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }
}
