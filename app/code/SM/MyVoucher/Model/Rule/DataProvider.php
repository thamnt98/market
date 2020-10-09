<?php

namespace SM\MyVoucher\Model\Rule;

use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider{

    protected $collection;

    /**
     * @var array
     */
    protected $loadedData;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \Magento\SalesRule\Model\Rule\Metadata\ValueProvider
     */
    protected $metadataValueProvider;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    protected $storeManager;

    public function __construct($name, $primaryFieldName, $requestFieldName, array $meta = [], array $data = [],
                                CollectionFactory $collectionFactory,
                                \Magento\Framework\Registry $registry,
                                \Magento\SalesRule\Model\Rule\Metadata\ValueProvider $metadataValueProvider,
                                StoreManagerInterface $storeManager)
    {
        $this->collection = $collectionFactory->create();
        $this->coreRegistry = $registry;
        $this->metadataValueProvider = $metadataValueProvider;
        $meta = array_replace_recursive($this->getMetadataValues(), $meta);
        $this->dataPersistor = $dataPersistor ?? \Magento\Framework\App\ObjectManager::getInstance()->get(
                DataPersistorInterface::class
            );
        $this->storeManager = $storeManager;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * Get metadata values
     *
     * @return array
     */
    protected function getMetadataValues()
    {
        $rule = $this->coreRegistry->registry(\Magento\SalesRule\Model\RegistryConstants::CURRENT_SALES_RULE);
        return $this->metadataValueProvider->getMetadataValues($rule);
    }

    /**
     * @inheritdoc
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        /** @var Rule $rule */
        foreach ($items as $rule) {
            $rule->load($rule->getId());
            $rule->setDiscountAmount($rule->getDiscountAmount() * 1);
            $rule->setDiscountQty($rule->getDiscountQty() * 1);

            $this->loadedData[$rule->getId()] = $rule->getData();
            $this->loadedData[$rule->getId()] = $rule->getData();
            if ($rule->getData('voucher_image')) {
                $m['voucher_image'][0]['name'] = $rule->getData('voucher_image');
                $m['voucher_image'][0]['url'] = $this->getMediaUrl().$rule->getData('voucher_image');
                $fullData = $this->loadedData;
                $this->loadedData[$rule->getId()] = array_merge($fullData[$rule->getId()], $m);
            }
        }
        $data = $this->dataPersistor->get('sale_rule');
        if (!empty($data)) {
            $rule = $this->collection->getNewEmptyItem();
            $rule->setData($data);
            $this->loadedData[$rule->getId()] = $rule->getData();
            $this->dataPersistor->clear('sale_rule');
        }

        return $this->loadedData;
    }

    public function getMediaUrl()
    {
        $mediaUrl = $this->storeManager->getStore()
                ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).'sm/tmp/icon/';
        return $mediaUrl;
    }
}