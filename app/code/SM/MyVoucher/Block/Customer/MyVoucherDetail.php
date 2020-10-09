<?php

namespace SM\MyVoucher\Block\Customer;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class MyVoucherDetail extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \SM\MyVoucher\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * MyVoucherDetail constructor.
     *
     * @param \Magento\Checkout\Model\Session                  $checkoutSession
     * @param \SM\MyVoucher\Helper\Data                        $helper
     * @param \Magento\Framework\Registry                      $registry
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array                                            $data
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \SM\MyVoucher\Helper\Data $helper,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->registry = $registry;
        $this->helper = $helper;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @return \SM\MyVoucher\Api\Data\RuleDataInterface|null
     */
    public function getVoucher()
    {
        return $this->registry->registry('voucher');
    }

    /**
     * @override
     *
     * @return string
     */
    public function toHtml()
    {
        if ($this->getVoucher() && $this->getVoucher()->getId()) {
            return parent::toHtml();
        } else {
            return '';
        }
    }

    /**
     * @param \SM\MyVoucher\Api\Data\RuleDataInterface $voucher
     *
     * @return string
     */
    public function getToDate($voucher)
    {
        return $this->helper->getToDateTxt($voucher);
    }

    /**
     * @return \Magento\Quote\Api\Data\CartInterface|\Magento\Quote\Model\Quote|null
     */
    public function getQuote()
    {
        try {
            return $this->checkoutSession->getQuote();
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @return int
     */
    public function getQuoteId()
    {
        return $this->checkoutSession->getQuoteId();
    }

    /**
     * @return bool
     */
    public function isApplied()
    {
        $quote = $this->getQuote();
        $voucher = $this->getVoucher();
        if ($quote && $voucher) {
            $ids = explode(',', $quote->getAppliedRuleIds());
            if (in_array($voucher->getId(), $ids)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return string
     */
    public function getImageUrl()
    {
        return $this->helper->getVoucherImage($this->getVoucher());
    }
}
