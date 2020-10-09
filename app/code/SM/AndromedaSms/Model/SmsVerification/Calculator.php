<?php

declare(strict_types=1);

namespace SM\AndromedaSms\Model\SmsVerification;

use Magento\Framework\Stdlib\DateTime as MagentoDateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use SM\AndromedaSms\Api\Entity\SmsVerificationInterface;
use SM\AndromedaSms\Helper\Config;

class Calculator
{
    const NO_ATTEMPT = 0;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var TimezoneInterface
     */
    protected $timezone;

    /**
     * Calculator constructor.
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
     * @param null|SmsVerificationInterface $smsVerification
     * @return int
     */
    public function calculateFailedAttempt(?SmsVerificationInterface $smsVerification): int
    {
        if (is_null($smsVerification) || $smsVerification->getIsVerified()) {
            return self::NO_ATTEMPT;
        }

        $hrsToUnlock = $this->config->getNumberOfHoursToUnlock();
        $date = $this->timezone->date();
        $smsVerificationCreatedAt = $this->timezone->date($smsVerification->getCreatedAt());
        $date->modify("-{$hrsToUnlock} hour");
        if ($date->format(MagentoDateTime::DATETIME_PHP_FORMAT) > $smsVerificationCreatedAt) {
            return self::NO_ATTEMPT;
        }

        return $smsVerification->getFailedAttempt() + 1;
    }
}
