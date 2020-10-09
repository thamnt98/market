<?php
/**
 * @category Magento
 * @package SM\DigitalProduct\Ui\Component\Form\Category\Type
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author Hung Viet Nguyen <hungnv6@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\DigitalProduct\Ui\Component\Form\Category;

use Magento\Catalog\Model\Category as CategoryModel;
use Magento\Catalog\Model\ResourceModel\Category\Collection as CategoryCollection;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Exception\LocalizedException;
use SM\DigitalProduct\Helper\Config;

/**
 * Class Options
 * @package SM\DigitalProduct\Ui\Component\Form\Category\Type
 */
class Options implements OptionSourceInterface
{
    /**
     * @var CategoryCollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * @var RequestInterface
     */
    protected $request;
    /**
     * @var Config
     */
    protected $configHelper;

    /**
     * @param CategoryCollectionFactory $categoryCollectionFactory
     * @param RequestInterface $request
     * @param Config $configHelper
     */
    public function __construct(
        CategoryCollectionFactory $categoryCollectionFactory,
        RequestInterface $request,
        Config $configHelper
    ) {
        $this->configHelper = $configHelper;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->request = $request;
    }

    /**
     * {@inheritdoc}
     * @throws LocalizedException
     */
    public function toOptionArray()
    {
        return $this->getCategoriesTree();
    }

    /**
     * Retrieve categories tree
     *
     * @return array
     * @throws LocalizedException
     */
    protected function getCategoriesTree()
    {
        $categoryById = [];
        $storeId = $this->request->getParam('store');

        $parentCategoryId = $this->configHelper->getC0CategoryId();
        if (!is_null($parentCategoryId)) {
            /* @var $subCategories CategoryCollection */
            $subCategories = $this->categoryCollectionFactory->create();

            $subCategories->addAttributeToFilter('parent_id', $parentCategoryId)
                ->addAttributeToSelect(['name'])
                ->setStoreId($storeId);
            /** @var CategoryModel $category */
            foreach ($subCategories as $category) {
                $categoryById[] = [
                    "label" => $category->getName(),
                    "value" => $category->getId()
                ];
            }
        }

        return $categoryById;
    }
}
