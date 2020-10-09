<?php
namespace SM\Checkout\Model;

/**
 * @codeCoverageIgnore
 */
class AccountManagementResponse extends \Magento\Framework\Api\AbstractExtensibleObject implements \SM\Checkout\Api\Data\AccountManagementResponseInterface
{
    /**
     * Constant for confirmation status
     */
    const KEY_STATUS = 'status';
    const KEY_RESULT = 'result';

    /**
     * {@inheritdoc}
     */
    public function getStatus()
    {
        return $this->_get(self::KEY_STATUS);
    }

    /**
     * {@inheritdoc}
     */
    public function getResult()
    {
        return $this->_get(self::KEY_RESULT);
    }


    /**
     * @param bool $status
     * @return $this
     */
    public function setStatus($status)
    {
        return $this->setData(self::KEY_STATUS, $status);
    }

    /**
     * @param string $result
     * @return $this
     */
    public function setResult($result)
    {
        return $this->setData(self::KEY_RESULT, $result);
    }
}
