<?php

namespace SM\MobileApi\Model\GreetingMessage;

use SM\MobileApi\Helper\GreetingMessage\Config;
use SM\MobileApi\Model\Data\HomepageMessage\ConfigMessageFactory;

/**
 * Class Resolver
 * @package SM\MobileApi\Model\GreetingMessage
 */
class Resolver
{
    /**
     * @var Config
     */
    protected $messageConfig;

    /**
     * @var ConfigMessageFactory
     */
    protected $configMessageFactory;

    /**
     * Resolver constructor.
     * @param Config $messageConfig
     * @param ConfigMessageFactory $configMessageFactory
     */
    public function __construct(
        Config $messageConfig,
        ConfigMessageFactory $configMessageFactory
    ) {
        $this->messageConfig        = $messageConfig;
        $this->configMessageFactory = $configMessageFactory;
    }

    /**
     * @param array $listingConfigMessage
     * @return \SM\MobileApi\Api\Data\HomepageMessage\ConfigMessageInterface[]
     */
    public function resolveConfigMessage(array $listingConfigMessage)
    {
        $configMessage = [];
        foreach ($listingConfigMessage as $type => $message) {
            $configMessageModel = $this->configMessageFactory->create();
            $configMessageModel->setMessage($message);
            $configMessageModel->setType($type);
            $configMessage[] = $configMessageModel;
        }

        return $configMessage;
    }
}
