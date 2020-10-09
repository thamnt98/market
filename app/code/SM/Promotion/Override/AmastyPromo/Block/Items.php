<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Promotion
 *
 * Date: September, 21 2020
 * Time: 6:23 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Promotion\Override\AmastyPromo\Block;

use Magento\Catalog\Block\Product\ReviewRendererInterface;

class Items extends \Amasty\Promo\Block\Items
{
    /**
     * @var ReviewRendererInterface
     */
    protected $reviewRenderer;

    /**
     * @var \Amasty\Promo\Model\Config
     */
    protected $modelConfig;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * Items constructor.
     *
     * @param ReviewRendererInterface                           $reviewRenderer
     * @param \Magento\Framework\View\Element\Template\Context  $context
     * @param \Amasty\Promo\Helper\Data                         $promoHelper
     * @param \Magento\Catalog\Helper\Image                     $helperImage
     * @param \Magento\Framework\Url\Helper\Data                $urlHelper
     * @param \Magento\Catalog\Api\ProductRepositoryInterface   $productRepository
     * @param \Magento\Catalog\Block\Product\View               $productView
     * @param \Magento\Framework\Registry                       $registry
     * @param \Magento\Store\Model\Store                        $store
     * @param \Magento\Catalog\Helper\Data                      $catalogHelper
     * @param \Magento\Framework\Json\EncoderInterface          $jsonEncoder
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Amasty\Promo\Model\Config                        $modelConfig
     * @param \Magento\Framework\App\ProductMetadataInterface   $productMetadata
     * @param \Magento\Framework\Json\DecoderInterface          $jsonDecoder
     * @param array                                             $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\ReviewRendererInterface $reviewRenderer,
        \Magento\Framework\View\Element\Template\Context $context,
        \Amasty\Promo\Helper\Data $promoHelper,
        \Magento\Catalog\Helper\Image $helperImage,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Block\Product\View $productView,
        \Magento\Framework\Registry $registry,
        \Magento\Store\Model\Store $store,
        \Magento\Catalog\Helper\Data $catalogHelper,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Amasty\Promo\Model\Config $modelConfig,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        \Magento\Framework\Json\DecoderInterface $jsonDecoder,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $promoHelper,
            $helperImage,
            $urlHelper,
            $productRepository,
            $productView,
            $registry,
            $store,
            $catalogHelper,
            $jsonEncoder,
            $priceCurrency,
            $modelConfig,
            $productMetadata,
            $jsonDecoder,
            $data
        );
        $this->reviewRenderer = $reviewRenderer;
        $this->modelConfig = $modelConfig;
        $this->priceCurrency = $priceCurrency;
    }

    public function toHtml()
    {
        if ($this->getItems()) {
            return parent::toHtml();
        }

        return '';
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return string
     */
    public function getProductImageUrl($product)
    {
        $this->helperImage->init($product, 'cart_page_product_thumbnail')
            ->keepFrame(false)
            ->constrainOnly(true)
            ->resize(160, 160);

        return $this->helperImage->getUrl();
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return string
     */
    public function getReviewsSummaryHtml(\Magento\Catalog\Model\Product $product)
    {
        return $this->reviewRenderer->getReviewsSummaryHtml(
            $product,
            \Magento\Catalog\Block\Product\ReviewRendererInterface::SHORT_VIEW,
            false
        );
    }

    public function isShowProductReview()
    {
        return (bool)$this->modelConfig->getScopeValue("messages/show_review_in_popup");
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return string
     */
    public function getOptionsHtml(\Magento\Catalog\Model\Product $product)
    {
        $result = '';
        /** @var \Magento\Catalog\Block\Product\View\Options\AbstractOptions $optionsBlock */
        $optionsBlock = $this->getChildBlock($product->getTypeId() . '_prototype');
        if ($optionsBlock) {
            $result .= $optionsBlock->setProduct($product)->toHtml();
            if ($product->getTypeId() === 'giftcard') {
                $result .= $this->getGiftCardPrice($product);
            }
        }

        try {
            $result .= parent::getOptionsHtml($product);
        } catch (\Exception $e) {
        }

        return $result;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return string
     */
    public function getAddToHtml($product)
    {
        /** @var \Magento\Catalog\Block\Product\ProductList\Item\Container $block */
        $block = $this->getChildBlock('addto');
        if ($block) {
            return $block->setProduct($product)->getChildHtml();
        } else {
            return '';
        }
    }

    /**
     * @param $price
     *
     * @return string
     */
    public function getPriceTxt($price)
    {
        return $this->priceCurrency->getCurrency()->formatTxt($price, ['precision' => 0]);
    }
}
