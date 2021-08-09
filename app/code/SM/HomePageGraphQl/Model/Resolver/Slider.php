<?php
namespace SM\HomePageGraphQl\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

/**
 * Class Slider
 * @package SM\HomePageGraphQl\Model\Resolver
 */
class Slider implements ResolverInterface
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
                    'image' => rtrim($baseUrl, '/') . '/media/home/slider_banner/Homepage_Banner-01.png'
                ],
                [
                    'title' => '',
                    'content' => '',
                    'image' => rtrim($baseUrl, '/') . '/media/home/slider_banner/Homepage_Banner-02.png'
                ],
                [
                    'title' => '',
                    'content' => '',
                    'image' => rtrim($baseUrl, '/') . '/media/home/slider_banner/Homepage_Banner-03.png'
                ],
                [
                    'title' => '',
                    'content' => '',
                    'image' => rtrim($baseUrl, '/') . '/media/home/slider_banner/Homepage_Banner-04.png'
                ],
            ]
        ];

        return $data;
    }
}
