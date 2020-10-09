<?php

/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_InspireMe
 *
 * Date: March, 30 2020
 * Time: 5:47 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\InspireMe\Setup\Patch\Data;

class CreatePublishedDateAttribute implements \Magento\Framework\Setup\Patch\DataPatchInterface
{
    /**
     * @var \Magento\Framework\Setup\ModuleDataSetupInterface
     */
    protected $setup;

    /**
     * @var \Mirasvit\Blog\Setup\InstallData\PostSetupFactory
     */
    protected $postSetupFactory;

    /**
     * AddPostViewsCount constructor.
     *
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $setup
     * @param \Mirasvit\Blog\Setup\InstallData\PostSetupFactory $postSetupFactory
     */
    public function __construct(
        \Magento\Framework\Setup\ModuleDataSetupInterface $setup,
        \Mirasvit\Blog\Setup\InstallData\PostSetupFactory $postSetupFactory
    ) {
        $this->setup = $setup;
        $this->postSetupFactory = $postSetupFactory;
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


    public function apply()
    {
        $this->setup->startSetup();

        /** @var \Mirasvit\Blog\Setup\InstallData\PostSetup $postSetup */
        $postSetup = $this->postSetupFactory->create(['setup' => $this->setup]);
        $postSetup->addAttribute(
            'blog_post',
            'published_date',
            [
                'type'             => 'datetime',
                'label'            => 'Published Date',
                'input'            => 'date',
                'visible_on_front' => false,
                'global'           => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE
            ]
        );

        $this->setup->endSetup();
    }
}

