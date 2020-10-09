<?php


namespace SM\MobileApi\Model\Data\Catalog\Product;

use Magento\Framework\Model\AbstractExtensibleModel;
use SM\MobileApi\Api\Data\Catalog\Product\Review\OverviewInterface;

/**
 * Class Review
 * @package SM\MobileApi\Model\Data\Catalog\Product
 */
class Review extends AbstractExtensibleModel implements OverviewInterface
{
    public function getPercent()
    {
        return $this->getData(self::PERCENT);
    }

    public function setPercent($value)
    {
        return $this->setData(self::PERCENT, $value);
    }

    public function getReviewCounter()
    {
        return $this->getData(self::REVIEW_COUNTER);
    }

    public function setReviewCounter($value)
    {
        return $this->setData(self::REVIEW_COUNTER, $value);
    }
}
