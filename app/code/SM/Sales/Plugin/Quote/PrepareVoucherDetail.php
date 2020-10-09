<?php

namespace SM\Sales\Plugin\Quote;

use SM\MyVoucher\Model\AmastyRules\DiscountRegistry;

/**
 * Class PrepareCouponDetail
 * @package SM\Sales\Plugin\Quote
 */
class PrepareVoucherDetail
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $checkoutSession;
    /**
     * @var \Magento\Quote\Model\ResourceModel\Quote
     */
    private $resourceModel;

    /**
     * PrepareVoucherDetail constructor.
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Quote\Model\ResourceModel\Quote $resourceModel
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Quote\Model\ResourceModel\Quote $resourceModel
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->resourceModel = $resourceModel;
    }

    /**
     * @param DiscountRegistry $subject
     * @param $result
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function afterGetRulesWithAmount(
        DiscountRegistry $subject,
        $result
    ) {
        $array = [];

        if ($result) {
            /** @var \SM\Promotion\Api\Data\DiscountBreakdownInterface $item */
            foreach ($result as $item) {
                $array[] = [
                    \SM\Promotion\Api\Data\DiscountBreakdownInterface::KEY_RULE_ID     => $item->getId(),
                    \SM\Promotion\Api\Data\DiscountBreakdownInterface::KEY_COUPON_CODE => $item->getCode(),
                    \SM\Promotion\Api\Data\DiscountBreakdownInterface::RULE_NAME       => $item->getRuleName(),
                    \SM\Promotion\Api\Data\DiscountBreakdownInterface::RULE_AMOUNT     => $item->getRuleAmount(),
                ];
            }
        }

        $detail = json_encode($array);
        $quote = $this->checkoutSession->getQuote();
        $table = $this->resourceModel->getMainTable();
        $this->resourceModel->getConnection()->update(
            $table,
            ['voucher_detail' => $detail],
            ['entity_id = ?' => $quote->getId()]
        );

        return $result;
    }
}
