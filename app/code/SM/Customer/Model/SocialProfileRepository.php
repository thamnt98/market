<?php

namespace SM\Customer\Model;

use Exception;
use Hybrid_User_Profile;
use Magento\Customer\Model\Customer;
use Mageplaza\SocialLogin\Model\SocialFactory;
use SM\Customer\Api\Data\SocialProfileRepositoryInterface;

class SocialProfileRepository extends Hybrid_User_Profile implements SocialProfileRepositoryInterface
{
    /**
     * @var SocialFactory
     */
    private $apiObject;
    private $userProfile;

    public function __construct(SocialFactory $apiObject, Hybrid_User_Profile $hybridUserProfile)
    {
        $this->apiObject = $apiObject;
        $this->userProfile = $hybridUserProfile;
    }

    /**
     * @param $userProfile
     * @param $type
     * @return bool|Customer|mixed
     * @throws Exception
     */
    public function createCustomerProcess($userProfile, $type)
    {
        $name = explode(' ', $userProfile->displayName ?: __('New User'));

        $user = array_merge(
            [
                'email'      => $userProfile->email ?: $userProfile->identifier . '@' . strtolower($type) . '.com',
                'firstname'  => $userProfile->firstName ?: (array_shift($name) ?: $userProfile->identifier),
                'lastname'   => $userProfile->lastName ?: (array_shift($name) ?: $userProfile->identifier),
                'identifier' => $userProfile->identifier,
                'type'       => $type,
                'password'   => isset($userProfile->password) ? $userProfile->password : null
            ],
            $this->getUserData($userProfile)
        );

        return $this->createCustomer($user, $type);
    }

    /**
     * Create customer from social data
     *
     * @param $user
     * @param $type
     *
     * @return bool|Customer|mixed
     * @throws Exception
     */
    public function createCustomer($user, $type)
    {
        $customer = $this->apiObject->getCustomerByEmail($user['email'], $this->getStore()->getWebsiteId());
        if ($customer->getId()) {
            $this->apiObject->setAuthorCustomer($user['identifier'], $customer->getId(), $type);
        } else {
            try {
                $customer = $this->apiObject->createCustomerSocial($user, $this->getStore());
            } catch (Exception $e) {
                $this->emailRedirect($e->getMessage(), false);

                return false;
            }
        }

        return $customer;
    }

    public function setHybridUserProfile($userProfile)
    {
        $this->userProfile = $userProfile;
    }
}
