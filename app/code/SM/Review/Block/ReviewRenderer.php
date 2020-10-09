<?php

namespace SM\Review\Block;

use Magento\Catalog\Model\Product;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template\Context;
use Magento\Review\Model\ReviewFactory;
use Magento\Review\Model\ReviewSummaryFactory;

/**
 * Class ReviewRenderer
 * @package SM\Review\Block
 */
class ReviewRenderer extends \Magento\Review\Block\Product\ReviewRenderer
{
    const TOP_VIEW = "TOP_VIEW";
    const BOTTOM_VIEW = "BOTTOM_VIEW";

    /**
     * @var ReviewSummaryFactory
     */
    protected $summaryFactory;

    /**
     * ReviewRenderer constructor.
     * @param Context $context
     * @param ReviewFactory $reviewFactory
     * @param ReviewSummaryFactory $summaryFactory
     * @param array $data
     * @param ReviewSummaryFactory|null $reviewSummaryFactory
     */
    public function __construct(
        Context $context,
        ReviewFactory $reviewFactory,
        ReviewSummaryFactory $summaryFactory,
        array $data = [],
        ReviewSummaryFactory $reviewSummaryFactory = null
    ) {
        $this->summaryFactory = $summaryFactory;
        parent::__construct($context, $reviewFactory, $data, $reviewSummaryFactory);
    }

    /**
     * @param Product $product
     * @param string $templateType
     * @param bool $displayIfNoReviews
     * @return string
     * @throws NoSuchEntityException
     */
    public function getSummaryShort(
        Product $product,
        $templateType = self::BOTTOM_VIEW,
        $displayIfNoReviews = false
    ) {
        if ($product->getRatingSummary() === null) {
            $this->summaryFactory->create()->appendSummaryDataToObject(
                $product,
                $this->_storeManager->getStore()->getId()
            );
        }

        if ($templateType == self::BOTTOM_VIEW) {
            $this->setTemplate("SM_Review::helper/summary_short.phtml");
        } else {
            $this->setTemplate("SM_Review::helper/summary.phtml");
        }

        $this->setProduct($product);

        return $this->toHtml();
    }
}
