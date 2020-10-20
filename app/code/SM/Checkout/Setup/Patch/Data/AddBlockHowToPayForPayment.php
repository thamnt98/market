<?php
declare(strict_types=1);


namespace SM\Checkout\Setup\Patch\Data;

use Magento\Customer\Model\Indexer\Address\AttributeProvider;
use Magento\Customer\Setup\CustomerSetup;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Model\Entity\Attribute\Set;
use Magento\Eav\Model\Entity\Attribute\SetFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;


class AddBlockHowToPayForPayment implements DataPatchInterface, PatchRevertableInterface
{

    /**
     * @var ModuleDataSetupInterface
     */
    protected $moduleDataSetup;
    /**
     * @var CustomerSetup
     */
    protected $customerSetupFactory;
    /**
     * @var SetFactory
     */
    protected $attributeSetFactory;
    /**
     * @var \Magento\Cms\Model\BlockFactory
     */
    protected $_blockFactory;

    /**
     * Constructor
     *
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param CustomerSetupFactory $customerSetupFactory
     * @param SetFactory $attributeSetFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        CustomerSetupFactory $customerSetupFactory,
        SetFactory $attributeSetFactory,
        \Magento\Cms\Model\BlockFactory $blockFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->customerSetupFactory = $customerSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
        $this->_blockFactory = $blockFactory;

    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        $page = $this->_blockFactory->create();
        $page->setTitle('How to Pay')
             ->setIdentifier('how-to-pay')
             ->setIsActive(true)
             ->setContent('<div id="accordion">
        <div data-role="collapsible" class="title-accordion">
            <div data-role="trigger" class="arrow-title">
                <span >Bank Mega ATM</span>
            </div>
        </div>
        <div data-role="content" class="content-accordion">
            <p>
                Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore
                et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut
                aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum
                dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui o
                fficia deserunt mollit anim id est laborum.
            </p>
        </div>

        <div data-role="collapsible" class="title-accordion">
            <div data-role="trigger" class="arrow-title">
                <span>Mega Mobile App</span>
            </div>
        </div>
        <div data-role="content" class="content-accordion">
            <p>
                Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore
                et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut
                aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum
                dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui o
                fficia deserunt mollit anim id est laborum.
            </p>
        </div>

        <div data-role="collapsible" class="title-accordion">
            <div data-role="trigger" class="arrow-title">
                <span >Mega Internet</span>
            </div>
        </div>
        <div data-role="content" class="content-accordion">
            <p>
                Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore
                et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut
                aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum
                dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui o
                fficia deserunt mollit anim id est laborum.
            </p>
        </div>

        <div data-role="collapsible" class="title-accordion">
            <div data-role="trigger" class="arrow-title">
                <span >Bank Mega Branch Office</span>
            </div>
        </div>
        <div data-role="content" class="content-accordion">
            <p>
                Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore
                et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut
                aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum
                dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui o
                fficia deserunt mollit anim id est laborum.
            </p>
        </div>
    </div>
')
             ->save();

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    public function revert()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [

        ];
    }
}

