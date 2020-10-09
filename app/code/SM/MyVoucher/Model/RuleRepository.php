<?php
/**
 * @category SM
 * @package  SM_MyVoucher
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Hung Pham <hungpv@smartosc.com>
 *
 * Copyright © 2020 SmartOSC. All rights reserved.
 * http://www.smartosc.com
 */

namespace SM\MyVoucher\Model;

use SM\MyVoucher\Api\Data\RuleDataInterface;
use SM\MyVoucher\Api\RuleRepositoryInterface;
use Zend_Db_Select;
use Magento\Framework\App\State;
use Magento\Framework\App\Area;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class RuleRepository implements RuleRepositoryInterface
{
    /**
     * @var \Magento\SalesRule\Model\ResourceModel\Coupon\CollectionFactory
     */
    protected $couponCollectionFactory;

    /**
     * @var \SM\MyVoucher\Model\Data\RuleData
     */
    protected $ruleDataFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $date;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \SM\Promotion\Helper\Validation
     */
    protected $ruleValidationHelper;

    /**
     * @var \Magento\SalesRule\Model\RuleFactory
     */
    protected $ruleFactory;

    /**
     * @var State
     */
    protected $state;
    /**
     * @var \Magento\Webapi\Model\Authorization\TokenUserContext
     */
    protected $tokenUserContext;

    /**
     * @var \Magento\Framework\View\Asset\Repository
     */
    protected $asset;

    /**
     * Quote repository.
     *
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \SM\MyVoucher\Helper\Data
     */
    protected $helper;

    /**
     * RuleRepository constructor.
     *
     * @param \SM\Promotion\Helper\Validation                                 $ruleValidationHelper
     * @param \Magento\SalesRule\Model\RuleFactory                            $ruleFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface              $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface                      $storeManager
     * @param \Magento\Customer\Api\CustomerRepositoryInterface               $customerRepository
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface            $date
     * @param \Magento\SalesRule\Model\ResourceModel\Coupon\CollectionFactory $salesRuleCoupon
     * @param \SM\MyVoucher\Model\Data\RuleDataFactory                        $ruleDataFactory
     * @param \SM\MyVoucher\Helper\Data                                       $helper
     */
    public function __construct(
        \SM\Promotion\Helper\Validation $ruleValidationHelper,
        \Magento\SalesRule\Model\RuleFactory $ruleFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $date,
        \Magento\SalesRule\Model\ResourceModel\Coupon\CollectionFactory $salesRuleCoupon,
        \SM\MyVoucher\Model\Data\RuleDataFactory $ruleDataFactory,
        State $state,
        \Magento\Webapi\Model\Authorization\TokenUserContext $tokenUserContext,
        \Magento\Framework\View\Asset\Repository $asset,
        \SM\MyVoucher\Helper\Data $helper,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
    ) {
        $this->couponCollectionFactory = $salesRuleCoupon;
        $this->ruleDataFactory = $ruleDataFactory;
        $this->storeManager = $storeManager;
        $this->customerRepository = $customerRepository;
        $this->date = $date;
        $this->scopeConfig = $scopeConfig;
        $this->ruleValidationHelper = $ruleValidationHelper;
        $this->ruleFactory = $ruleFactory;
        $this->state              = $state;
        $this->tokenUserContext   = $tokenUserContext;
        $this->asset = $asset;
        $this->helper = $helper;
        $this->quoteRepository = $quoteRepository;
        $this->messageManager = $messageManager;
    }

    /**
     * @param int $customerId
     * @param string $query
     * @return RuleDataInterface[]
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getVoucherByCustomer($customerId,$query = '')
    {
        /** @var \Magento\SalesRule\Model\ResourceModel\Coupon\Collection $couponCollection */

        $customer = $this->customerRepository->getById($customerId);
        $couponCollection = $this->getVoucherCollection($customer);

        $result = [];
        /** @var \Magento\SalesRule\Model\Coupon $coupon */
        foreach ($couponCollection as $coupon) {
            $rule = $this->ruleFactory->create()->load($coupon->getRuleId());
            if($this->isLoggedInByAPI()){
                $isSearch = $this->isSearch($rule, $query);
                if ($isSearch == true && $this->ruleValidationHelper->validateApiCustomer($rule,$customerId)) {
                    $ruleData = $this->prepareRuleData($coupon);
                    $result[] = $ruleData;
                }
            }else {
                if ($this->ruleValidationHelper->validateCustomer($rule)) {
                    $ruleData = $this->prepareRuleData($coupon);
                    $result[] = $ruleData;
                }
            }
        }

        return $result;
    }

    /**
     * Check if rule is search by name or description
     * @param $rule
     * @param $query
     * @return bool
     */
    public function isSearch($rule,$query){
        if ($query != '') {
            if (strpos(strtolower($rule->getName()), strtolower($query)) !== false ||
                strpos(strtolower($rule->getDiscountText()), strtolower($query)) !== false) {
                return true;
            }
            return false;
        }else{
            return true;
        }
    }

    /**
     * @param int $customerId
     * @param int $voucherId
     * @return RuleDataInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getVoucherDetailByCustomer($customerId, $voucherId)
    {
        $customer = $this->customerRepository->getById($customerId);
        $couponCollection = $this->getVoucherCollection($customer);
        $couponCollection->getSelect()->where('main_table.rule_id = ?', $voucherId);
        $coupon = $couponCollection->setPageSize(1)->getLastItem();

        return $this->prepareRuleData($coupon);
    }

    /**
     * Apply coupon code
     * @param int $cartId
     * @param string $couponCode
     * @return bool
     * @throws \Exception
     */
    public function applyVoucher($cartId, $couponCode)
    {
        /** @var  \Magento\Quote\Model\Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);

        if (!$quote->getItemsCount()) {
            if ($quote->getApplyVoucher() && $quote->getApplyVoucher() != '') {
                $oldApplyCoupon = explode(',', $quote->getApplyVoucher());
            } else {
                $oldApplyCoupon = [];
            }
            if (!in_array($couponCode, $oldApplyCoupon)) {
                $newApplyCoupon = $oldApplyCoupon;
                $newApplyCoupon[] = $couponCode;
                $newApplyCoupon = implode(',', $newApplyCoupon);
                $quote->setApplyVoucher($newApplyCoupon);
                $this->quoteRepository->save($quote);
            }
            throw new \Magento\Framework\Webapi\Exception(__('The "%1" Cart doesn\'t contain products.', $cartId),105);
        }
        if (!$quote->getStoreId()) {
            throw new \Magento\Framework\Webapi\Exception(__('Cart isn\'t assigned to correct store'),106);
        }
        $quote->getShippingAddress()->setCollectShippingRates(true);

        try {
            $quoteSave = false;

            if ($quote->getApplyVoucher() && $quote->getApplyVoucher() != '') {
                $oldApplyCoupon = explode(',', $quote->getApplyVoucher());
            } else {
                $oldApplyCoupon = [];
            }
            if (!in_array($couponCode, $oldApplyCoupon)) {
                $newApplyCoupon = $oldApplyCoupon;
                $newApplyCoupon[] = $couponCode;
                $newApplyCoupon = implode(',', $newApplyCoupon);
                $quote->setApplyVoucher($newApplyCoupon);
                $quoteSave = true;
            }
            $oldCoupon = explode(',', $quote->getCouponCode());
            if (!in_array($couponCode, $oldCoupon)) {
                $newCoupon = $oldCoupon;
                $newCoupon[] = $couponCode;
                $newCoupon = implode(',', $newCoupon);
                if ($quote->getCouponCode() == null) {
                    $quote->setCouponCode($couponCode);
                } else {
                    $quote->setCouponCode($newCoupon);
                }
                $quote->collectTotals();
                $quoteSave = true;
            }
            if ($quoteSave) {
                $this->quoteRepository->save($quote);
            }
        } catch (LocalizedException $e) {
            throw new \Magento\Framework\Webapi\Exception(__('The coupon code couldn\'t be applied: ' . $e->getMessage()),100);
        } catch (\Exception $e) {
            throw new \Magento\Framework\Webapi\Exception(
                __("The coupon code couldn't be applied. Verify the coupon code and try again."),101);
        }
        $couponAfterCollect = explode(',', $quote->getCouponCode());
        if (!in_array($couponCode, $couponAfterCollect)) {
            throw new \Magento\Framework\Webapi\Exception(__("The coupon code isn't valid. Verify the code and try again."),102);
        }
        return true;
    }


    /**
     * @param int $customerId
     * @return int
     */
    public function getCountVoucher($customerId){
        $voucher = $this->getVoucherByCustomer($customerId);
        return count($voucher);
    }

    /**
     * @param \Magento\SalesRule\Model\Coupon $coupon
     * @return RuleDataInterface
     */
    public function prepareRuleData($coupon)
    {
        /** @var RuleDataInterface $ruleData */
        $ruleData = $this->ruleDataFactory->create();
        $ruleData->setId($coupon->getRuleId());
        $ruleData->setArea($coupon->getArea());
        $ruleData->setName($coupon->getName());
        $ruleData->setDescription($coupon->getDescription());
        $ruleData->setDiscountAmount($coupon->getDiscountAmount());
        $ruleData->setHowToUse($coupon->getHowToUse());
        $ruleData->setTermCondition($coupon->getTermCondition());
        $ruleData->setCode($coupon->getCode());
        $ruleData->setFromDate($coupon->getFromDate());
        // Staging content will update expire date of coupon.
        // @see \Magento\SalesRuleStaging\Model\Coupon\ExpirationDateResolver::execute
        $ruleData->setToDate($coupon->getData('to_date'));
        $ruleData->setDiscountText($coupon->getData('discount_text'));
        $ruleData->setRedirectUrl($coupon->getData('front_redirect_url'));
        $ruleData->setDiscountNote($coupon->getData('discount_note'));
        $ruleData->setIsExpired($coupon->getData('expired'));
        $ruleData->setExpireDate($this->helper->getToDateTxt($coupon));
        $ruleData->setMobileArea($coupon->getData('mobile_redirect_area'));
        $ruleData->setMobileRedirect($coupon->getData('mobile_redirect'));

        // Image path
        if (!empty($coupon->getData("voucher_image"))) {
            $image = 'sm/tmp/icon/' . $coupon->getData("voucher_image");
            $ruleData->setImage($image);
            $mediaUrl = $this->storeManager->getStore()->getBaseUrl(
                \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
            );
            $ruleData->setMobileImage($mediaUrl.$image);
        }

        // TODO: Mapping discount type
        $ruleData->setDiscountType($coupon->getSimpleAction());

        $ruleData->setUseLeft($this->getCouponUseLeft($coupon));
        $ruleData->setAvailable(
            $ruleData->isExpired() == 0 &&
            $ruleData->getUseLeft() != 0 &&
            (empty($ruleData->getToDate()) || strtotime($ruleData->getToDate()) > time())
        );

        return $ruleData;
    }

    /**
     * @param \Magento\SalesRule\Model\Coupon $coupon
     * @return int
     */
    public function getCouponUseLeft($coupon)
    {
        $limit = (int)$coupon->getUsageLimit();
        $perCustomer = (int)$coupon->getUsagePerCustomer();
        $customerUsed = (int)$coupon->getData('customer_times_used');

        if ($limit === 0 && $perCustomer === 0) {
            return -1;
        } elseif ($limit != 0 && $perCustomer != 0) {
            $usageCounter = min($limit - $coupon->getTimesUsed(), $perCustomer - $customerUsed);
        } elseif ($limit == 0) {
            $usageCounter = $perCustomer - $customerUsed;
        } else {
            $usageCounter = $limit - $coupon->getTimesUsed();
        }

        return max(0, $usageCounter);
    }

    /**
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @return \Magento\SalesRule\Model\ResourceModel\Coupon\Collection
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getVoucherCollection($customer)
    {
        $expiredDisplayTime = $this->getTimeToDisplayExpired();
        /** @var \Magento\SalesRule\Model\ResourceModel\Coupon\Collection $couponCollection */
        $couponCollection = $this->couponCollectionFactory->create();
        $select = $couponCollection->getSelect();

        $select->joinLeft(
            ['sales_rule' => $couponCollection->getTable('salesrule')],
            'main_table.rule_id = sales_rule.rule_id'
        );

        $select->joinRight(
            ['sales_rule_website' => $couponCollection->getTable('salesrule_website')],
            "sales_rule.row_id = sales_rule_website.row_id
            AND sales_rule_website.website_id = {$this->storeManager->getStore()->getWebsiteId()}",
            ['website_id']
        );

        $select->joinRight(
            ['rule_customer_group' => $couponCollection->getTable('salesrule_customer_group')],
            "sales_rule.row_id = rule_customer_group.row_id
            AND rule_customer_group.customer_group_id = {$customer->getGroupId()}",
            ['customer_group_id']
        );

        $select->joinLeft(
            ['sales_rule_customer' => $couponCollection->getTable('salesrule_customer')],
            "main_table.rule_id = sales_rule_customer.rule_id
            AND sales_rule_customer.customer_id = {$customer->getId()}",
            []
        );

        $select->joinLeft(
            ['sales_rule_customer_all' => $couponCollection->getTable('salesrule_customer')],
            "main_table.rule_id = sales_rule_customer_all.rule_id",
            []
        );

        $select->joinLeft(
            ['am_rule_counter' => $couponCollection->getTable('amasty_amrules_usage_counter')],
            "main_table.rule_id = am_rule_counter.salesrule_id",
            ['counter']
        );

        $select->joinLeft(
            ['am_rule_limit' => $couponCollection->getTable('amasty_amrules_usage_limit')],
            "main_table.rule_id = am_rule_limit.salesrule_id",
            ['counter']
        );

        $select->where('type = 0 OR main_table.customer_id = ?', $customer->getId());
        $select->where(
            'sales_rule.is_active = 1 OR (' .
                'sales_rule.is_active = 0 AND sales_rule.created_in != 1 AND sales_rule.to_date IS NULL AND ' .
                'DATE_ADD(sales_rule.from_date, INTERVAL ' . $expiredDisplayTime . ' day) > NOW()' .
            ')'
        );

        $select->group('main_table.rule_id');

        $select->reset(Zend_Db_Select::COLUMNS);
        $select->columns([
            'sales_rule.row_id',
            'main_table.rule_id',
            'sales_rule.name',
            'sales_rule.description',
            'sales_rule.discount_amount',
            'sales_rule.simple_action',
            'sales_rule.how_to_use',
            'sales_rule.term_condition',
            'sales_rule.is_active',
            'main_table.usage_limit',
            'SUM(sales_rule_customer_all.times_used) as times_used',
            'main_table.usage_per_customer',
            'sales_rule_customer.times_used as customer_times_used',
            'main_table.code',
            'am_rule_limit.limit',
            'am_rule_counter.count',
            'sales_rule.from_date',
            'sales_rule.to_date',
            'main_table.expiration_date',
            'sales_rule.discount_text',
            'sales_rule.discount_note',
            'sales_rule.area',
            'sales_rule.voucher_image',
            'sales_rule.front_redirect_url',
            'sales_rule.mobile_redirect',
            'sales_rule.mobile_redirect_area',
            'IF(' .
                'sales_rule.is_active = 0 AND sales_rule.created_in != 1 AND sales_rule.to_date IS NULL AND ' .
                'DATE_ADD(sales_rule.from_date, INTERVAL ' . $expiredDisplayTime . ' day) > NOW(), 1, 0' .
            ') AS expired',
        ]);

        return $couponCollection;
    }

    protected function getTimeToDisplayExpired()
    {
        return (int) $this->scopeConfig->getValue(
            'sm_sales_rule/display/expired_day_number'
        );
    }

    public function isLoggedInByAPI()
    {
        if ($this->state->getAreaCode() == Area::AREA_WEBAPI_REST && (bool)$this->tokenUserContext->getUserId()) {
            return true;
        }
    }

}
