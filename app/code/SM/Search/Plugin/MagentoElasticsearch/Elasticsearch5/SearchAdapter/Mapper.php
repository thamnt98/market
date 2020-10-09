<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Search
 *
 * Date: June, 01 2020
 * Time: 5:29 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Search\Plugin\MagentoElasticsearch\Elasticsearch5\SearchAdapter;

class Mapper
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * Mapper constructor.
     *
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->request = $request;
    }

    /**
     * @param \Magento\Elasticsearch\Elasticsearch5\SearchAdapter\Mapper $subject
     * @param array                                                      $result
     *
     * @return array
     */
    public function afterBuildQuery(
        \Magento\Elasticsearch\Elasticsearch5\SearchAdapter\Mapper $subject,
        $result
    ) {
        $order = $this->request->getParam(\Magento\Catalog\Model\Product\ProductList\Toolbar::ORDER_PARAM_NAME);
        if (in_array($order, $this->getAllowKeys())) {
            $result['body']["stored_fields"][] = '_source';
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
