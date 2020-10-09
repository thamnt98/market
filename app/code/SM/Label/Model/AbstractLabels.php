<?php
/**
 * @category  SM
 * @package   SM_Label
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 * @author    Logan Ta <phuongtt2@smartosc.com>
 *
 * @copyright 2020 Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

declare(strict_types=1);

namespace SM\Label\Model;

use Amasty\Base\Model\Serializer;
use Amasty\Label\Api\Data\LabelInterface;
use Amasty\Label\Model\GiftCard\Model\Product\ReadHandler;
use Magento\Catalog\Model\Product;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\GroupedProduct\Model\Product\Type\Grouped;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Bundle\Model\Product\Type as BundleType;
use Amasty\Label\Model\AbstractLabels as AbstractLabelsDefault;

class AbstractLabels extends AbstractLabelsDefault
{
    const PRODUCT_STOCK_MODE = 'product_stock';

    /**
     * Catalog data
     *
     * @var \Magento\Catalog\Helper\Data
     */
    protected $catalogData = null;

    /**
     * @var bool
     */
    protected $isOutOfStockOnly = null;

    /**
     * Stock Registry
     *
     * @var \Magento\CatalogInventory\Api\StockRegistryInterface
     */
    private $stockRegistry;

    /**
     * @var \Amasty\Label\Helper\Config
     */
    protected $helper;

    /**
     * @var  array
     */
    private $prices;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var RuleFactory
     */
    private $ruleFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;

    /**
     * @var Configurable
     */
    private $configurableType;

    /**
     * @var \Magento\GiftCard\Model\Product\ReadHandler
     */
    private $readHandler;

    /**
     * @var \Magento\Framework\Filesystem\Driver\File
     */
    protected $file;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Amasty\Label\Model\RuleFactory $ruleFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Helper\Data $catalogData,
        \Amasty\Label\Helper\Config $helper,
        PriceCurrencyInterface $priceCurrency,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Amasty\Base\Model\Serializer $serializer,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurableType,
        \Amasty\Label\Model\GiftCard\Model\Product\ReadHandler $readHandler,
        \Magento\Framework\Filesystem\Driver\File $file,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $ruleFactory,
            $catalogData,
            $stockRegistry,
            $priceCurrency,
            $helper,
            $date,
            $serializer,
            $storeManager,
            $timezone,
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data,
            $this->setCacheTags([self::CACHE_TAG]),
            $configurableType,
            $file,
            $readHandler
        );
    }

    /**
     * @param Product $product
     * @param null $mode
     * @param null $parent
     */
    public function init(Product $product, $mode = null, $parent = null)
    {
        $this->setProduct($product);
        $this->setParentProduct($parent);
        $this->prices = [];

        // auto detect product page
        if ($mode) {
            $this->setMode($mode == self::CATEGORY_MODE ? 'cat' : 'prod');
        } else {
            $this->setMode('cat');
        }
    }
}
