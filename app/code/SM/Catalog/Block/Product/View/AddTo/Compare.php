<?php

/**
 * @category  SM
 * @package   SM_Catalog
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 * @author    Logan Ta <phuongtt2@smartosc.com>
 *
 * @copyright 2020 Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

declare(strict_types=1);

namespace SM\Catalog\Block\Product\View\AddTo;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Block\Product\View\AddTo\Compare as compareDefault;

/**
 * Class Compare
 * @package SM\Catalog\Block\Product\View\AddTo
 */
class Compare extends compareDefault
{
    const VALUE_YES = 1;
    /**
     * @var \Magento\Framework\Data\Helper\PostHelper
     */
    protected $postHelper;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Magento\Catalog\Helper\Product\Compare
     */
    protected $_helper;

    protected $storeManager;

    protected $categoryRepository;

    protected $resourceConnection;

    public function __construct(\Magento\Catalog\Block\Product\Context $context,
                                \Magento\Framework\Url\EncoderInterface $urlEncoder,
                                \Magento\Framework\Json\EncoderInterface $jsonEncoder,
                                \Magento\Framework\Stdlib\StringUtils $string,
                                \Magento\Catalog\Helper\Product $productHelper,
                                \Magento\Catalog\Model\ProductTypes\ConfigInterface $productTypeConfig,
                                \Magento\Framework\Locale\FormatInterface $localeFormat,
                                \Magento\Customer\Model\Session $customerSession,
                                ProductRepositoryInterface $productRepository,
                                \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
                                \Magento\Framework\Data\Helper\PostHelper $postHelper,
                                \Magento\Framework\UrlInterface $urlBuilder,
                                \Magento\Catalog\Model\CategoryRepository $categoryRepository,
                                \Magento\Catalog\Helper\Product\Compare $helper,
                                \Magento\Store\Model\StoreManagerInterface $storeManager,
                                \Magento\Framework\App\ResourceConnection $resourceConnection,
                                array $data = [])
    {
        $this->postHelper = $postHelper;
        $this->urlBuilder = $urlBuilder;
        $this->_helper = $helper;
        $this->storeManager = $storeManager;
        $this->categoryRepository = $categoryRepository;
        $this->resourceConnection = $resourceConnection;
        parent::__construct($context, $urlEncoder, $jsonEncoder, $string, $productHelper, $productTypeConfig, $localeFormat, $customerSession, $productRepository, $priceCurrency, $data);
    }

    /**
     * Return compare params
     *
     * @return string
     * @since 101.0.1
     */
    public function getPostDataParams()
    {
        $product = $this->getProduct();
        return $this->_compareProduct->getPostDataParams($product);
    }

    /**
     * @return bool
     */
    public function isAllowAddToComepare()
    {
        $count = 0;
        $productId = $this->getProduct()->getId();

        $connection = $this->resourceConnection->getConnection();
        $sql = "SELECT category_id FROM catalog_category_product WHERE product_id = ".$productId;

        $categorisList = $connection->fetchAll($sql);

        foreach ($categorisList as $catId) {
            if ($catId["category_id"]) {
                $category = $this->categoryRepository->get($catId["category_id"], $this->_storeManager->getStore()->getId());
                if ($category->getAllowCompare() == self::VALUE_YES) {
                    $count++;
                }
            }
        }
        if ($count == 0) return false;
        return true;
    }

    /**
     * Get add to compare list url
     * @return string
     */
    public function getAddUrl($params = []){
        return $this->_getUrl('catalog/product_compare/add',$params);
    }

    /**
     * Build url
     * @param $route
     * @param array $params
     * @return string
     */
    public function _getUrl($route, $params = []){
        return $this->_urlBuilder->getUrl($route, $params);

    }
}
