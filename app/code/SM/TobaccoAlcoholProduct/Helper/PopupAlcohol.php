<?php
/**
 * SM\TobaccoAlcoholProduct\Helper
 *
 * @copyright Copyright © 2020 SmartOSC. All rights reserved.
 * @author    hungnv6@smartosc.com
 */

namespace SM\TobaccoAlcoholProduct\Helper;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Customer\Model\ResourceModel\CustomerRepository;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

/**
 * Class PopupTobacco
 * @package SM\TobaccoAlcoholProduct\Helper
 */
class PopupAlcohol extends AbstractHelper
{
    const IS_ALCOHOL_INFORMED = "is_alcohol_informed";

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var CurrentCustomer
     */
    protected $currentCustomer;

    /**
     * @var CustomerRepository
     */
    protected $customerRepository;

    /**
     * PopupTobacco constructor.
     * @param \Magento\Framework\Registry $registry
     * @param Session $customerSession
     * @param Context $context
     * @param CurrentCustomer $currentCustomer
     * @param CustomerRepository $customerRepository
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        Session $customerSession,
        Context $context,
        CurrentCustomer $currentCustomer,
        CustomerRepository $customerRepository
    ) {
        $this->customerRepository = $customerRepository;
        $this->currentCustomer = $currentCustomer;
        $this->customerSession = $customerSession;
        $this->registry = $registry;
        parent::__construct($context);
    }

    /**
     * Check whether current category is alcohol/tobacco or not
     *
     * @param $category
     * @return bool
     */
    public function isCategoryAlcohol($category)
    {
        if ($category->getData("is_alcohol") || $category->getData("is_tobacco")) {
            return true;
        }
        return false;
    }

    /**
     * Check whether current product is alcohol/tobacco or not
     *
     * @param $product
     * @return bool
     */
    public function isProductAlcohol($product)
    {
        if (($product->getCustomAttribute("is_alcohol") && $product->getCustomAttribute("is_alcohol")->getValue()) ||
            ($product->getCustomAttribute("is_tobacco") && $product->getCustomAttribute("is_tobacco")->getValue())
        ) {
            return true;
        }
        return false;
    }

    /**
     * @return mixed
     */
    public function getCurrentCategory()
    {
        return $this->registry->registry('current_category');
    }

    /**
     * @return ProductInterface
     */
    public function getCurrentProduct()
    {
        return $this->registry->registry('current_product');
    }

    /**
     * @param CustomerInterface $customer
     * @return bool
     */
    public function isCustomerInformed($customer)
    {
        if ($this->customerSession->isLoggedIn()) {
            if ($customer->getCustomAttribute(self::IS_ALCOHOL_INFORMED)) {
                return $customer->getCustomAttribute(self::IS_ALCOHOL_INFORMED)->getValue();
            }
        }
        return false;
    }

    /**
     * @param bool $isSearch
     * @return false|CustomerInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCurrentCustomer(bool $isSearch)
    {
        if ($this->customerSession->isLoggedIn()) {
            if ($isSearch) {
                /**
                 * For some reason, customer data in session is missing in search result page
                 * => Cannot check value of attribute 'is_alcohol_informed'
                 * That's why I have to use repository to get customer data in search page.
                 *
                 * TODO: Investigate this issue.
                 */
                return $this->customerRepository->getById($this->customerSession->getCustomerId());
            }
            return $this->currentCustomer->getCustomer();
        }
        return false;
    }
}
