<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\InspireMe\Setup\Patch\Data;

use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Mirasvit\Blog\Setup\InstallData\PostSetup;
use Mirasvit\Blog\Setup\InstallData\PostSetupFactory;
use Zend_Validate_Exception;

/**
 * Class CreateTempPostViewsCount
 * @package SM\InspireMe\Setup\Patch\Data
 */
class CreateTempPostViewsCount implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    protected $setup;

    /**
     * @var PostSetupFactory
     */
    protected $postSetupFactory;

    /**
     * AddPostViewsCount constructor.
     * @param ModuleDataSetupInterface $setup
     * @param PostSetupFactory $postSetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $setup,
        PostSetupFactory $postSetupFactory
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
     * @return DataPatchInterface|void
     * @throws LocalizedException
     * @throws Zend_Validate_Exception
     */
    public function apply()
    {
        $this->setup->startSetup();

        /** @var PostSetup $postSetup */
        $postSetup = $this->postSetupFactory->create(['setup' => $this->setup]);
        foreach ($this->getAttributes() as $code => $data) {
            $postSetup->addAttribute('blog_post', $code, $data);
        }

        $this->setup->endSetup();
    }

    /**
     * @return array
     */
    private function getAttributes()
    {
        return [
            'temp_views_count' => [
                'type' => 'int',
                'label' => 'Temp Post Views Count',
                'input' => 'text',
                'global' => ScopedAttributeInterface::SCOPE_STORE,
                'default' => 0,
            ],
        ];
    }
}
