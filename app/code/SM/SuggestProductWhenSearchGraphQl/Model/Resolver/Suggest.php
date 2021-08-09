<?php

namespace SM\SuggestProductWhenSearchGraphQl\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

use SM\SuggestProductWhenSearchGraphQl\Api\AutocompleteInterface;

/**
 * Class Suggest
 * @package SM\RecommendSearchCatalogGraphQl\Model\Resolver
 */
class Suggest implements ResolverInterface
{
    /**
     * @var AutocompleteInterface
     */
    protected $autocomplete;

    /**
     * Suggest constructor.
     * @param AutocompleteInterface $autocomplete
     */
    public function __construct(
        AutocompleteInterface $autocomplete
    )
    {
        $this->autocomplete = $autocomplete;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $keyword = $args['keyword'] ?? null;
        $autocompleteData = $this->autocomplete->getItems($keyword);
        $responseData = [];
        foreach ($autocompleteData as $resultItem) {
            $responseData[] = $resultItem->toArray();
        }
        return $responseData;
    }
}
