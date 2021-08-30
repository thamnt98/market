<?php

namespace SM\StoreGraphQl\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;

use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use \Magento\Framework\Filesystem\Io\File;

/**
 * Class Stores
 * @package SM\StoreGraphQl\Model\Resolver
 */
class Stores implements ResolverInterface
{
    /**
     * @var File
     */
    protected $directoryList;

    /**
     * Stores constructor.
     */
    public function __construct(File $directoryList)
    {
        $this->directoryList = $directoryList;
    }

    /**
     * @param Field $field
     * @param \Magento\Framework\GraphQl\Query\Resolver\ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return array
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $content = file_get_contents(__DIR__ . '../../../Data/stores.json');
        $stores = json_decode($content)->data;
        if (isset($args['search']) && !empty($args['search'])) {
            $search = trim($args['search']);
            foreach ($stores as $key => $store) {
                if (stripos($store->store_name, $search) === false) {
                    unset($stores[$key]);
                }
            }
        }
        return [
            'total_count' => count($stores),
            'items' => $stores
        ];
    }
}
