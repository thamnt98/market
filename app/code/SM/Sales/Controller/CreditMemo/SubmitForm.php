<?php
/**
 * Class SubmitForm
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
use Magento\Framework\App\Action\HttpPostActionInterface;
use SM\Sales\Api\Data\Creditmemo\FormInformationInterface;
use SM\Sales\Model\Creditmemo\RequestFormData;
use Magento\Framework\Controller\ResultFactory;

class SubmitForm extends Action implements HttpPostActionInterface
{
    /**
     * @var \Magento\Sales\Api\CreditmemoRepositoryInterface
     */
    private $creditmemoRepository;

    /**
     * @var \Magento\Customer\Model\SessionFactory
     */
    private $sessionFactory;

    /**
     * @var RequestFormData
     */
    private $requestFormData;

    /**
     * @var \Magento\Customer\Model\Customer
     */
    private $customer;

    /**
     * @var \SM\Sales\Model\Creditmemo\SendToJira
     */
    private $sendToJira;

    /**
     * SubmitForm constructor.
     * @param Context $context
     * @param \Magento\Customer\Model\SessionFactory $sessionFactory
     * @param \Magento\Sales\Api\CreditmemoRepositoryInterface $creditmemoRepository
     * @param RequestFormData $requestFormData
     * @param \SM\Sales\Model\Creditmemo\SendToJira $sendToJira
     */
    public function __construct(
        Context $context,
        \Magento\Customer\Model\SessionFactory $sessionFactory,
        \Magento\Sales\Api\CreditmemoRepositoryInterface $creditmemoRepository,
        \SM\Sales\Model\Creditmemo\RequestFormData $requestFormData,
        \SM\Sales\Model\Creditmemo\SendToJira $sendToJira
    ) {
        $this->creditmemoRepository = $creditmemoRepository;
        $this->sessionFactory = $sessionFactory;
        $this->requestFormData = $requestFormData;
        $this->sendToJira = $sendToJira;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute(): \Magento\Framework\Controller\ResultInterface
    {
        $params = $this->getRequest()->getParams();
        $setFormData = $this->requestFormData->setFormData($params['creditmemo_id']);
        $creditmemo = $this->requestFormData->getCreditmemo();
        $isSubmitted = $creditmemo->getCreditmemoStatus() == FormInformationInterface::SUBMITTED_VALUE;
        if ($setFormData && !$isSubmitted) {
            if (!$this->validateCustomer()) {
                $this->messageManager->addErrorMessage(__('Something went wrong while summiting your request'));
                return $this->goBack();
            } else {
                $this->sendToJira->setCustomer($this->customer);
            }

            try {
                $params = $this->prepareParams($params);
                if ($this->sendToJira->send($params)) {
                    $this->messageManager->addSuccessMessage(__('Your request has been sent successfully'));
                    return $this->goToSuccessPage();
                } else {
                    $this->messageManager->addErrorMessage(__('Something went wrong while summiting your request'));
                    return $this->goBack();
                }
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $this->goBack();
            }
        } elseif (!$params) {
            $this->messageManager->addErrorMessage(__('The Parameters can\'t empty!'));
            return $this->goBack();
        }

        $this->messageManager->addErrorMessage(__('Your request dosen\'t exist'));
        return $this->goBack();
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    protected function goBack(): \Magento\Framework\Controller\ResultInterface
    {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        return $resultRedirect;
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    protected function goToSuccessPage(): \Magento\Framework\Controller\ResultInterface
    {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_url->getUrl('sales/creditmemo/requestsuccess'));
        return $resultRedirect;
    }

    /**
     * @return bool
     */
    protected function validateCustomer(): bool
    {
        $this->customer = $this->sessionFactory->create()->getCustomer();
        return $this->requestFormData->validateCustomer($this->customer->getId());
    }

    /**
     * @param array $params
     * @return array
     */
    protected function prepareParams(array $params): array
    {
        return [
            RequestFormData::BANK_KEY => $params['bank'],
            RequestFormData::ACCOUNT_NAME_KEY => $params['account_name'],
            RequestFormData::ACCOUNT_KEY => (int)$params['account_number'],
            RequestFormData::ORDER_REFERENCE_NUMBER_KEY =>  $this->requestFormData->getReferenceNumber(),
            RequestFormData::PAYMENT_NUMBER_KEY  => $this->requestFormData->getPaymentNumber(),
            RequestFormData::TOTAL_REFUND_KEY => (int) $this->requestFormData->getTotalRefund(),
            RequestFormData::CREDITMEMO_ID_KEY => (int)$this->requestFormData->getCreditmemoId(),
            RequestFormData::PAYMENT_METHOD_KEY => $this->requestFormData->getOrder()->getPayment()->getMethod()
        ];
    }
}
