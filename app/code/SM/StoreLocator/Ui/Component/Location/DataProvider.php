<?php

namespace SM\StoreLocator\Ui\Component\Location;

use Magento\Ui\DataProvider\AbstractDataProvider;
use SM\StoreLocator\Model\Store\ResourceModel\Location\Collection;
use SM\StoreLocator\Model\Store\ResourceModel\Location\CollectionFactory;

/**
 * Class DataProvider
 * @package SM\StoreLocator\Ui\Component\Location
 */
class DataProvider extends AbstractDataProvider
{
    /**
     * @var Collection
     */
    protected $collection;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
    }

    /**
     * @return array
     */
    public function getData()
    {
        if (!$this->getCollection()->isLoaded()) {
            $this->getCollection()->load();
        }
        $items = $this->getCollection()->toArray();
        return [
            'totalRecords' => $this->getCollection()->getSize(),
            'items' => $items['items'],
        ];
    }
}
