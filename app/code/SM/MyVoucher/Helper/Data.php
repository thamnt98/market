<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_MyVoucher
 *
 * Date: July, 03 2020
 * Time: 3:19 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\MyVoucher\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;

    /**
     * @var \Magento\Framework\View\Asset\Repository
     */
    protected $asset;

    /**
     * @var \Magento\SalesRule\Model\Utility
     */
    protected $utility;

    /**
     * @var \Magento\SalesRule\Model\RuleFactory
     */
    protected $ruleFactory;

    protected $coupon;

    /**
     * @var \SM\Promotion\Helper\Validation
     */
    protected $validationHelper;

    /**
     * Data constructor.
     *
     * @param \SM\Promotion\Helper\Validation                      $validationHelper
     * @param \Magento\SalesRule\Model\Utility                     $utility
     * @param \Magento\SalesRule\Model\RuleFactory                 $ruleFactory
     * @param \Magento\Framework\View\Asset\Repository             $asset
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param \Magento\Store\Model\StoreManagerInterface           $storeManager
     * @param \Magento\Framework\App\Helper\Context                $context
     */
    public function __construct(
        \SM\Promotion\Helper\Validation $validationHelper,
        \Magento\SalesRule\Model\Utility $utility,
        \Magento\SalesRule\Model\RuleFactory $ruleFactory,
        \Magento\Framework\View\Asset\Repository $asset,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Helper\Context $context
    ) {
        parent::__construct($context);
        $this->storeManager = $storeManager;
        $this->timezone = $timezone;
        $this->asset = $asset;
        $this->utility = $utility;
        $this->ruleFactory = $ruleFactory;
        $this->validationHelper = $validationHelper;
    }

    /**
     * @param \SM\MyVoucher\Api\Data\RuleDataInterface $voucher
     *
     * @return string
     */
    public function getVoucherImage($voucher)
    {
        try {
            if ($voucher->getImage()) {
                $mediaUrl = $this->storeManager->getStore()->getBaseUrl(
                    \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                );

                return $mediaUrl . '/' . $voucher->getImage();
            } else {
                return $this->asset->getUrl('images/svg-icons/Voucher.svg');
            }
        } catch (\Exception $e) {
            return $this->asset->getUrl('images/svg-icons/Voucher.svg');
        }
    }

    /**
     * @param \SM\MyVoucher\Api\Data\RuleDataInterface $voucher
     *
     * @return \Magento\Framework\Phrase|string
     */
    public function getTimeLeftTxt($voucher)
    {
        $toDate = $voucher->getToDate();
        if ($voucher->isExpired()) {
            return __('Expired');
        }

        if (empty($toDate)) {
            return '';
        }

        $now = $this->timezone->date();
        $toDate = $this->timezone->date(strtotime($toDate));
        $timeLeft = date_diff($now, $toDate);
        $result = '';
        if ($timeLeft->d > 0) {
            $result .= __('%1 days', $timeLeft->d) . ' ';
        }

        if ($timeLeft->h > 0) {
            $result .= __('%1 hours', $timeLeft->h);
        }

        return trim($result);
    }

    /**
     * @param \SM\MyVoucher\Api\Data\RuleDataInterface $voucher
     *
     * @return string
     */
    public function getToDateTxt($voucher)
    {
        if ($voucher->getToDate()) {
            try {
                $date = $this->timezone->date(strtotime($voucher->getToDate()));

                return __('Valid until %1 WIB', $date->format('d M Y H:i'));
            } catch (\Exception $e) {
                return '';
            }
        } else {
            return '';
        }
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @param int                        $ruleId
     *
     * @return bool
     */
    public function validateRule($quote, $ruleId)
    {
        try {
            /** @var \Magento\SalesRule\Model\Rule $rule */
            $rule = $this->ruleFactory->create()->load($ruleId);
            $addresses = $quote->getAllShippingAddresses();
            /** @var \Magento\Quote\Model\Quote\Item[] $items */
            $items = $quote->getAllItems();

            foreach ($addresses as $address) { // validate address
                if ($this->utility->canProcessRule($rule, $address)) {
                    if ($rule->getApplyToShipping()) {
                        return true;
                    }
                } else {
                    return false;
                }
            }

            if (!$this->validationHelper->validateProductSetByCart($rule, $quote) ||
                !$this->validationHelper->validateBuyXY($rule, $quote)
            ) {
                return false;
            }

            foreach ($items as $item) {
                if (is_null($item->getData('is_active'))) {
                    if (!$quote->isVirtual() && ($item->getIsVirtual() && !$item->getParentItemId())) {
                        $item->setData('is_active', 0);
                    } else {
                        $item->setData('is_active', 1);
                    }
                }

                if (!$item->getData('is_active') || empty($item->getRowTotal())) {
                    continue;
                }

                if ($rule->getActions()->validate($item) && $rule->getDiscountStep() <= $item->getQty()) {
                    return true;
                }
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
}
