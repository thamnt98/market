<?php

namespace SM\MobileApi\Api;

/**
 * Interface HomeInterface
 * @package SM\MobileApi\Api
 */
interface HomeInterface
{
    /**
     * @return \SM\HeroBanner\Api\Data\BannerInterface[]
     */
    public function getHomeSlider();

    /**
     * @return \SM\MobileApi\Api\Data\Product\ListInterface
     */
    public function getMostPopular();

    /**
     * @param int $customerId
     * @return \SM\MobileApi\Api\Data\HomepageMessage\GreetingMessageInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getGreetingMessage($customerId);
}
