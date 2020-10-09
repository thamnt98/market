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

namespace SM\Catalog\Block\Product;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Block\Product\View as ViewDefault;
use Magento\Catalog\Helper\Product;
use Magento\Catalog\Model\ProductTypes\ConfigInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Json\EncoderInterface as JsonEncoderInterface;
use Magento\Framework\Locale\FormatInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Stdlib\StringUtils;
use Magento\Framework\Url\EncoderInterface as UrlEncoderInterface;
use Magento\Setup\Exception;
use SM\Catalog\Helper\Data;
use SM\Catalog\Model\Source\Delivery\Method;
use SM\Catalog\Block\Product\ProductList\Item\AddTo\Iteminfo;
use SM\Help\Model\Config as HelpConfig;
use SM\Help\Api\TopicRepositoryInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use SM\Catalog\Helper\StorePickup as HelperPickup;
use Magento\Cms\Model\Template\FilterProvider;
use Magento\Framework\Pricing\Helper\Data as PricingHelper;
use SM\Catalog\Helper\Delivery as HelperDelivery;
use SM\Bundle\Helper\BundleAttribute as BundleHelper;

/**
 * Class View
 * @package SM\Catalog\Block\Product
 */
class View extends ViewDefault
{
    const PRODUCT_CONFIGURABLE = 'configurable';
    const PRODUCT_BUNDLE = 'bundle';
    const PRODUCT_GROUPED = 'grouped';
    const PRODUCT_SIMPLE = 'simple';
    const VALUE_YES = '1';
    const VALUE_NO = '0';
    const DISTANCE = 'distance';
    const DISTANCE_TYPE = 'Km';
    const DISTANCE_MAX_KM = 100000;

    /**
     * @var Data
     */
    public $helperData;

    /**
     * @var Method
     */
    protected $deliverySource;

    /**
     * @var Iteminfo
     */
    public $itemInfo;

    /**
     * @var HelpConfig
     */
    protected $helpConfig;

    /**
     * @var TopicRepositoryInterface
     */
    protected $topicRepository;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var HelperPickup
     */
    protected $helperPickup;

    /**
     * @var FilterProvider
     */
    protected $filterProvider;

    /**
     * @var PricingHelper
     */
    public $priceHelper;

    /**
     * @var HelperDelivery
     */
    protected $helperDelivery;
    /**
     * @var BundleHelper
     */
    private $bundleHelper;

    /**
     * View constructor.
     * @param Context $context
     * @param UrlEncoderInterface $urlEncoder
     * @param JsonEncoderInterface $jsonEncoder
     * @param StringUtils $string
     * @param Product $productHelper
     * @param ConfigInterface $productTypeConfig
     * @param FormatInterface $localeFormat
     * @param Session $customerSession
     * @param ProductRepositoryInterface $productRepository
     * @param PriceCurrencyInterface $priceCurrency
     * @param Data $helperData
     * @param Method $deliverySource
     * @param Iteminfo $itemInfo
     * @param HelpConfig $helpConfig
     * @param TopicRepositoryInterface $topicRepository
     * @param CustomerRepositoryInterface $customerRepository
     * @param HelperPickup $helperPickup
     * @param FilterProvider $filterProvider
     * @param PricingHelper $priceHelper
     * @param HelperDelivery $helperDelivery
     * @param BundleHelper $bundleHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        UrlEncoderInterface $urlEncoder,
        JsonEncoderInterface $jsonEncoder,
        StringUtils $string,
        Product $productHelper,
        ConfigInterface $productTypeConfig,
        FormatInterface $localeFormat,
        Session $customerSession,
        ProductRepositoryInterface $productRepository,
        PriceCurrencyInterface $priceCurrency,
        Data $helperData,
        Method $deliverySource,
        Iteminfo $itemInfo,
        HelpConfig $helpConfig,
        TopicRepositoryInterface $topicRepository,
        CustomerRepositoryInterface $customerRepository,
        HelperPickup $helperPickup,
        FilterProvider $filterProvider,
        PricingHelper $priceHelper,
        HelperDelivery $helperDelivery,
        BundleHelper $bundleHelper,
        array $data = []
    ) {
        $this->helperData = $helperData;
        $this->deliverySource = $deliverySource;
        $this->itemInfo = $itemInfo;
        $this->helpConfig = $helpConfig;
        $this->topicRepository = $topicRepository;
        $this->customerRepository = $customerRepository;
        $this->helperPickup = $helperPickup;
        $this->filterProvider = $filterProvider;
        $this->priceHelper = $priceHelper;
        $this->helperDelivery = $helperDelivery;
        $this->bundleHelper = $bundleHelper;

        parent::__construct(
            $context,
            $urlEncoder,
            $jsonEncoder,
            $string,
            $productHelper,
            $productTypeConfig,
            $localeFormat,
            $customerSession,
            $productRepository,
            $priceCurrency,
            $data
        );
    }

    /**
     * @param $product
     * @return float|int|null
     */
    public function getDiscountPercent($product)
    {
        if ($product->getTypeId() == \Magento\Bundle\Model\Product\Type::TYPE_CODE) {
            return $this->getDiscountBundleMin($this->bundleHelper->getMinAmount($product, true),
                $this->bundleHelper->getMinAmount($product, true, true));
        } else {
            return $this->helperData->getDiscountPercent($product);
        }
    }

