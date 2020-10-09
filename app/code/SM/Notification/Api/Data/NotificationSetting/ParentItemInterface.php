<?php

namespace SM\Notification\Api\Data\NotificationSetting;

interface ParentItemInterface
{
    /**
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * @return int
     */
    public function getId();
    /**
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param \SM\Notification\Api\Data\NotificationSetting\ChildItemInterface[] $list
     * @return $this
     */
    public function setChildItem($list);

    /**
     * @return \SM\Notification\Api\Data\NotificationSetting\ChildItemInterface[]
     */
    public function getChildItem();
}
