<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\Customer\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\PatchInterface;

/**
 * Class UpdateDistrictAttributeCustomerAddress
 * @package SM\Customer\Setup\Patch\Data
 */
class UpdateDistrictAttributeCustomerAddress implements \Magento\Framework\Setup\Patch\DataPatchInterface
{
    /**
     * @var \Magento\Framework\Setup\ModuleDataSetupInterface
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
     * @param \Magento\Customer\Setup\CustomerSetupFactory $customerSetupFactory
     */
    public function __construct(
        \Magento\Framework\Setup\ModuleDataSetupInterface $setup,
        \Magento\Customer\Setup\CustomerSetupFactory $customerSetupFactory
    ) {
        $this->setup = $setup;
        $this->customerSetupFactory = $customerSetupFactory;
    }

    /**
     * @return array|string[]
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @return array|string[]
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @return UpdateDistrictAttributeCustomerAddress|void
     */
    public function apply()
    {
        $this->setup->startSetup();

        /** @var \Magento\Customer\Setup\CustomerSetup $customerSetup */
        $customerSetup = $this->customerSetupFactory->create(['setup' => $this->setup]);

        try {
            $districtAttr = $customerSetup->getEavConfig()->getAttribute(
                'customer_address',
                'district'
            );

            $districtAttr->setData('frontend_input', 'select');
            $districtAttr->setData('source_model', 'SM\Customer\Model\ResourceModel\Address\Attribute\Source\District');
            $districtAttr->save();
        } catch (\Exception $e) {
        }

        $this->setup->endSetup();
    }
}
