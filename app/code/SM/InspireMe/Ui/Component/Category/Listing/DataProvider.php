<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\InspireMe\Ui\Component\Category\Listing;

use Magento\Customer\Ui\Component\Listing\AttributeRepository;
use Magento\Framework\Api\Filter;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\Reporting;
use Mirasvit\Blog\Model\ResourceModel\Category\Collection;

/**
 * Class DataProvider
 * @package SM\InspireMe\Ui\Component\Category
 */
class DataProvider extends \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider
{
    private $collection;

    /**
     * @var AttributeRepository
     */
    private $attributeRepository;

    /**
     * @param string                $name
     * @param string                $primaryFieldName
     * @param string                $requestFieldName
     * @param Reporting             $reporting
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param RequestInterface      $request
     * @param FilterBuilder         $filterBuilder
     * @param AttributeRepository   $attributeRepository
     * @param array                 $meta
     * @param array                 $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        Reporting $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        AttributeRepository $attributeRepository,
        array $meta = [],
        array $data = []
    ) {
        $this->attributeRepository = $attributeRepository;

        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $reporting,
            $searchCriteriaBuilder,
            $request,
            $filterBuilder,
            $meta,
            $data
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $collection = $this->getCollection();

        foreach ($collection as $category) {
            $category->setData('category_ids', $category->getCategoryIds());
        }

        return $this->searchResultToOutput($collection);
    }

    /**
     * @return SearchResultInterface
     */
    public function getCollection()
    {
        if (!$this->collection) {
            /** @var Collection $collection */
            $this->collection = $this->getSearchResult();

            $this->collection->addAttributeToSelect('*')
                ->addFieldToFilter(\Mirasvit\Blog\Api\Data\CategoryInterface::PARENT_ID, ['neq' => 0]);
        }

        return $this->collection;
    }

    /**
     * @param SearchResultInterface $searchResult
     *
     * @return array
     */
    protected function searchResultToOutput(SearchResultInterface $searchResult)
    {
        $arrItems                 = [];
        $arrItems['totalRecords'] = $searchResult->getTotalCount();

        $arrItems['items'] = [];
        foreach ($searchResult->getItems() as $item) {
            $arrItems['items'][] = $item->getData();
        }

        return $arrItems;
    }

    /**
     * @param Filter $filter
     * @return mixed|void
     */
    public function addFilter(Filter $filter)
    {
        if ($filter->getField() === 'fulltext') {
            $collection = $this->getCollection();
            $collection->addFieldToFilter('name', ['like' => '%' . $filter->getValue() . '%']);
        } else {
            return parent::addFilter($filter);
        }
    }
}
