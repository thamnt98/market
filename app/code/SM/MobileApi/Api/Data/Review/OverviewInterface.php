<?php
namespace SM\MobileApi\Api\Data\Review;

/**
 * Interface for storing review overview
 */
interface OverviewInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const TITLE = 'title';
    const CODE = 'code';
    const TYPE = 'type';
    const REVIEW_COUNTER = 'review_counter';
    const RATING_SUMMARY_PERCENT = 'rating_summary_percent';
    const RATING_SUMMARY_AMOUNT = 'rating_summary_amount';
    const PRODUCT_NAME = 'product_name';

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
     * Get reviews count
     *
     * @return string
     */
    public function getProductName();

    /**
     * @param string $name
     * @return $this
     */
    public function setProductName($name);

    /**
     * Get rating count
     *
     * @return int
     */
    public function getRatingSummaryPercent();

    /**
     * @param int $rating
     * @return $this
     */
    public function setRatingSummaryPercent($rating);
    /**
     * Get rating count
     *
     * @return float
     */
    public function getRatingSummaryAmount();

    /**
     * @param float $rating
     * @return $this
     */
    public function setRatingSummaryAmount($rating);

    /**
     * Get review count
     *
     * @return int
     */
    public function getReviewCounter();

    /**
     * @param int $reviewCounter
     * @return $this
     */
    public function setReviewCounter($reviewCounter);

}
