<?php
/**
 * @category Trans
 * @package  Trans_Sprint
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Rifai <muhammad.rifai@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */
namespace Trans\Sprint\Model\Payment;

use Trans\Sprint\Helper\Config;

/**
 * Class BcaCc
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 */
class BcaCc extends \Magento\Payment\Model\Method\AbstractMethod {
	/**
	 * {@inheritdoc}
	 */
	protected $_code = 'sprint_bca_cc';

	/**
	 * {@inheritdoc}
	 */
	protected $_isInitializeNeeded = true;

	/**
	 * @param  \Magento\Quote\Api\Data\CartInterface|array $quote
	 * @return boolean
	 */
	public function isAvailable(
		\Magento\Quote\Api\Data\CartInterface $quote = null
	) {
		if (!$this->_scopeConfig->getValue(Config::ENABLE_MODULE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) {
			return false;
		}

		if (!$this->_scopeConfig->getValue('payment/' . $this->_code . '/' . Config::INSTALLMENT_TERM, \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) {
			return false;
		}

		return parent::isAvailable($quote);
	}
}
