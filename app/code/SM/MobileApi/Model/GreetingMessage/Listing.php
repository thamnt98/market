<?php

namespace SM\MobileApi\Model\GreetingMessage;

use SM\MobileApi\Helper\GreetingMessage\Config;
use SM\Notification\Model\Source\RedirectType;

/**
 * Class Listing
 * @package SM\MobileApi\Model\GreetingMessage
 */
class Listing
{
    const START_TIME = 'start_time';
    const END_TIME   = 'end_time';
    const MESSAGE    = 'message';
    const REDIRECT   = 'redirect';

    const FIRST_TIME_MESSAGE = 'first_time_message';
    const MORNING_MESSAGE    = 'morning_message';
    const AFTERNOON_MESSAGE  = 'afternoon_message';
    const AFTER_WEEK_MESSAGE = 'after_week_message';

    /**
     * @var Config
     */
    protected $messageConfig;


    /**
     * Listing constructor.
     * @param Config $config
     */
    public function __construct(
        Config $config
    ) {
        $this->messageConfig = $config;
    }

    /**
     * @param string $name
     * @return array
     */
    public function getList($name)
    {
        $data  = [];

        //Get campaign message config
        $campaignStartTime = $this->messageConfig->getCampaignStartTime();
        $campaignEndTime   = $this->messageConfig->getCampaignEndTime();
        $campaignMessage   = $this->messageConfig->getCampaignMessage();
        $campaignRedirect  = RedirectType::TYPE_CAMPAIGN;

        $data[] = [
                self::START_TIME => str_replace(',', ':', $campaignStartTime),
                self::END_TIME   => str_replace(',', ':', $campaignEndTime),
                self::MESSAGE    => sprintf($campaignMessage, $campaignRedirect),
                self::REDIRECT   => $campaignRedirect
            ];

        //Get random message config
        $randomStartTime = $this->messageConfig->getRandomStartTime();
        $randomEndTime   = $this->messageConfig->getRandomEndTime();
        $randomMessage   = $this->messageConfig->getRandomMessage();

        $data[] = [
                self::START_TIME => str_replace(',', ':', $randomStartTime),
                self::END_TIME   => str_replace(',', ':', $randomEndTime),
                self::MESSAGE    => sprintf($randomMessage, $name),
                self::REDIRECT   => null
            ];

        return $data;
    }

    /**
     * @param string $name
     * @return array
     */
    public function getConfigMessage($name)
    {
        $firstTimeMessage = $this->messageConfig->getFirstTimeSignInMessage();
        $morningMessage   = $this->messageConfig->getMorningSignInMessage();
        $afternoonMessage = $this->messageConfig->getAfternoonSignInMessage();
        $afterWeekSignIn  = $this->messageConfig->getAfterWeekSignInMessage();

        $messageConfig = [
                self::FIRST_TIME_MESSAGE => sprintf($firstTimeMessage, $name),
                self::MORNING_MESSAGE    => sprintf($morningMessage, $name),
                self::AFTERNOON_MESSAGE  => sprintf($afternoonMessage, $name),
                self::AFTER_WEEK_MESSAGE => sprintf($afterWeekSignIn, $name)
            ];

        return $messageConfig;
    }
}
