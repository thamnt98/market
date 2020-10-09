<?php
/**
 * Class SelectedCategories
 * @package SM\Theme\Block\Category\Widget
 * @author Son Nguyen <sonnn@smartosc.com>
 */

declare(strict_types=1);

namespace SM\Theme\Block\Category\Widget;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Customer\Model\ResourceModel\CustomerFactory;
use Magento\Customer\Model\Customer;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Customer\Helper\Session\CurrentCustomer;
use SM\Theme\Helper\Data;

class SelectedCategories extends Template implements BlockInterface
{
    const SLECTED_CATEGORIES = 'selected_categories';
    const VALUE_DEFAULT = '0,0,0';
    const CATEGORY_C0_LEVEL = 2;
    const ORDER_COMPLETED = 'complete';

    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @var CollectionFactory
     */
    private $orderCustomer;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var Customer
     */
    protected $customer;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var CurrentCustomer
     */
    protected $currentCustomer;

    /**
     * @var Data
     */
    protected $imageHelper;

    /**
     * SelectedCategories constructor.
     * @param Template\Context $context
     * @param CategoryRepositoryInterface $categoryRepository
     * @param CustomerSession $customerSession
     * @param CollectionFactory $orderCustomer
     * @param CustomerFactory $customerFactory
     * @param CustomerRepositoryInterface $customerRepository
     * @param Customer $customer
     * @param ProductRepositoryInterface $productRepository
     * @param CurrentCustomer $currentCustomer
     * @param array $data
     */
    public function __construct(
        Data $imageHelper,
        Template\Context $context,
        CategoryRepositoryInterface $categoryRepository,
        CustomerSession $customerSession,
        CollectionFactory $orderCustomer,
        CustomerFactory $customerFactory,
        CustomerRepositoryInterface $customerRepository,
        Customer $customer,
        ProductRepositoryInterface $productRepository,
        CurrentCustomer $currentCustomer,
        array $data = []
    ) {
        $this->imageHelper = $imageHelper;
        $this->categoryRepository = $categoryRepository;
        $this->customerSession = $customerSession;
        $this->orderCustomer = $orderCustomer;
        $this->customer = $customer;
        $this->customerFactory = $customerFactory;
        $this->customerRepository = $customerRepository;
        $this->productRepository = $productRepository;
        $this->currentCustomer = $currentCustomer;

        parent::__construct($context, $data);
    }

    /**
     * Get selected categories scope store
     *
     * @param $id
     * @return \Magento\Catalog\Api\Data\CategoryInterface|string
     */
    public function getCategoryById($id)
    {
        try {
            return $this->categoryRepository->get($id);
        } catch (\Exception $e) {
            return '';
        }
    }

    /**
     * @return array
     */
    public function getSelectedCategoryIds()
    {
        return explode(',', $this->getData('categories'));
    }

    /**
     * @return int|null
     */
    public function getCustomerId()
    {
        return $this->customerSession->getCustomerId();
    }

    /**
     * Get current customer
     *
     * Return stored customer or get it from session
     *
     * @return \Magento\Customer\Api\Data\CustomerInterface
     * @since 102.0.1
     */
    public function getCustomer(): \Magento\Customer\Api\Data\CustomerInterface
    {
        $customer = $this->getData('customer');
        if ($customer === null) {
            $customer = $this->currentCustomer->getCustomer();
            $this->setData('customer', $customer);
        }
        return $customer;
    }

