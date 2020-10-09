<?php

namespace SM\Notification\Model\Data\Notification;

class ChildItem extends \Magento\Framework\Api\AbstractExtensibleObject implements \SM\Notification\Api\Data\NotificationSetting\ChildItemInterface
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
    public function setParentCode($id)
    {
        return $this->setData('parent_code', $id);
    }

    /**
     * {@inheritdoc}
     */
    public function getParentCode()
    {
        return $this->_get('parent_code');
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
    public function setValue($value)
    {
        return $this->setData('default_value', $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        return $this->_get('default_value');
    }

    /**
     * {@inheritdoc}
     */
    public function getParentId(){
        return $this->_get('parent_id');
    }

    /**
     * {@inheritdoc}
     */
    public function setParentId($data){
        return $this->setData('parent_id',$data);
    }
}
