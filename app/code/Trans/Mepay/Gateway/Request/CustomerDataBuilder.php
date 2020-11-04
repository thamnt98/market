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
namespace Trans\Mepay\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Trans\Mepay\Logger\Logger;

class CustomerDataBuilder implements BuilderInterface
{
  /**
   * @var string
   */
  const CUSTOMER = 'customer';

  /**
   * @var string
   */
  const NAME = 'name';

  /**
   * @var string
   */
  const EMAIL = 'email';

  /**
   * @var string
   */
  const PHONE_NUMBER = 'phoneNumber';

  /**
   * @var string
   */
  const COUNTRY = 'country';

  /**
   * @var string
   */
  const POSTAL_CODE = 'postalCode';

  /**
   * @var SubjectReader
   */
  private $subjectReader;

  /**
   * @var Logger
   */
  private $logger;

  /**
   * Constructor
   * @param SubjectReader $subjectReader
   * @param Logger        $logger
   */
  public function __construct(
    SubjectReader $subjectReader,
    Logger $logger
  ) {
      $this->subjectReader = $subjectReader;
      $this->logger = $logger;
  }

  /**
   * @inheritdoc
   */
  public function build(array $buildSubject)
  {
    $paymentDO = $this->subjectReader->readPayment($buildSubject);
    $order = $paymentDO->getOrder();
    $billingAddress = $order->getBillingAddress();
    return [
        self::CUSTOMER => [
            self::NAME => $billingAddress->getFirstName(),
            self::COUNTRY => $billingAddress->getCountryId(),
            self::POSTAL_CODE => $billingAddress->getPostcode(),
            self::PHONE_NUMBER => $billingAddress->getTelephone(),
            self::EMAIL => $billingAddress->getEmail(),
        ]
    ];
  }

  /**
   * Get customer
   * @param  \Magento\Sales\Api\Data\OrderInterface $order
   * @return array
   */
  public function getCustomer($order)
  {
   $billingAddress = $order->getBillingAddress();
    return [
        self::CUSTOMER => [
            self::NAME => $billingAddress->getFirstName(),
            self::COUNTRY => $billingAddress->getCountryId(),
            self::POSTAL_CODE => $billingAddress->getPostcode(),
            self::PHONE_NUMBER => $billingAddress->getTelephone(),
            self::EMAIL => $billingAddress->getEmail(),
        ]
    ]; 
  }
}
