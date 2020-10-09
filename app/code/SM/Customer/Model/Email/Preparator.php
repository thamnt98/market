<?php

declare(strict_types=1);

namespace SM\Customer\Model\Email;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\UrlInterface;
use SM\Customer\Helper\Config;

class Preparator
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * Preparator constructor.
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        UrlInterface $urlBuilder
    ) {
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @param CustomerInterface $customerData
     * @return string
     */
    public function getVerifyEmailUrl(CustomerInterface $customerData): string
    {
        // Ignore weak encryption algorithm report due to customer 's requirement
        $token = md5($customerData->getId() . $customerData->getEmail()); //phpcs:ignore Magento2.Security.InsecureFunction.FoundWithAlternative
        return $this->urlBuilder->getUrl('customer/verify/email', [
            '_query' => [
                Config::EMAIL_ATTRIBUTE_CODE => $customerData->getEmail(),
                Config::TOKEN_FIELD_NAME => $token,
            ]
        ]);
    }

    /**
     * @param CustomerInterface $customerData
     * @return string
     */
    public function getCustomerName(CustomerInterface $customerData): string
    {
        return "{$customerData->getFirstname()} {$customerData->getLastname()}";
    }
}
