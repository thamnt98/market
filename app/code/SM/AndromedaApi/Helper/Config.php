<?php

declare(strict_types=1);

namespace SM\AndromedaApi\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Config extends AbstractHelper
{
    const XML_PATH_ANDROMEDA_API_AUTHORIZATION_TEST_MODE = 'andromeda_api/authorization/test_mode';
    const XML_PATH_ANDROMEDA_API_AUTHORIZATION_TEST_BASE_URL = 'andromeda_api/authorization/test_base_url';
    const XML_PATH_ANDROMEDA_API_AUTHORIZATION_KEY = 'andromeda_api/authorization/key';
    const XML_PATH_ANDROMEDA_API_AUTHORIZATION_CORE_API_ENDPOINT = 'andromeda_api/authorization/core_api_endpoint';
    const XML_PATH_ANDROMEDA_API_AUTHORIZATION_CDP_SYNC_API_ENDPOINT = 'andromeda_api/authorization/cdp_sync_api_endpoint';

    /**
     * @return bool
     */
    public function isTestMode(): bool
    {
        return (bool) $this->scopeConfig->getValue(
            self::XML_PATH_ANDROMEDA_API_AUTHORIZATION_TEST_MODE
        );
    }

    /**
     * @return string
     */
    public function getTestBaseUrl(): string
    {
        return (string) $this->scopeConfig->getValue(
            self::XML_PATH_ANDROMEDA_API_AUTHORIZATION_TEST_BASE_URL
        );
    }

    /**
     * @return string
     */
    public function getAuthorizationKey(): string
    {
        return (string) $this->scopeConfig->getValue(
            self::XML_PATH_ANDROMEDA_API_AUTHORIZATION_KEY
        );
    }

    /**
     * @return string
     */
    public function getCoreApiEndpoint(): string
    {
        return (string) $this->scopeConfig->getValue(
            self::XML_PATH_ANDROMEDA_API_AUTHORIZATION_CORE_API_ENDPOINT
        );
    }

    /**
     * @return null|string
     */
    public function getCdpSyncApiEndpoint(): string
    {
        return (string) $this->scopeConfig->getValue(
            self::XML_PATH_ANDROMEDA_API_AUTHORIZATION_CDP_SYNC_API_ENDPOINT
        );
    }
}
