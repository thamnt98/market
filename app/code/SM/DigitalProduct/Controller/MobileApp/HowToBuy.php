<?php
/**
 * Class HowToBuy
 * @package SM\DigitalProduct\Controller\Mobile
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\DigitalProduct\Controller\MobileApp;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\PageFactory;
use SM\DigitalProduct\Helper\Config as DigitalConfig;
use SM\DigitalProduct\Helper\Category\Data as DigitalCategory;

class HowToBuy extends Action
{
    const MAP_TYPE_WITH_METHOD = [
        DigitalCategory::ELECTRICITY_BILL_VALUE => 'getElectricityBillHowToBuyBlockIdentifier',
        DigitalCategory::ELECTRICITY_TOKEN_VALUE => 'getElectricityHowToBuyBlockIdentifier',
        DigitalCategory::TOP_UP_VALUE => 'getTopUpHowToBuyBlockIdentifier',
        DigitalCategory::MOBILE_PACKAGE => 'getMobilePackageHowToBuyBlockIdentifier',
        DigitalCategory::MOBILE_PACKAGE_INTERNET_VALUE => 'getMobilePackageHowToBuyBlockIdentifier',
        DigitalCategory::MOBILE_PACKAGE_ROAMING_VALUE => 'getMobilePackageHowToBuyBlockIdentifier',
    ];

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var DigitalConfig
     */
    protected $digitalConfig;

    /**
     * Index constructor.
     * @param PageFactory $resultPageFactory
     * @param DigitalConfig $digitalConfig
     * @param Context $context
     */
    public function __construct(
        PageFactory $resultPageFactory,
        DigitalConfig $digitalConfig,
        Context $context
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->digitalConfig = $digitalConfig;
        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|ResultInterface
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $type = $this->getRequest()->getParam('type');

        if ($blockId = $this->getBlockIdFromType($type)) {
            $resultPage->getLayout()
                ->getBlock('how_to_buy_content')
                ->setData('how_to_buy', $blockId);
        }

        return $resultPage;
    }

    /**
     * @param $type
     * @return string|null
     */
    private function getBlockIdFromType($type)
    {
        if (isset(self::MAP_TYPE_WITH_METHOD[$type])) {
            $method = self::MAP_TYPE_WITH_METHOD[$type];
            return $this->digitalConfig->$method(true);
        }
        return null;
    }
}
