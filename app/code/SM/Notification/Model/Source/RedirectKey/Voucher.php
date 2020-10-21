<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Notification
 *
 * Date: October, 15 2020
 * Time: 3:16 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Notification\Model\Source\RedirectKey;

class Voucher implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var \Magento\SalesRule\Model\ResourceModel\Coupon\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var null|array
     */
    protected $options = null;

    /**
     * Order constructor.
     *
     * @param \Magento\SalesRule\Model\ResourceModel\Coupon\CollectionFactory $collectionFactory
     */
    public function __construct(
        \Magento\SalesRule\Model\ResourceModel\Coupon\CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    public function toOptionArray()
    {
        if (is_null($this->options)) {
            $this->options = [];
            /** @var \Magento\SalesRule\Model\ResourceModel\Coupon\Collection $coll */
            $coll = $this->collectionFactory->create();
            $coll->getSelect()
                ->joinInner(
                    ['rule' => 'salesrule'],
                    'rule.rule_id = main_table.rule_id',
                    ['name']
                )->where('rule.is_active = ?', 1);

            /** @var \Magento\SalesRule\Model\Coupon $coup */
            foreach ($coll->getItems() as $coup) {
                $this->options[] = [
                    'label' => $coup->getData('name'),
                    'value' => $coup->getRuleId(),
                ];
            }
        }

        return $this->options;
    }
}
