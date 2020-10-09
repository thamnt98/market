<?php


namespace SM\Customer\Setup\Patch\Data;


use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\PatchInterface;

class UpdateLocationAttribute implements \Magento\Framework\Setup\Patch\DataPatchInterface
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

    public static function getDependencies()
    {
        return [];
    }

    public function getAliases()
    {
        return [];
    }

    public function apply()
    {

        $this->setup->startSetup();

        /** @var \Magento\Customer\Setup\CustomerSetup $customerSetup */
        $customerSetup = $this->customerSetupFactory->create(['setup' => $this->setup]);

        try {
            $cityAttr = $customerSetup->getEavConfig()->getAttribute(
                \Magento\Customer\Model\Customer::ENTITY,
                'city'
            );

            $districtAttr = $customerSetup->getEavConfig()->getAttribute(
                \Magento\Customer\Model\Customer::ENTITY,
                'district'
            );

            $cityAttr->setData('frontend_input', 'select');
            $cityAttr->setData('source_model', 'SM\Customer\Model\ResourceModel\Address\Attribute\Source\City');
            $cityAttr->save();
            $districtAttr->setData('frontend_input', 'select');
            $districtAttr->setData('source_model', 'SM\Customer\Model\ResourceModel\Address\Attribute\Source\District');
            $districtAttr->save();
        } catch (\Exception $e) {
        }

        $this->setup->endSetup();
    }
}
