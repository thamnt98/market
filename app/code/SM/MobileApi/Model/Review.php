<?php
/**
 * Copyright © 2017 JMango360. All rights reserved.
 */

namespace SM\MobileApi\Model;

/**
 * Class Review
 * @package SM\MobileApi\Model
 */
class Review implements \SM\MobileApi\Api\ReviewInterface
{
    protected $reviewsResultFactory;
    protected $reviewFormResultFactory;
    protected $submitReviewResultFactory;
    protected $reviewGet;
    protected $reviewSubmit;

    public function __construct(
        \SM\MobileApi\Model\Data\Review\ReviewsResultFactory $reviewsResultFactory,
        \SM\MobileApi\Model\Data\Review\ReviewFormResultFactory $reviewFormResultFactory,
        \SM\MobileApi\Model\Data\Review\SubmitReviewResultFactory $submitReviewResultFactory,
        \SM\MobileApi\Model\Review\Get $reviewGet,
        \SM\MobileApi\Model\Review\Submit $reviewSubmit
    ) {
        $this->reviewsResultFactory = $reviewsResultFactory;
        $this->reviewFormResultFactory = $reviewFormResultFactory;
        $this->submitReviewResultFactory = $submitReviewResultFactory;
        $this->reviewGet = $reviewGet;
        $this->reviewSubmit = $reviewSubmit;
    }

    public function getReviews($product_id, $limit, $p)
    {
        $this->reviewGet->init($product_id, $limit, $p);
        /* @var $result \SM\MobileApi\Api\Data\Review\ReviewsResultInterface */
        $result = $this->reviewsResultFactory->create();
        $result->setOverview($this->reviewGet->getOverview($product_id));
        $result->setReviewCounter($this->reviewGet->getReviewCounter());
        $result->setReviews($this->reviewGet->getReviews());
        return $result;
    }

    public function saveReview()
    {
        /* @var $result \SM\MobileApi\Api\Data\Review\SubmitReviewResultInterface */
        $result = $this->submitReviewResultFactory->create();
        $submitResult = $this->reviewSubmit->submit();
        if (is_string($submitResult) || $submitResult instanceof \Magento\Framework\Phrase) {
            $result->setSuccess($submitResult);
        }

        return $result;
    }
}
