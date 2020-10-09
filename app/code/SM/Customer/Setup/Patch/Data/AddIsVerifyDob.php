<?php


namespace SM\Customer\Setup\Patch\Data;

use Magento\Customer\Model\Attribute;
use Magento\Customer\Model\Metadata\CustomerMetadata;
use Magento\Customer\Setup\CustomerSetup;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Eav\Model\Entity\Attribute\Source\Boolean;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use SM\Customer\Helper\Config;

class AddIsVerifyDob implements DataPatchInterface
{
    const USED_IN_FORMS = [
        'adminhtml_customer',
        'customer_account_edit'
    ];

    const DEFAULT_GROUP = 'General';

    /**
     * @var CustomerSetup
     */
    protected $customerSetup;

    /**
     * @var AttributeRepositoryInterface
     */
    protected $attributeRepository;

    /**
     * AddCountryOfResidenceAttribute constructor.
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param CustomerSetupFactory $customerSetupFactory
     * @codeCoverageIgnore
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        CustomerSetupFactory $customerSetupFactory
    ) {
        $this->customerSetup = $customerSetupFactory->create(['setup' => $moduleDataSetup]);
    }

    /**
     * @inheritdoc
     * @throws \Exception
     */
    public function apply(): void
    {
        $attributeData = [
            'label' => 'Is Disable Date Of Birth',
            'type' => 'int',
            'input' => 'select',
            'source' => Boolean::class,
            'default' => 0,
            'required' => true,
            'visible' => true,
            'user_defined' => true,
            'system' => false,
            'position' => 999,
            'group' => self::DEFAULT_GROUP,
            'is_html_allowed_on_front' => true,
            'visible_on_front' => true
        ];

        $this->customerSetup->addAttribute(
            CustomerMetadata::ENTITY_TYPE_CUSTOMER,
            Config::IS_DISABLED_DATE_OF_BIRTH,
            $attributeData
        );

        $this->addAttributeToForms();
    }

    /**
     * @throws \Exception
     */
    protected function addAttributeToForms(): void
    {
        /** @var Attribute $attribute */
        $attribute = $this->customerSetup->getEavConfig()->getAttribute(
            CustomerMetadata::ENTITY_TYPE_CUSTOMER,
            Config::IS_DISABLED_DATE_OF_BIRTH
        );
        $attribute->setData('used_in_forms', self::USED_IN_FORMS);

        // use AttributeRepository dont save used_in_forms
        $attribute->save();
    }

    /**
     * @inheritdoc
     * @codeCoverageIgnore
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * @inheritdoc
     * @codeCoverageIgnore
     */
    public function getAliases(): array
    {
        return [];
    }
}
