<?php
/**
 * Class RequestForm
 * @package SM\Sales\Block\Creditmemo
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\Sales\Block\Creditmemo;

use Magento\Customer\Model\SessionFactory;
use Magento\Framework\Pricing\Helper\Data;
use Magento\Framework\View\Element\Template;
use SM\Sales\Api\Data\Creditmemo\FormInformationInterface;
use SM\Sales\Model\Creditmemo\BankRepository;
use SM\Sales\Model\Creditmemo\RequestFormData;

class RequestForm extends Template
{
    /**
     * @var RequestFormData
     */
    private $requestFormData;

    /**
     * @var SessionFactory
     */
    private $sessionFactory;

    /**
     * @var Data
     */
    private $priceHelper;
    /**
     * @var BankRepository
     */
    private $bankRepository;

    /**
     * RequestForm constructor.
     * @param Template\Context $context
     * @param SessionFactory $sessionFactory
     * @param RequestFormData $requestFormData
     * @param Data $priceHelper
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        SessionFactory $sessionFactory,
        RequestFormData $requestFormData,
        Data $priceHelper,
        BankRepository $bankRepository,
        array $data = []
    ) {
        $this->requestFormData = $requestFormData;
        $this->sessionFactory = $sessionFactory;
        $this->priceHelper = $priceHelper;
        $this->bankRepository = $bankRepository;
        parent::__construct($context, $data);
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        if ($creditmemoId = $this->getRequest()->getParam('creditmemo_id')) {
            if ($this->requestFormData->setFormData($creditmemoId)
                && $this->validateCustomer()) {
                return parent::_toHtml();
            }
        }
        return '';
    }

    /**
     * @return bool
     */
    protected function validateCustomer(): bool
    {
        $customer = $this->sessionFactory->create();
        return $this->requestFormData->validateCustomer($customer->getId());
    }


    /**
     * @return string
     */
    public function getFormActionUrl(): string
    {
        if (!$this->isSubmitted()) {
            return $this->getUrl('sales/creditmemo/submitform');
        }

        return '';
    }

    /**
     * @return string
     */
    public function getTotalRefund(): string
    {
        return $this->requestFormData->getTotalRefund();
    }

    /**
     * @return string
     */
    public function getReferenceNumber(): string
    {
        return $this->requestFormData->getReferenceNumber();
    }

    /**
     * @return string
     */
    public function getCreditmemoId(): string
    {
        return $this->requestFormData->getCreditmemoId();
    }

    /**
     * @return \Magento\Sales\Api\Data\CreditmemoInterface
     */
    public function getCreditmemo(): \Magento\Sales\Api\Data\CreditmemoInterface
    {
        return $this->requestFormData->getCreditmemo();
    }

    /**
     * @return bool
     */
    public function isSubmitted(): bool
    {
        return $this->getCreditmemo()->getCreditmemoStatus() == FormInformationInterface::SUBMITTED_VALUE;
    }

    /**
     * @return string
     */
    public function getOrderUrl(): string
    {
        $order = $this->requestFormData->getOrder();
        if ($order->getIsVirtual()) {
            $orderDetailRouter = 'sales/order/digital';
        } else {
            $orderDetailRouter = 'sales/order/physical';
        }
        return $this->getUrl($orderDetailRouter, ['id' => $order->getData('parent_order')]);
    }

    /**
     * @param $value
     * @return float|string
     */
    public function formatCurrency($value)
    {
        return $this->priceHelper->currency($value, true, false);
    }

    /**
     * @return \SM\Sales\Model\Creditmemo\Data\Bank[]
     */
    public function getBanks()
    {
        return $this->bankRepository->getList();
    }
}
