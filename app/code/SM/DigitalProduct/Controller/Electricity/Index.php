<?php
declare(strict_types=1);

namespace SM\DigitalProduct\Controller\Electricity;

use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\Page;

/**
 * Class Index
 * @package SM\DigitalProduct\Controller\Electricity
 */
class Index extends \SM\DigitalProduct\Controller\Index
{
    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Forward|ResultInterface
     */
    public function execute()
    {
        if (!$this->customerSession->isLoggedIn()) {
            return $this->_redirect($this->_url->getUrl());
        }

        return $this->getResult($this->configHelper->getElectricityTitle());
    }
}
