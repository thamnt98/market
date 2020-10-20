<?php
declare(strict_types=1);

namespace SM\Checkout\Setup\Patch\Data;

use Magento\Customer\Setup\CustomerSetup;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Model\Entity\Attribute\SetFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;

class AddBlockPaymentNote implements DataPatchInterface, PatchRevertableInterface
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
     * @param \Magento\Cms\Model\BlockFactory $blockFactory
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

        try {
            $block = $this->_blockFactory->create();
            $block->setTitle('Payment Note - Virtual Account BCA')
             ->setIdentifier('payment_note_sprint_bca_va')
             ->setIsActive(true)
             ->setStores([0])
             ->setContent('This transaction will replace unpaid BCA Virtual Account bill.
Get the payment code after clicking "Pay"')
             ->save();
        } catch (\Exception $e) {
        }

        $bcaHowToPayData = [
            [
                "title" => "Bank Mega ATM",
                "identifier" => 'how-to-pay-sprint_bca_va-1',
                'is_active' => true,
                "stores" => [0],
                "content" => '1. Insert your ATM BCA card and enter your PIN.
2. Choose Other Transactions.
3. Choose Transfer.
4. Choose to BCA Virtual Account.
5. Enter the BCA Virtual Account number shown on your Transmart page. Choose Right.
6. Make sure the amount of payment and other information are correct, then choose Right.
7. Choose Yes to validate your payment.'
            ],
            [
                "title" => "M-Banking Bank Mega",
                "identifier" => 'how-to-pay-sprint_bca_va-2',
                'is_active' => true,
                "stores" => [0],
                "content" => '1. Log in to your mobile BCA application.
2. Choose m-BCA and enter your m-BCA access code. Choose Login.
3. Choose m-Transfer.
4. Choose BCA Virtual Account.
5. Enter the BCA Virtual Account number shown on your Transmart page. Choose OK.
6. Choose Send at the top right of your screen.
7. Continue your payment by choosing OK.
8. Enter your PIN to finish the payment.'
            ],
            [
                "title" => "Klik Bank Mega",
                "identifier" => 'how-to-pay-sprint_bca_va-3',
                'is_active' => true,
                "stores" => [0],
                "content" => '1. Log in to KlikBCA Individual.
2. Enter your User ID and PIN.
3. Choose Transfer Dana.
4. Choose Transfer ke BCA Virtual Account.
5. Enter the BCA Virtual Account number shown on your Transmart page.
6. Choose Lanjutkan.
7. Validate your payment by entering KeyBCA authorization code shown on your token. Choose Kirim.'
            ]
        ];

        foreach ($bcaHowToPayData as $data) {
            try {
                $blockHowToPay = $this->_blockFactory->create();
                $blockHowToPay->setData($data)->save();
            } catch (\Exception $e) {
                continue;
            }
        }

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * {@inheritdoc}
     */
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
        return [];
    }
}
