<?php
namespace SM\HomePageGraphQl\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

/**
 * Class Curated
 * @package SM\HomePageGraphQl\Model\Resolver
 */
class Curated implements ResolverInterface
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
                    'title' => '',
                    'content' => '',
                    'image' => rtrim($baseUrl, '/') . '/media/resized/452/k-beauty.png'
                ],
                [
                    'title' => '',
                    'content' => '',
                    'image' => rtrim($baseUrl, '/') . '/media/resized/452/female-daily.png'
                ],
                [
                    'title' => '',
                    'content' => '',
                    'image' => rtrim($baseUrl, '/') . '/media/resized/452/style-edit.png'
                ],
                [
                    'title' => '',
                    'content' => '',
                    'image' => rtrim($baseUrl, '/') . '/media/resized/452/home-fair.png'
                ],
            ]
        ];

        return $data;
    }
}
