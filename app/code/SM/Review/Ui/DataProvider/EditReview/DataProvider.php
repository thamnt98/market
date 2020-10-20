<?php

namespace SM\Review\Ui\DataProvider\EditReview;

use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Magento\Ui\DataProvider\Modifier\PoolInterface;
use SM\Review\Model\ResourceModel\ReviewEdit\Collection as ReviewEditCollection;
use SM\Review\Model\ResourceModel\ReviewEdit\CollectionFactory as ReviewEditCollectionFactory;
use SM\Review\Model\ReviewEdit;

/**
 * Class DataProvider
 * @package SM\Review\Ui\DataProvider\EditReview
 */
class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var ReviewEditCollection
     */
    protected $collection;

    /**
     * @var PoolInterface|null
     */
    protected $modifiersPool;

    /**
     * DataProvider constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param ReviewEditCollectionFactory $reviewEditCollectionFactory
     * @param array $meta
     * @param array $data
     * @param PoolInterface|null $modifiersPool
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        ReviewEditCollectionFactory $reviewEditCollectionFactory,
        PoolInterface $modifiersPool,
        array $meta = [],
        array $data = []
    ) {
        $this->modifiersPool = $modifiersPool;
        $this->collection = $reviewEditCollectionFactory->create();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getData()
    {
        $items = [];

        /** @var ReviewEdit $reviewEdit */
        foreach ($this->collection as $reviewEdit) {
            $items[] = $reviewEdit->getData();
        }
        $items = $this->collection->toArray();

        /** @var ModifierInterface $modifier */
        foreach ($this->modifiersPool->getModifiersInstances() as $modifier) {
            $items = $modifier->modifyData($items);
        }

        return $items;
    }
}
