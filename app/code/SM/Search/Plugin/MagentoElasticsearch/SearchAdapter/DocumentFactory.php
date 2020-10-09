<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Search
 *
 * Date: June, 01 2020
 * Time: 5:50 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Search\Plugin\MagentoElasticsearch\SearchAdapter;

use Magento\Framework\Api\AttributeInterface;
use Magento\Framework\Api\AttributeValue;

class DocumentFactory
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \SM\Search\Helper\ProductList\Sort
     */
    protected $helper;

    /**
     * Mapper constructor.
     *
     * @param \SM\Search\Helper\ProductList\Sort           $helper
     * @param \Magento\Framework\App\RequestInterface      $request
     */
    public function __construct(
        \SM\Search\Helper\ProductList\Sort $helper,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->request = $request;
        $this->helper = $helper;
    }

    /**
     * @param \Magento\Elasticsearch\SearchAdapter\DocumentFactory $subject
     * @param \Magento\Framework\Api\Search\Document               $result
     * @param array                                                $rawDocument
     *
     * @return \Magento\Framework\Api\Search\Document
     */
    public function afterCreate(
        \Magento\Elasticsearch\SearchAdapter\DocumentFactory $subject,
        $result,
        $rawDocument
    ) {
        $order = $this->request->getParam(\Magento\Catalog\Model\Product\ProductList\Toolbar::ORDER_PARAM_NAME);
        if (in_array($order, $this->getAllowKeys())) {
            $mapFieldName = $this->helper->getMapFieldName();
            $field = $this->helper->getFieldName();
            if ($mapFieldName && $field) {
                $value = $rawDocument['_source'][$mapFieldName] ?? null;
                $result->setCustomAttribute(
                    $field,
                    new AttributeValue([
                        AttributeInterface::ATTRIBUTE_CODE => $mapFieldName,
                        AttributeInterface::VALUE => $value,
                    ])
                );
            }
        }

        return $result;
    }

    /**
     * @return array
     */
    protected function getAllowKeys()
    {
        return [
            \SM\Catalog\Helper\ProductList\Toolbar::SORT_BY_DISCOUNT,
            \SM\Catalog\Helper\ProductList\Toolbar::SORT_BY_PRICE_HIGH_TO_LOW,
            \SM\Catalog\Helper\ProductList\Toolbar::SORT_BY_PRICE_LOW_TO_HIGH,
        ];
    }
}
