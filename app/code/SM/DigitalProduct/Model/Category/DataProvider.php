<?php
declare(strict_types=1);

namespace SM\DigitalProduct\Model\Category;

use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\View\Element\UiComponent\Context;
use Magento\Ui\DataProvider\AbstractDataProvider;
use SM\DigitalProduct\Api\Data\CategoryInterface;
use SM\DigitalProduct\Model\Category;
use SM\DigitalProduct\Model\ResourceModel\Category\Collection;
use SM\DigitalProduct\Model\ResourceModel\Category\CollectionFactory;

/**
 * Class DataProvider
 * @package SM\DigitalProduct\Model\Category
 */
class DataProvider extends AbstractDataProvider
{
    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var Collection
     */
    protected $collection;
    /**
     * @var
     */
    protected $loadedData;
    /**
     * @var Context
     */
    protected $context;

    /**
     * Constructor
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param Context $context
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        DataPersistorInterface $dataPersistor,
        Context $context,
        array $meta = [],
        array $data = []
    ) {
        $this->context = $context;
        $this->collection = $collectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $storeId = $this->context->getRequestParam("store", 0);
        $items = $this->collection->loadContentByStoreId($storeId)->getItems();
        /** @var Category $model */
        foreach ($items as $model) {
            $model = $this->processThumbnail($model);
//            $model = $this->processType($model);
            $this->loadedData[$model->getId()] = $model->getData();
        }

        $data = $this->dataPersistor->get('sm_digitalproduct_category');
        if (!empty($data)) {
            $model = $this->collection->getNewEmptyItem();
            $model->setData($data);
            $model = $this->processThumbnail($model, true);
//            $model = $this->processType($model);
            $this->loadedData[$model->getId()] = $model->getData();
            $this->dataPersistor->clear('sm_digitalproduct_category');
        }
        return $this->loadedData;
    }

    /**
     * @param Category $model
     * @param null $persistor
     * @return Category
     */
    protected function processThumbnail($model, $persistor = false)
    {
        if (!is_null($model->getData(CategoryInterface::THUMBNAIL))) {
            if ($persistor == true) {
                $model->setData(
                    CategoryInterface::THUMBNAIL,
                    $model->getData(CategoryInterface::THUMBNAIL)
                );
            } else {
                $model->setData(
                    CategoryInterface::THUMBNAIL,
                    [0 => json_decode($model->getData(CategoryInterface::THUMBNAIL), true)]
                );
            }
        }
        return $model;
    }
}
