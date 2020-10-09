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
use SM\DigitalProduct\Api\Data\OperatorIconInterface;
use Trans\DigitalProduct\Model\DigitalProductOperatorList;
use Trans\DigitalProduct\Model\ResourceModel\DigitalProductOperatorList\Collection as OperatorCollection;
use Trans\DigitalProduct\Model\ResourceModel\DigitalProductOperatorList\CollectionFactory as OperatorCollectionFactory;

/**
 * Class OperatorIconData
 * @package SM\DigitalProduct\Setup\Patch\Data
 */
class OperatorIconData implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var OperatorCollectionFactory
     */
    protected $operatorCollectionFactory;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        OperatorCollectionFactory $operatorCollectionFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->operatorCollectionFactory = $operatorCollectionFactory;
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

        /** @var OperatorCollection $operatorCollection */
        $operatorCollection = $this->operatorCollectionFactory->create();

        $data = [];
        /** @var DigitalProductOperatorList $operator */
        foreach ($operatorCollection as $operator) {
            $data[$operator->getBrandId()] =
                [OperatorIconInterface::BRAND_ID => $operator->getBrandId()];
        }

        $this->moduleDataSetup->getConnection()->insertArray(
            $this->moduleDataSetup->getTable('sm_digitalproduct_operator_icon'),
            [OperatorIconInterface::BRAND_ID],
            $data
        );
        $this->moduleDataSetup->endSetup();
    }
}
