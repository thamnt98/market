<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Promotion
 *
 * Date: June, 22 2020
 * Time: 4:14 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Promotion\Block\Catalog\Product\View;

class PromoLabel extends \Magento\Catalog\Block\Product\View
{
    /**
     * @var \Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory
     */
    protected $ruleCollFact;

    /**
     * @var \Magento\SalesRule\Model\Rule
     */
    protected $rule = null;

    /**
     * @var \SM\Promotion\Helper\Validation
     */
    protected $ruleValidateHelper;

    /**
     * PromoLabel constructor.
     *
     * @param \SM\Promotion\Helper\Validation                                                    $ruleValidateHelper
     * @param \Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory $ruleCollFact
     * @param \Magento\Catalog\Block\Product\Context                        $context
     * @param \Magento\Framework\Url\EncoderInterface                       $urlEncoder
     * @param \Magento\Framework\Json\EncoderInterface                      $jsonEncoder
     * @param \Magento\Framework\Stdlib\StringUtils                         $string
     * @param \Magento\Catalog\Helper\Product                               $productHelper
     * @param \Magento\Catalog\Model\ProductTypes\ConfigInterface           $productTypeConfig
     * @param \Magento\Framework\Locale\FormatInterface                     $localeFormat
     * @param \Magento\Customer\Model\Session                               $customerSession
     * @param \Magento\Catalog\Api\ProductRepositoryInterface               $productRepository
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface             $priceCurrency
     * @param array                                                         $data
     */
    public function __construct(
        \SM\Promotion\Helper\Validation $ruleValidateHelper,
        \Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory $ruleCollFact,
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Url\EncoderInterface $urlEncoder,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Catalog\Helper\Product $productHelper,
        \Magento\Catalog\Model\ProductTypes\ConfigInterface $productTypeConfig,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        array $data = []
    ) {
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
        $this->ruleCollFact = $ruleCollFact;
        $this->ruleValidateHelper = $ruleValidateHelper;
    }

    /**
     * @return \Magento\SalesRule\Model\Rule|null
     */
    public function getPromo()
    {
        if ($this->rule === null) {
            if ($product = $this->getProduct()) {
                $product->setData('product', $product);
                try {
                    $customerGroup = $this->customerSession->getCustomerGroupId();
                } catch (\Exception $e) {
                    $customerGroup = \Magento\Customer\Model\Group::NOT_LOGGED_IN_ID;
                }

                /** @var \Magento\SalesRule\Model\ResourceModel\Rule\Collection $coll */
                $coll = $this->ruleCollFact->create();
                $coll->addIsActiveFilter()
                    ->addCustomerGroupFilter($customerGroup)
                    ->addFieldToFilter('coupon_type', \Magento\SalesRule\Model\Rule::COUPON_TYPE_NO_COUPON)
                    ->addOrder(
                        \Magento\SalesRule\Model\Data\Rule::KEY_SORT_ORDER,
                        \Magento\SalesRule\Model\ResourceModel\Rule\Collection::SORT_ORDER_ASC
                    );;

                /** @var \Magento\SalesRule\Model\Rule $item */
                foreach ($coll as $item) {
                    if ($this->ruleValidateHelper->validateProduct($item, $product)) {
                        $this->rule = $item;
                        break;
                    }
                }
            }
        }

        return $this->rule;
    }

    /**
     * @return string
     */
    public function getPromoNotify()
    {
        $promo = $this->getPromo();
        if ($promo) {
            return $promo->getName();
        }

        return '';
    }

    /**
     * @return string
     */
    public function getPromoTooltip()
    {
        $promo = $this->getPromo();
        if ($promo) {
            return $promo->getData('how_to_use');
        }

        return '';
    }

    /**
     * @override
     * @return string
     */
    public function toHtml()
    {
        if (empty($this->getPromoNotify())) {
            return '';
        }

        return parent::toHtml();
    }
}
