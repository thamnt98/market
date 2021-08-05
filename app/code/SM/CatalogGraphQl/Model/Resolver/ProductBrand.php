<?php
namespace SM\CatalogGraphQl\Model\Resolver;

use Magento\Framework\Exception\LocalizedException;
use Magento\Catalog\Model\Product;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

/**
 * Class ProductBrand
 * @package SM\CatalogGraphQl\Model\Resolver
 */
class ProductBrand implements ResolverInterface
{
    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        if (!isset($value['model'])) {
            throw new LocalizedException(__('"model" value should be specified'));
        }

        /** @var Product $product */
        $product = $value['model'];
        $attributeName = 'shop_by_brand';

        $data = null;
        if ($product->getData($attributeName)) {
            $data = $product->getAttributeText($attributeName);
        }

        return $data;
    }
}
