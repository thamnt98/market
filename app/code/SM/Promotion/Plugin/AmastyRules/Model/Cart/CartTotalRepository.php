<?php
/**
 * Class CartTotalRepository
 * @package SM\Promotion\Plugin\AmastyRules\Model\Cart
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */


namespace SM\Promotion\Plugin\AmastyRules\Model\Cart;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use SM\Promotion\Api\Data\RuleSuggestionInterfaceFactory;
use SM\Promotion\Helper\RuleSuggestion;

class CartTotalRepository
{
    /**
     * Quote repository.
     *
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var RuleSuggestion
     */
    private $ruleSuggestionHelper;

    /**
     * @var RuleSuggestionInterfaceFactory
     */
    private $ruleSuggestionFactory;

    /**
     * CartTotalRepository constructor.
     * @param CartRepositoryInterface $quoteRepository
     * @param RuleSuggestion $ruleSuggestionHelper
     * @param RuleSuggestionInterfaceFactory $ruleSuggestionFactory
     */
    public function __construct(
        CartRepositoryInterface $quoteRepository,
        RuleSuggestion $ruleSuggestionHelper,
        RuleSuggestionInterfaceFactory $ruleSuggestionFactory
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->ruleSuggestionHelper = $ruleSuggestionHelper;
        $this->ruleSuggestionFactory = $ruleSuggestionFactory;
    }

    /** APO-4839
     *
     * @param \Magento\Quote\Api\CartTotalRepositoryInterface $subject
     * @param $cartId
     */
    public function beforeGet(\Magento\Quote\Api\CartTotalRepositoryInterface $subject, $cartId)
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        try {
            $quote = $this->quoteRepository->getActive($cartId);
            $quote->setTotalsCollectedFlag(false);
            $quote->collectTotals();
        } catch (NoSuchEntityException $e) {
        }
    }

    /**
     * @param \Magento\Quote\Api\CartTotalRepositoryInterface $subject
     * @param \Magento\Quote\Api\Data\TotalsInterface $result
     * @param $cartId
     * @return \Magento\Quote\Api\Data\TotalsInterface
     * @throws NoSuchEntityException
     */
    public function afterGet(
        \Magento\Quote\Api\CartTotalRepositoryInterface $subject,
        \Magento\Quote\Api\Data\TotalsInterface $result,
        $cartId
    ) {
        $quote = $this->quoteRepository->getActive($cartId);
        $appliedRuleIds = $quote->getAppliedRuleIds();
        $customerGroupId = $quote->getData('customer_group_id');
        $customerId = $quote->getData('customer_id');
        $ruleSuggestion = $this->ruleSuggestionHelper->getRule($appliedRuleIds, $customerGroupId, $customerId);
        $extensionAttributes = $result->getExtensionAttributes();

        if ($ruleSuggestion) {
            $ruleSuggestionData = $this->ruleSuggestionFactory->create();
            $ruleSuggestionData->setData($ruleSuggestion->getData());
            /** @var \Magento\Quote\Api\Data\TotalsExtensionInterface $extensionAttributes */
            $extensionAttributes->setAmruleSuggestion($ruleSuggestionData);
        }

        return $result;
    }
}
