<?php
/**
 * @category Trans
 * @package  Trans_Sprint
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Randy <muhammad.randy@ctcorpdigital.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */
namespace Trans\Sprint\Model\Payment;

use Trans\Sprint\Helper\Config;

/**
 * Class MegaDebit
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 */
class MegaDebit extends \Magento\Payment\Model\Method\AbstractMethod
{
    /**
     * {@inheritdoc}
     */
    protected $_code = 'sprint_mega_debit';

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

        if (!$this->_scopeConfig->getValue('payment/' . $this->_code . '/' . Config::PAYMENT_CHANNEL_ID, \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) {
            return false;
        }

        return parent::isAvailable($quote);
    }
}
