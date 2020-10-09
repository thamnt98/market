<?php

namespace SM\Customer\Model;

/**
 * Class Authentication
 * @package SM\Customer\Model
 */
class Authentication extends \Magento\Customer\Model\Authentication
{
    /**
     * Configuration path to customer lockout threshold
     */
    const LOCKOUT_THRESHOLD_PATH = 'sm_customer/recovery/lockout_threshold';

    /**
     * Configuration path to customer max login failures number
     */
    const MAX_FAILURES_PATH = 'sm_customer/recovery/lockout_failures';

    /**
     * Get lock threshold
     *
     * @return int
     */
    protected function getLockThreshold()
    {
        return $this->backendConfig->getValue(self::LOCKOUT_THRESHOLD_PATH) * 60;
    }

    /**
     * Get max failures
     *
     * @return int
     */
    protected function getMaxFailures()
    {
        return $this->backendConfig->getValue(self::MAX_FAILURES_PATH);
    }
}
