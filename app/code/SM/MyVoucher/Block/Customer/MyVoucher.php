<?php

namespace SM\MyVoucher\Block\Customer;

class MyVoucher extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;
    /**
     * @var \SM\MyVoucher\Model\RuleRepository
     */
    protected $ruleRepository;

    /**
     * @var \SM\MyVoucher\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    private $date;

    /**
     * MyVoucher constructor.
     *
     * @param \SM\MyVoucher\Helper\Data                            $helper
     * @param \Magento\Framework\View\Element\Template\Context     $context
     * @param \SM\MyVoucher\Model\RuleRepository                   $ruleRepository
     * @param \Magento\Customer\Model\Session                      $customerSession
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $date
     * @param array                                                $data
     */
    public function __construct(
        \SM\MyVoucher\Helper\Data $helper,
        \Magento\Framework\View\Element\Template\Context $context,
        \SM\MyVoucher\Model\RuleRepository $ruleRepository,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $date,
        array $data = []
    ) {
        $this->customerSession = $customerSession;
        $this->ruleRepository = $ruleRepository;
        $this->date = $date;
        parent::__construct($context, $data);
        $this->helper = $helper;
    }

    /**
     * @return \SM\MyVoucher\Api\Data\RuleDataInterface[]
     */
    public function getAllVoucher()
    {
        try {
            return $this->ruleRepository->getVoucherByCustomer($this->customerSession->getCustomerId());
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * @param \SM\MyVoucher\Api\Data\RuleDataInterface $coupon
     * @return \Magento\Framework\Phrase|string
     */
    public function getTimeLeft($coupon)
    {
        return $this->helper->getTimeLeftTxt($coupon);
    }

    /**
     * @param \SM\MyVoucher\Api\Data\RuleDataInterface $voucher
     *
     * @return string
     */
    public function getVoucherImage($voucher)
    {
        return $this->helper->getVoucherImage($voucher);
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
     * @param \SM\MyVoucher\Api\Data\RuleDataInterface $voucher
     *
     * @return string
     */
    public function getCouponUrl($voucher)
    {
        return $this->getUrl(
            'myvoucher/voucher/detail',
            [
                'id' => $voucher->getId()
            ]
        );
    }
}
