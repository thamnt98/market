<?php
/**
 * Copyright © 2017 JMango360. All rights reserved.
 */

namespace SM\MobileApi\Api\Data\Review;

/**
 * Interface for storing submit review response
 */
interface SubmitReviewResultInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const SUCCESS = 'success';

    /**
     * Get success message
     *
     * @return string
     */
    public function getSuccess();

    /**
     * @param string $value
     * @return $this
     */
    public function setSuccess($value);
}
