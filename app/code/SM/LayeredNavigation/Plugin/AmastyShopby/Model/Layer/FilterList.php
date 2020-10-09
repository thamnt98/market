<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_LayeredNavigation
 *
 * Date: April, 27 2020
 * Time: 10:16 AM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\LayeredNavigation\Plugin\AmastyShopby\Model\Layer;

class FilterList
{
    /**
     * @var \SM\LayeredNavigation\Model\ResourceModel\Category\FilterList\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * FilterList constructor.
     *
     * @param \SM\LayeredNavigation\Model\ResourceModel\Category\FilterList\CollectionFactory $collectionFactory
     * @param \Magento\Framework\App\RequestInterface                                         $request
     */
    public function __construct(
        \SM\LayeredNavigation\Model\ResourceModel\Category\FilterList\CollectionFactory $collectionFactory,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->request = $request;
    }

    /**
     * @param \Amasty\Shopby\Model\Layer\FilterList                $subject
     * @param \Magento\Catalog\Model\Layer\Filter\AbstractFilter[] $result
     * @param \Magento\Catalog\Model\Layer                         $layer
     *
     * @return \Magento\Catalog\Model\Layer\Filter\AbstractFilter[]
     */
    public function afterGetFilters(
        \Amasty\Shopby\Model\Layer\FilterList $subject,
        $result,
        \Magento\Catalog\Model\Layer $layer
    ) {
        $category= $layer->getCurrentCategory();
        if ($category->getId()) {
            /** @var \SM\LayeredNavigation\Model\ResourceModel\Category\FilterList\Collection $coll */
            $coll = $this->collectionFactory->create();
            $coll->addFieldToFilter('category_id', $category->getId())
                ->setOrder('position', \Magento\Framework\Data\Collection::SORT_ORDER_ASC);

            $attributeList = $coll->getColumnValues('attribute_code');
            foreach ($result as $key => $filter) {
                try {
                    $code = $filter->getAttributeModel()->getAttributeCode();
                } catch (\Exception $exception) {
                    $code = $filter->getRequestVar();
                }

                if (!in_array($code, $attributeList)) {
                    unset($result[$key]);
                } else {
                    $filter->setData('position', array_search($code, $attributeList));
                }
            }
        }

        usort($result, [$this, 'sortingByPosition']);

        return $result;
    }

    /**
     * @param \Magento\Catalog\Model\Layer\Filter\AbstractFilter $one
     * @param \Magento\Catalog\Model\Layer\Filter\AbstractFilter $two
     *
     * @return bool
     */
    protected function sortingByPosition($one, $two)
    {
        return $one->getData('position') > $two->getData('position');
    }
}
