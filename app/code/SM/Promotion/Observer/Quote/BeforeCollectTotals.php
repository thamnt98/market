<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Promotion
 *
 * Date: October, 07 2020
 * Time: 2:25 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Promotion\Observer\Quote;

class BeforeCollectTotals implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \SM\Promotion\Model\Rule\Validator\CustomerUses
     */
    protected $customerUses;

    /**
     * BeforeCollectTotals constructor.
     *
     * @param \SM\Promotion\Model\Rule\Validator\CustomerUses $customerUses
     */
    public function __construct(
        \SM\Promotion\Model\Rule\Validator\CustomerUses $customerUses
    ) {
        $this->customerUses = $customerUses;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        \SM\Promotion\Plugin\Model\Rule\Action\Discount\SetOf::$cache = [];
        $this->customerUses->reset();
    }
}
