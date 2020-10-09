<?php
declare(strict_types=1);

namespace SM\DigitalProduct\Model\OperatorIcon;

use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use SM\DigitalProduct\Api\Data\OperatorIconInterface;
use SM\DigitalProduct\Model\OperatorIcon;
use SM\DigitalProduct\Model\ResourceModel\OperatorIcon\Collection;
use SM\DigitalProduct\Model\ResourceModel\OperatorIcon\CollectionFactory;

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
     * Constructor
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = []
    ) {
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
        $items = $this->collection->getItems();
        /** @var OperatorIcon $model */
        foreach ($items as $model) {
            $model = $this->processIcon($model);
            $this->loadedData[$model->getId()] = $model->getData();
        }
        $data = $this->dataPersistor->get('sm_digitalproduct_operator_icon');

        if (!empty($data)) {
            $model = $this->collection->getNewEmptyItem();
            $model->setData($data);
            $model = $this->processIcon($model);
            $this->loadedData[$model->getId()] = $model->getData();
            $this->dataPersistor->clear('sm_digitalproduct_operator_icon');
        }

        return $this->loadedData;
    }

    /**
     * @param OperatorIcon $model
     * @return OperatorIcon
     */
    protected function processIcon($model)
    {
        if (!is_null($model->getData(OperatorIconInterface::ICON))) {
            if ($model->getData(OperatorIconInterface::ICON) == "") {
                $model->setData(
                    OperatorIconInterface::ICON,
                    null
                );
            } else {
                $model->setData(
                    OperatorIconInterface::ICON,
                    [0 => json_decode($model->getData(OperatorIconInterface::ICON), true)]
                );
            }
        }
        return $model;
    }
}
