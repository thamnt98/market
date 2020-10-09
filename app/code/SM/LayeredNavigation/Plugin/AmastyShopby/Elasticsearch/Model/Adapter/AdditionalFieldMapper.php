<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_LayeredNavigation
 *
 * Date: May, 29 2020
 * Time: 11:21 AM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\LayeredNavigation\Plugin\AmastyShopby\Elasticsearch\Model\Adapter;

class AdditionalFieldMapper extends \Amasty\Shopby\Plugin\Elasticsearch\Model\Adapter\AdditionalFieldMapper
{
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $fields = []
    ) {
        unset($fields['am_on_sale']);
        parent::__construct($customerSession, $storeManager, $fields);
    }
}
