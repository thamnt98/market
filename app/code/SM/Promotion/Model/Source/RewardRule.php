<?php


namespace SM\Promotion\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

class RewardRule implements OptionSourceInterface
{
    /**
     * @var \Magento\SalesRule\Model\ResourceModel\Coupon\CollectionFactory
     */
    private $collectionFactory;
    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * RewardRule constructor.
     * @param \Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory $collectionFactory
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory $collectionFactory,
        \Magento\Framework\Registry $registry
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->registry = $registry;
    }

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {

        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('coupon_type', 2)
            ->addFieldToFilter('use_auto_generation', 1);
//            ->addFieldToFilter('is_active', 1); // Reward coupon may not active at the moment we create this rule
        $rule = $this->registry->registry(\Magento\SalesRule\Model\RegistryConstants::CURRENT_SALES_RULE);
        if(!empty($rule)) {
            $collection->addFieldToFilter('rule_id', ['neq' => $rule->getId()]);
        }
        $result = [
            [
                'value' => 0,
                'label' => __('-- Please select reward rule --')
            ],
        ];

        /** @var  \Magento\SalesRule\Model\Rule $rule */
        foreach ($collection as $rule) {
            $result[] = [
                'value' => $rule->getId(),
                'label' => $rule->getName()
            ];
        }
        return $result;
    }
}