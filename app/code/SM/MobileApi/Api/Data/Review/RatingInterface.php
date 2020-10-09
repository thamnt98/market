<?php
namespace SM\MobileApi\Api\Data\Review;

/**
 * Interface for storing rating data
 */
interface RatingInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const TITLE = 'title';
    const CODE = 'code';
    const TYPE = 'type';
    const ID = 'id';
    const VALUES = 'values';
    const SELECTED = 'selected';
    const PERCENT = 'percent';

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle();

    /**
     * @param string $value
     * @return $this
     */
    public function setTitle($value);

    /**
     * Get Code
     *
     * @return string
     */
    public function getCode();

    /**
     * @param string $value
     * @return $this
     */
    public function setCode($value);

    /**
     * Get Type
     *
     * @return string
     */
    public function getType();

    /**
     * @param string $value
     * @return $this
     */
    public function setType($value);

    /**
     * Get percent number
     *
     * @return int
     */
    public function getPercent();

    /**
     * @param int $value
     * @return $this
     */
    public function setPercent($value);

    /**
     * Get rating ID
     *
     * @return string
     */
    public function getId();

    /**
     * @param string $value
     * @return $this
     */
    public function setId($value);

    /**
     * Get rating values, for display stars
     *
     * @return string[]
     */
    public function getValues();

    /**
     * @param string[] $value
     * @return $this
     */
    public function setValues($value);

    /**
     * Get rating value selected
     *
     * @return string
     */
    public function getSelected();

    /**
     * @param string $value
     * @return $this
     */
    public function setSelected($value);
}
