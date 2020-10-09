<?php
/**
 * @category Magento
 * @package SM\DigitalProduct\Controller\Index
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author Hung Viet Nguyen <hungnv6@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\DigitalProduct\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\PageFactory;
use SM\DigitalProduct\Helper\Config;

/**
 * Class Index
 * @package SM\DigitalProduct\Controller\Index
 */
class Index extends Action
{
    /**
     * @var ResultFactory
     */
    protected $result;

    /**
     * @var Config
     */
    protected $configHelper;

    /**
     * Constructor
     *
     * @param Context $context
     * @param ResultFactory $result
     * @param Config $configHelper
     */
    public function __construct(
        Context $context,
        ResultFactory $result,
        Config $configHelper
    ) {
        $this->configHelper = $configHelper;
        $this->result = $result;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Forward|ResultInterface
     */
    public function execute()
    {
        /**
         * @var \Magento\Framework\Controller\Result\Forward $result
         */
        $result = $this->result->create(ResultFactory::TYPE_FORWARD);

        if ($this->configHelper->isEnable()) {
            $result->setController($this->configHelper->getHomePageType())
                ->forward('index');
        } else {
            $result->setController('cms')
                ->forward('noroute');
        }
        return $result;
    }
}
