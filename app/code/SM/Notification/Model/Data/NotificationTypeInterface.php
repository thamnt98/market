<?php

namespace SM\Notification\Model\Data;

class NotificationTypeInterface extends \Magento\Framework\Api\AbstractExtensibleObject implements \SM\Notification\Api\Data\NotificationTypeInterface
{
    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        return $this->setData('name', $name);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->_get('name');
    }

    /**
     * {@inheritdoc}
     */
    public function setValue($value)
    {
        return $this->setData('value', $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        return $this->_get('value');
    }
}
