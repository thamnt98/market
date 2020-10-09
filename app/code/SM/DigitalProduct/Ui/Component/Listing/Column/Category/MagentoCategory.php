<?php
/**
 * @category Magento
 * @package SM\DigitalProduct\Ui\Component\Listing\Column\Category
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author Hung Viet Nguyen <hungnv6@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\DigitalProduct\Ui\Component\Listing\Column\Category;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\ResourceModel\Category\Collection as CategoryCollection;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use SM\DigitalProduct\Helper\Category\Data;

/**
 * Class MagentoCategory
 * @package SM\DigitalProduct\Ui\Component\Listing\Column\Category
 */
class MagentoCategory extends Column
{
    /**
     * @var CategoryCollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * @var Data
     */
    protected $typeHelper;

    /**
     * MagentoCategory constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param CategoryCollectionFactory $categoryCollectionFactory
     * @param Data $typeHelper
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        CategoryCollectionFactory $categoryCollectionFactory,
        Data $typeHelper,
        array $components = [],
        array $data = []
    ) {
        $this->typeHelper = $typeHelper;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        /** @var CategoryCollection $categoryCollection */
        $categoryCollection = $this->categoryCollectionFactory->create();
        $categoryCollection->addNameToResult();

        $types = $this->typeHelper->getTypeOptions();

        $categories = [];
        /** @var Category $category */
        foreach ($categoryCollection as $category) {
            $categories[$category->getId()] = $category->getName();
        }

        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                if (isset($item["magento_category_ids"])) {
                    $catLabel = [];
                    $tmpCatIds = explode(",", $item["magento_category_ids"]);
                    foreach ($tmpCatIds as $magentoCat) {
                        if (isset($categories[$magentoCat])) {
                            $catLabel[] = $categories[$magentoCat];
                        }
                    }

                    $item["magento_category_ids"] = $catLabel;
                }

                if (isset($item["type"])) {
                    $typeLabel = [];
                    $tmpTypes = explode(",", $item["type"]);
                    foreach ($tmpTypes as $tmpTypeId) {
                        if (isset($types[$tmpTypeId])) {
                            $typeLabel[] = $types[$tmpTypeId];
                        }
                    }

                    $item["type"] = $typeLabel;
                }
            }
        }
        return $dataSource;
    }
}
