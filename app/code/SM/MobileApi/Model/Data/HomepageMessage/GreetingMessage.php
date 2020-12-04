<?php


namespace SM\MobileApi\Model\Data\HomepageMessage;

use Magento\Framework\Api\AbstractExtensibleObject;
use SM\MobileApi\Api\Data\HomepageMessage\GreetingMessageInterface;

/**
 * Class GreetingMessage
 * @package SM\MobileApi\Model\Data\HomepageMessage
 */
class GreetingMessage extends AbstractExtensibleObject implements GreetingMessageInterface
{

    /**
     * @inheritDoc
     */
    public function getMessage()
    {
        return $this->_get(self::MESSAGE);
    }

    /**
     * @inheritDoc
     */
    public function setMessage($message)
    {
        return $this->setData(self::MESSAGE, $message);
    }

    /**
     * @inheritDoc
     */
    public function getTimeRange()
    {
        return $this->_get(self::TIME_RANGE);
    }

    /**
     * @inheritDoc
     */
    public function setTimeRange($time)
    {
        return $this->setData(self::TIME_RANGE, $time);
    }

    /**
     * @inheritDoc
     */
    public function getRedirectType()
    {
        return $this->_get(self::REDIRECT_TYPE);
    }

    /**
     * @inheritDoc
     */
    public function setRedirectType($type)
    {
        return $this->setData(self::REDIRECT_TYPE, $type);
    }

    /**
     * @inheritDoc
     */
    public function getConfig()
    {
        return $this->_get(self::CONFIG);
    }

    /**
     * @inheritDoc
     */
    public function setConfig($config)
    {
        return $this->setData(self::CONFIG, $config);
    }
}
