<?php
/**
 * @category Magento
 * @package SM\DigitalProduct\Model\Source\MagentoCategory
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author Hung Viet Nguyen <hungnv6@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\DigitalProduct\Model\Source\MagentoCategory;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\ResourceModel\Category\Collection as CategoryCollection;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Options
 * @package SM\DigitalProduct\Model\Source\MagentoCategory
 */
class Options implements OptionSourceInterface
{
    /**
     * @var CategoryCollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * Options constructor.
     * @param CategoryCollectionFactory $categoryCollectionFactory
     */
    public function __construct(
        CategoryCollectionFactory $categoryCollectionFactory
    ) {
        $this->categoryCollectionFactory = $categoryCollectionFactory;
    }

    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        /** @var CategoryCollection $categoryCollection */
        $categoryCollection = $this->categoryCollectionFactory->create();
        $categoryCollection->addFieldToFilter("level", 1);

        /** @var Category $defaultCategory */
        $defaultCategory = $categoryCollection->getFirstItem();

        /** @var CategoryCollection $categoryCollection */
        $categoryCollection = $this->categoryCollectionFactory->create();
        $categoryCollection->addFieldToFilter("parent_id", $defaultCategory->getEntityId());
        $categoryCollection->addNameToResult();
        $options = [];
        /** @var Category $category */
        foreach ($categoryCollection as $category) {
            $options[] = [
                "value" => $category->getEntityId(),
                "label" => $category->getName()
            ];
        }
        return $options;
    }
}
