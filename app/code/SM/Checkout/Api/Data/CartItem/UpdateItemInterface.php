<?php

namespace SM\Checkout\Api\Data\CartItem;

interface UpdateItemInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const ITEM_ID = 'item_id';
    const IS_CHECKED = 'is_checked';

    /**
     * @return int
     */
    public function getItemId();

    /**
     * @param int $data
     * @return $this
     */
    public function setItemId($data);

    /**
     * @return boolean
     */
    public function getIsChecked();

    /**
     * @param boolean $data
     * @return $this
     */
    public function setIsChecked($data);
}
