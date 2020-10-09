<?php

/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Installation
 *
 * Date: May, 16 2020
 * Time: 11:18 AM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Installation\Plugin\MagentoCheckout\Model;

class CompositeConfigProvider
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var \SM\Installation\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Framework\View\Asset\Repository
     */
    protected $asset;

    /**
     * CompositeConfigProvider constructor.
     *
     * @param \Magento\Framework\View\Asset\Repository           $asset
     * @param \SM\Installation\Helper\Data                       $helper
     * @param \Magento\Store\Model\StoreManagerInterface         $storeManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\View\Asset\Repository $asset,
        \SM\Installation\Helper\Data $helper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->helper = $helper;
        $this->asset = $asset;
    }

    /**
     * @param \Magento\Checkout\Model\CompositeConfigProvider $subject
     * @param                                                 $result
     *
     * @return array
     */
    public function afterGetConfig(\Magento\Checkout\Model\CompositeConfigProvider $subject, $result)
    {
        if ($this->helper->isEnabled()) {
            $result['installationConfig'] = [
                'tooltip'    => $this->helper->getTooltip(),
                'tooltipUrl' => $this->asset->getUrlWithParams('images/info.svg', []),
                'showNote'   => true
            ];
        }

        return $result;
    }
}
