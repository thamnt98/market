<?php
/**
 * @category Magento
 * @package SM\DigitalProduct\Block
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author Hung Viet Nguyen <hungnv6@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\DigitalProduct\Block;

use Magento\Framework\View\Element\Template;
use SM\DigitalProduct\Helper\Config;

/**
 * Class Banner
 * @package SM\DigitalProduct\Block
 */
class Banner extends Template
{
    /**
     * @var Config
     */
    protected $configHelper;

    /**
     * Banner constructor.
     * @param Template\Context $context
     * @param Config $configHelper
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Config $configHelper,
        array $data = []
    ) {
        $this->configHelper = $configHelper;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getBannerIdentifier()
    {
        return $this->configHelper->getBannerBlockIdentifier();
    }
}
