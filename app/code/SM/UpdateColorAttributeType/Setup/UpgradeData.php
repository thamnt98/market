<?php

namespace SM\UpdateColorAttributeType\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var \Magento\Catalog\Model\Product\Attribute\Repository
     */
    protected $productAttributeRepository;

    /**
     * UpgradeData constructor.
     * @param \Magento\Catalog\Model\Product\Attribute\Repository $productAttributeRepository
     */
    public function __construct(
        \Magento\Catalog\Model\Product\Attribute\Repository $productAttributeRepository
    ) {
        $this->productAttributeRepository = $productAttributeRepository;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '1.0.0', '<')) {
            try {
                $colorAttribute = $this->productAttributeRepository->get('color');
                if ($colorAttribute->getBackendType() != 'int') {
                    $connection = $setup->getConnection();
                    $connection->update(
                        $connection->getTableName('eav_attribute'),
                        ['backend_type' => 'int'],
                        ['attribute_id = ?' => $colorAttribute->getAttributeId()]
                    );
                }
            } catch (\Exception $e) {
                // error
            }
        }
    }
}
