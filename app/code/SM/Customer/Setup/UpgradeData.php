<?php

namespace SM\Customer\Setup;

use Magento\Customer\Model\Customer;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class UpgradeData implements \Magento\Framework\Setup\UpgradeDataInterface
{
    /**
     * @var \Magento\Eav\Setup\EavSetupFactory
     */
    protected $eavSetupFactory;

    /**
     * UpgradeData constructor.
     * @param \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Validate_Exception
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        if ($context->getVersion() == '' || version_compare($context->getVersion(), '1.0.2') < 0) {
            $data= ['frontend_label' => "Is Verified Email"];
            $eavSetup->updateAttribute(Customer::ENTITY, 'is_verified_email', $data);
        }

        if (version_compare($context->getVersion(), '1.0.3', '<')) {
            $this->truncateDeviceTable($setup);
        }

        $setup->endSetup();
    }

    protected function truncateDeviceTable(ModuleDataSetupInterface $setup)
    {
        $table = $setup->getTable(\SM\Customer\Model\ResourceModel\CustomerDevice::TABLE_NAME);

        if ($setup->tableExists($table)) {
            $setup->getConnection()->truncateTable($table);
        }
    }
}
