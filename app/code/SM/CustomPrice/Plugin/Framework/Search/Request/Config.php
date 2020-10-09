<?php


namespace SM\CustomPrice\Plugin\Framework\Search\Request;

use Magento\Catalog\Model\Entity\Attribute;
use Magento\Framework\App\ObjectManager as ObjectManager;
use Magento\Framework\Search\Request\QueryInterface;
use SM\CustomPrice\Model\Customer as Customer;
use Magento\CatalogSearch\Model\Search\RequestGenerator as RequestGenerator;
use Magento\CatalogSearch\Model\Search\RequestGenerator\GeneratorResolver;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory as CollectionFactory;

class Config
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var CollectionFactory
     */
    protected $productAttributeCollectionFactory;

    protected $generatorResolver;

    /**
     * Config constructor.
     * @param \Magento\Customer\Model\Session $customerSession
     * @param CollectionFactory $productAttributeCollectionFactory
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        CollectionFactory $productAttributeCollectionFactory,
        GeneratorResolver $generatorResolver = null
    ) {
        $this->customerSession = $customerSession;
        $this->productAttributeCollectionFactory = $productAttributeCollectionFactory;
        $this->generatorResolver = $generatorResolver
            ?: ObjectManager::getInstance()->get(GeneratorResolver::class);
    }

    public function afterGet(\Magento\Framework\Search\Request\Config $subject, $result)
    {
        $ommiCode = $this->customerSession->getOmniStoreId();
        if (isset($result) && $ommiCode !== null) {
            if (isset($result['query']) && isset($result['size'])
                && in_array($result['query'], ['catalog_view_container', 'quick_search_container'], true)
            ) {
                $priceCode = Customer::PREFIX_OMNI_NORMAL_PRICE . $ommiCode;
                $specialPriceCode = Customer::PREFIX_OMNI_FINAL_PRICE . $ommiCode;

                $attributeCollection = $this->productAttributeCollectionFactory->create();
                $attributeCollection->addFieldToFilter('attribute_code', array(
                        array('in' => [$priceCode, $specialPriceCode])
                    )
                );
                $container = $result['query'];
                foreach ($attributeCollection as $attribute) {
                    //Add Query Reference for container
                    $queryName = $attribute->getAttributeCode() . '_query';
                    $result['queries'][$container]['queryReference'][] = [
                        'clause' => 'must',
                        'ref' => $queryName,
                    ];
                    //Add filter
                    $filterName = $attribute->getAttributeCode() . RequestGenerator::FILTER_SUFFIX;
                    $result['queries'][$queryName] = [
                        'name' => $queryName,
                        'type' => QueryInterface::TYPE_FILTER,
                        'filterReference' => [
                            [
                                'clause' => 'must',
                                'ref' => $filterName,
                            ]
                        ],
                    ];
                    //Add bucket
                    $bucketName = $attribute->getAttributeCode() . RequestGenerator::BUCKET_SUFFIX;
                    $generatorType = $attribute->getFrontendInput() === 'price' ? $attribute->getFrontendInput() : $attribute->getBackendType();
                    $generator = $this->generatorResolver->getGeneratorForType($generatorType);
                    $result['filters'][$filterName] = $generator->getFilterData($attribute,
                        $filterName);
                    $result['aggregations'][$bucketName] = $generator->getAggregationData($attribute,
                        $bucketName);
                    //Process Price for custom attribute
                    $result = $this->processPriceAttribute(false, $attribute, $result);
                }
            }
        }

        return $result;
    }

    /**
     * Modify request for price attribute.
     *
     * @param bool $useFulltext
     * @param Attribute $attribute
     * @param array $request
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
