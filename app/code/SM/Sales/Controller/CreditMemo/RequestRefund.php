<?php
/**
 * Class RequestRefund
 * @package SM\Sales\Controller\CreditMemo
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\Sales\Controller\CreditMemo;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Integration\Model\Oauth\TokenFactory;

class RequestRefund extends Action
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \SM\Sales\Model\AuthorizationToken
     */
    private $authorizationToken;

    /**
     * RequestRefund constructor.
     * @param Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \SM\Sales\Model\AuthorizationToken $authorizationToken
     */
    public function __construct(
        Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \SM\Sales\Model\AuthorizationToken $authorizationToken
    ) {
        $this->authorizationToken = $authorizationToken;
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /**
         * @var \Magento\Framework\View\Result\Page $resultPage
         */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $request = $this->getRequest();

        if ($request->getParam('creditmemo_id')) {
            $resultPage->getConfig()->getTitle()->set(__('Request Refund'));
            return $resultPage;
        }

        /**
         * @var \Magento\Framework\Controller\Result\Redirect $result
         */
        $result = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $result->setPath('');
    }
}
