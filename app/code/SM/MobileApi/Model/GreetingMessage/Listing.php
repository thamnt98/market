<?php

namespace SM\MobileApi\Model\GreetingMessage;

use Magento\Framework\App\CacheInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Store\Model\StoreManagerInterface;
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
    const EVENING_MESSAGE    = 'evening_message';
    const AFTER_WEEK_MESSAGE = 'after_week_message';

    const CACHE_MESSAGE_CONFIG_KEY = 'config_message_cache';
    const CACHE_MESSAGE_KEY        = 'message_cache';

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
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Listing constructor.
     * @param Config $config
     * @param CacheInterface $cache
     * @param SerializerInterface $serializer
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Config $config,
        CacheInterface $cache,
        SerializerInterface $serializer,
        StoreManagerInterface $storeManager
    ) {
        $this->messageConfig = $config;
        $this->cache         = $cache;
        $this->serializer    = $serializer;
        $this->storeManager  = $storeManager;
    }

    /**
     * Get list message with time range logic
     * @param string $name
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getList($name)
    {
        $storeCode = $this->storeManager->getStore()->getCode();
        $cacheKey  = self::CACHE_MESSAGE_KEY . $storeCode;
        $cache     = $this->cache->load($cacheKey);

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
                self::MESSAGE    => $randomMessage,
                self::REDIRECT   => null
            ];
            $this->cache->save($this->serializer->serialize($data), $cacheKey);
            $cache = $this->cache->load($cacheKey);
        }
        $cache = $this->serializer->unserialize($cache);
        if (isset($cache[1])) {
            $cache[1][self::MESSAGE] = sprintf($cache[1][self::MESSAGE], $name);
        }
        return $cache;
    }

    /**
     * Get list message without time range logic
     * @param string $name
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getConfigMessage($name)
    {
        $storeCode = $this->storeManager->getStore()->getCode();
        $cacheKey  = self::CACHE_MESSAGE_CONFIG_KEY . $storeCode;
        $cache     = $this->cache->load($cacheKey);

        if (!$cache) {
            $firstTimeMessage = $this->messageConfig->getFirstTimeSignInMessage();
            $morningMessage   = $this->messageConfig->getMorningSignInMessage();
            $afternoonMessage = $this->messageConfig->getAfternoonSignInMessage();
            $eveningMessage   = $this->messageConfig->getEveningSignInMessage();
            $afterWeekSignIn  = $this->messageConfig->getAfterWeekSignInMessage();

            $messageConfig = [
                self::FIRST_TIME_MESSAGE => sprintf($firstTimeMessage, $name),
                self::MORNING_MESSAGE    => sprintf($morningMessage, $name),
                self::AFTERNOON_MESSAGE  => sprintf($afternoonMessage, $name),
                self::EVENING_MESSAGE    => sprintf($eveningMessage, $name),
                self::AFTER_WEEK_MESSAGE => sprintf($afterWeekSignIn, $name)
            ];

            $messageJson = $this->serializer->serialize($messageConfig);
            $this->cache->save($messageJson, $cacheKey, []);

            $cache = $this->cache->load($cacheKey);
            return $this->serializer->unserialize($cache);
        }

        return $this->serializer->unserialize($cache);
    }
}
