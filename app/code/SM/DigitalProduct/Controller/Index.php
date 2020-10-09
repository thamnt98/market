<?php
declare(strict_types=1);

namespace SM\DigitalProduct\Controller;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Result\PageFactory;
use SM\DigitalProduct\Helper\Config;

/**
 * Class Index
 * @package SM\DigitalProduct\Controller
 */
abstract class Index extends Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    /**
     * @var Config
     */
    protected $configHelper;

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var ResultFactory
     */
    private $result;

    /**
     * Constructor
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Config $configHelper
     * @param Session $customerSession
     */
    public function __construct(
        Context $context,
        ResultFactory $result,
        Config $configHelper,
        Session $customerSession
    ) {
        $this->customerSession = $customerSession;
        $this->configHelper = $configHelper;
        $this->result = $result;
        parent::__construct($context);
    }

    /**
     * @param $pageTitle
     * @return \Magento\Framework\Controller\Result\Forward|\Magento\Framework\Controller\ResultInterface
     */
    protected function getResult($pageTitle)
    {
        if ($this->configHelper->isEnable()) {
            $resultPage = $this->resultPage();
            $resultPage->getConfig()->getTitle()->set($pageTitle);
            return $resultPage;
        }

        return $this->resultToNotFound();
    }

    /**
     * @return bool
     */
    protected function isEnable()
    {
        return $this->configHelper->isEnable();
    }

    /**
     * @return \Magento\Framework\Controller\Result\Forward
     */
    protected function resultToNotFound()
    {
        /**
         * @var \Magento\Framework\Controller\Result\Forward $result
         */
        $result = $this->result->create(ResultFactory::TYPE_FORWARD);
        $result->setController('cms')
            ->forward('noroute');
        return $result;
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    protected function resultPage()
    {
        return $this->result->create(ResultFactory::TYPE_PAGE);
    }
}
