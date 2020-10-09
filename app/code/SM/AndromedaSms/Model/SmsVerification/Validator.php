<?php

declare(strict_types=1);

namespace SM\AndromedaSms\Model\SmsVerification;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\DateTime as MagentoDateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\Webapi\Exception;
use SM\AndromedaSms\Api\Data\SmsVerification\VerifySms\TestMode\ResponseInterface as VerifySmsResponse;
use SM\AndromedaSms\Api\Entity\SmsVerificationInterface;
use SM\AndromedaSms\Api\Repository\SmsVerificationRepositoryInterface;
use SM\AndromedaSms\Helper\Config;

class Validator
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var TimezoneInterface
     */
    protected $timezone;

    /**
     * @var SmsVerificationRepositoryInterface
     */
    protected $repository;

    /**
     * Calculator constructor.
     * @param Config $config
     * @param TimezoneInterface $timezone
     * @param SmsVerificationRepositoryInterface $repository
     */
    public function __construct(
        Config $config,
        TimezoneInterface $timezone,
        SmsVerificationRepositoryInterface $repository
    ) {
        $this->config = $config;
        $this->timezone = $timezone;
        $this->repository = $repository;
    }

    /**
     * @param int $attempt
     * @param null|SmsVerificationInterface $smsVerification
     * @throws LocalizedException
     */
    public function validateFailedAttempt(int $attempt, ?SmsVerificationInterface $smsVerification): void
    {
        $failedToLock = $this->config->getNumberOfFailedToLock();
        if ($attempt >= $failedToLock) {
            $hrsToUnlock = $this->config->getNumberOfHoursToUnlock();
            $date = $this->timezone->date($smsVerification->getCreatedAt());
            $date->modify("+{$hrsToUnlock} hour");

            throw new Exception(
                __(
                    "You have failed {$failedToLock} attempt, please try again after " .
                        $date->format(MagentoDateTime::DATETIME_PHP_FORMAT)
                ),
                0,
                403
            );
        }
    }

    /**
     * @param SmsVerificationInterface $smsVerification
     * @throws LocalizedException
     * @throws \Exception
     */
    public function validateExpired(SmsVerificationInterface $smsVerification): void
    {
        $expiredIn = $this->config->getVerificationCodeExpiredIn();

        $now = $this->timezone->date();
        $createdAt = new \DateTime($smsVerification->getCreatedAt());
        $expiredAt = $this->timezone->date($createdAt);
        $expiredAt->modify("+{$expiredIn} second");

        if ($now->getTimestamp() > $expiredAt->getTimestamp()) {
            throw new LocalizedException(
                __('Verification code is expired after %1 seconds, please get another code', $expiredIn)
            );
        }
    }

    /**
     * @param VerifySmsResponse $response
     * @throws LocalizedException
     */
    public function validateVerifySmsResponse(VerifySmsResponse $response): void
    {
        if (!$response->getIsVerified()) {
            throw new LocalizedException(
                __('Make sure the code is correct')
            );
        }
    }

    /**
     * @param string $phoneNumber
     * @throws LocalizedException
     */
    public function validateVerified(string $phoneNumber): void
    {
        $smsVerification = $this->repository->getByPhoneNumber($phoneNumber);
        if ($smsVerification->getIsVerified() === 0) {
            throw new LocalizedException(__('Failed to verify OTP'));
        }
    }
}
