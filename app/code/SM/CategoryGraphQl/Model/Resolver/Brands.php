<?php
namespace SM\CategoryGraphQl\Model\Resolver;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\ResourceModel\Category\Collection;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use SM\Category\Model\Entity\Attribute\Source\CategoryType;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

/**
 * Class Brands
 * @package SM\CategoryGraphQl\Model\Resolver
 */
class Brands implements ResolverInterface
{
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * Brands constructor.
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $store = $context->getExtensionAttributes()->getStore();
        $baseUrl = $store->getBaseUrl();

        $search = false;
        if (!empty($args['search'])) {
            $search = $args['search'];
        }

        $collection = $this->collectionFactory->create();
        $collection->addFieldToSelect('name')
            ->addFieldToSelect('logo')
            ->addFieldToFilter('category_type', CategoryType::TYPE_BRAND)
            ->addFieldToFilter('is_active', 1)
            ->setOrder('name', Collection::SORT_ORDER_ASC);

        if ($search) {
            $collection->addFieldToFilter('name', ['like' => '%'.$search.'%']);
        }

        $brandArray = [];
        /** @var Category $brand */
        foreach ($collection->getItems() as $brand) {
            $brandArray[$brand->getId()] = $brand->getData();
            $brandArray[$brand->getId()]['id'] = $brand->getId();
            $brandArray[$brand->getId()]['model'] = $brand;

            if ($logoImagePath = $brand->getData('logo')) {
                $isRelativeUrl = substr($logoImagePath, 0, 1) === '/';
                if ($isRelativeUrl) {
                    $brandArray[$brand->getId()]['logo'] = rtrim($baseUrl, '/') . $logoImagePath;
                } else {
                    $brandArray[$brand->getId()]['logo'] = $brand->getImageUrl('logo');
                }
            }
        }

        $data = [
            'total_count' => $collection->getSize(),
            'items' => $brandArray
        ];

        return $data;
    }
}
