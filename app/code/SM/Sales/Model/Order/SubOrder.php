<?php
/**
 * @category Magento
 * @package SM\Sales\Model\Order
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author Hung Viet Nguyen <hungnv6@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\Sales\Model\Order;

use Magento\Catalog\Api\Data\ProductOptionInterfaceFactory;
use Magento\Catalog\Helper\Image;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\Area;
use Magento\Framework\DataObject;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Webapi\Request;
use Magento\Inventory\Model\ResourceModel\Source\Collection as SourceCollection;
use Magento\Inventory\Model\ResourceModel\Source\CollectionFactory as SourceCollectionFactory;
use Magento\InventoryApi\Api\Data\SourceInterface;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Sales\Api\OrderAddressRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Item;
use Magento\Sales\Model\OrderRepository;
use Magento\Store\Model\App\Emulation;
use Magento\Store\Model\StoreManagerInterface;
use SM\DigitalProduct\Api\Data\Order\DigitalProductInterfaceFactory;
use SM\Installation\Api\Data\InstallationServiceInterface;
use SM\Installation\Api\Data\InstallationServiceInterfaceFactory;
use SM\MobileApi\Model\Data\Catalog\Product\StoreInfoFactory;
use SM\Sales\Api\Data\DeliveryAddressDataInterface;
use SM\Sales\Api\Data\DeliveryAddressDataInterfaceFactory;
use SM\Sales\Api\Data\DetailItemDataInterface;
use SM\Sales\Api\Data\DetailItemDataInterfaceFactory;
use SM\Sales\Api\Data\ItemOptionDataInterface;
use SM\Sales\Api\Data\ItemOptionDataInterfaceFactory;
use SM\Sales\Api\Data\SubOrderDataInterface;
use SM\Sales\Api\Data\SubOrderDataInterfaceFactory;
use SM\Sales\Api\ParentOrderRepositoryInterface;
use SM\Sales\Helper\StatusState;
use SM\Sales\Model\ParentOrderRepository;
use SM\Sales\Model\ResourceModel\Order\Collection as OrderCollection;

/**
 * Class SubOrder
 * @package SM\Sales\Model\Order
 */
class SubOrder
{
    /**
     * @var ItemOptionDataInterfaceFactory
     */
    protected $itemOptionDataFactory;

    /**
     * @var Emulation
     */
    protected $appEmulation;
    /**
     * @var Image
     */
    protected $imageHelper;

    /**
     * @var SubOrderDataInterfaceFactory
     */
    protected $subOrderDataFactory;
    /**
     * @var DetailItemDataInterfaceFactory
     */
    protected $orderItemDataFactory;

    /**
     * @var InstallationServiceInterfaceFactory
     */
    protected $installationServiceFactory;
    /**
     * @var OrderRepository
     */
    protected $orderRepository;
    /**
     * @var DeliveryAddressDataInterfaceFactory
     */
    protected $deliveryDataFactory;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var ProductOptionInterfaceFactory
     */
    protected $productOptionDataFactory;

    /**
     * @var DigitalProductInterfaceFactory
     */
    protected $digitalDataFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var StatusState
     */
    protected $stateHelper;

    /**
     * @var OrderAddressRepositoryInterface
     */
    protected $orderAddressRepository;

    /**
     * @var SourceRepositoryInterface
     */
    protected $sourceRepoInterface;

    /**
     * @var StoreInfoFactory
     */
    protected $storeInfoFactory;

    /**
     * @var SourceCollectionFactory
     */
    protected $sourceCollectionFactory;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlInterface;

    /**
     * @var \SM\Sales\Model\Data\HandleOrderStatusHistory
     */
    private $history;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;
    /**
     * @var DataObjectFactory
     */
    protected $dataObjectFactory;

    /**
     * @var \SM\FreshProductApi\Helper\Fresh
     */
    protected $fresh;

    /**
     * @var string
     */
    private $currentCustomerToken;

    /**
     * @var \SM\Sales\Model\Order\Updater
     */
    protected $orderUpdater;

