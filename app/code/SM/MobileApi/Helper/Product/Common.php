<?php

namespace SM\MobileApi\Helper\Product;

use Magento\Framework\Exception\NoSuchEntityException;

class Common extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Magento\CatalogInventory\Api\StockRegistryInterface
     */
    protected $_stockRegistry;

    /**
     * @var \Magento\CatalogInventory\Api\StockStateInterface
     */
    protected $_stockState;
    protected $labelIndex;
    protected $maxLabelCount = null;
    protected $config;
    protected $labelsFactory;
    protected $labelsResource;
    protected $timezone;
    protected $_storeManager;
    protected $helperStorePickup;
    protected $storeInfoFactory;
    protected $helperDelivery;
    protected $deliveryIntoFactory;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManagerInterface,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\CatalogInventory\Api\StockStateInterface $stockState,
        \Amasty\Label\Model\ResourceModel\Index $labelIndex,
        \Amasty\Label\Helper\Config $config,
        \Amasty\Label\Model\LabelsFactory $labelsFactory,
        \Amasty\Label\Model\ResourceModel\Labels $labelsResource,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \SM\Catalog\Helper\StorePickup $helperStorePickup,
        \SM\MobileApi\Model\Data\Catalog\Product\StoreInfoFactory $storeInfoFactory,
        \SM\Catalog\Helper\Delivery $helperDelivery,
        \SM\MobileApi\Model\Data\Catalog\Product\DeliveryIntoFactory $deliveryIntoFactory
    ) {
        $this->_objectManager       = $objectManagerInterface;
        $this->_stockRegistry       = $stockRegistry;
        $this->_stockState          = $stockState;
        $this->labelIndex           = $labelIndex;
        $this->config               = $config;
        $this->labelsFactory        = $labelsFactory;
        $this->labelsResource       = $labelsResource;
        $this->timezone             = $timezone;
        $this->_storeManager        = $storeManager;
        $this->helperStorePickup    = $helperStorePickup;
        $this->storeInfoFactory     = $storeInfoFactory;
        $this->helperDelivery       = $helperDelivery;
        $this->deliveryIntoFactory  = $deliveryIntoFactory;

        parent::__construct($context);
    }

    /**
     * Check product is salable or not, don't rely on $product->getIsSalable()
     *
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return bool
     */
    public function isProductSalable($product)
    {
        if ($product) {
            if ($this->isProductAllowedBackOrder($product)) {
                //if product is allowed back order => always return true
                return true;
            } else {
                if ($this->isProductEnabled($product) && $this->getProductQty($product) > 0) {
                    //TODO: should check manage stock or not???
                    return true;
                } else {
                    return false;
                }
            }
        }

        return false;
    }

    /**
     * Check product is allowed back order
     *
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return bool
     */
    public function isProductAllowedBackOrder($product)
    {
        $stockItem = $this->_stockRegistry->getStockItem($product->getId());
        if ($stockItem) {
            return $stockItem->getBackorders();
        }

        return false;
    }

    /**
     * Check product is enabled or not. 1 is enabled, 2 is disabled.
     *
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return bool
     */
    public function isProductEnabled($product)
    {
        if ($product) {
            return \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED == $product->getStatus();
        }

        return false;
    }

    /**
     * Get product quantity
     *
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return int
     */
    public function getProductQty($product)
    {
        if ($product) {
            return $this->_stockState->getStockQty($product->getId());
        }

        return 0;
    }

    /**
     * @param $productId
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getLabels($productId)
    {
        $storeId = $this->_storeManager->getStore()->getId();
        $labelIds = $this->labelIndex->getIdsFromIndex($productId, $storeId);
        if (empty($labelIds)) {
            return [];
        }

        return array_unique($labelIds);
    }

    /**
     * @param $product
     * @return array
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getProductLabel($product)
    {
        $maxLabelCount = $this->getMaxLabelCount();
        $applied = 0;
        $result = [];
        $pos = [];
        foreach ($this->getLabels($product->getId()) as $label) {
            if ($applied == $maxLabelCount) {
                break;
            }
            /* @var $labelData \SM\MobileApi\Api\Data\ProductLabel\ProductLabelInterface */
            $labelData = $this->labelsFactory->create();

            $this->labelsResource->load($labelData, (int)$label);

            if (!$labelData->getId()) {
                throw new NoSuchEntityException(__('Labels with specified ID "%1" not found.', (int)$label));
            }
            if ($labelData->getStatus() == 1) {
                $checkDate = true;
                $stores = (explode(",", $labelData->getStores()));
                $storeId = $this->_storeManager->getStore()->getId();
                if (!in_array($storeId, $stores)) {
                    $checkDate = false;
                }
                if ($labelData->getDateRangeEnabled() == 1) {
                    $fromDate = $labelData->getFromDate() ?: null;
                    $toDate = $labelData->getToDate() ?: null;
                    $now = $this->timezone->date()->format('Y-m-d H:i:s');
                    if (($fromDate !== null && $now < $fromDate)
                        || ($toDate !== null && $now > $toDate)
                    ) {
                        $checkDate = false;
                    }
                }
                if ($checkDate == true) {
                    foreach ($labelData->getData() as $key => $value) {
                        if ($key == 'prod_style') {
                            $productStyle = explode(';', preg_replace('/\s+/', '', $value));
                            $labelData->setProductBackGround($this->getTextStyle($productStyle, 'background-color'));
                            $labelData->setProductTextColor($this->getTextStyle($productStyle, 'color'));
                        }
                        if ($key == 'cat_style') {
                            $catStyle = explode(';', preg_replace('/\s+/', '', $value));
                            $labelData->setCategoryBackGround($this->getTextStyle($catStyle, 'background-color'));
                            $labelData->setCategoryTextColor($this->getTextStyle($catStyle, 'color'));
                        }
                    }
                    $labelData->setProductText($labelData->getProdTxt());
                    $labelData->setCategoryText($labelData->getCatTxt());
                    $result[] = $labelData;
                    $pos[] = $labelData->getPos();
                }
            }
            $applied++;
        }
        if (count($pos) > 0) {
            krsort($pos);
            array_multisort($pos, SORT_ASC, $result);
        }
        return isset($result[0]) ? $result[0] : null;
    }

    /**
     * @param $data
     * @param $param
     * @return mixed|string
     */
    public function getTextStyle($data, $param)
    {
        if (count($data)) {
            for ($i = 0; $i < count($data); $i++) {
                if ($data[$i]) {
                    $code = explode(':', $data[$i]);
                    if (count($code) == 2 && ($code[0] == $param)) {
                        return $code[1];
                    }
                }
            }
        }
        return '';
    }

    /**
     * @return int
     */
    protected function getMaxLabelCount()
    {
        if ($this->maxLabelCount === null) {
            $this->maxLabelCount = $this->config->getMaxLabels();
        }

        return $this->maxLabelCount;
    }

    /**
     * Get Store Info
     *
     * @param $product
     * @return array
     * @throws NoSuchEntityException
     */
    public function getStoreInfo($product)
    {
        $data = [];
        if ($this->helperStorePickup->isSimpleProductPDP($product)) {
            $storeInfo = $this->helperStorePickup->getSourcesListSimple($product);
        }
        if ($this->helperStorePickup->isConfigurableProductPDP($product)) {
            $storeInfo = $this->helperStorePickup->getSourcesListConfigurable($product);
        }
        if ($this->helperStorePickup->isBundleProductPDP($product)) {
            $storeInfo = $this->helperStorePickup->getSourcesListBundle($product);
        }
        if (isset($storeInfo) && !empty($storeInfo)) {
            foreach ($storeInfo as $key => $value) {
                $storeInfoFactory = $this->storeInfoFactory->create();
                $storeInfoFactory->setData($value);
                $storeInfoFactory->setName($value['name'] . ' (' . $key . ' km)');
                $storeInfoFactory->setOpenUntil($this->helperStorePickup->getStoreTimeAvailable());
                $data[] = $storeInfoFactory;
            }
        }
        return $data;
    }

    /**
     * @param $product
     * @return array
     * @throws NoSuchEntityException
     */
    public function getDeliveryMethodProduct($product)
    {
        $deliveryMethod = $this->helperDelivery->getDeliveryMethod($product);
        $data = [];
        foreach ($deliveryMethod as $key => $value) {
            $deliveryFactory = $this->deliveryIntoFactory->create();
            $deliveryFactory->setValue($value['value']);
            $deliveryFactory->setLabel($value['label']->getText());

            $data[] = $deliveryFactory;
        }
        return $data;
    }
}
