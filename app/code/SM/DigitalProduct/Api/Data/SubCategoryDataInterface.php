<?php


namespace SM\DigitalProduct\Api\Data;

/**
 * Interface SubCategoryDataInterface
 * @package SM\DigitalProduct\Api\Data
 */
interface SubCategoryDataInterface
{
    const TYPE = "type";
    const CATEGORY_NAME = "category_name";
    const TOOLTIP = "tooltip";
    const INFO = "info";
    const HOW_TO_BUY = "how_to_buy";

    /**
     * @return string
     */
    public function getType();

    /**
     * @param string$value
     * @return $this
     */
    public function setType($value);

    /**
     * @return string
     */
    public function getCategoryName();

    /**
     * @param string $value
     * @return $this
     */
    public function setCategoryName($value);

    /**
     * @return string
     */
    public function getTooltip();

    /**
     * @param string $value
     * @return $this
     */
    public function setTooltip($value);

    /**
     * @return string
     */
    public function getInfo();

    /**
     * @param string $value
     * @return $this
     */
    public function setInfo($value);

    /**
     * @return string
     */
    public function getHowToBuy();

    /**
     * @param string $value
     * @return $this
     */
    public function setHowToBuy($value);
}
