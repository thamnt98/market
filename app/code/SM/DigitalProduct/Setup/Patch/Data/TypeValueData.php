<?php
/**
 * @category Magento
 * @package SM\DigitalProduct\Setup\Patch\Data
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author Hung Viet Nguyen <hungnv6@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\DigitalProduct\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use SM\DigitalProduct\Api\Data\CategoryInterface;
use SM\DigitalProduct\Helper\Category\Data;

/**
 * Class CategoryData
 * @package SM\DigitalProduct\Setup\Patch\Data
 */
class TypeValueData implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var Data
     */
    protected $typeHelper;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        Data $typeHelper
    ) {
        $this->typeHelper = $typeHelper;
        $this->moduleDataSetup = $moduleDataSetup;
    }

    /**
     * @inheritDoc
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function apply()
    {
        $this->moduleDataSetup->startSetup();
        $setup = $this->moduleDataSetup;

        $types = $this->typeHelper->getTypeOptions();

        $data = [];
        foreach ($types as $keyType => $type) {
            $data[] =
                [CategoryInterface::TYPE => $keyType];
        }

        $this->moduleDataSetup->getConnection()->insertArray(
            $this->moduleDataSetup->getTable('sm_digitalproduct_category'),
            [CategoryInterface::TYPE],
            $data
        );
        $this->moduleDataSetup->endSetup();
    }
}