    /**
     * SubOrder constructor.
     * @param ItemOptionDataInterfaceFactory $itemOptionDataFactory
     * @param Emulation $appEmulation
     * @param Image $imageHelper
     * @param SubOrderDataInterfaceFactory $subOrderDataFactory
     * @param DetailItemDataInterfaceFactory $orderItemDataFactory
     * @param InstallationServiceInterfaceFactory $installationServiceFactory
     * @param OrderRepository $orderRepository
     * @param DeliveryAddressDataInterfaceFactory $deliveryDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param ProductOptionInterfaceFactory $productOptionDataFactory
     * @param DigitalProductInterfaceFactory $digitalDataFactory
     * @param StoreManagerInterface $storeManager
     * @param StatusState $stateHelper
     * @param OrderAddressRepositoryInterface $orderAddressRepository
     * @param SourceRepositoryInterface $sourceRepoInterface
     * @param StoreInfoFactory $storeInfoFactory
     * @param SourceCollectionFactory $sourceCollectionFactory
     * @param \Magento\Framework\UrlInterface $urlInterface
     * @param \SM\Sales\Model\Data\HandleOrderStatusHistory $history
     * @param DataObjectFactory $dataObjectFactory
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \SM\FreshProductApi\Helper\Fresh $fresh
     * @param Request $request
     * @param \SM\Sales\Model\Order\Updater $updater
     */
    public function __construct(
        ItemOptionDataInterfaceFactory $itemOptionDataFactory,
        Emulation $appEmulation,
        Image $imageHelper,
        SubOrderDataInterfaceFactory $subOrderDataFactory,
        DetailItemDataInterfaceFactory $orderItemDataFactory,
        InstallationServiceInterfaceFactory $installationServiceFactory,
        OrderRepository $orderRepository,
        DeliveryAddressDataInterfaceFactory $deliveryDataFactory,
        DataObjectHelper $dataObjectHelper,
        ProductOptionInterfaceFactory $productOptionDataFactory,
        DigitalProductInterfaceFactory $digitalDataFactory,
        StoreManagerInterface $storeManager,
        StatusState $stateHelper,
        OrderAddressRepositoryInterface $orderAddressRepository,
        SourceRepositoryInterface $sourceRepoInterface,
        StoreInfoFactory $storeInfoFactory,
        SourceCollectionFactory $sourceCollectionFactory,
        \Magento\Framework\UrlInterface $urlInterface,
        \SM\Sales\Model\Data\HandleOrderStatusHistory $history,
        DataObjectFactory $dataObjectFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \SM\FreshProductApi\Helper\Fresh $fresh,
        Request $request,
        Updater $updater
    ) {
        $this->dataObjectFactory = $dataObjectFactory;
        $this->fresh = $fresh;
        $this->sourceCollectionFactory = $sourceCollectionFactory;
        $this->stateHelper = $stateHelper;
        $this->storeManager = $storeManager;
        $this->digitalDataFactory = $digitalDataFactory;
        $this->productOptionDataFactory = $productOptionDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->deliveryDataFactory = $deliveryDataFactory;
        $this->orderRepository = $orderRepository;
        $this->subOrderDataFactory = $subOrderDataFactory;
        $this->orderItemDataFactory = $orderItemDataFactory;
        $this->installationServiceFactory = $installationServiceFactory;
        $this->appEmulation = $appEmulation;
        $this->imageHelper = $imageHelper;
        $this->itemOptionDataFactory = $itemOptionDataFactory;
        $this->orderAddressRepository = $orderAddressRepository;
        $this->sourceRepoInterface = $sourceRepoInterface;
        $this->storeInfoFactory = $storeInfoFactory;
        $this->urlInterface = $urlInterface;
        $this->history = $history;
        $this->productRepository = $productRepository;
        $this->request = $request;
        $this->orderUpdater = $updater;
    }

