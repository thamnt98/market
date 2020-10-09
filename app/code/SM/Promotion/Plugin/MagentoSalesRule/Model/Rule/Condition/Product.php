<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Promotion
 *
 * Date: July, 04 2020
 * Time: 5:28 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Promotion\Plugin\MagentoSalesRule\Model\Rule\Condition;

class Product
{
    /**
     * @var \SM\Promotion\Helper\Validation
     */
    protected $validationHelper;

    /**
     * @var \Amasty\Rules\Model\Rule\Validator\ValidatorPool
     */
    protected $validatorPool;

    /**
     * Product constructor.
     *
     * @param \Amasty\Rules\Model\Rule\Validator\ValidatorPool $validatorPool
     * @param \SM\Promotion\Helper\Validation                  $validationHelper
     */
    public function __construct(
        \Amasty\Rules\Model\Rule\Validator\ValidatorPool  $validatorPool,
        \SM\Promotion\Helper\Validation $validationHelper
    ) {
        $this->validationHelper = $validationHelper;
        $this->validatorPool = $validatorPool;
    }

    /**
     * @param                                        $subject
     * @param \Closure                               $proceed
     * @param \Magento\Framework\Model\AbstractModel $model
     *
     * @return bool
     */
    public function aroundValidate(
        $subject,
        \Closure $proceed,
        \Magento\Framework\Model\AbstractModel $model
    ) {
        if ($model instanceof \Magento\Quote\Model\Quote\Address\Item) {
            $item = $model->getQuoteItem();
        } elseif (($model instanceof \Magento\Quote\Model\Quote\Item)) {
            $item = $model;
        }

        if (isset($item)) {
            if (!$this->validateActiveItem($item)) { // Item not active.
                return false;
            }

            if (!$this->validationHelper->validateSpecial($subject->getData('rule'), $item)) { // check special
                return false;
            }

            if ($subject instanceof \Magento\Rule\Model\Condition\Combine ||
                $subject instanceof \Magento\SalesRule\Model\Rule\Condition\Product\Combine
            ) { // Amasty logic
                $check = $this->validatorPool->validate($subject, $item);
                if ($check !== null) {
                    return $check;
                }
            }
        }

        if ($subject instanceof \Magento\SalesRule\Model\Rule\Condition\Product) {
            $product = $this->getProductToValidate($subject, $model);
            if ($model->getProduct() !== $product) {
                // We need to replace product only for validation and keep original product for all other cases.
                $clone = clone $model;
                $clone->setProduct($product);
                $model = $clone;
            }
        }

        return $proceed($model);
    }

    /**
     * @param \Magento\Quote\Model\Quote\Item $quoteItem
     *
     * @return bool
     */
    protected function validateActiveItem($quoteItem)
    {
        if (!empty($quoteItem)) {
            $quote = $quoteItem->getQuote();
            if (is_null($quoteItem->getData('is_active'))) {
                if (!$quote->isVirtual() && $quoteItem->getIsVirtual()) {
                    $quoteItem->setData('is_active', 0);
                } else {
                    $quoteItem->setData('is_active', 1);
                }
            }

            if (!(int)$quoteItem->getData('is_active')) {
                return false;
            }

            if ($quote->isVirtual()) {
                if (!$quoteItem->getIsVirtual()) {
                    return false;
                }
            } else {
                if ($quoteItem->getIsVirtual() && !$quoteItem->getParentItemId()) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Select proper product for validation.
     *
     * @param \Magento\SalesRule\Model\Rule\Condition\Product $subject
     * @param \Magento\Framework\Model\AbstractModel $model
     *
     * @return \Magento\Catalog\Model\Product
     */
    protected function getProductToValidate(
        \Magento\SalesRule\Model\Rule\Condition\Product $subject,
        \Magento\Framework\Model\AbstractModel $model
    ) {
        if ($model instanceof \Magento\Quote\Model\Quote\Item\AbstractItem) {
            /** @var \Magento\Catalog\Model\Product $product */
            $product = $model->getProduct();
            $attrCode = $subject->getAttribute();

            /* Check for attributes which are not available for configurable products */
            if (!$model->getParentItem() &&
                $product->getTypeId() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE &&
                !$product->hasData($attrCode)
            ) {
                /** @var \Magento\Catalog\Model\AbstractModel $childProduct */
                $childProduct = current($model->getChildren())->getProduct();
                if ($childProduct->hasData($attrCode)) {
                    $product = $childProduct;
                }
            }

            return $product;
        } else {
            return $model;
        }
    }
}
