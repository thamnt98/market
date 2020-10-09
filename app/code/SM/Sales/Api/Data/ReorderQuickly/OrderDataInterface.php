<?php


namespace SM\Sales\Api\Data\ReorderQuickly;

/**
 * Interface OrderDataInterface
 * @package SM\Sales\Api\Data\ReorderQuickly
 */
interface OrderDataInterface
{
    const ENTITY_ID = "entity_id";
    const TRANSACTION_ID = "transaction_id";
    const CREATED_AT = "created_at";
    const GRAND_TOTAL = "grand_total";
    const ITEM_LEFT = "item_left";
    const ITEM_IMAGES = "item_images";

    /**
     * @return int
     */
    public function getEntityId();

    /**
     * @param int $value
     * @return $this
     */
    public function setEntityId($value);

    /**
     * @return string
     */
    public function getTransactionId();

    /**
     * @param string $value
     * @return $this
     */
    public function setTransactionId($value);

    /**
     * @return string
     */
    public function getCreatedAt();

    /**
     * @param string $value
     * @return $this
     */
    public function setCreatedAt($value);

    /**
     * @return int
     */
    public function getGrandTotal();

    /**
     * @param int $value
     * @return $this
     */
    public function setGrandTotal($value);

    /**
     * @return int
     */
    public function getItemLeft();

    /**
     * @param int $value
     * @return $this
     */
    public function setItemLeft($value);

    /**
     * @return string[]
     */
    public function getItemImages();

    /**
     * @param string[] $value
     * @return $this
     */
    public function setItemImages($value);
}
