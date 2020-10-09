<?php

declare(strict_types=1);

namespace SM\AndromedaSms\Model\SmsVerification\TestMode;

use Magento\Framework\Exception\LocalizedException;
use SM\AndromedaApi\Model\Integration\Preparator as IntegrationPreparator;

class Validator
{
    /**
     * @var IntegrationPreparator
     */
    protected $integrationPreparator;

    /**
     * Validator constructor.
     * @param IntegrationPreparator $integrationPreparator
     */
    public function __construct(
        IntegrationPreparator $integrationPreparator
    ) {
        $this->integrationPreparator = $integrationPreparator;
    }

    /**
     * @param string $authToken
     * @throws LocalizedException
     */
    public function validateAuthToken(string $authToken): void
    {
        $token = $this->integrationPreparator->getToken();
        if ($token != $authToken) {
            throw new LocalizedException(__('Token mismatch, %1 | %2', $authToken, $token));
        }
    }
}
