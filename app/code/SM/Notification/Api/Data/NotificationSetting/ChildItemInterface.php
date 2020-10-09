<?php

namespace SM\Notification\Api\Data\NotificationSetting;

interface ChildItemInterface
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
     * @param string $code
     * @return $this
     */
    public function setParentCode($code);

    /**
     * @return string
     */
    public function getParentCode();

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
     * @param int $value
     * @return $this
     */
    public function setValue($value);

    /**
     * @return int
     */
    public function getValue();

    /**
     * @return int
     */
    public function getParentId();

    /**
     * @param int $data
     * @return $this
     */
    public function setParentId($data);
}
