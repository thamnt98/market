<?php


namespace SM\Sales\Api\Data;

/**
 * Interface StatusHistoryDataInterface
 * @package SM\Sales\Api\Data
 */
interface StatusHistoryDataInterface
{
    const STATUS = "status";
    const LABEL = "label";
    const ORDER_UPDATE = "comment";
    const CREATED_AT = "created_at";
    const ICON = "icon";
    const ICON_CLASS = "icon_class";
    const IS_ACTIVE = "is_active";
    const RAW_FORMAT_DATE = "raw_created_at";

    /**
     * @return string
     */
    public function getStatus();

    /**
     * @return string
     */
    public function getLabel();

    /**
     * @return string
     */
    public function getOrderUpdate();

    /**
     * @return string
     */
    public function getCreatedAt();

    /**
     * @return string
     */
    public function getIcon();

    /**
     * @return string
     */
    public function getIconClass();

    /**
     * @return bool
     */
    public function getIsActive();

    /**
     * @param string $value
     * @return $this
     */
    public function setStatus($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setLabel($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setOrderUpdate($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setCreatedAt($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setIcon($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setIconClass($value);

    /**
     * @param bool $value
     * @return $this
     */
    public function setIsActive($value);
}
