<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Checkout
 *
 * Date: September, 01 2020
 * Time: 5:16 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Checkout\Plugin\MagentoCheckout\Controller;

class Cart
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $cartRepository;

    /**
     * Cart constructor.
     *
     * @param \Magento\Checkout\Model\Session            $checkoutSession
     * @param \Magento\Quote\Api\CartRepositoryInterface $cartRepository
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Quote\Api\CartRepositoryInterface $cartRepository
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->cartRepository = $cartRepository;
    }

    public function beforeExecute(\Magento\Checkout\Controller\Cart\Index $subject)
    {
        try {
            $quote = $this->checkoutSession->getQuote();

            $quote->getPayment()->setMethod(null);
            $quote->getShippingAddress()->setShippingMethod(null);

            if ($quote->getIsMultiShipping()) {
                $quote->setIsMultiShipping(0);
                $extensionAttributes = $quote->getExtensionAttributes();
                if ($extensionAttributes && $extensionAttributes->getShippingAssignments()) {
                    $extensionAttributes->setShippingAssignments([]);
                }
            }

            $this->cartRepository->save($quote);
        } catch (\Exception $e) {
        }
    }
}
