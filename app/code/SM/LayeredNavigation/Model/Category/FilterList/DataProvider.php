<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_LayeredNavigation
 *
 * Date: April, 27 2020
 * Time: 4:15 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\LayeredNavigation\Model\Category\FilterList;

class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \SM\LayeredNavigation\Helper\Data\FilterList
     */
    protected $helper;

    /**
     * @var array
     */
    protected $filters = [];

    /**
     * DataProvider constructor.
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $collectionFactory
     * @param \SM\LayeredNavigation\Helper\Data\FilterList                             $helper
     * @param \Magento\Framework\App\RequestInterface                                  $request
     * @param string                                                                   $name
     * @param string                                                                   $primaryFieldName
     * @param string                                                                   $requestFieldName
     * @param array                                                                    $meta
     * @param array                                                                    $data
     */
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $collectionFactory,
        \SM\LayeredNavigation\Helper\Data\FilterList $helper,
        \Magento\Framework\App\RequestInterface $request,
        $name,
        $primaryFieldName,
        $requestFieldName,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);

        $this->request = $request;
        $this->collectionFactory = $collectionFactory;
        $this->helper = $helper;

        $this->prepareCollection();
    }

    /**
     * @return $this
     */
    protected function prepareCollection()
    {
        /** @var \Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection $collection */
        $collection = $this->collectionFactory->create();
        $this->collection = $collection;

        return $this;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        $arrItems = [];
        $options = $this->getOptions();
        $arrItems['totalRecords'] = count($options);
        $arrItems['items'] = [];
        $arrItems['allIds'] = array_keys($options);
        $arrItems['selected'] = [];

        foreach ($options as $item) {
            $arrItems['items'][] = $item;
        }

        return $arrItems;
    }

    protected function getOptions()
    {
        $options = $this->helper->getAllOptions();
        foreach ($this->filters as $code => $value) {
            $value = strtolower($value);
            foreach ($options as $key => $item) {
                if (strpos($value, strtolower($item[$code] ?? '')) === false) {
                    unset($options[$key]);
                }
            }
        }

        return $options;
    }

    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
        $this->filters[$filter->getField()] = $filter->getValue();
    }
}
