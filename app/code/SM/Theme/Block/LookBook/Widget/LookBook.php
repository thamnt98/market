<?php
/**
 * Class LookBook
 * @package SM\Theme\Block\LookBook\Widget
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

declare(strict_types=1);

namespace SM\Theme\Block\LookBook\Widget;

use Magento\Framework\App\Action\Action;
use SM\GTM\Block\Product\View as GTMProduct;

class LookBook extends \MGS\Lookbook\Block\AbstractLookbook implements \Magento\Widget\Block\BlockInterface
{
    /**
     * @var \MGS\Lookbook\Model\LookbookFactory
     */
    protected $lookbookFactory;
    /**
     * @var GTMProduct
     */
    private $gtmProduct;

    /**
     * Lookbook constructor.
     * @param GTMProduct $gtmProduct
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Catalog\Block\Product\Context $productContext
     * @param \MGS\Lookbook\Helper\Data $_helper
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $_productCollectionFactory
     * @param \Magento\Framework\Url\Helper\Data $urlHelper
     * @param \MGS\Lookbook\Model\LookbookFactory $lookbookFactory
     * @param \Magento\Swatches\Helper\Data $swatchHelper
     * @param array $data
     */
    public function __construct(
        GTMProduct $gtmProduct,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Block\Product\Context $productContext,
        \MGS\Lookbook\Helper\Data $_helper,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $_productCollectionFactory,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        \MGS\Lookbook\Model\LookbookFactory $lookbookFactory,
        \Magento\Swatches\Helper\Data $swatchHelper,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $productContext,
            $_helper,
            $swatchHelper,
            $_productCollectionFactory,
            $urlHelper,
            $data
        );
        $this->lookbookFactory = $lookbookFactory;
        $this->gtmProduct = $gtmProduct;
    }

    /**
     * @return \MGS\Lookbook\Model\Lookbook|void
     */
    public function getLookbook()
    {
        $lookbookId = $this->getData('lookbook_id');
        $lookbook = $this->lookbookFactory->create()->load($lookbookId);

        if ($lookbook->getId() && $lookbook->getStatus()) {
            return $lookbook;
        }

        return;
    }

    /**
     * @param $lookbook
     * @return string
     */
    public function getPinHtml($lookbook)
    {
        $pins = $lookbook->getPins();
        $arrPin = json_decode($pins, true);
        $html = '';
        $width = $this->_helper->getStoreConfig('lookbook/general/pin_width');
        $height = $this->_helper->getStoreConfig('lookbook/general/pin_height');
        $background = $this->_helper->getStoreConfig('lookbook/general/pin_background');
        $color = $this->_helper->getStoreConfig('lookbook/general/pin_text');
        $radius = round($width / 2);

        if ($arrPin) {
            $i = 0;
            $firstProduct = '';
            $relatedProduct = '';
            $arrProduct = [];
            $position = 1;
            foreach ($arrPin as $pin) {
                $imgWidth = $pin['imgW'];
                $imgHeight = $pin['imgH'];
                $top = $pin['top'];
                $left = $pin['left'];
                $leftPercent = ($left * 100) / $imgWidth;
                $topPercent = ($top * 100) / $imgHeight;
                $productBase = null;
                $label = 'Not available';
                if (!empty($pin['text'])) {
                    $productBase = $this->getProductInfo($pin['text']);
                    $relatedBlock = $this->getLayout()->getBlock('blog.post.related.products');
                    if ($relatedBlock) {
                        $currentPost = $relatedBlock->getCurrentPost();
                        if ($currentPost) {
                            $label = $currentPost->getRelatedProductsTitle();
                        }
                    }
                }
                $html .= '<div class="pin__type pin__type--area" data-gtm-position="' . $position . '" data-gtm-list="' . $this->escapeHtml($label) . '" data-gtm-product="' . $this->escapeHtml($this->gtmProduct->getGtmData($productBase)) . '" data-gtm-event="' . $this->escapeHtml(GTMProduct::PRODUCT_EVENT_CLICK_NAME_NO_REDIRECT) . '" style="width:' . $pin['width'] . 'px; height:' . $pin['height'] . 'px; background:#' . $background . '; color:#' . $color . '; -webkit-border-radius:' . $radius . 'px; -moz-border-radius:' . $radius . 'px; border-radius:' . $radius . 'px; line-height:' . $height . 'px; left:' . $leftPercent . '%; top:' . $topPercent . '%">';

                $html .= '<span class="pin-label" data-gtm-event="' . $this->escapeHtml(GTMProduct::PRODUCT_EVENT_CLICK_NAME_NO_REDIRECT) . '">' . $pin['label'] . '</span>';
                if (trim($pin['custom_text']) != '') {
                    $html .= $this->getCustomTextHtml($pin);
                } else {
                    $html .= $this->getProductInfoHtml($pin);
                }
                $html .= '</div>';
                $position++;
            }
            $html .= $this->getAddAllButtonHtml($arrProduct);
        }
        return $html;
    }

    /**
     * @param $arrProduct
     * @return string
     */
    protected function getAddAllButtonHtml($arrProduct)
    {
        $html = '';
        if ((count($arrProduct) > 0) && $this->_helper->getStoreConfig('lookbook/general/show_add_all_button')) {
            $i = 0;
            $relatedProductIds = [];
            foreach ($arrProduct as $productId => $_product) {
                $i++;
                if ($i == 1) {
                    $firstProduct = $_product;
                } else {
                    $relatedProductIds[] = $productId;
                }
            }
            $postParams = $this->getAddToCartPostParams($firstProduct);
            $html .= '<form data-role="tocart-form" action="' . $postParams['action'] . '" method="post">
							<input type="hidden" name="product" value="' . $postParams['data']['product'] . '">';
            if (count($relatedProductIds) > 0) {
                $relatedProduct = implode(',', $relatedProductIds);
                $html .= '<input type="hidden" name="related_product" value="' . $relatedProduct . '">';
            }

            $html .= '<input type="hidden" name="' . Action::PARAM_NAME_URL_ENCODED . '" value="' . $postParams['data'][Action::PARAM_NAME_URL_ENCODED] . '">' . $this->getBlockHtml('formkey') . '
							<button type="submit" class="action tocart primary addallbutton">
								<span>' . __('Add All To Cart') . '</span>
							</button>
						</form>';
        }

        return $html;
    }

    /**
     * @param $pin
     * @return string
     */
    protected function getCustomTextHtml($pin)
    {
        $productImageWidth=300;
       // $productImageWidth = $this->_helper->getStoreConfig('lookbook/general/popup_image_width');

        $html = $pinTitle = '';
        if (trim($pin['custom_label']) != '') {
            $pinTitle = $pin['custom_label'];
        } elseif ($product = $this->getProductInfo($pin['text'])) {
            $pinTitle = $product->getName();
        }
        if ($pinTitle != '') {
            $html .= '<div class="pin__title" data-gtm-event="' . $this->escapeHtml(GTMProduct::PRODUCT_EVENT_CLICK_NAME_NO_REDIRECT) . '">' . $pinTitle . '</div>';
        }
        $html .= '<div data-gtm-event="' . $this->escapeHtml(GTMProduct::PRODUCT_EVENT_CLICK_NAME_NO_REDIRECT) . '" class="pin__popup pin__popup--' . $pin['position'] . ' pin__popup--fade pin__popup_text_content" style="width:' . ($productImageWidth + 30) . 'px">';
        if ($pinTitle != '') {
            $html .= '<div class="popup__title">' . $pinTitle . '</div>';
        }
        $html .= '<div class="popup__content">' . $pin['custom_text'] . '</div></div>';

        return $html;
    }

    /**
     * @param $pin
     * @return string
     */
    protected function getProductInfoHtml($pin)
    {
        $html = '';
        if ($product = $this->getProductInfo($pin['text'])) {
            $productImageWidth = $this->_helper->getStoreConfig('lookbook/general/popup_image_width');
            $productImageHeight = $this->_helper->getStoreConfig('lookbook/general/popup_image_height');
            $popupWidth=450;
            $saleableClass = '';
            if (!$product->getTypeInstance()->hasOptions($product) && ($product->getTypeId() == 'simple')) {
                $arrProduct[$product->getId()] = $product;
                $saleableClass = 'addable';
            }
            $productBase = null;
            if (!empty($pin['text'])) {
                $productBase = $this->getProductInfo($pin['text']);
            }
            // Product Name - Tooltip
            $html .= '<div class="pin__title ' . $saleableClass . '" data-gtm-event="' . $this->escapeHtml(GTMProduct::PRODUCT_EVENT_CLICK_NAME_NO_REDIRECT) . '">' . $product->getName() . '</div>';
            $html .= '<div data-gtm-event="' . $this->escapeHtml(GTMProduct::PRODUCT_EVENT_CLICK_NAME_NO_REDIRECT) . '" class="pin__popup pin__popup--' . $pin['position'] . ' pin__popup--fade" style="width:' . (int)($popupWidth + 30) . 'px"><div class="popup__content popup__content--product">';
            // Product Image
            $productImageUrl = $this->_imagehelper->init(
                $product,
                'category_page_grid'
            )->resize($productImageWidth, $productImageHeight)->getUrl();
            $html .= '<img src="' . $productImageUrl . '" width="' . $productImageWidth . '" height="' . $productImageHeight . '" alt="" />';

            $html .= '<div class="popup-product-content-detail product-item">';
            // Product Name
            $html .= '<h3>' . $product->getName() . '</h3>';

            // Product Prices
            $html .= $this->getProductPrice($product);

            // Links
            $html .= '<div><a data-gtm-product="' . $this->escapeHtml($this->gtmProduct->getGtmData($productBase)) . '"
							    data-gtm-event="' . $this->escapeHtml(GTMProduct::PRODUCT_EVENT_SEE_DETAIL_INSPIRE_ME) . '"
							    href="' . $product->getProductUrl() . '">' . __('See Details') . '</a>';

            $postParams = $this->getAddToCartPostParams($product);

            $html .= '<form data-role="default-tocart-form" action="' . $postParams['action'] . '" method="post">
							<input type="hidden" name="product" value="' . $postParams['data']['product'] . '">
							<input type="hidden" name="' . Action::PARAM_NAME_URL_ENCODED . '" value="' . $postParams['data'][Action::PARAM_NAME_URL_ENCODED] . '">' . $this->getBlockHtml('formkey') . '
							<button type="submit" title="' . __('Add To Cart') . '" class="action tocart primary"
							    data-gtm-product="' . $this->escapeHtml($this->gtmProduct->getGtmData($productBase)) . '"
							    data-gtm-event="' . $this->escapeHtml(GTMProduct::PRODUCT_EVENT_ADD_TO_CART) . '">
								<span>' . __('Add To Cart') . '</span>
							</button>
						</form>';

            $html .= '</div></div></div></div>';
        }

        return $html;
    }
}
