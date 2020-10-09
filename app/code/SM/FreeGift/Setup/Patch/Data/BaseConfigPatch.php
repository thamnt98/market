<?php


namespace SM\FreeGift\Setup\Patch\Data;


use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;

class BaseConfigPatch implements DataPatchInterface, PatchRevertableInterface
{
    /**
     * @var \Magento\Framework\App\Config\ConfigResource\ConfigInterface
     */
    protected $config;
    /**
     * @var \Magento\Framework\Setup\ModuleDataSetupInterface
     */
    protected $moduleDataSetup;
    /**
     * @var \Magento\Store\Api\StoreRepositoryInterface
     */
    protected $storeRepository;

    /**
     * BaseConfigPatch constructor.
     * @param \Magento\Framework\App\Config\ConfigResource\ConfigInterface $config
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $moduleDataSetup
     * @param \Magento\Store\Api\StoreRepositoryInterface $storeRepository
     */
    public function __construct(
        \Magento\Framework\App\Config\ConfigResource\ConfigInterface $config,
        \Magento\Framework\Setup\ModuleDataSetupInterface $moduleDataSetup,
        \Magento\Store\Api\StoreRepositoryInterface $storeRepository
    ) {
        $this->config = $config;
        $this->moduleDataSetup = $moduleDataSetup;
        $this->storeRepository = $storeRepository;
    }

    /**
     * Get array of patches that have to be executed prior to this.
     *
     * Example of implementation:
     *
     * [
     *      \Vendor_Name\Module_Name\Setup\Patch\Patch1::class,
     *      \Vendor_Name\Module_Name\Setup\Patch\Patch2::class
     * ]
     *
     * @return string[]
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * Get aliases (previous names) for the patch.
     *
     * @return string[]
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * Run code inside patch
     * If code fails, patch must be reverted, in case when we are speaking about schema - then under revert
     * means run PatchInterface::revert()
     *
     * If we speak about data, under revert means: $transaction->rollback()
     *
     * @return $this
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        $storeEn = $this->getStoreByCode('en_US');
        $storeId = $this->getStoreByCode('id_ID');
        // Change config value for Popup name
        $popupHeaderText = $this->getAmPath(\Amasty\Promo\Model\Config::GROUP_PROMO_MESSAGES, 'popup_title');
        $openPopup = $this->getAmPath(\Amasty\Promo\Model\Config::GROUP_PROMO_MESSAGES, 'auto_open_popup');
//        if ($storeEn) {
//            $this->config->saveConfig($popupHeaderText, 'Choose Your Free Gift',
//                \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeEn);
//        }

        if ($storeId) {
            $this->config->saveConfig($popupHeaderText, 'Pilih Hadiah Gratis Anda',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORES, $storeId);
        }
        // Default value
        $this->config->saveConfig($popupHeaderText, 'Choose Your Free Gift');
        $this->config->saveConfig($openPopup, '1');
        $this->moduleDataSetup->getConnection()->endSetup();
    }

    protected function getAmPath($group, $path = null)
    {
        $path = \Amasty\Promo\Model\Config::PROMO_SECTION . $group . $path;
        return $path;
    }

    /**
     * Rollback all changes, done by this patch
     *
     * @return void
     */
    public function revert()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        //Here should go code that will revert all operations from `apply` method
        //Please note, that some operations, like removing data from column, that is in role of foreign key reference
        //is dangerous, because it can trigger ON DELETE statement
        $this->moduleDataSetup->getConnection()->endSetup();
    }

    private function getStoreByCode($code)
    {

        try {
            $store = $this->storeRepository->get($code);
            return $store->getId();
        } catch (NoSuchEntityException $e) {
            return null;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}