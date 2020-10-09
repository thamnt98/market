<?php

namespace SM\MobileApi\Model\Product;

/**
 * Class Attribute
 * @package SM\MobileApi\Model\Product
 */
class Attribute
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory
     */
    protected $attributeCollectionFactory;

    /**
     * @var \SM\MobileApi\Model\Data\Store\ProductAttributesFactory
     */
    protected $productAttributesFactory;

    /**
     * @var \SM\MobileApi\Model\Data\Store\Setting\ProductAttributeFactory
     */
    protected $productAttributeFactory;

    /**
     * List of attributes should not return to mobile.
     * Ex: category_ids
     *
     * @var array
     */
    protected $excludedAttributes = [
        'category_ids', 'image', 'media_gallery', 'thumbnail', 'gallery'
    ];

    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $attributeCollectionFactory,
        \SM\MobileApi\Model\Data\Store\ProductAttributesFactory $productAttributesFactory,
        \SM\MobileApi\Model\Data\Store\Setting\ProductAttributeFactory $productAttributeFactory
    ) {
        $this->attributeCollectionFactory = $attributeCollectionFactory;
        $this->productAttributesFactory = $productAttributesFactory;
        $this->productAttributeFactory = $productAttributeFactory;
    }

    /**
     * Get product attributes
     *
     * @return \SM\MobileApi\Api\Data\Store\Setting\ProductAttributeInterface[]
     */
    public function getAttributes()
    {
        $attributeNull = $this->productAttributeFactory->create();
        $attributeNull->setLabel('');
        $attributeNull->setValue('');

        $data[] = $attributeNull;

        $collection = $this->attributeCollectionFactory->create()->addVisibleFilter();
        $collection->addFieldToFilter('main_table.attribute_code', ['nin' => $this->excludedAttributes]);

        foreach ($collection as $item) {
            /* @var $item \Magento\Eav\Model\Entity\Attribute */
            $attributeData = $this->productAttributeFactory->create();
            $attributeData->setLabel($item->getFrontendLabel());
            $attributeData->setValue($item->getAttributeCode());

            $data[] = $attributeData;
        }

        return $data;
    }
}
