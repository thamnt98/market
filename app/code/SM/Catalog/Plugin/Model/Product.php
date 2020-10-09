<?php
/**
 * Class Product
 * @package SM\Catalog\Plugin\Model
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

declare(strict_types=1);

namespace SM\Catalog\Plugin\Model;

use Magento\Store\Model\ScopeInterface;

class Product
{
    const XML_PATH_GROUP_PREFIX = 'trans_catalog/product/group_name_prefix';
    const XML_PATH_BUNDLE_PREFIX = 'trans_catalog/product/bundle_name_prefix';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    private $request;

    /**
     * Product constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\App\Request\Http $request
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\Request\Http $request
    ) {
        $this->request = $request;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param \Magento\Catalog\Model\Product $subject
     * @param $result
     * @return string
     */
    public function afterGetName(
        \Magento\Catalog\Model\Product $subject,
        $result
    ) {
        if ($this->request->getFullActionName() != 'catalog_product_view') {
            $productTypeId = $subject->getTypeId();
            switch ($productTypeId) {
                case 'grouped':
                    $result = $this->getPrefixName(self::XML_PATH_GROUP_PREFIX) . ' ' . $result;
                    break;
                case 'bundle':
                    $result = $this->getPrefixName(self::XML_PATH_BUNDLE_PREFIX) . ' ' . $result;
                    break;
                default:
                    break;
            }
        }

        return $result;
    }

    /**
     * @param $path
     * @return null|string
     */
    protected function getPrefixName($path)
    {
        $prefixName = $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE);
        $prefixName = $prefixName ? trim($prefixName) : '' ;

        return $prefixName;
    }
}
