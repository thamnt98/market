<?php
/**
 * @category Trans
 * @package  Trans_Sprint
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Imam Kusuma <imam.kusuma@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */
namespace Trans\Sprint\Model;

use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment\Transaction\BuilderInterface;
use Magento\Sales\Model\Service\InvoiceService;
use Trans\Sprint\Helper\Config;

/**
 * Class PaymentNotify
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class PaymentNotify implements \Trans\Sprint\Api\PaymentNotifyInterface
{
    /**
     * @var \Trans\Sprint\Helper\Config
     */
    protected $config;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $orderFactory;

    /**
     * @var InvoiceService
     */
    protected $invoiceService;

    /**
     * @var BuilderInterface
     */
    protected $builderInterface;

    /**
     * @var \Magento\Sales\Model\Order\Email\Sender\InvoiceSender
     */
    protected $invoiceSender;

    /**
     * @var \Trans\Sprint\Api\Data\SprintPaymentFlagInterfaceFactory
     */
    protected $flagFactory;

    /**
     * @var \Magento\Sales\Api\OrderManagementInterface
     */
    protected $orderManagement;

    /**
     * @var \Trans\Sprint\Helper\Data
     */
    protected $dataHelper;

    /**
     * @var \Trans\Sprint\Api\SprintPaymentFlagRepositoryInterface
     */
    protected $flagRepository;

    /**
     * @var \Trans\Sprint\Api\SprintResponseRepositoryInterface
     */
    protected $responseRepository;

    /**
     * @var EventManager
     */
    protected $eventManager;

    /**
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Magento\Sales\Api\OrderManagementInterface $orderManagement
     * @param BuilderInterface $builderInterface
     * @param InvoiceService $invoiceService
     * @param \Magento\Sales\Model\Order\Email\Sender\InvoiceSender $invoiceSender
     * @param \Trans\Sprint\Api\Data\SprintPaymentFlagInterfaceFactory $flagFactory
     * @param \Trans\Sprint\Api\SprintPaymentFlagRepositoryInterface $flagRepository
     * @param \Trans\Sprint\Api\SprintResponseRepositoryInterface $responseRepository
     * @param \Trans\Sprint\Helper\Data $dataHelper
     * @param \Trans\Sprint\Helper\SalesOrder $orderHelper
     * @param EventManager $eventManager
     */
    public function __construct(
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Sales\Api\OrderManagementInterface $orderManagement,
        \Magento\Sales\Api\Data\OrderStatusHistoryInterfaceFactory $orderStatusHistoryInterfaceFactory,
        \Magento\Sales\Api\OrderStatusHistoryRepositoryInterface $orderStatusHistoryRepoInterface,
        BuilderInterface $builderInterface,
        InvoiceService $invoiceService,
        \Magento\Sales\Model\Order\Email\Sender\InvoiceSender $invoiceSender,
        \Trans\Sprint\Api\Data\SprintPaymentFlagInterfaceFactory $flagFactory,
        \Trans\Sprint\Api\SprintPaymentFlagRepositoryInterface $flagRepository,
        \Trans\Sprint\Api\SprintResponseRepositoryInterface $responseRepository,
        \Trans\Sprint\Helper\Data $dataHelper,
        \Trans\Sprint\Helper\SalesOrder $orderHelper,
        EventManager $eventManager,
        \Trans\IntegrationOrder\Api\IntegrationOrderPaymentRepositoryInterface $orderPaymentRepo
    ) {
        $this->orderHelper                        = $orderHelper;
        $this->orderStatusHistoryInterfaceFactory = $orderStatusHistoryInterfaceFactory;
        $this->orderStatusHistoryRepoInterface    = $orderStatusHistoryRepoInterface;
        $this->dataHelper                         = $dataHelper;
        $this->orderFactory                       = $orderFactory;
        $this->orderManagement                    = $orderManagement;
        $this->builderInterface                   = $builderInterface;
        $this->invoiceService                     = $invoiceService;
        $this->invoiceSender                      = $invoiceSender;
        $this->responseRepository                 = $responseRepository;
        $this->flagRepository                     = $flagRepository;
        $this->flagFactory                        = $flagFactory;
        $this->eventManager                       = $eventManager;
        $this->orderPaymentRepo                   = $orderPaymentRepo;

        $this->logger = $this->dataHelper->getLogger();
        $this->config = $this->dataHelper->getConfigHelper();
    }

    /**
     * {@inheritdoc}
     */
    public function processingNotify($postData)
    {
        $this->logger->info('Process Notify Start');

        $this->savePaymentFlag($postData);
        $this->updateResponse($postData);

        $orderSuccessState = $this->config->getPaidState() ? $this->config->getPaidState() : Order::STATE_PROCESSING;

        $this->logger->info('Transaction Status = ' . $postData['transactionStatus']);
        $this->logger->info('Transaction Message = ' . $postData['transactionMessage']);

        if ($postData['transactionStatus']) {
            $transactionNo = $postData['transactionNo'];
            $this->logger->info('TransactionNo = ' . $postData['transactionNo']);

            $suborders = $this->orderHelper->getSubOrders($transactionNo);

            $paymentSuccess = $postData['transactionStatus'] === Config::PAYMENT_FLAG_SUCCESS_CODE || $postData['transactionStatus'] === Config::TRANSACTION_STATUS_SUCCESS;

            $this->logger->info('$paymentSuccess: ' . $paymentSuccess);
            try {
                $mainOrder = $this->orderHelper->getMainOrder($transactionNo);
            } catch (\Exception $e) {
                $mainOrder = $this->orderHelper->getOrderByIncrementId($transactionNo);
            }

            if ($paymentSuccess) {
                $invoice = $this->saveInvoice($mainOrder);
                $this->saveOrderPaymentTransaction($mainOrder, $postData, $invoice);
            }

            if (!$postData['transactionStatus'] != Config::PAYMENT_FLAG_DECLINED_02) {
                if ($mainOrder instanceof \Magento\Sales\Api\Data\OrderInterface) {
                    // $orderEntityId = $mainOrder->getEntityId();
                    $this->orderCancelled($mainOrder);
                    // $orderHistory = $this->orderStatusHistoryInterfaceFactory->create();
                    // if ($this->orderCancelled($mainOrder)) {
                    //     $orderHistory->setParentId($orderEntityId);
                    //     $orderHistory->setStatus('canceled');
                    //     $this->orderStatusHistoryRepoInterface->save($orderHistory);
                    // }
                    /**
                     * Digital Order is not sent to OMS
                     * Reference: APO-1418
                     */
                    if (!$mainOrder->getIsVirtual()) {
                        $this->updateStatusToOms(
                            $postData['transactionNo'],
                            Config::OMS_CANCEL_PAYMENT_ORDER
                        );
                    }
                }
            }

            foreach ($suborders as $order) {
                $orderIncrementId = $order->getIncrementId();

                $this->logger->info('$orderIncrementId = ' . $orderIncrementId);
                if ($orderIncrementId) {
                    $dataOrder = $this->orderFactory->create()->loadByIncrementId($orderIncrementId);
                } else {
                    $this->logger->info('Order not found');
                    continue;
                }

                switch ($paymentSuccess) {
                    case true:
                        $message = __($this->dataHelper->serializeJson($postData));

                        // $payment = $dataOrder->getPayment();
                        // $payment->setLastTransactionId($orderIncrementId);
                        // $payment->setTransactionId($orderIncrementId);
                        // $payment->setAdditionalInformation([\Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS => (array) $postData]);
                        // $payment->setCcNumberEnc(isset($postData['cardNo']) ? $postData['cardNo'] : '');

                        // $trans = $this->builderInterface;
                        // $transaction = $trans->setPayment($payment)
                        //  ->setOrder($dataOrder)
                        //  ->setTransactionId($postData['transactionNo'])
                        //  ->setAdditionalInformation([\Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS => (array) $postData])
                        //  ->setFailSafe(true)
                        //  ->build(\Magento\Sales\Model\Order\Payment\Transaction::TYPE_CAPTURE);

                        // $payment->addTransactionCommentsToOrder($transaction, $message);

                        // $invoice = $this->saveInvoice($dataOrder, $postData);

                        $comment  = __('Create invoice fail');
                        $notified = false;
                        if ($invoice instanceof \Magento\Sales\Model\Order\Invoice) {
                            $comment  = __('Notified customer about invoice #%1.', $invoice->getId());
                            $notified = true;
                        }

                        $dataOrder->addStatusHistoryComment(
                            $comment
                        )->setIsCustomerNotified($notified);

                        /** change order status */
                        $dataOrder->setState($orderSuccessState);
                        $dataOrder->setStatus($orderSuccessState);

                        try {
                            // $payment->save();
                            $dataOrder->save();
                            // $transaction->save();
                            $this->logger->info('Transaction Message = ' . $postData['transactionMessage']);

                            /**
                             * Digital Order is not sent to OMS
                             * Reference: APO-1418
                             */
                            if (!$dataOrder->getIsVirtual()) {
                                $this->updateStatusToOms($postData['transactionNo'], Config::OMS_SUCCESS_PAYMENT_OPRDER);
                            }
                        } catch (\Exception $exception) {
                            throw new CouldNotSaveException(
                                new Phrase(__('Could not save the data: %1', $exception->getMessage()))
                            );
                        }
                        break;

                    default:
                        if (!$postData['transactionStatus'] != Config::PAYMENT_FLAG_DECLINED_02) {
                            $this->orderCancelled($dataOrder);

                            /**
                             * Digital Order is not sent to OMS
                             * Reference: APO-1418
                             */
                            if (!$dataOrder->getIsVirtual()) {
                                $this->updateStatusToOms(
                                    $postData['transactionNo'],
                                    Config::OMS_CANCEL_PAYMENT_ORDER
                                );
                            }
                        }
                        break;
                }
            }
        }

        $this->logger->info('Process Notify End');

        return true;
    }

    /**
     * Order cancelled
     *
     * @param \Magento\Sales\Model\OrderFactory $order
     */
    protected function orderCancelled($order)
    {
        $this->orderManagement->cancel($order->getId());
        return true;
    }

    /**
     * Save payment flag data
     *
     * @param array $postData
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function savePaymentFlag($postData)
    {
        $this->logger->info('Save Payment Flag Start');
        if ($postData) {
            try {
                $paymentFlag = $this->flagRepository->getByTransactionNo($postData['transactionNo']);
            } catch (\Exception $e) {
                $paymentFlag = $this->flagFactory->create();
            }

            try {
                $data['currency']            = isset($postData['currency']) ? $postData['currency'] : null;
                $data['transaction_no']      = $postData['transactionNo'];
                $data['transaction_amount']  = $postData['transactionAmount'];
                $data['transaction_date']    = $postData['transactionDate'];
                $data['channel_type']        = isset($postData['channelType']) ? $postData['channelType'] : null;
                $data['transaction_feature'] = isset($postData['transactionFeature']) ? $postData['transactionFeature'] : null;
                $data['transaction_status']  = $postData['transactionStatus'];
                $data['transaction_message'] = $postData['transactionMessage'];
                $data['customer_account']    = isset($postData['customerAccount']) ? $postData['customerAccount'] : null;
                $data['card_token']          = isset($postData['cardToken']) ? $postData['cardToken'] : null;
                $data['card_token_use']      = isset($postData['cardTokenUse']) ? $postData['cardTokenUse'] : null;
                $data['card_no']             = isset($postData['cardNo']) ? $postData['cardNo'] : null;
                $data['flag_type']           = isset($postData['flagType']) ? $postData['flagType'] : null;
                $data['insert_id']           = isset($postData['insertId']) ? $postData['insertId'] : null;
                $data['payment_reff_id']     = isset($postData['paymentReffId']) ? $postData['paymentReffId'] : null;
                $data['auth_code']           = isset($postData['authCode']) ? $postData['authCode'] : null;
                $data['additional_data']     = isset($postData['additionalData']) ? $postData['additionalData'] : null;

                $paymentFlag->addData($data);
                $this->flagRepository->save($paymentFlag);
            } catch (\Exception $e) {
                $this->logger->info('Generate error = ' . $e->getMessage());
            }
        }
        $this->logger->info('Save Payment Flag End');
    }

    /**
     * Update response data flag
     *
     * @param array $postData
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function updateResponse($postData)
    {
        $this->logger->info('Update Response Data Start');
        if ($postData) {
            try {
                $responseRepository = $this->responseRepository->getByTransactionNo($postData['transactionNo']);

                if (isset($postData['transactionStatus']) && $postData['transactionStatus'] === Config::PAYMENT_FLAG_SUCCESS_CODE) {
                    $responseRepository->setFlag('success');
                }

                if (isset($postData['transactionStatus']) && $postData['transactionStatus'] === Config::PAYMENT_FLAG_DECLINED_02) {
                    $responseRepository->setFlag('success');
                }

                if (isset($postData['transactionStatus']) && $postData['transactionStatus'] === Config::PAYMENT_FLAG_DECLINED_04) {
                    $responseRepository->setFlag('expired');
                }

                if (isset($postData['transactionStatus']) && $postData['transactionStatus'] === Config::PAYMENT_FLAG_DECLINED_05) {
                    $responseRepository->setFlag('cancelled');
                }

                if (isset($postData['transactionStatus']) && $postData['transactionStatus'] === Config::TRANSACTION_STATUS_SUCCESS) {
                    $responseRepository->setFlag('success');
                }

                $this->logger->info('ID = ' . $responseRepository->getId());
                $this->logger->info('Transaction No = ' . $postData['transactionNo']);
                $this->responseRepository->save($responseRepository);
            } catch (\Exception $e) {
                $this->logger->info('Generate error = ' . $e->getMessage());
            }
        }
        $this->logger->info('Update Response Data End');
    }

    /**
     * Save invoice and send invoice notification
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $mainOrder
     * @return \Magento\Sales\Model\Order\Invoice|bool
     */
    protected function saveInvoice($mainOrder)
    {
        $this->logger->info('****** start send invoice ******');

        if ($mainOrder instanceof \Magento\Sales\Api\Data\OrderInterface) {
            try {
                $invoice = $this->invoiceService->prepareInvoice($mainOrder);
                $invoice->setGrandTotal($mainOrder->getData('grand_total'));
                $invoice->setBaseGrandTotal($mainOrder->getData('base_grand_total'));
                $invoice->register();

                $invoice->save();

                // send invoice
                $this->logger->info('****** send invoice ******');
                $this->invoiceSender->send($invoice);
                $this->logger->info('****** end send invoice ******');

                return $invoice;
            } catch (\Exception $e) {
                $this->logger->info('Error ' . __FUNCTION__ . ' ' . $e->getMessage());
            } catch (LocalizedException $e) {
                $this->logger->info('Error ' . __FUNCTION__ . ' ' . $e->getMessage());
            }
        }

        $this->logger->info('****** send invoice fail ******');
        $this->logger->info('****** end send invoice ******');
        return false;
    }

    /**
     * Save payment transaction main order (is_parent == 1)
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $dataOrder
     * @param array $postData
     * @return bool
     */
    protected function saveOrderPaymentTransaction($dataOrder, $postData, $invoice)
    {
        $this->logger->info('****** start saveMainOrderPaymentTransaction ******');
        $orderSuccessState = $this->config->getPaidState() ? $this->config->getPaidState() : Order::STATE_PROCESSING;

        if ($dataOrder instanceof \Magento\Sales\Api\Data\OrderInterface) {
            $payment = $dataOrder->getPayment();
            $payment->setLastTransactionId($dataOrder->getData('increment_id'));
            $payment->setTransactionId($dataOrder->getData('increment_id'));
            $payment->setAdditionalInformation([\Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS => (array) $postData]);
            $payment->setCcNumberEnc(isset($postData['cardNo']) ? $postData['cardNo'] : '');

            $message = __($this->dataHelper->serializeJson($postData));

            $trans       = $this->builderInterface;
            $transaction = $trans->setPayment($payment)
                ->setOrder($dataOrder)
                ->setTransactionId($postData['transactionNo'])
                ->setAdditionalInformation([\Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS => (array) $postData])
                ->setFailSafe(true)
                ->build(\Magento\Sales\Model\Order\Payment\Transaction::TYPE_CAPTURE);

            $payment->addTransactionCommentsToOrder($transaction, $message);

            // $invoice = $this->saveInvoice($dataOrder, $postData);

            $comment  = __('Create invoice fail');
            $notified = false;
            if ($invoice instanceof \Magento\Sales\Model\Order\Invoice) {
                $comment  = __('Notified customer about invoice #%1.', $invoice->getId());
                $notified = true;
            }

            $dataOrder->addStatusHistoryComment(
                $comment
            )->setIsCustomerNotified($notified);

            /** change order status */
            $dataOrder->setState($orderSuccessState);
            $dataOrder->setStatus($orderSuccessState);

            try {
                /**
                 * Handle Digital success or fail
                 * Reference: APO-1418, APO-3123
                 */
                $this->eventManager->dispatch(
                    'trans_sprint_payment_success_update_before',
                    ['order' => $dataOrder]
                );

                $payment->save();
                $dataOrder->save();
                $transaction->save();

                $this->logger->info('****** end saveMainOrderPaymentTransaction ******');

                return true;
            } catch (\Exception $e) {
                $this->logger->info('Error saveMainOrderPaymentTransaction ' . $e->getMessage());
                $this->logger->info('****** end saveMainOrderPaymentTransaction ******');
                return false;
            }
        }

        $this->logger->info('****** end saveMainOrderPaymentTransaction ******');
        return false;
    }

    /**
     * Event pre dispatch for hit status to OMS
     * @param string $orderId
     * @param string $status
     */
    protected function updateStatusToOms($orderNo, $status)
    {

        $this->eventManager->dispatch(
            'update_payment_oms',
            [
                'reference_number' => $orderNo,
                'payment_status' => $status,
            ]
        );
    }
}
