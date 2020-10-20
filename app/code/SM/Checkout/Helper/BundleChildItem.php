<?php

namespace SM\Checkout\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

class BundleChildItem extends AbstractHelper
{
    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    private $productRepository;
    /**
     * @var \Magento\Quote\Model\ResourceModel\Quote\Item\CollectionFactory
     */
    private $quoteItemCollectionFactory;

    public function __construct(
        Context $context,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Quote\Model\ResourceModel\Quote\Item\CollectionFactory $quoteItemCollectionFactory
    ) {
        $this->productRepository = $productRepository;
        $this->quoteItemCollectionFactory = $quoteItemCollectionFactory;
        parent::__construct($context);
    }

    public function getDefaultValue($item)
    {
        $arrQty = [];
        $quoteItemCollection = $this->quoteItemCollectionFactory->create();
        $quoteItemCollection->getSelect()
            ->where('parent_item_id=?', $item->getId());
        $parentProd = $this->productRepository->getById($item->getProduct()->getId());
        foreach ($parentProd->getExtensionAttributes()->getBundleProductOptions() as $options) {
            foreach ($options->getProductLinks() as $item) {
                $arrQty[$item->getEntityId()] = ['qty' => $item->getQty(), 'item_id' => ''];
            }
        }
        foreach ($quoteItemCollection as $quoteItem) {
            $arrQty[$quoteItem->getProductId()]['item_id'] = $quoteItem->getItemId();
        }
        return $arrQty;
    }
}