    /**
     * @param OrderItemInterface $orderItem
     * @return array|bool
     */
    public function getItemOptions($orderItem)
    {
        $result = [];
        $options = $orderItem->getProductOptions();

        if ($options) {
            if (isset($options['options'])) {
                $result = array_merge($result, $options['options']);
            }
            if (isset($options['additional_options'])) {
                $result = array_merge($result, $options['additional_options']);
            }
            if (isset($options['attributes_info'])) {
                $result = array_merge($result, $options['attributes_info']);
            }
            if (isset($options["bundle_options"])) {
                $result = array_merge($result, $options["bundle_options"]);
            }
        }
        if ($result) {
            $itemOptions = [];
            $key = 1;
            foreach ($result as $option) {
                /** @var ItemOptionDataInterface $itemOptionData */
                $itemOptionData = $this->itemOptionDataFactory->create();
                $itemOptionData->setOptionLabel($option["label"]);

                if (is_array($option["value"])) {
                    $itemOptionData->setOptionType(ParentOrderRepository::OPTION_TYPE_BUNDLE);
                    $itemOptionData->setOptionValue(json_encode($this->formatBundleOptions($option["value"], $key)));
                    $product = $this->productRepository->getById($orderItem->getProductId());
                    $productTypeInstance = $product->getTypeInstance();
                    $productOption = $productTypeInstance
                        ->getSelectionsCollection($productTypeInstance->getOptionsIds($product), $product)
                        ->getItems();

                    $selectOption = $this->getSelectedProduct($productOption, $options);

                    if (isset($selectOption[$option['option_id']])) {
                        $itemOptionData->setOptionSelection($selectOption[$option['option_id']]);
                    }
                    $key++;
                } else {
                    $itemOptionData->setOptionType(ParentOrderRepository::OPTION_TYPE_CONFIGURABLE);
                    $itemOptionData->setOptionValue($option["value"]);
                }
                $itemOptions[] = $itemOptionData;
            }
            return $itemOptions;
        } else {
            return false;
        }
    }

    public function formatBundleOptions($values, $key)
    {
        foreach ($values as &$value) {
            $value["title"] = __("Bundle ") . $key;
        }

        return $values;
    }

    /**
     * @param $productOption
     * @param $option
     * @return array
     */
    public function getSelectedProduct($productOption, $option)
    {
        $optionSelected = [];
        $selectOption = [];
        $infoBuyRequest = $option['info_buyRequest'];
        $bundleOption = $infoBuyRequest['bundle_option'];
        foreach ($productOption as $optionId => $productOpt) {
            foreach ($bundleOption as $opt) {
                if (is_array($opt)) {
                    if (in_array($productOpt->getSelectionId(), $opt)) {
                        $optionSelected[] = $productOpt;
                        if ($productOpt->getTypeId() == Configurable::TYPE_CODE) {
                            if (!empty($infoBuyRequest['super_attribute'])) {
                                $supperAttributes = $infoBuyRequest['super_attribute'];
                                $attrOpt = 0;
                                foreach ($bundleOption as $k => $v) {
                                    if (in_array($productOpt->getSelectionId(), $v)) {
                                        $attrOpt = $k;
                                    }
                                }
                                $attributes = $productOpt->getTypeInstance()->getConfigurableAttributesAsArray($productOpt);
                                $attributeId = head(array_keys($supperAttributes[$attrOpt][$productOpt->getSelectionId()]));
                                $attributeSelected = head(array_values($supperAttributes[$attrOpt][$productOpt->getSelectionId()]));
                                $attrbute_code = $attributes[$attributeId]['attribute_code'];

                                foreach ($attributes[$attributeId]['values'] as $value) {
                                    if ($value["value_index"] == $attributeSelected) {
                                        $selectOption[$productOpt->getOptionId()] = $value["store_label"];
                                    }
                                }
                            }
                        } else {
                            $selectOption[$productOpt->getData("option_id")] = $productOpt->getData("name");
                        }
                    }
                } else {
                    if ($productOpt->getSelectionId() == $opt) {
                        $optionSelected[] = $productOpt;
                        if ($productOpt->getTypeId() == Configurable::TYPE_CODE) {
                            if (!empty($infoBuyRequest['super_attribute'])) {
                                $supperAttributes = $infoBuyRequest['super_attribute'];
                                $attrOpt = 0;
                                foreach ($bundleOption as $k => $v) {
                                    if ($productOpt->getSelectionId() == $v) {
                                        $attrOpt = $k;
                                    }
                                }
                                $attributes = $productOpt->getTypeInstance()->getConfigurableAttributesAsArray($productOpt);
                                $attributeId = head(array_keys($supperAttributes[$attrOpt][$productOpt->getSelectionId()]));
                                $attributeSelected = head(array_values($supperAttributes[$attrOpt][$productOpt->getSelectionId()]));
                                $attrbute_code = $attributes[$attributeId]['attribute_code'];

                                foreach ($attributes[$attributeId]['values'] as $value) {
                                    if ($value["value_index"] == $attributeSelected) {
                                        $selectOption[$productOpt->getOptionId()] = $value["store_label"];
                                    }
                                }
                            }
                        } else {
                            $selectOption[$productOpt->getData("option_id")] = $productOpt->getData("name");
                        }
                    }
                }
            }
        }

        return $selectOption;
    }

