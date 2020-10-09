<?php


namespace SM\CustomPrice\Pricing\Price;


use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\LinkedProductSelectBuilderInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\App\ResourceConnection;
use Magento\Store\Model\StoreManagerInterface;

class LowestPriceOptionsProvider extends \Magento\ConfigurableProduct\Pricing\Price\LowestPriceOptionsProvider
{
    protected $resource;
    protected $linkedProductSelectBuilder;
    protected $collectionFactory;
    protected $storeManager;
    protected $linkedProductMap;
    /**
     * @var Session
     */
    protected $customerSession;
    protected $basePrice  = null;
    protected $promoPrice = null;

    public function __construct(
        ResourceConnection $resourceConnection,
        LinkedProductSelectBuilderInterface $linkedProductSelectBuilder,
        CollectionFactory $collectionFactory,
        StoreManagerInterface $storeManager,
        Session $session,
        \Magento\Eav\Model\Config $eavConfig
    ) {
        parent::__construct($resourceConnection, $linkedProductSelectBuilder, $collectionFactory, $storeManager);
        $this->resource                   = $resourceConnection;
        $this->linkedProductSelectBuilder = $linkedProductSelectBuilder;
        $this->collectionFactory          = $collectionFactory;
        $this->storeManager               = $storeManager;
        $this->customerSession            = $session;
        if ($session->isLoggedIn()) {
            $this->promoPrice = $session->getOmniFinalPriceAttributeCode();
            $this->basePrice  = $session->getOmniNormalPriceAttributeCode();
            $promoPrice       = $eavConfig->getAttribute('catalog_product', $this->promoPrice);
            $basePrice        = $eavConfig->getAttribute('catalog_product', $this->basePrice);
            if (!$promoPrice || !$promoPrice->getAttributeId()) {
                $this->promoPrice = null;
            }
            if (!$basePrice || !$basePrice->getAttributeId()) {
                $this->basePrice = null;
            }

        }

    }

    public function getProducts(ProductInterface $product)
    {
        $key = $this->storeManager->getStore()->getId() . '-' . $product->getId();
        if (!isset($this->linkedProductMap[$key])) {
            $productIds        = $this->resource->getConnection()->fetchCol(
                '(' . implode(') UNION (', $this->linkedProductSelectBuilder->build($product->getId())) . ')'
            );
            $productCollection = $this->collectionFactory->create()
                                                         ->addAttributeToSelect(
                                                             [
                                                                 'price',
                                                                 'special_price',
                                                                 'special_from_date',
                                                                 'special_to_date',
                                                                 'tax_class_id'
                                                             ]
                                                         );

            if (!empty($this->basePrice)) {
                $productCollection->addAttributeToSelect($this->basePrice);
            }
            if (!empty($this->promoPrice)) {
                $productCollection->addAttributeToSelect($this->promoPrice);
            }
            $productCollection
                ->addIdFilter($productIds)
                ->getItems();
            $this->linkedProductMap[$key] = $productCollection;
        }
        return $this->linkedProductMap[$key];
    }
}
