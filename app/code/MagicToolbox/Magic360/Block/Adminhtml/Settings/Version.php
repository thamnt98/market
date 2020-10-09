<?php

namespace MagicToolbox\Magic360\Block\Adminhtml\Settings;

/**
 * Module version block
 *
 */
class Version extends \Magento\Framework\View\Element\Template
{
    /**
     * Object manager
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * Data helper
     *
     * @var \MagicToolbox\Magic360\Helper\Data
     */
    protected $dataHelper = null;

    /**
     * Class constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->dataHelper = $this->objectManager->get(\MagicToolbox\Magic360\Helper\Data::class);
    }

    /**
     * Get module version
     *
     * @return string
     */
    public function getModuleVersion()
    {
        $version = $this->dataHelper->getModuleVersion('MagicToolbox_Magic360');
        $version = $version ? $version : '';

        return $version;
    }

    /**
     * Get upgrade message
     *
     * @return string
     */
    public function getUpgradeMessage()
    {
        $modulesData = $this->dataHelper->getModulesData();

        if (!isset($modulesData['MagicToolbox_Sirv'])) {
            return '';
        }

        $version = $modulesData['MagicToolbox_Sirv'];
        $requiredVersion = '2.1.0';

        $message = '';
        if (version_compare($version, $requiredVersion, '<')) {
            $message = 'Notice: you have installed MagicToolbox_Sirv module by version ' . $version . '.' .
                ' Please, update it at least to version ' . $requiredVersion;
        }

        return $message;
    }
}