    /**
     * @param $itemResults
     * @return int[]
     */
    public function itemProcess($itemResults)
    {
        $parentOrderIds = [];
        /** @var Item $orderItem */
        foreach ($itemResults as $orderItem) {
            $parentOrderIds[] = $orderItem->getData("parent_entity_id");
        }
        return $parentOrderIds;
    }

    /**
     * @param int $subOrderId
     * @throws InputException
     * @throws NoSuchEntityException
     * @throws AlreadyExistsException
     * @throws \Exception
     */
    public function updateStatusComplete($subOrderId)
    {
        $order = $this->orderRepository->get($subOrderId);
        $orderStatus = $order->getStatus();
        if ($orderStatus == ParentOrderRepositoryInterface::STATUS_DELIVERED
            || $orderStatus == ParentOrderRepositoryInterface::PICK_UP_BY_CUSTOMER) {
            $this->orderUpdater->updateStatusOrder($order);
        } else {
            throw new \Exception(__('The order isn\'t delivered.'));
        }
    }

    /**
     * @param OrderCollection $orderCollection
     * @param $cancelType
     * @param bool $hasInvoice
     * @return DataObject
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function handleSubOrders($orderCollection, &$cancelType, $hasInvoice = false)
    {
        $result = $this->dataObjectFactory->create();
        $this->appEmulation->startEnvironmentEmulation(
            $this->storeManager->getStore()->getId(),
            Area::AREA_FRONTEND,
            true
        );

        $sourceInformation = $this->getStoreInformation($orderCollection);

        $subOrders = [];
        /** @var Order $subOrder */
        foreach ($orderCollection as $subOrder) {
            /** @var SubOrderDataInterface $subOrderData */
            $subOrderData = $this->subOrderDataFactory
                ->create()
                ->setId($subOrder->getEntityId())
                ->setSubOrderId($subOrder->getData("reference_order_id"))
                ->setParentOrder($subOrder->getData("parent_order"))
                ->setStatus($subOrder->getStatus())
                ->setStatusLabel($subOrder->getStatusLabel())
                ->setCreatedAt($subOrder->getCreatedAt())
                ->setTotalPayment($subOrder->getGrandTotal())
                ->setSubtotal($subOrder->getSubtotal())
                ->setStoreInfo($this->getStoreInfo($subOrder, $sourceInformation));

            if ($hasInvoice) {
                $subOrderData->setInvoiceLink($this->getInvoiceLink($subOrder->getParentOrder()));
            }

            if (!$subOrder->getIsVirtual()) {
                $this->setDeliveryData($subOrder, $subOrderData);
                $this->setStatusHistoriesData($subOrder, $subOrderData);
                $cancelType[] = $this->history->getCancelType();
            }

            $this->setItemsData($subOrder, $subOrderData);
            $subOrders[$subOrder->getData("parent_order")][] = $subOrderData;
        }
        $this->appEmulation->stopEnvironmentEmulation();
        $result
            ->setData("sub_orders", $subOrders)
            ->setData("has_invoice", $hasInvoice);
        return $result;
    }

    /**
     * @param Order $subOrder
     * @param SubOrderDataInterface $subOrderData
     */
    private function setStatusHistoriesData(Order $subOrder, SubOrderDataInterface $subOrderData)
    {
        $subOrderData->setStatusHistory($this->history->getStatusHistory($subOrder));
        $subOrderData->setStatusHistoryDetails($this->history->getStatusHistoryDetails());
    }

    /**
     * @param Order $subOrderModel
     * @param SubOrderDataInterface $subOrderData
     */
    public function setDeliveryData($subOrderModel, $subOrderData)
    {
        $shippingDescription = $subOrderModel->getShippingDescription();

        if ($this->isStorePickUp($subOrderModel)) {
            $subOrderData->setShippingMethod(__("Pick Up in Store"));
        } else {
            if (!is_null($shippingDescription)) {
                $shippingDescription = explode(" - ", $shippingDescription);
                if (isset($shippingDescription[1])) {
                    $subOrderData->setShippingMethod($shippingDescription[1]);
                }
            }

            $addressDetails = "";
            $address = "";

            $street = $subOrderModel->getData("street");
            if ($street) {
                $street = explode(PHP_EOL, $street);
                if (isset($street[0])) {
                    $address = $street[0];
                }

                if (isset($street[1])) {
                    $addressDetails = $street[1];
                }
            }

            /** @var DeliveryAddressDataInterface $deliveryData */
            $deliveryData = $this->deliveryDataFactory->create();
            $deliveryData
                ->setFullName($this->getFullName($subOrderModel))
                ->setAddress($address)
                ->setProvince($subOrderModel->getData("region"))
                ->setStreet($addressDetails)
                ->setCountry($subOrderModel->getData("district"))
                ->setDistrict($subOrderModel->getData("district"))
                ->setCity($subOrderModel->getData("city"))
                ->setTelephone($subOrderModel->getData("telephone"))
                ->setPostcode($subOrderModel->getData("postcode"))
                ->setAddressName($subOrderModel->getData("address_tag"));
            $subOrderData->setDeliveryAddress($deliveryData);
        }

        $subOrderData
            ->setShippingMethodCode($subOrderModel->getShippingMethod())
            ->setTrackingNumber(implode(", ", $subOrderModel->getTrackingNumbers()))
            ->setDeliveryFee($subOrderModel->getShippingAmount());
    }

    /**
     * @param Order $subOrderModel
     * @return string
     */
    public function getFullName($subOrderModel)
    {
        $name = '';
        $name .= $subOrderModel->getData("firstname");
        if ($subOrderModel->getData("middlename")) {
            $name .= ' ' . $subOrderModel->getData("middlename");
        }
        $name .= ' ' . $subOrderModel->getData("lastname");
        return $name;
    }

    /**
     * @param Order $subOrderModel
     * @param SubOrderDataInterface $subOrderData
     */
    private function setItemsData($subOrderModel, $subOrderData)
    {
        $items = [];
        $childItems = [];
        $totalPrice = 0;
        $isDigital = $subOrderModel->getIsVirtual();
        /** @var Item $orderItem */
        foreach ($subOrderModel->getItemsCollection() as $orderItem) {
            if ($orderItem->getParentItemId()) {
                $childItems[$orderItem->getParentItemId()][] = $orderItem;
                continue;
            }
            if ($orderItem->getProduct()) {
                /** @var DetailItemDataInterface $itemData */
                $itemData = $this->orderItemDataFactory
                    ->create()
                    ->setItemId($orderItem->getItemId())
                    ->setTotal(
                        $orderItem->getBasePrice() * (int)$orderItem->getQtyOrdered()
                    )
                    ->setSku($orderItem->getProduct()->getSku())
                    ->setProductName($orderItem->getProduct()->getName())
                    ->setQuantity($orderItem->getQtyOrdered())
                    ->setPrice($orderItem->getBasePrice())
                    ->setUrl($orderItem->getProduct()->getProductUrl());

                $itemData->setImageUrl(
                    $this->imageHelper->init($orderItem->getProduct(), 'product_base_image')->getUrl()
                );
                $itemData->setProductType($orderItem->getProduct()->getTypeId());
                $this->setAdditionalItemData($orderItem, $itemData, $orderItem->getProduct());
                $options = $this->getItemOptions($orderItem);
                if ($options != false) {
                    $itemData
                        ->setHasOptions(1)
                        ->setOptions($options);
                } else {
                    $itemData
                        ->setHasOptions(0);
                }
                $itemData->setFreshProduct($this->fresh->populateObject($orderItem->getProduct()));
                $items[$orderItem->getId()] = $itemData;
            }
            $totalPrice += $orderItem->getBasePrice() * (int)$orderItem->getQtyOrdered();
        }

        if ($subOrderModel->getData("parent_order") && $subOrderModel->getSubtotal() <= 0) {
            $subOrderData->setSubtotal($totalPrice);
        }

        $subOrderData->setItems($items);
    }

    /**
     * @param Order $order
     * @param SourceInterface[] $inventorySource
     * @return \SM\MobileApi\Model\Data\Catalog\Product\StoreInfo
     */
    public function getStoreInfo($order, $inventorySource)
    {
        if ($this->isStorePickUp($order) && isset($inventorySource[$order->getStorePickUp()])) {
            $source = $inventorySource[$order->getStorePickUp()];
            $storeInfo = $this->storeInfoFactory->create();
            $storeInfo->setName($source->getName());
            $storeInfo->setCity($source->getCity());
            $storeInfo->setStreet($source->getStreet());
            $storeInfo->setPostcode($source->getPostcode());
            $storeInfo->setRegion($source->getRegion());
            $storeInfo->setPickUpTime($order->getStorePickUpDelivery());
            $storeInfo->setPickUpDate($order->getStorePickUpTime());
            return $storeInfo;
        }

        return null;
    }

    /**
     * @param Item $itemModel
     * @param DetailItemDataInterface $itemData
     * @param $product
     */
    private function setAdditionalItemData($itemModel, $itemData, $product)
    {
        $itemBuyRequest = $itemModel->getProductOptionByCode('info_buyRequest') ?? [];
        if ($itemModel->getProductType() == "virtual") {
            $productOption = $itemModel->getProductOption();
            if ($productOption->getExtensionAttributes()
                && $productOption->getExtensionAttributes()->getDigitalData()) {
                $productOption->getExtensionAttributes()
                    ->getDigitalData()
                    ->getDigital()
                    ->setServiceType($this->getServiceType($product));
            }
            $itemData->setProductOption($productOption);
            $itemData->setBuyRequest(json_encode($itemBuyRequest));
        }

        if (isset($itemBuyRequest[\SM\Installation\Helper\Data::QUOTE_OPTION_KEY])) {
            /** @var InstallationServiceInterface $installationService */
            $installationService = $this->installationServiceFactory->create();
            $installationService->setData($itemBuyRequest[\SM\Installation\Helper\Data::QUOTE_OPTION_KEY]);
            $itemData->setInstallationService($installationService);
        }
    }

    /**
     * @param $product
     * @return string
     */
    private function getServiceType($product)
    {
        $category = $product->getCategoryCollection()->addAttributeToSelect('name')->getFirstItem();
        return $category->getName();
    }

    /**
     * @param OrderCollection $orderCollection
     * @return \Magento\Framework\DataObject[]|SourceInterface[]
     */
    public function getStoreInformation($orderCollection)
    {
        $sourceCode = [];

        /** @var Order $subOrder */
        foreach ($orderCollection as $subOrder) {
            array_push($sourceCode, $subOrder->getStorePickUp());
        }
        $sourceCode = array_unique($sourceCode);

        /** @var SourceCollection $sourceCollection */
        $sourceCollection = $this->sourceCollectionFactory->create()
            ->addFieldToFilter("source_code", ["in" => $sourceCode]);

        $sourceInformation = [];
        /** @var SourceInterface $source */
        foreach ($sourceCollection as $source) {
            $sourceInformation[$source->getSourceCode()] = $source;
        }
        return $sourceInformation;
    }

    public function isStorePickUp($order)
    {
        return $order->getShippingMethod() == "store_pickup_store_pickup";
    }

    /**
     * @return string|void
     */
    public function getCurrentCustomerToken()
    {
        if (empty($this->currentCustomerToken)) {
            $authorizationHeaderValue = $this->request->getHeader('Authorization');
            if (!$authorizationHeaderValue) {
                return;
            }

            $headerPieces = explode(" ", $authorizationHeaderValue);
            if (count($headerPieces) !== 2) {
                return;
            }

            $tokenType = strtolower($headerPieces[0]);
            if ($tokenType !== 'bearer') {
                return;
            }
            $this->currentCustomerToken = $headerPieces[1] ?? '';
        }

        return $this->currentCustomerToken;
    }

    /**
     * @param $orderId
     * @return string|null
     */
    public function getInvoiceLink($orderId)
    {
        if ($token = $this->getCurrentCustomerToken()) {
            return $this->urlInterface->getUrl(
                'sales/invoice/mobileview',
                ['id' => $orderId, 'token' => $token]
            );
        }

        return null;
    }
}
