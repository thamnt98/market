<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\Help\Block\Help;

use Magento\Framework\View\Element\Template;

/**
 * Class NeedHelp
 * @package SM\Help\Block\Help
 */
class NeedHelp extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \SM\Help\Model\Config
     */
    protected $config;

    /**
     * NeedHelp constructor.
     * @param \SM\Help\Model\Config $config
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(
        \SM\Help\Model\Config $config,
        Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->config = $config;
    }

    /**
     * Get Url To Help Page
     * @return string
     */
    public function getHelpUrl()
    {
        return $this->config->getBaseUrl();
    }
}
