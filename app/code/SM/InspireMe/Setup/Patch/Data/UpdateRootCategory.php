<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\InspireMe\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * Class UpdateRootCategory
 * @package SM\InspireMe\Setup\Patch\Data
 */
class UpdateRootCategory implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    protected $setup;

    /**
     * @var \Mirasvit\Blog\Api\Repository\CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * UpdateRootCategory constructor.
     * @param ModuleDataSetupInterface $setup
     * @param \Mirasvit\Blog\Api\Repository\CategoryRepositoryInterface $categoryRepository
     */
    public function __construct(
        ModuleDataSetupInterface $setup,
        \Mirasvit\Blog\Api\Repository\CategoryRepositoryInterface $categoryRepository
    ) {
        $this->setup = $setup;
        $this->categoryRepository = $categoryRepository;
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
        $this->setup->startSetup();

        /** @var \Mirasvit\Blog\Model\Category $rootCategory */
        $rootCategory = $this->categoryRepository->getCollection()
            ->addFieldToFilter(\Mirasvit\Blog\Api\Data\CategoryInterface::PARENT_ID, ['eq' => 0])
            ->getFirstItem();
        $rootCategory->setName('All Topics')
            ->setUrlKey('all-topics');
        $this->categoryRepository->save($rootCategory);

        $this->setup->endSetup();
    }
}
