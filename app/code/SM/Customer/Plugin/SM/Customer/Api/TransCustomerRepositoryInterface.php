<?php

namespace SM\Customer\Plugin\SM\Customer\Api;

use Exception;
use Magento\Customer\Api\Data\CustomerInterface;
use Mageplaza\SocialLogin\Model\Social;
use Mageplaza\SocialLogin\Model\SocialFactory;
use SM\Customer\Api\TransCustomerRepositoryInterface as BaseTransCustomerRepositoryInterface;

class TransCustomerRepositoryInterface
{
    /**
     * @var SocialFactory
     */
    private $socialFactory;

    public function __construct(SocialFactory $socialFactory)
    {
        $this->socialFactory = $socialFactory;
    }

    /**
     * @param BaseTransCustomerRepositoryInterface $subject
     * @param CustomerInterface $result
     * @param CustomerInterface $customer
     * @return mixed
     * @throws Exception
     */
    public function afterTransSave(BaseTransCustomerRepositoryInterface $subject, $result, $customer)
    {
        $extensionAttributes = $customer->getExtensionAttributes();
        if ($extensionAttributes->getIdentifier()) {
            /** @var Social $socialModel */
            $socialModel = $this->socialFactory->create();
            $socialModel->setAuthorCustomer(
                $extensionAttributes->getIdentifier(),
                $result->getId(),
                $extensionAttributes->getLoginType()
            );
        }

        return $result;
    }
}
