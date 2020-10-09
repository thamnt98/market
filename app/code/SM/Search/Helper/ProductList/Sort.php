<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Search
 *
 * Date: June, 02 2020
 * Time: 10:21 AM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Search\Helper\ProductList;

use SM\CustomPrice\Model\Customer as CustomerPrice;
use SM\LayeredNavigation\Plugin\MagentoElasticsearch\Model\Adapter\BatchDataMapper\ProductDataMapper;

class Sort extends \Magento\Framework\App\Helper\AbstractHelper
{
    const DISCOUNT_FIELD_NAME = 'index_discount_percent';
    const PRICE_FIELD_NAME    = 'index_price';

    /**
     * @var \SM\CustomPrice\Model\ResourceModel\District
     */
    protected $omniDistrict;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $session;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Sort constructor.
     *
     * @param \SM\CustomPrice\Model\ResourceModel\District $omniDistrict
     * @param \Magento\Customer\Model\Session              $session
     * @param \Magento\Store\Model\StoreManagerInterface   $storeManager
     * @param \Magento\Framework\App\Helper\Context        $context
     */
    public function __construct(
        \SM\CustomPrice\Model\ResourceModel\District $omniDistrict,
        \Magento\Customer\Model\Session $session,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Helper\Context $context
    ) {
        parent::__construct($context);
        $this->omniDistrict = $omniDistrict;
        $this->session = $session;
        $this->storeManager = $storeManager;
    }

    /**
     * @return string
     */
    public function getMapFieldName()
    {
        $order = $this->_getRequest()->getParam(\Magento\Catalog\Model\Product\ProductList\Toolbar::ORDER_PARAM_NAME);
        $code = $this->session->getOmniStoreId();
        if ($order === \SM\Catalog\Helper\ProductList\Toolbar::SORT_BY_DISCOUNT) {
            return $code ? ProductDataMapper::DISCOUNT_PERCENT_PREFIX . $code
                : ProductDataMapper::DISCOUNT_PERCENT_FIELD_NAME;
        } elseif ($order === \SM\Catalog\Helper\ProductList\Toolbar::SORT_BY_PRICE_HIGH_TO_LOW ||
            $order === \SM\Catalog\Helper\ProductList\Toolbar::SORT_BY_PRICE_LOW_TO_HIGH
        ) {
            if ($code) {
                return CustomerPrice::PREFIX_OMNI_FINAL_PRICE . $code;
            } else {
                try {
                    $customerGroup = $this->session->getCustomerGroupId();
                    $websiteId = $this->storeManager->getWebsite()->getId();
                } catch (\Exception $e) {
                    $customerGroup = 0;
                    $websiteId = 0;
                }

                return 'price_' . $customerGroup . '_' . $websiteId;
            }
        }

        return '';
    }

    /**
     * @return string
     */
    public function getFieldName()
    {
        $order = $this->_getRequest()->getParam(\Magento\Catalog\Model\Product\ProductList\Toolbar::ORDER_PARAM_NAME);
        switch ($order) {
            case \SM\Catalog\Helper\ProductList\Toolbar::SORT_BY_DISCOUNT:
                return self::DISCOUNT_FIELD_NAME;
            case \SM\Catalog\Helper\ProductList\Toolbar::SORT_BY_PRICE_HIGH_TO_LOW:
            case \SM\Catalog\Helper\ProductList\Toolbar::SORT_BY_PRICE_LOW_TO_HIGH:
                return self::PRICE_FIELD_NAME;
            default:
                return '';
        }
    }

    /**
     * @return bool
     */
    public function isDecimalField()
    {
        $order = $this->_getRequest()->getParam(\Magento\Catalog\Model\Product\ProductList\Toolbar::ORDER_PARAM_NAME);
        switch ($order) {
            case \SM\Catalog\Helper\ProductList\Toolbar::SORT_BY_DISCOUNT:
            case \SM\Catalog\Helper\ProductList\Toolbar::SORT_BY_PRICE_HIGH_TO_LOW:
            case \SM\Catalog\Helper\ProductList\Toolbar::SORT_BY_PRICE_LOW_TO_HIGH:
                return true;
            default:
                return false;
        }
    }
}
