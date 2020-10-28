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
namespace Trans\Mepay\Controller\Payment;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\RequestInterface;
use Trans\Mepay\Helper\Payment\Transaction;
use Trans\Mepay\Helper\Response\Response;

abstract class AbstractAction extends Action
{
  /**
   * @var Session
   */
  protected $checkoutSession;

  /**
   * @var Transaction
   */
  protected $transaction;

  /**
   * @var Response
   */
  protected $response;

  /**
   * Constructor
   * @param Context     $context
   * @param Session     $checkoutSession
   * @param Transaction $transaction
   * @param Response    $response
   */
  public function __construct(
    Context $context,
    Session $checkoutSession,
    Transaction $transaction,
    Response $response
  ) {
    $this->checkoutSession = $checkoutSession;
    $this->transaction = $transaction;
    $this->response = $response;
    parent::__construct($context);
  }

    /**
     * @return string
     */
    public function getOrderIncrementId()
    {
        return $this->checkoutSession->getLastRealOrderId();
    }

    /**
     * @return mixed
     */
    public function getOrderId()
    {
        return $this->transaction->getOrderByIncrementId($this->getOrderIncrementId())->getId();
    }

  /**
   * Csrf Validation Exception
   * @param  RequestInterface $request
   * @return mixing
   */
    public function createCsrfValidationException(RequestInterface $request)
    {
        return null;
    }

    /**
     * Validate for Csrf
     * @param  RequestInterface $request
     * @return boolean
     */
    public function validateForCsrf(RequestInterface $request)
    {
        return true;
    }
}
