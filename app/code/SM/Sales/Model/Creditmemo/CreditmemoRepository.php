<?php
/**
 * Class CreditmemoRepository
 * @package SM\Sales\Model\Creditmemo
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\Sales\Model\Creditmemo;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Webapi\Exception;
use \SM\Sales\Api\Data\Creditmemo\FormInformationInterface;
use \SM\Sales\Api\Data\Creditmemo\BankInterface;

class CreditmemoRepository implements \SM\Sales\Api\CreditmemoRepositoryInterface
{
    /**
     * @var \SM\Sales\Api\Data\Creditmemo\FormInformationInterfaceFactory
     */
    private $formInformationFactory;

    /**
     * @var \Magento\Sales\Api\CreditmemoRepositoryInterface
     */
    private $creditmemoRepository;

    /**
     * @var \SM\Sales\Api\Data\Creditmemo\BankInterfaceFactory
     */
    private $bankFactory;

    /**
     * @var \SM\Sales\Model\Creditmemo\SendToJira
     */
    private $sendToJira;

    /**
     * @var RequestFormData
     */
    private $requestFormData;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * CreditmemoRepository constructor.
     * @param \SM\Sales\Api\Data\Creditmemo\FormInformationInterfaceFactory $formInformationFactory
     * @param \Magento\Sales\Api\CreditmemoRepositoryInterface $creditmemoRepository
     * @param CustomerRepositoryInterface $customerRepository
     * @param \SM\Sales\Api\Data\Creditmemo\BankInterfaceFactory $bankFactory
     * @param SendToJira $sendToJira
     * @param RequestFormData $requestFormData
     */
    public function __construct(
        \SM\Sales\Api\Data\Creditmemo\FormInformationInterfaceFactory $formInformationFactory,
        \Magento\Sales\Api\CreditmemoRepositoryInterface $creditmemoRepository,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \SM\Sales\Api\Data\Creditmemo\BankInterfaceFactory $bankFactory,
        \SM\Sales\Model\Creditmemo\SendToJira $sendToJira,
        \SM\Sales\Model\Creditmemo\RequestFormData $requestFormData
    ) {
        $this->formInformationFactory = $formInformationFactory;
        $this->creditmemoRepository = $creditmemoRepository;
        $this->bankFactory = $bankFactory;
        $this->sendToJira = $sendToJira;
        $this->requestFormData = $requestFormData;
        $this->customerRepository = $customerRepository;
    }

    /**
     * @inheritDoc
     * @throws \Magento\Framework\Webapi\Exception
     */
    public function getFormInfo($customerId, $creditmemoId)
    {
        /**
         * @var \SM\Sales\Model\Creditmemo\Data\FormInformation $formData
         */
        $formData = $this->formInformationFactory->create();

        $formData->setData(FormInformationInterface::BANKS, $this->getBanks())
            ->setData(FormInformationInterface::IS_SUBMITTED, false);

        try {
            $creditmemo = $this->creditmemoRepository->get($creditmemoId);
            $formData->setData(FormInformationInterface::TOTAL_REFUND, $creditmemo->getGrandTotal())
                ->setData(FormInformationInterface::ORDER_ID, $creditmemo->getOrderId())
                ->setData(FormInformationInterface::PARENT_ORDER_ID, $creditmemo->getOrder()->getData('parent_order'))
                ->setData(
                    FormInformationInterface::REFERENCE_NUMBER,
                    $creditmemo->getOrder()->getData('reference_number')
                );
            if ($creditmemo->getCreditmemoStatus() == FormInformationInterface::SUBMITTED_VALUE) {
                $formData->setData(FormInformationInterface::IS_SUBMITTED, true);
            }
        } catch (\Exception $e) {
            $this->throwException($e->getMessage());
        }
        return $formData;
    }

    /**
     * @inheritDoc
     * @throws \Magento\Framework\Webapi\Exception
     */
    public function submitRequest($customerId, $data)
    {
        if ($data && isset($data['creditmemo_id'])) {
            try {
                $this->requestFormData->setFormData($data['creditmemo_id']);
                $creditmemo = $this->requestFormData->getCreditmemo();
                $this->sendToJira->setCustomer($this->customerRepository->getById($customerId));
                $data = $this->prepareParams((array)$data);
                $isSubmitted = $creditmemo->getCreditmemoStatus() == FormInformationInterface::SUBMITTED_VALUE;

                if ($isSubmitted) {
                    throw new \Exception('Something went wrong while summiting your request');
                }
                if ($this->sendToJira->send($data)) {
                    return true;
                } else {
                    throw new \Exception('Something went wrong while summiting your request');
                }
            } catch (\Exception $e) {
                $this->throwException($e->getMessage());
            }
        }

        $this->throwException('Something went wrong while summiting your request');
    }

    /**
     * @param array $params
     * @return array
     * @throws Exception
     */
    protected function prepareParams(array $params): array
    {
        return [
            RequestFormData::BANK_KEY => $this->prepareBankName($params['bank']),
            RequestFormData::ACCOUNT_NAME_KEY => $params['account_name'],
            RequestFormData::ACCOUNT_KEY => (int) $params['account_number'],
            RequestFormData::ORDER_REFERENCE_NUMBER_KEY =>  $this->requestFormData->getReferenceNumber(),
            RequestFormData::PAYMENT_NUMBER_KEY  => $this->requestFormData->getPaymentNumber(),
            RequestFormData::TOTAL_REFUND_KEY => (int) $this->requestFormData->getTotalRefund(),
            RequestFormData::CREDITMEMO_ID_KEY => (int)$this->requestFormData->getCreditmemoId(),
            RequestFormData::PAYMENT_METHOD_KEY => $this->requestFormData->getOrder()->getPayment()->getMethod()
        ];
    }

    /**
     * @param $message
     * @throws \Magento\Framework\Webapi\Exception
     */
    private function throwException($message)
    {
        throw new \Magento\Framework\Webapi\Exception(
            __($message),
            0,
            \Magento\Framework\Webapi\Exception::HTTP_BAD_REQUEST
        );
    }

    /**
     * @return Data\Bank[]
     */
    private function getBanks()
    {
        /**
         * @var \SM\Sales\Model\Creditmemo\Data\Bank $bank
         */
        $bank = $this->bankFactory->create();
        $bank->setData(BankInterface::CODE, 'bank_mega');
        $bank->setData(BankInterface::NAME, 'Bank Mega');

        return [$bank];
    }

    /**
     * @param $bank
     * @return mixed
     * @throws \Exception
     */
    private function prepareBankName($bank)
    {
        foreach ($this->getBanks() as $bankObject) {
            if ($bank == $bankObject->getCode()) {
                return $bank;
            }
        }
        throw new \Exception('Invalid Bank Name');
    }
}
