<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\InspireMe\Setup\Patch\Data;

/**
 * Class CreateArticleContentFieldMobile
 * @package SM\InspireMe\Setup\Patch\Data
 */
class CreateArticleContentFieldMobile implements \Magento\Framework\Setup\Patch\DataPatchInterface
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

    /**
     * @return CreateArticleContentFieldMobile|void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Validate_Exception
     */
    public function apply()
    {
        $this->setup->startSetup();

        /** @var \Mirasvit\Blog\Setup\InstallData\PostSetup $postSetup */
        $postSetup = $this->postSetupFactory->create(['setup' => $this->setup]);
        $postSetup->addAttribute(
            'blog_post',
            'mobile_main_content',
            [
                'type'             => 'text',
                'label'            => 'Mobile Main Content',
                'input'            => 'text',
                'global'           => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE
            ]
        )->addAttribute(
            'blog_post',
            'mobile_sub_content',
            [
                'type'             => 'text',
                'label'            => 'Mobile Sub Content',
                'input'            => 'text',
                'global'           => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE
            ]
        );

        $this->setup->endSetup();
    }
}
