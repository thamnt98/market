<?php

declare(strict_types=1);

namespace SM\Search\Observer;

use Magento\Catalog\Model\Product;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use SM\Search\Helper\Config;
use SM\Search\Model\Product\Attribute\Resolver;

class CatalogProductSaveBefore implements ObserverInterface
{
    /**
     * @var Resolver
     */
    protected $resolver;

    /**
     * Construct
     *
     * @param Resolver $resolver
     */
    public function __construct(
        Resolver $resolver
    ) {
        $this->resolver = $resolver;
    }

    /**
     * @param EventObserver $observer
     */
    public function execute(EventObserver $observer): void
    {
        /** @var Product $product */
        $product = $observer->getEvent()->getProduct();
        $product->setCustomAttribute(
            Config::CATEGORY_NAMES_ATTRIBUTE_CODE,
            $this->resolver->resolveCategoryNamesAttribute($product)
        );
    }
}
