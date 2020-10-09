<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\Customer\Setup\Patch\Data;

/**
 * Class RemoveNotSpecifiedOptionGenderAttribute
 * @package SM\Customer\Setup\Patch\Data
 */
class RemoveNotSpecifiedOptionGenderAttribute implements \Magento\Framework\Setup\Patch\DataPatchInterface
{
    /**
     * @var \Magento\Framework\Setup\ModuleDataSetupInterface
     */
    protected $moduleDataSetup;

    /**
     * @var \Magento\Customer\Setup\CustomerSetupFactory
     */
    protected $customerSetupFactory;

    /**
     * @var \Magento\Customer\Model\AttributeFactory
     */
    protected $attributeFactory;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * AddNonSpecifiedGenderAttributeOption constructor.
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $moduleDataSetup
     * @param \Magento\Customer\Setup\CustomerSetupFactory $customerSetupFactory
     * @param \Magento\Customer\Model\AttributeFactory $attributeFactory
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Framework\Setup\ModuleDataSetupInterface $moduleDataSetup,
        \Magento\Customer\Setup\CustomerSetupFactory $customerSetupFactory,
        \Magento\Customer\Model\AttributeFactory $attributeFactory,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->customerSetupFactory = $customerSetupFactory;
        $this->attributeFactory = $attributeFactory;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        $this->moduleDataSetup->startSetup();

        /** @var \Magento\Customer\Setup\CustomerSetup $customerSetup */
        $customerSetup = $this->customerSetupFactory->create(['setup' => $this->moduleDataSetup]);

        try {
            $entityTypeId = $customerSetup->getEntityTypeId(\Magento\Customer\Model\Customer::ENTITY);

            /** @var \Magento\Customer\Model\Attribute $attribute */
            $attribute = $this->attributeFactory->create();
            $attribute = $attribute->loadByCode($entityTypeId, 'gender');

            /** @var \Magento\Eav\Api\Data\AttributeOptionInterface[] $options */
            $options = $attribute->getOptions();
            $optionsToRemove = [];

            foreach ($options as $option) {
                if ($option->getLabel() == 'Not Specified') {
                    $optionsToRemove['delete'][$option->getValue()] = true;
                    $optionsToRemove['value'][$option->getValue()] = true;
                }
            }

            $customerSetup->addAttributeOption($optionsToRemove);
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
        }

        $this->moduleDataSetup->endSetup();
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }
}
