<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Customer
 *
 * Date: May, 07 2020
 * Time: 6:38 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Customer\Setup\Patch\Data;


class UpdateCustomerFormAttribute implements \Magento\Framework\Setup\Patch\DataPatchInterface
{
    /**
     * @var \Magento\Framework\Setup\ModuleDataSetupInterface|ModuleDataSetupInterface
     */
    protected $setup;
    /**
     * @var \Magento\Customer\Setup\CustomerSetupFactory
     */
    protected $customerSetupFactory;

    /**
     * AddSelectedCategoriesAttribute constructor.
     *
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $setup
     * @param \Magento\Customer\Setup\CustomerSetupFactory      $customerSetupFactory
     */
    public function __construct(
        \Magento\Framework\Setup\ModuleDataSetupInterface $setup,
        \Magento\Customer\Setup\CustomerSetupFactory $customerSetupFactory
    ) {
        $this->setup = $setup;
        $this->customerSetupFactory = $customerSetupFactory;
    }

    public function apply()
    {
        $this->setup->startSetup();

        /** @var \Magento\Customer\Setup\CustomerSetup $customerSetup */
        $customerSetup = $this->customerSetupFactory->create(['setup' => $this->setup]);

        try {
            $selectedCategoriesAttr = $customerSetup->getEavConfig()->getAttribute(
                \Magento\Customer\Model\Customer::ENTITY,
                'selected_categories'
            );

            $loyaltyAttr = $customerSetup->getEavConfig()->getAttribute(
                \Magento\Customer\Model\Customer::ENTITY,
                'loyalty'
            );

            $selectedCategoriesAttr->setData('used_in_forms', ['adminhtml_customer'])->save();
            $loyaltyAttr->setData('used_in_forms', ['adminhtml_customer'])->save();
        } catch (\Exception $e) {
        }

        $this->setup->endSetup();
    }

    /**
     * @return array|string[]
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @return array|string[]
     */
    public static function getDependencies()
    {
        return [];
    }
}
