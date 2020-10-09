<?php

/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_GTM
 *
 * Date: April, 13 2020
 * Time: 2:59 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\GTM\Model\Setup;

abstract class AbstractUpdateConfig implements \Magento\Framework\Setup\Patch\DataPatchInterface
{
    /**
     * @var \Magento\Framework\Setup\ModuleDataSetupInterface
     */
    protected $setup;

    /**
     * @var \SM\GTM\Helper\Setup\UpdateConfig
     */
    protected $gtmSetup;

    /**
     * AddGtmConfig constructor.
     *
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $setup
     * @param \SM\GTM\Helper\Setup\UpdateConfig                 $gtmSetup
     */
    public function __construct(
        \Magento\Framework\Setup\ModuleDataSetupInterface $setup,
        \SM\GTM\Helper\Setup\UpdateConfig $gtmSetup
    ) {
        $this->setup = $setup;
        $this->gtmSetup = $gtmSetup;
    }

    public static function getDependencies()
    {
        return [];
    }

    public function getAliases()
    {
        return [];
    }
}
