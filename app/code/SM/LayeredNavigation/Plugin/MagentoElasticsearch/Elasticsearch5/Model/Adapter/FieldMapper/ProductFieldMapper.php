<?php

/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_LayeredNavigation
 *
 * Date: May, 20 2020
 * Time: 10:44 AM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\LayeredNavigation\Plugin\MagentoElasticsearch\Elasticsearch5\Model\Adapter\FieldMapper;

use Magento\Customer\Model\Session;

class ProductFieldMapper
{

    /**
     * @var Session
     */
    protected $session;

    /**
     * ProductFieldMapper constructor.
     * @param Session $session
     */
    public function __construct(
        Session $session
    ) {
        $this->session = $session;
    }

    /**
     * Convert Vip price field name by website and customer group.
     *
     * @param \Magento\Elasticsearch\Elasticsearch5\Model\Adapter\FieldMapper\ProductFieldMapper $subject
     * @param string                                                                             $result
     * @param string                                                                             $attributeCode
     *
     * @return string
     */
    public function afterGetFieldName(
        \Magento\Elasticsearch\Elasticsearch5\Model\Adapter\FieldMapper\ProductFieldMapper $subject,
        $result,
        $attributeCode
    ) {
        if ($attributeCode === \SM\LayeredNavigation\Helper\Data\FilterList::DISCOUNT_OPTION_CODE) {
            $result = $attributeCode . '_' . $this->session->getOmniStoreId();
        }

        return $result;
    }
}
