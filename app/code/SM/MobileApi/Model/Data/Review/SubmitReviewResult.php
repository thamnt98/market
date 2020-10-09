<?php

namespace SM\MobileApi\Model\Data\Review;

/**
 * Class SubmitReviewResult
 * @package SM\MobileApi\Model\Data\Review
 */
class SubmitReviewResult extends \Magento\Framework\Model\AbstractExtensibleModel implements \SM\MobileApi\Api\Data\Review\SubmitReviewResultInterface
{
    public function getSuccess()
    {
        return $this->getData(self::SUCCESS);
    }

    public function setSuccess($value)
    {
        return $this->setData(self::SUCCESS, $value);
    }
}
