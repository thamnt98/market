<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\InspireMe\Setup;

use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Mirasvit\Blog\Api\Repository\CategoryRepositoryInterface;
use Mirasvit\Blog\Model\Category;
use Mirasvit\Blog\Setup\InstallData\PostSetup;
use Mirasvit\Blog\Setup\InstallData\PostSetupFactory;
use Mirasvit\Blog\Setup\InstallData\CategorySetupFactory;
use Zend_Validate_Exception;

/**
 * Class UpgradeData
 * @package SM\InspireMe\Setup
 */
class InstallData implements InstallDataInterface
{
    /**
     * @var PostSetupFactory
     */
    protected $postSetupFactory;

    /**
     * @var EavSetupFactory
     */
    protected $eavSetupFactory;

    /**
     * @var CategorySetupFactory
     */
    protected $categorySetupFactory;

    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * UpgradeData constructor.
     * @param PostSetupFactory $postSetupFactory
     * @param CategorySetupFactory $categorySetupFactory
     * @param EavSetupFactory $eavSetupFactory
     * @param CategoryRepositoryInterface $categoryRepository
     */
    public function __construct(
        PostSetupFactory $postSetupFactory,
        CategorySetupFactory $categorySetupFactory,
        EavSetupFactory $eavSetupFactory,
        CategoryRepositoryInterface $categoryRepository
    ) {
        $this->postSetupFactory = $postSetupFactory;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->categorySetupFactory = $categorySetupFactory;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @return array
     */
    private function getAttributes()
    {
        return [
            'position' => [
                'type' => 'int',
                'label' => 'Position on Homepage',
                'input' => 'text',
                'global' => ScopedAttributeInterface::SCOPE_STORE,
                'default' => null,
            ],
        ];
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws LocalizedException
     * @throws Zend_Validate_Exception
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        /** @var PostSetup $postSetup */
        $postSetup = $this->postSetupFactory->create(['setup' => $setup]);
        foreach ($this->getAttributes() as $code => $data) {
            $postSetup->addAttribute('blog_post', $code, $data);
        }

        $this->updateCategory($setup);

        $this->updateConfig($setup, 'blog/seo/base_route', 'inspireme');
        $this->updateConfig($setup, 'blog/appearance/blog_name', 'Inspire Me');
        $this->updateConfig($setup, 'blog/seo/base_meta_title', 'Inspire Me');
        $this->updateConfig($setup, 'blog/seo/base_meta_description', 'Inspire Me');
        $this->updateConfig($setup, 'blog/seo/base_meta_keywords', 'Inspire Me');
        $this->updateConfig($setup, 'blog/general/is_review', 0);

        $setup->endSetup();
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param string $configPath
     * @param mixed $value
     */
    private function updateConfig($setup, $configPath, $value)
    {
        $conn = $setup->getConnection();
        $table = $setup->getTable('core_config_data');

        $select = $conn->select()->from($table)->where("path = '$configPath'");
        $data = $conn->fetchAssoc($select);
        if (count($data) < 1) {
            $data[] = [
                'scope' => 'default',
                'scope_id' => 0,
                'path' => $configPath,
                'value' => $value
            ];
        }
        foreach ($data as $item) {
            $item['value'] = $value;
            $conn->insertOnDuplicate($table, $item, ['value']);
        }
    }

    /**
     * @param ModuleDataSetupInterface $setup
     */
    private function updateCategory($setup)
    {
        $defaultCategory = $this->getDefaultCategory();
        $categorySetup = $this->categorySetupFactory->create(['setup' => $setup]);
        foreach ($defaultCategory as $item) {
            /** @var Category $category */
            $category = $categorySetup->getCategoryFactory()->create();
            $category
                ->setName($item['name'])
                ->setParentId($item['parent_id'])
                ->setPath($item['path'])
                ->setLevel($item['level'])
                ->setPosition($item['position'])
                ->setStatus($item['status']);
            $this->categoryRepository->save($category);
        }
    }

    /**
     * @return array
     */
    private function getDefaultCategory()
    {
        return [
            [
                'name' => __('Living'),
                'parent_id' => 1,
                'path' => '1/2',
                'level' => 1,
                'position' => 1,
                'status' => 1,
            ],
            [
                'name' => __('Cooking'),
                'parent_id' => 1,
                'path' => '1/3',
                'level' => 1,
                'position' => 2,
                'status' => 1,
            ],
            [
                'name' => __('Fashion'),
                'parent_id' => 1,
                'path' => '1/4',
                'level' => 1,
                'position' => 3,
                'status' => 1,
            ],
            [
                'name' => __('Do it Yourself'),
                'parent_id' => 1,
                'path' => '1/5',
                'level' => 1,
                'position' => 4,
                'status' => 1,
            ],
        ];
    }
}
