<?php
namespace SM\CategoryGraphQl\Model\Resolver;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\ResourceModel\Category\Collection;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use SM\Category\Model\Entity\Attribute\Source\CategoryType;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

/**
 * Class FavoriteBrands
 * @package SM\CategoryGraphQl\Model\Resolver
 */
class FavoriteBrands implements ResolverInterface
{
    const CONFIG_FAVORITE_BRAND = 'trans_catalog/brand/favorite_brands';

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * FavoriteBrands constructor.
     * @param CollectionFactory $collectionFactory
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $store = $context->getExtensionAttributes()->getStore();
        $baseUrl = $store->getBaseUrl();

        $favoriteBrandIds = $this->scopeConfig->getValue(self::CONFIG_FAVORITE_BRAND);
        if (!$favoriteBrandIds) {
            return [
                'total_count' => 0,
                'items' => []
            ];
        }

        $collection = $this->collectionFactory->create();
        $collection->addFieldToSelect('name')
            ->addFieldToSelect('logo')
            ->addFieldToFilter('category_type', CategoryType::TYPE_BRAND)
            ->addFieldToFilter('is_active', 1)
            ->addIdFilter($favoriteBrandIds)
            ->setOrder('name', Collection::SORT_ORDER_ASC);

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
