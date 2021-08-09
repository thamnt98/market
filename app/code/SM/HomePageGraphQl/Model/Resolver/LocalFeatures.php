<?php
namespace SM\HomePageGraphQl\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

/**
 * Class LocalFeatures
 * @package SM\HomePageGraphQl\Model\Resolver
 */
class LocalFeatures implements ResolverInterface
{
    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        // Placeholder data
        $store = $context->getExtensionAttributes()->getStore();
        $baseUrl = $store->getBaseUrl();

        $data = [
            'items' => [
                [
                    'title' => 'BRODO',
                    'content' => 'Simple yet luxurious shoes for gentle- men with thoughtful design.',
                    'image' => rtrim($baseUrl, '/') . '/media/home/local_features/local_feature1.png'
                ],
                [
                    'title' => 'BLP BEAUTY',
                    'content' => 'Beauty products for everyone to feel good, look good & live at their best.',
                    'image' => rtrim($baseUrl, '/') . '/media/home/local_features/local_feature2.png'
                ],
                [
                    'title' => 'SEJAUH MATA MEMAN...',
                    'content' => 'Longlasting & timeless clothing with environmental & social impacts.',
                    'image' => rtrim($baseUrl, '/') . '/media/home/local_features/local_feature3.png'
                ],
                [
                    'title' => 'NAH PROJECT',
                    'content' => 'Reasonable price, high quality products with transparent pricing.',
                    'image' => rtrim($baseUrl, '/') . '/media/home/local_features/local_feature4.png'
                ],
            ]
        ];

        return $data;
    }
}
