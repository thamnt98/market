<?php
declare(strict_types=1);

namespace SM\DigitalProduct\Controller\Topup;

use Magento\Framework\Controller\ResultInterface;

/**
 * Class Index
 * @package SM\DigitalProduct\Controller\MobileTopup
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

        return $this->getResult($this->configHelper->getTopUpTitle());
    }
}
