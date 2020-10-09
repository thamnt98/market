<?php

namespace SM\MobileApi\Helper;

class Review
{
    /**
     * @var \Magento\Review\Model\ReviewFactory
     */
    protected $reviewFactory;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    public function __construct(
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Review\Model\ReviewFactory $reviewFactory
    ) {
        $this->reviewFactory = $reviewFactory;
        $this->moduleManager = $moduleManager;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     */
    public function isReviewEnable($product = null)
    {
        return $this->moduleManager->isEnabled('Magento_Review');
    }

    /**
     * Append review data to collection
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function addReviewData($collection)
    {
        $collection->load();
        $this->reviewFactory->create()->appendSummary($collection);
    }
}
