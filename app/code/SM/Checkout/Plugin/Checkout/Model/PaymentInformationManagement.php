<?php
/**
 * @category    SM
 * @package     SM_Customer
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 * @author      Nam Nguyen <namnd2@smartosc.com>
 *
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\Checkout\Plugin\Checkout\Model;

/**
 * Class PaymentInformationManagement
 * @package SM\Checkout\Plugin\Checkout\Model
 */
class PaymentInformationManagement {
	/**
	 * @var bool
	 */
	protected $error = false;

	/**
	 * @var string
	 */
	protected $message = '';

	/**
	 * @var \Magento\Quote\Api\CartRepositoryInterface
	 */
	protected $cartRepository;

	/**
	 * @var \Magento\Checkout\Model\Session
	 */
	protected $checkoutSession;

	/**
	 * PaymentInformationManagement constructor.
	 * @param \Magento\Quote\Api\CartRepositoryInterface $cartRepository
	 * @param \Magento\Checkout\Model\Session $checkoutSession
	 */
	public function __construct(
		\Magento\Quote\Api\CartRepositoryInterface $cartRepository,
		\Magento\Checkout\Model\Session $checkoutSession
	) {
		$this->cartRepository  = $cartRepository;
		$this->checkoutSession = $checkoutSession;
	}

	/**
	 * @param \Magento\Checkout\Model\PaymentInformationManagement $subject
	 * @param int $cartId
	 * @param \Magento\Quote\Api\Data\PaymentInterface $paymentMethod
	 * @param \Magento\Quote\Api\Data\AddressInterface|null $billingAddress
	 * @return array
	 * @throws \Exception
	 */
	public function beforeSavePaymentInformationAndPlaceOrder(
		\Magento\Checkout\Model\PaymentInformationManagement $subject,
		$cartId,
		\Magento\Quote\Api\Data\PaymentInterface $paymentMethod,
		\Magento\Quote\Api\Data\AddressInterface $billingAddress = null
	) {
		$this->checkoutSession->setArea(\SM\Checkout\Helper\OrderReferenceNumber::AREA_WEB);
		$quote = $this->cartRepository->get($cartId);
		if (!$quote->getIsVirtual()) {
			$items = $this->handleQuoteAddress($quote);
			if (!$this->error) {
				foreach ($quote->getAllVisibleItems() as $item) {
					$itemId = $item->getId();
					$qty    = (int) $item->getQty();
					if (!isset($items[$itemId]) || $items[$itemId] != $qty) {
						$this->error   = true;
						$this->message = __('Cart has been updated.');
						break;
					}
				}
			}

			if ($this->error) {
				throw new \Exception($this->message);
			}
		}
		return [$cartId, $paymentMethod, $billingAddress];
	}

	/**
	 * @param $quote
	 * @return array
	 */
	protected function handleQuoteAddress($quote) {
		$items = [];
		foreach ($quote->getAllShippingAddresses() as $_address) {
			$shippingMethodList  = [];
			$_shippingRateGroups = $_address->getGroupedAllShippingRates();
			if ($_shippingRateGroups) {
				foreach ($_shippingRateGroups as $code => $_rates) {
					foreach ($_rates as $_rate) {
						$shippingMethodList[] = $_rate->getCode();
						if (!in_array($_rate->getCode(), $shippingMethodList)) {
							$shippingMethodList[] = $_rate->getCode();
						}
					}
				}
			}
			// if (!in_array($_address->getShippingMethod(), $shippingMethodList)) {
			//     $this->error = true;
			//     $this->message = __('OAR: Invalid Shipping Method.');
			//     break;
			// }
			$items = $items + $this->getItemsOfAddress($_address);
		}
		return $items;
	}

	/**
	 * @param $address
	 * @return array
	 */
	protected function getItemsOfAddress($address) {
		$items = [];
		foreach ($address->getAllVisibleItems() as $item) {
			if ($item instanceof \Magento\Quote\Model\Quote\Address\Item) {
				$items[$item->getQuoteItemId()] = (int) $item->getQty();
			} else {
				$items[$item->getId()] = (int) $item->getQty();
			}

		}
		return $items;
	}
}
