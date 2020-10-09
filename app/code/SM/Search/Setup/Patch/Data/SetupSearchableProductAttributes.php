<?php

declare(strict_types=1);

namespace SM\Search\Setup\Patch\Data;

use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use SM\Search\Helper\Config;

class SetupSearchableProductAttributes implements DataPatchInterface
{
    const VERSION = '1.1.0';

    const SEARCHABLE_ATTRIBUTES = [
        'sku',
        'name',
        'brand',
        Config::CATEGORY_NAMES_ATTRIBUTE_CODE,
    ];

    const SKIP_ATTRIBUTES = [
        'url_key',
        'status',
        'price',
    ];

    /**
     * @var EavSetup
     */
    protected $eavSetup;

    /**
     * @var ProductAttributeRepositoryInterface
     */
    protected $attributeRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory $eavSetupFactory
     * @param ProductAttributeRepositoryInterface $attributeRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @codeCoverageIgnore
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory,
        ProductAttributeRepositoryInterface $attributeRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->eavSetup = $eavSetupFactory->create(['setup' => $moduleDataSetup]);
        $this->attributeRepository = $attributeRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @inheritdoc
     * @throws \Exception
     */
    public function apply(): void
    {
        $searchResult = $this->attributeRepository->getList($this->searchCriteriaBuilder->create());
        foreach ($searchResult->getItems() as $attribute) {
            if (\in_array($attribute->getAttributeCode(), self::SKIP_ATTRIBUTES)) {
                continue;
            }

            if (\in_array($attribute->getAttributeCode(), self::SEARCHABLE_ATTRIBUTES) && !$attribute->getIsSearchable()) {
                $attribute->setIsSearchable(1);
                $this->attributeRepository->save($attribute);
            }

            if (!\in_array($attribute->getAttributeCode(), self::SEARCHABLE_ATTRIBUTES) && $attribute->getIsSearchable()) {
                $attribute->setIsSearchable(0);
                $this->attributeRepository->save($attribute);
            }
        }
    }

    /**
     * @inheritdoc
     * @codeCoverageIgnore
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * @inheritdoc
     * @codeCoverageIgnore
     */
    public function getAliases(): array
    {
        return [];
    }
}
