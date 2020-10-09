<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Installation
 *
 * Date: April, 20 2020
 * Time: 4:21 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Installation\Setup\Patch\Data;

class CreateProductInstallationServiceAttribute implements \Magento\Framework\Setup\Patch\DataPatchInterface
{
    /**
     * @var \Magento\Eav\Setup\EavSetupFactory
     */
    protected $eavSetupFactory;

    /**
     * CreateProductInstallationAttribute constructor.
     *
     * @param \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    public static function getDependencies()
    {
        return [];
    }

    public function getAliases()
    {
        return [];
    }

    /**
     * @return CreateProductInstallationServiceAttribute|void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Validate_Exception
     */
    public function apply()
    {
        /** @var \Magento\Eav\Setup\EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create();
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'is_service',
            [
                'type'                    => 'int',
                'frontend'                => '',
                'label'                   => 'Installation',
                'group'                   => 'Product Details',
                'input'                   => 'boolean',
                'global'                  => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'backend'                 => \Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend::class,
                'visible'                 => true,
                'required'                => false,
                'default'                 => 0,
                'searchable'              => false,
                'filterable'              => true,
                'comparable'              => false,
                'visible_on_front'        => false,
                'unique'                  => false,
                'is_used_in_grid'         => false,
                'is_visible_in_grid'      => false,
                'is_filterable_in_grid'   => false,
                'user_defined'            => true,
            ]
        );
    }
}
