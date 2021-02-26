<?php

/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Sales
 *
 * Date: February, 23 2021
 * Time: 2:32 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Sales\Observer;

class QuoteCollectTotalAfter implements \Magento\Framework\Event\ObserverInterface
{
    const VOUCHER_DETAIL_CODE = 'voucher_detail';

    /**
     * @var \SM\MyVoucher\Model\AmastyRules\DiscountRegistry
     */
    protected $discountRegistry;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $serializer;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $connection;

    /**
     * QuoteCollectTotalAfter constructor.
     *
     * @param \Magento\Framework\App\ResourceConnection    $resourceConnection
     * @param \Amasty\Rules\Model\DiscountRegistry         $discountRegistry
     * @param \Magento\Framework\Serialize\Serializer\Json $serializer
     */
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Amasty\Rules\Model\DiscountRegistry $discountRegistry,
        \Magento\Framework\Serialize\Serializer\Json $serializer
    ) {
        $this->discountRegistry = $discountRegistry;
        $this->serializer       = $serializer;
        $this->connection       = $resourceConnection->getConnection();
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $rules = $this->discountRegistry->getRulesWithAmount();
        if (count($rules) < 1) {
            return;
        }

        $data = [];
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote     = $observer->getEvent()->getData('quote');
        $oldDetail = $quote->getData(self::VOUCHER_DETAIL_CODE);

        foreach ($rules as $rule) {
            $data[] = [
                \SM\Promotion\Api\Data\DiscountBreakdownInterface::KEY_RULE_ID     => $rule->getId(),
                \SM\Promotion\Api\Data\DiscountBreakdownInterface::KEY_COUPON_CODE => $rule->getCode(),
                \SM\Promotion\Api\Data\DiscountBreakdownInterface::RULE_NAME       => $rule->getRuleName(),
                \SM\Promotion\Api\Data\DiscountBreakdownInterface::RULE_AMOUNT     => $rule->getRuleAmount(),
            ];
        }

        $quote->setData(self::VOUCHER_DETAIL_CODE, $this->serializer->serialize($data));
        if ($quote->getEntityId() && $oldDetail !== $quote->getData(self::VOUCHER_DETAIL_CODE)) {
            $this->connection
                ->update(
                    'quote',
                    [self::VOUCHER_DETAIL_CODE => $quote->getData(self::VOUCHER_DETAIL_CODE)],
                    "entity_id = {$quote->getEntityId()}"
                );
        }
    }
}
