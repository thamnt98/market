<?php

namespace SM\CatalogGraphQl\Model\Resolver;

use Magento\Catalog\Model\CategoryRepository;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Store\Api\Data\StoreInterface;
use Magento\CatalogGraphQl\DataProvider\Product\LayeredNavigation\LayerBuilder;

/**
 * Class AddCategoryParentToAggregation
 * @package SM\CatalogGraphQl\Model\Resolver
 */
class AddCategoryParentToAggregation implements ResolverInterface
{
    /**
     * @var LayerBuilder
     */
    private $layerBuilder;
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * AddCategoryParentToAggregation constructor.
     * @param LayerBuilder $layerBuilder
     * @param CategoryRepository $categoryRepository
     */
    public function __construct(
        LayerBuilder $layerBuilder,
        CategoryRepository $categoryRepository
    )
    {
        $this->layerBuilder = $layerBuilder;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @inheritdoc
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    )
    {
        if (!isset($value['layer_type']) || !isset($value['search_result'])) {
            return null;
        }

        $aggregations = $value['search_result']->getSearchAggregation();

        if ($aggregations) {
            /** @var StoreInterface $store */
            $store = $context->getExtensionAttributes()->getStore();
            $storeId = (int)$store->getId();
            $aggregations = $this->layerBuilder->build($aggregations, $storeId);
            foreach ($aggregations as $k1 => $aggregation) {
                if ($aggregation['attribute_code'] == 'category_id') {
                    $options = $aggregation['options'];
                    foreach ($options as $k2 => $option) {
                        $categoryId = $option['value'];
                        $parentCategoryId = $this->categoryRepository->get($categoryId, $storeId)->getParentId();
                        $options[$k2]['parent'] = $parentCategoryId;
                    }
                    $aggregations[$k1]['options'] = $options;
                    break;
                }
            }
            return $aggregations;
        } else {
            return [];
        }
    }
}
