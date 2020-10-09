<?php

namespace SM\MobileApi\Model\Catalog\Layer;

/**
 * Class FilterListFactory
 * @package SM\MobileApi\Model\Catalog\Layer
 */
class FilterListFactory
{
    protected $moduleManager;
    protected $objectManager;

    /**
     * FilterListFactory constructor.
     *
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\ObjectManagerInterface $objectManager
    )
    {
        $this->moduleManager = $moduleManager;
        $this->objectManager = $objectManager;
    }

    /**
     * Support: Amasty_Shopby
     *
     * @param array $data
     * @param bool $isSearch
     * @return \Magento\Catalog\Model\Layer\FilterList
     */
    public function create(array $data = array(), $isSearch = false)
    {
        if ($isSearch) {
            $data['filterableAttributes'] = $this->objectManager->create(
                'Magento\Catalog\Model\Layer\Search\FilterableAttributeList'
            );
        } else {
            $data['filterableAttributes'] = $this->objectManager->create(
                'Magento\Catalog\Model\Layer\Category\FilterableAttributeList'
            );
        }

        if ($this->moduleManager->isEnabled('Amasty_Shopby')) {
            $instanceName = 'Amasty\Shopby\Model\Layer\FilterList';
            $data['filters'] = [
                'attribute' => 'Amasty\Shopby\Model\Layer\Filter\Attribute',
                'price' => 'Amasty\Shopby\Model\Layer\Filter\Price',
                'decimal' => 'Amasty\Shopby\Model\Layer\Filter\Decimal',
//                'category' => 'Amasty\Shopby\Model\Layer\Filter\Category',
            ];
        } else {
            $instanceName = 'Magento\Catalog\Model\Layer\FilterList';
            $data['filters'] = [
                'attribute' => 'Magento\CatalogSearch\Model\Layer\Filter\Attribute',
                'price' => 'Magento\CatalogSearch\Model\Layer\Filter\Price',
                'decimal' => 'Magento\CatalogSearch\Model\Layer\Filter\Decimal',
                'category' => 'Magento\CatalogSearch\Model\Layer\Filter\Category'
            ];
        }

        return $this->objectManager->create($instanceName, $data);
    }
}
