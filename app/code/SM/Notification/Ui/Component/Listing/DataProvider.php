<?php

namespace SM\Notification\Ui\Component\Listing;

class DataProvider  extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var \SM\Notification\Model\ResourceModel\Notification\Collection
     */
    protected $collection;

    /**
     * @var array
     */
    protected $loadedData = null;

    /**
     * DataProvider constructor.
     *
     * @param \SM\Notification\Model\ResourceModel\Notification\CollectionFactory $collectionFactory
     * @param string                                                              $name
     * @param string                                                              $primaryFieldName
     * @param string                                                              $requestFieldName
     * @param array                                                               $meta
     * @param array                                                               $data
     */
    public function __construct(
        \SM\Notification\Model\ResourceModel\Notification\CollectionFactory $collectionFactory,
        $name,
        $primaryFieldName,
        $requestFieldName,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $this->prepareCollection($collectionFactory->create());
    }

    /**
     * @return array
     */
    public function getData()
    {
        if (is_null($this->loadedData)) {
            $this->loadedData['items'] = [];
            /** @var \SM\Notification\Model\Notification $item */
            foreach ($this->getCollection() as $item) {
                $this->loadedData['items'][] = $item->getData();
            }

            $this->loadedData['totalRecords'] = count($this->loadedData['items']);
        }

        return $this->loadedData;
    }

    /**
     * @param \SM\Notification\Model\ResourceModel\Notification\Collection $collection
     */
    protected function prepareCollection($collection)
    {
        $collection->addFieldToFilter('admin_type', ['notnull' => true]);

        return $collection;
    }

    /**
     * @param \Magento\Framework\Api\Filter $filter
     */
    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
        $this->getCollection()->addFieldToFilter(
            'main_table.' . $filter->getField(),
            [$filter->getConditionType() => $filter->getValue()]
        );
    }
}
