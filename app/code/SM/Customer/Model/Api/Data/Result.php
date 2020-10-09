<?php


namespace SM\Customer\Model\Api\Data;

/**
 * Class Result
 * @package SM\Customer\Model\Api\Data
 */
class Result extends \Magento\Framework\Model\AbstractExtensibleModel implements \SM\Customer\Api\Data\ResultInterface
{
    /**
     * @inheritDoc
     */
    public function getMessage()
    {
        return $this->getData(self::MESSAGE);
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
    public function getArgument()
    {
        return $this->getData(self::ARGUMENT);
    }

    /**
     * @inheritDoc
     */
    public function setArgument($argument)
    {
        return $this->setData(self::ARGUMENT, $argument);
    }
}
