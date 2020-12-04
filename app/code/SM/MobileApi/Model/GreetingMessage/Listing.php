<?php

namespace SM\MobileApi\Model\GreetingMessage;

use Magento\Framework\App\CacheInterface;
use Magento\Framework\Serialize\SerializerInterface;
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
     * @var CacheInterface
     */
    protected $cache;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var string
     */
    protected $cacheMessageConfigKey = 'config_message_cache';

    protected $cacheMessageKey = 'message_cache';

    /**
     * Listing constructor.
     * @param Config $config
     * @param CacheInterface $cache
     * @param SerializerInterface $serializer
     */
    public function __construct(
        Config $config,
        CacheInterface $cache,
        SerializerInterface $serializer
    ) {
        $this->messageConfig = $config;
        $this->cache = $cache;
        $this->serializer = $serializer;
    }

    /**
     * @param string $name
     * @return array
     */
    public function getList($name)
    {
        $cache = $this->cache->load($this->cacheMessageKey);

        if (!$cache) {
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

            $this->cache->save($this->serializer->serialize($data), $this->cacheMessageKey);
            $cache = $this->cache->load($this->cacheMessageKey);
            return $this->serializer->unserialize($cache);
        } else {
            $cache = $this->serializer->unserialize($cache);
        }

        return $cache;
    }

    /**
     * @param string $name
     * @return array
     */
    public function getConfigMessage($name)
    {
        $cache = $this->cache->load($this->cacheMessageConfigKey);

        if (!$cache) {
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

            $messageJson = $this->serializer->serialize($messageConfig);
            $this->cache->save($messageJson, $this->cacheMessageConfigKey, [], 500);

            $cache = $this->cache->load($this->cacheMessageConfigKey);
            return $this->serializer->unserialize($cache);
        }

        return $this->serializer->unserialize($cache);
    }
}