    /**
     * @param $selectedIdsDefault
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function setSelectedCategoriesCustomer($selectedIdsDefault)
    {
        if ($selectedIdsDefault) {
            $customerId = $this->getCustomerId();
            //get last order
            $lastOrder = $this->getLastOrder($customerId);
            /** save attribute categories selected for customer*/
            if (!$lastOrder) {
                //if lastOrder is false so customer has not any order on site
                //set default value categories selected if attribute value is 0
                if (is_array($selectedIdsDefault)
                    && isset($selectedIdsDefault[0])
                    && isset($selectedIdsDefault[1])
                    && isset($selectedIdsDefault[2])
                ) {
                    $this->saveAttributeSelectedCategories($customerId, $selectedIdsDefault[0], $selectedIdsDefault[1],
                        $selectedIdsDefault[2]);
                }
            } else {
                //if lastOrder is true so customer has at least one order on site
                /** process new categories selected from last order*/
                $productChooseId = $this->getProductIdChoose($lastOrder);
                $productChoose = $this->productRepository->getById($productChooseId);
                //getCategoryId C0 of product choose
                $categoryIds = $productChoose->getCategoryIds();
                $newFirstPlaceCat = NULL;
                foreach ($categoryIds as $catId) {
                    $catItem = $this->categoryRepository->get($catId);
                    if ($catItem->getLevel() == self::CATEGORY_C0_LEVEL) {
                        $newFirstPlaceCat = $catItem->getId();
                    }
                }
                //call save attribute value with new first place cat
                if($newFirstPlaceCat){
                    //get old value of customer attribute
                    $oldValueofAttribute = $this->getSeletedCategoriesAttr($customerId);
                    $oldValueArray = explode(',', $oldValueofAttribute);
                    if($newFirstPlaceCat != $oldValueArray[0]){
                        $this->saveAttributeSelectedCategories($customerId, $newFirstPlaceCat, $oldValueArray[0],
                            $oldValueArray[1]);
                    }
                }
            }
        }
    }

    /**
     * @param $customerId
     * @param $selectedIdsDefault
     * @return array|string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getSelectedCategoriesCustomer($customerId, $selectedIdsDefault)
    {
        if ($selectedIdsDefault) {
            /** running set attribute new value if data value is 0*/
            $this->setSelectedCategoriesCustomer($selectedIdsDefault);
            //get attribute value selected categories of customer
            $attributeCustomer = $this->getSeletedCategoriesAttr($customerId);
            if (!is_null($attributeCustomer) && $attributeCustomer != self::VALUE_DEFAULT) {
                return explode(',', $attributeCustomer);
            } else {
                return $selectedIdsDefault;
            }
        }

        return $selectedIdsDefault;
    }

    /**
     * @param $customerId
     * @return null
     */
    public function getLastOrder($customerId)
    {
        //get last order
        $lastOrder = false;
        if ($customerId != NULL) {
            $customerOrder = $this->orderCustomer->create()
                ->addFieldToFilter('customer_id', $customerId)
                ->addFieldToFilter('status', self::ORDER_COMPLETED)
                ->setOrder('entity_id', 'desc')
                ->load();

            if ($customerId && $customerOrder->getSize() > 0) {
                foreach ($customerOrder as $orderItem) {
                    if (!$lastOrder) {
                        $lastOrder = $orderItem;
                    }
                }
            }
        }

        return $lastOrder;
    }

    /**
     * @param $lastOrder
     * @return bool|null
     */
    public function getProductIdChoose($lastOrder)
    {
        if ($lastOrder) {
            $productChoose = false;
            foreach ($lastOrder->getAllItems() as $item) {
                if (!$productChoose) {
                    $productChoose = $item;
                }
            }

            return $productChoose->getProductId();
        }

        return NULL;
    }

    /**
     * @param $customerId
     * @return mixed|string
     */
    public function getSeletedCategoriesAttr($customerId)
    {
        try {
            $customer = $this->getCustomer();
        } catch (NoSuchEntityException $noSuchEntityException) {
            return self::VALUE_DEFAULT;
        }
        $attribute = $customer->getCustomAttribute(self::SLECTED_CATEGORIES);
        if ($attribute && $attribute->getValue() != self::VALUE_DEFAULT) {
            return $attribute->getValue();
        }

        return self::VALUE_DEFAULT;
    }

    /**
     * @param $customerId
     * @param $firstPlaceCat
     * @param $secondPlaceCat
     * @param $thirdPlaceCat
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function saveAttributeSelectedCategories($customerId, $firstPlaceCat, $secondPlaceCat, $thirdPlaceCat)
    {
        $attributeVal = [$firstPlaceCat, $secondPlaceCat, $thirdPlaceCat];
        $selectedCatValDefault = $this->getSeletedCategoriesAttr($customerId);
        $selectedCatDefaultArray = explode(',', $selectedCatValDefault);
        if (is_array($attributeVal) && is_array($selectedCatDefaultArray)) {
            //filter value and met condition to save
            if ((isset($selectedCatDefaultArray[0]) && $selectedCatDefaultArray[0] != $firstPlaceCat)
                || (isset($selectedCatDefaultArray[1]) && $selectedCatDefaultArray[1] != $secondPlaceCat)
                || (isset($selectedCatDefaultArray[2]) && $selectedCatDefaultArray[2] != $thirdPlaceCat)) {
                //process value to save
                $saveVal = implode(',', $attributeVal);

                //save customer attribute
                $customer = $this->customer->load($customerId);
                $customerData = $customer->getDataModel();
                $customerData->setCustomAttribute(self::SLECTED_CATEGORIES, $saveVal);
                $customer->updateData($customerData);
                $customerResource = $this->customerFactory->create();
                $customerResource->saveAttribute($customer, self::SLECTED_CATEGORIES);
            }
        }
    }

    /**
     * @param $image
     * @param $width
     * @param $height
     * @return bool|string
     * @throws \Exception
     */
    public function getImageResize($image, $width = null, $height = null)
    {
        return $this->imageHelper->getImageResize($image, $width, $height);
    }
}
