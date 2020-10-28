<?php
/**
 * @category Trans
 * @package  Trans_Mepay
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Anan Fauzi <anan.fauzi@transdigital.co.id>
 *
 * Copyright © 2020 PT CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */
namespace Trans\Mepay\Model;

use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\InvoiceRepositoryInterface;
use Magento\Sales\Model\Service\InvoiceService;
use Magento\Sales\Model\Order\Email\Sender\InvoiceSender;

class Invoice
{
  /**
   * @var InvoiceRepositoryInterface
   */
  protected $invoiceRepo;

  /**
   * @var InvoiceService
   */
  protected $invoiceService;

  /**
   * @var InvoiceSender
   */
  protected $invoiceSender;

  /**
   * Constructor
   * @param InvoiceRepositoryInterface $invoiceRepo
   * @param InvoiceService             $invoiceService
   * @param InvoiceSender              $invoiceSender
   */
  public function __construct(
    InvoiceRepositoryInterface $invoiceRepo,
    InvoiceService $invoiceService,
    InvoiceSender $invoiceSender
  ) {
    $this->invoiceRepo = $invoiceRepo;
    $this->invoiceService = $invoiceService;
    $this->invoiceSender = $invoiceSender;
  }

  /**
   * Create invoice
   * @param  \Magento\Sales\Api\Data\OrderInterfaceFactory $order
   * @param  \Trans\Mepay\Api\Data\TransactionInterface $transaction
   * @return \Magento\Sales\Api\Data\InvoiceInterface
   */
  public function create($order, $transaction)
  {
    $invoice = $this->invoiceService->prepareInvoice($order);
    $invoice->setGrandTotal($transaction->getAmount());
    $invoice->setBaseGrandTotal($transaction->getAmount());
    $invoice->register();
    $this->invoiceRepo->save($invoice);
    return $invoice; 
  }

  /**
   * Send
   * @param  \Magento\Sales\Api\Data\InvoiceInterface $invoice
   * @return void
   */
  public function send($invoice)
  {
    $this->invoiceSender->send($invoice);
  }
}