    /**
     * @param $sumSpecialPriceMin
     * @param $sumPriceMin
     * @return float|null
     */
    public function getDiscountBundleMin($sumSpecialPriceMin, $sumPriceMin)
    {
        if (is_null($sumPriceMin) || is_null($sumSpecialPriceMin) || $sumPriceMin <= $sumSpecialPriceMin
        ) {
            return NULL;
        }

        return round(
            ($sumPriceMin - $sumSpecialPriceMin) * 100 / $sumPriceMin
        );
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return string
     */
    public function getOriginalPrice($product)
    {
        if ($product->getTypeId() == \Magento\Bundle\Model\Product\Type::TYPE_CODE) {
            return $this->priceHelper->currency($this->bundleHelper->getMinAmount($product, true, true));
        } else {
            $minProduct = $this->helperData->getMinProduct($product);

            return $this->priceHelper->currency($minProduct->getPrice(), true, false);
        }
    }

    /**
     * @param $product
     * @return bool
     */
    public function isShowBadgeDiscount($product)
    {
        return $this->itemInfo->isShowBadgeDiscount($product);
    }

    /**
     * @param $product
     * @return null
     */
    public function getFirstItemOfConfigProduct($product)
    {
        return $this->itemInfo->getFirstItemOfConfigProduct($product);
    }

    /**
     * @return array
     * @throws NoSuchEntityException
     */
    public function getDeliveryMethod()
    {
        $product = $this->getProduct();

        return $this->helperDelivery->getDeliveryMethod($product);
    }

    /**
     * @return int
     */
    public function getTopicShowTabReturn()
    {
        return $this->helpConfig->getTopicShowTabReturn() ? explode(',',
            $this->helpConfig->getTopicShowTabReturn()) : [];
    }

    /**
     * @param $topicId
     * @return array|\SM\Help\Api\Data\TopicInterface
     */
    public function getTopicById($topicId)
    {
        try {
            return $this->topicRepository->getById($topicId);
        } catch (\Exception $exception) {
            return [];
        }
    }

    /**
     * @param $topicId
     * @return array|\SM\Help\Api\Data\TopicInterface[]
     */
    public function getChildTopicsFromParrentId($topicId)
    {
        try {
            return $this->topicRepository->getChildTopics($topicId);
        } catch (\Exception $exception) {
            return [];
        }
    }

    /**
     * @param $topicId
     * @return array|\SM\Help\Api\Data\QuestionInterface[]
     */
    public function getChildQuestionsFromParrentId($topicId)
    {
        try {
            return $this->topicRepository->getChildQuestions($topicId);
        } catch (\Exception $exception) {
            return [];
        }
    }

    /**
     * @param $questionContent
     * @return string
     * @throws \Exception
     */
    public function getQuestionContent($questionContent)
    {
        return $this->filterProvider->getPageFilter()->filter($questionContent);
    }

    /**
     * @param $sourceListSimple
     * @param $sourceListConfig
     * @param $sourceListBundle
     * @return bool
     */
    public function hasSourceListAvailable($sourceListSimple, $sourceListConfig, $sourceListBundle)
    {
        if ($this->helperPickup->hasSourceListAvailable($sourceListSimple, $sourceListConfig, $sourceListBundle)) {
            return true;
        }

        return false;
    }

    /**
     * @param $product
     * @return array
     */
    public function getSourcesListSimple($product)
    {
        return $this->helperPickup->getSourcesListSimple($product);
    }

    /**
     * @param $product
     * @return array
     * @throws NoSuchEntityException
     */
    public function getSourcesListConfigurable($product)
    {
        return $this->helperPickup->getSourcesListConfigurable($product);
    }

    /**
     * @param $product
     * @return array
     * @throws NoSuchEntityException
     */
    public function getSourcesListBundle($product)
    {
        return $this->helperPickup->getSourcesListBundle($product);
    }

    /**
     * @return mixed
     */
    public function getMainCustomerAddress()
    {
        return $this->helperPickup->getMainCustomerAddress();
    }

    /**
     * @return array
     */
    public function getLatLongAddressPinPoint()
    {
        return $this->helperPickup->getLatLongAddressPinPoint();
    }

    /**
     * @return string
     */
    public function getStoreTimeAvailable()
    {
        return $this->helperPickup->getStoreTimeAvailable();
    }
}
