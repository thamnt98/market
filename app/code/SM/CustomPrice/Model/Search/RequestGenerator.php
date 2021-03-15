<?php
namespace SM\CustomPrice\Model\Search;

use Magento\Catalog\Model\Entity\Attribute;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory;
use Amasty\Shopby\Helper\FilterSetting;
use Magento\CatalogSearch\Model\Search\RequestGenerator\GeneratorResolver;
use Magento\Customer\Model\Session;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Search\Request\FilterInterface;
use Magento\Framework\Search\Request\QueryInterface;
use SM\CustomPrice\Model\Customer;

/**
 * Class RequestGenerator
 * @package SM\CustomPrice\Model\Search
 */
class RequestGenerator extends \Magento\CatalogSearch\Model\Search\RequestGenerator
{
    /**
     * @var FilterSetting
     */
    protected $settingHelper;
    protected $productAttributeCollectionFactory;
    /**
     * @param CollectionFactory $productAttributeCollectionFactory
     */

    protected $currentAttribute = null;
    protected $customerSession;
    protected $generatorResolver;

    public function __construct(
        CollectionFactory $productAttributeCollectionFactory,
        FilterSetting $settingHelper,
        Session $session,
        GeneratorResolver $generatorResolver = null
    ) {
        $this->productAttributeCollectionFactory = $productAttributeCollectionFactory;
        $this->settingHelper                     = $settingHelper;
        $this->customerSession                   = $session;
        parent::__construct($productAttributeCollectionFactory);
        $this->generatorResolver = $generatorResolver
            ?: ObjectManager::getInstance()->get(GeneratorResolver::class);
        $this->currentAttribute = $session->getOmniFinalPriceAttributeCode();

    }

    /**
     * @return array
     */
    public function generate()
    {
        $requests = [];
        if (empty($this->currentAttribute)) {
            return [];
        }
        $requests['catalog_view_container'] = $this->generateCustomPriceRequest(
            'catalog_view_container'
        );
        $requests['quick_search_container'] = $this->generateCustomPriceRequest(
            'quick_search_container'
        );
        return $requests;
    }

    /**
     * @param $container
     * @return array
     */
    protected function generateCustomPriceRequest($container)
    {
        $request = [];
        foreach ($this->getSearchableAttributes() as $attribute) {
            /** @var $attribute Attribute */
            if (!in_array($attribute->getAttributeCode(), ['price', 'category_ids'], true)) {
                $queryName                                          = $attribute->getAttributeCode() . '_query';
                $request['queries'][$container]['queryReference'][] = [
                    'clause' => 'must',
                    'ref'    => $queryName,
                ];
                $filterName                                         = $attribute->getAttributeCode() . self::FILTER_SUFFIX;
                $request['queries'][$queryName]                     = [
                    'name'            => $queryName,
                    'type'            => QueryInterface::TYPE_FILTER,
                    'filterReference' => [
                        [
                            'clause' => 'must',
                            'ref'    => $filterName,
                        ]
                    ],
                ];
                $bucketName                                         = $attribute->getAttributeCode() . self::BUCKET_SUFFIX;
                $generatorType                                      = $attribute->getFrontendInput() === 'price'
                    ? $attribute->getFrontendInput()
                    : $attribute->getBackendType();
                $generator                                          = $this->generatorResolver->getGeneratorForType($generatorType);
                $request['filters'][$filterName]                    = $generator->getFilterData($attribute,
                    $filterName);
                $request['aggregations'][$bucketName]               = $generator->getAggregationData($attribute,
                    $bucketName);
            }
            if (!$attribute->getIsSearchable() || in_array($attribute->getAttributeCode(), ['price'], true)) {
                // Some fields have their own specific handlers
                continue;
            }
        $request = $this->processPriceAttribute(false, $attribute, $request);
        }
        return $request;
    }

    protected function getSearchableAttributes()
    {
        /** @var \Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection $productAttributes */
        $productAttributes = $this->productAttributeCollectionFactory->create();
        $productAttributes->addFieldToFilter('attribute_code', array(
            array('like' => Customer::PREFIX_OMNI_FINAL_PRICE.'%')
        ));
        return $productAttributes;
    }

    /**
     * Modify request for price attribute.
     *
     * @param bool      $useFulltext
     * @param Attribute $attribute
     * @param array     $request
     * @return array
     */
    private function processPriceAttribute($useFulltext, $attribute, $request)
    {
        // Match search by custom price attribute isn't supported
        if ($useFulltext && $attribute->getFrontendInput() !== 'price') {
            $request['queries']['search']['match'][] = [
                'field' => $attribute->getAttributeCode(),
                'boost' => $attribute->getSearchWeight() ?: 1,
            ];
        }

        return $request;
    }
}
