<?php

namespace SM\CustomPrice\Model;

use Magento\Checkout\Model\Cart;
use Magento\Customer\Api\CustomerMetadataInterface;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Customer\Model\AccountConfirmation;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Math\Random;
use Magento\Framework\Reflection\DataObjectProcessor;
use SM\CustomPrice\Model\ResourceModel\District;
use Magento\Customer\Model\Config\Share;
use Magento\Customer\Model\ResourceModel\Address\CollectionFactory;
use Magento\Customer\Model\ResourceModel\Customer as ResourceCustomer;



class Customer extends \Magento\Customer\Model\Customer
{
    const OMNI_STORE_ID            = 'omni_store_id';
    const PREFIX_OMNI_FINAL_PRICE  = 'promo_price_';
    const PREFIX_OMNI_NORMAL_PRICE = 'base_price_';

    public function getDefaultOmniStoreCode()
    {
        return $this->_scopeConfig->getValue('sm_customer/customer_omni_store/default_omni_store_code');
    }
}
