<?php
namespace SM\HomePageGraphQl\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

/**
 * Class OfflineBanner
 * @package SM\HomePageGraphQl\Model\Resolver
 */
class OfflineBanner implements ResolverInterface
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
            'title' => '',
            'content' => '',
            'image' => rtrim($baseUrl, '/') . '/media/home/footer/Store_Locator.png'
        ];

        return $data;
    }
}
