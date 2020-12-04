<?php

namespace SM\MobileApi\Helper\GreetingMessage;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Config
 * @package SM\MobileApi\Helper\GreetingMessage
 */
class Config extends AbstractHelper
{
    const FIRST_TIME_SIGN_IN_MESSAGE = 'sm_message/first_time_sign_in/first_time_message';
    const MORNING_SIGN_IN_MESSAGE    = 'sm_message/morning_sign_in/morning_message';
    const AFTERNOON_SIGN_IN_MESSAGE  = 'sm_message/afternoon_sign_in/afternoon_message';
    const AFTER_WEEK_SIGN_IN_MESSAGE = 'sm_message/after_week_sign_in/after_week_message';

    const CAMPAIGN_MESSAGE    = 'sm_message/campaign/campaign_message';
    const CAMPAIGN_START_TIME = 'sm_message/campaign/campaign_start_time';
    const CAMPAIGN_END_TIME   = 'sm_message/campaign/campaign_end_time';

    const RANDOM_MESSAGE    = 'sm_message/random_message/message';
    const RANDOM_START_TIME = 'sm_message/random_message/random_message_start_time';
    const RANDOM_END_TIME   = 'sm_message/random_message/random_message_end_time';

    /**
     * @return string
     */
    public function getFirstTimeSignInMessage()
    {
        return $this->scopeConfig->getValue(
            self::FIRST_TIME_SIGN_IN_MESSAGE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return string
     */
    public function getMorningSignInMessage()
    {
        return $this->scopeConfig->getValue(
            self::MORNING_SIGN_IN_MESSAGE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return string
     */
    public function getAfternoonSignInMessage()
    {
        return $this->scopeConfig->getValue(
            self::AFTERNOON_SIGN_IN_MESSAGE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return string
     */
    public function getAfterWeekSignInMessage()
    {
        return $this->scopeConfig->getValue(
            self::AFTER_WEEK_SIGN_IN_MESSAGE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return string
     */
    public function getCampaignMessage()
    {
        return $this->scopeConfig->getValue(
            self::CAMPAIGN_MESSAGE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return string
     */
    public function getCampaignStartTime()
    {
        return $this->scopeConfig->getValue(
            self::CAMPAIGN_START_TIME,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return string
     */
    public function getCampaignEndTime()
    {
        return $this->scopeConfig->getValue(
            self::CAMPAIGN_END_TIME,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return string
     */
    public function getRandomMessage()
    {
        return $this->scopeConfig->getValue(
            self::RANDOM_MESSAGE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return string
     */
    public function getRandomStartTime()
    {
        return $this->scopeConfig->getValue(
            self::RANDOM_START_TIME,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return string
     */
    public function getRandomEndTime()
    {
        return $this->scopeConfig->getValue(
            self::RANDOM_END_TIME,
            ScopeInterface::SCOPE_STORE
        );
    }
}
