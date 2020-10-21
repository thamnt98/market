<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Installation
 *
 * Date: April, 21 2020
 * Time: 10:24 AM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Installation\Block;

class Form extends \Magento\Framework\View\Element\Template
{
    const USED_FIELD_NAME = 'is_installation';
    const NOTE_FIELD_NAME = 'installation_note';
    const FEE_FIELD_NAME  = 'installation_fee';

    /**
     * @var \SM\Installation\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $product = null;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * Installation constructor.
     *
     * @param \Magento\Checkout\Model\Session                   $checkoutSession
     * @param \Magento\Framework\Registry                       $registry
     * @param \SM\Installation\Helper\Data                      $helper
     * @param \Magento\Catalog\Block\Product\Context            $context
     * @param \Magento\Catalog\Api\ProductRepositoryInterface   $productRepository
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param array                                             $data
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\Registry $registry,
        \SM\Installation\Helper\Data $helper,
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->helper = $helper;
        $this->productRepository = $productRepository;
        $this->priceCurrency = $priceCurrency;
        $this->registry = $registry;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @return bool
     */
    public function isEnable()
    {
        $product = $this->getProduct();

        return (bool)$product->getData(\SM\Installation\Helper\Data::PRODUCT_ATTRIBUTE) && $this->helper->isEnabled();
    }

    /**
     * @return \Magento\Catalog\Model\Product|null
     */
    public function getProduct()
    {
        if ($this->product === null) {
            if ($this->getData('product')) {
                $this->product = $this->getData('product');
            } elseif ($this->registry->registry('product')) {
                $this->product = $this->registry->registry('product');
            }
        }

        return $this->product;
    }

    /**
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     *
     * @return $this
     */
    public function setProduct($product)
    {
        try {
            $this->product = $this->productRepository->getById($product->getId());
        } catch (\Exception $e) {
        }

        return $this;
    }

    /**
     * @return bool|false|\Magento\Quote\Model\Quote\Item|null
     */
    public function getItem()
    {
        try {
            if ($this->getData('item_id')) {
                return $this->checkoutSession->getQuote()->getItemById($this->getData('item_id'));
            } elseif ($product = $this->getProduct()) {
                return $this->checkoutSession->getQuote()->getItemByProduct($product);
            }
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @return array
     */
    public function getDefaultData()
    {
        $result = [
            'defaultNote'    => '',
            'defaultChecked' => 'false'
        ];
        $item = $this->getItem();
        if ($item) {
            try {
                $option = $item->getOptionByCode('info_buyRequest');

                if ($option) {
                    $value = \Zend_Json_Decoder::decode($option->getValue());
                    $value = $value[\SM\Installation\Helper\Data::QUOTE_OPTION_KEY] ?? [];
                    $result['defaultChecked'] = ($value[self::USED_FIELD_NAME] ? 'true' : 'false') ?? 'false';
                    $result['defaultNote'] = $value[self::NOTE_FIELD_NAME] ?? '';
                    $result['fee'] = $this->getInstallationFee();
                }
            } catch (\Exception $e) {
            }
        }

        return $result;
    }

    /**
     * Get Title.
     *
     * @return \Magento\Framework\Phrase|string
     */
    public function getTitle()
    {
        if ($this->getData('item_id')) {
            return '';
        }

        return __('Installation');
    }

    /**
     * Get tooltip.
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTooltip()
    {
        return __($this->helper->getTooltip());
    }

    /**
     * @return string
     */
    public function hasRemovePopup()
    {
        if ($this->getData('hasRemovePopup') !== null) {
            return "{$this->getData('hasRemovePopup')}";
        }

        return 'true';
    }

    /**
     * @return float
     */
    public function getInstallationFee()
    {
        // todo
        return 0;
    }

    /**
     * @override
     *
     * @return string
     */
    public function toHtml()
    {
        if ($this->isEnable()) {
            return parent::toHtml();
        }

        return '';
    }
}
