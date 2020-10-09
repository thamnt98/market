<?php

namespace SM\Notification\Model\Data\Notification;

class ParentItem extends \Magento\Framework\Api\AbstractExtensibleObject implements \SM\Notification\Api\Data\NotificationSetting\ParentItemInterface
{
    /**
     * {@inheritdoc}
     */
    public function setId($id)
    {
        return $this->setData('entity_id', $id);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->_get('entity_id');
    }

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
    public function setChildItem($list)
    {
        return $this->setData('child_item', $list);
    }

    /**
     * {@inheritdoc}
     */
    public function getChildItem()
    {
        return $this->_get('child_item');
    }
}
