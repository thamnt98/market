<?php

declare(strict_types=1);

namespace SM\AndromedaApi\Model\Integration;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use SM\AndromedaApi\Helper\Config;

class Preparator
{
    const TEST_MODE_API_PATH = 'rest/V1/andromeda-test-mode';
    const AUTH_TOKEN = 'auth_token';
    const AUTH_TOKEN_DATE_FORMAT = 'ymdHi';

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var TimezoneInterface
     */
    protected $timezone;

    /**
     * Preparator constructor.
     * @param Config $config
     * @param TimezoneInterface $timezone
     */
    public function __construct(
        Config $config,
        TimezoneInterface $timezone
    ) {
        $this->config = $config;
        $this->timezone = $timezone;
    }

    /**
     * @param string $apiPath
     * @return string
     */
    public function getUri(string $apiPath): string
    {
        if ($this->config->isTestMode()) {
            $endpoint = $this->config->getTestBaseUrl() . self::TEST_MODE_API_PATH;
        } else {
            $endpoint = $this->config->getCoreApiEndpoint();
        }

        return $endpoint . $apiPath;
    }

    /**
     * @param array $headers
     * @return array
     */
    public function resolveHeaders(array $headers): array
    {
        if (!isset($headers['Content-Type'])) {
            $headers['Content-Type'] = 'application/json';
        }
        return $headers;
    }

    /**
     * @param null|array $body
     * @param string $method
     * @return null|array
     * @throws LocalizedException
     */
    public function attachAuthToken(?array $body, string $method): ?array
    {
        if (in_array($method, ['POST', 'PUT'])) {
            $body = $body ?? [];
            $body[self::AUTH_TOKEN] = $this->getToken();
        }

        return $body;
    }

    /**
     * @param \DateTimeInterface $date
     * @return string
     * @throws LocalizedException
     */
    public function getToken(?\DateTimeInterface $date=null): string
    {
        $key = $this->config->getAuthorizationKey();
        if (!$key) {
            throw new LocalizedException(__('Authorization key is not configured'));
        }

        $time = $this->timezone->convertConfigTimeToUtc($date, self::AUTH_TOKEN_DATE_FORMAT);

        // Ignore weak encryption algorithm report due to customer 's requirement
        return md5($key . $time); //phpcs:ignore Magento2.Security.InsecureFunction.FoundWithAlternative
    }
}
