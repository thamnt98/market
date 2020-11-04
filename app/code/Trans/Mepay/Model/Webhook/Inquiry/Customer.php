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
namespace Trans\Mepay\Model\Webhook\Inquiry;

use Magento\Framework\DataObject;
use Trans\Mepay\Api\Data\InquiryCustomerInterface;

class Customer extends DataObject implements InquiryCustomerInterface
{
   /**
   * @inheritdoc
   */
  public function getName()
  {
    return $this->_getData(InquiryCustomerInterface::NAME);
  }

  /**
   * @inheritdoc
   */
  public function setName($data)
  {
    $this->setData(InquiryCustomerInterface::NAME, $data);
  }

  /**
   * @inheritdoc
   */
  public function getEmail()
  {
    return $this->_getData(InquiryCustomerInterface::EMAIL);
  }

  /**
   * @inheritdoc
   */
  public function setEmail($data)
  {
    $this->setData(InquiryCustomerInterface::EMAIL, $data);
  }

  /**
   * @inheritdoc
   */
  public function getPhoneNumber()
  {
    return $this->_getData(InquiryCustomerInterface::PHONE_NUMBER);
  }

  /**
   * @inheritdoc
   */
  public function setPhoneNumber($data)
  {
    $this->setData(InquiryCustomerInterface::PHONE_NUMBER, $data);
  }

  /**
   * @inheritdoc
   */
  public function getCountry()
  {
    return $this->_getData(InquiryCustomerInterface::COUNTRY);
  }

  /**
   * @inheritdoc
   */
  public function setCountry($data)
  {
    $this->setData(InquiryCustomerInterface::COUNTRY, $data);
  }

  /**
   * @inheritdoc
   */
  public function getPostalCode()
  {
    return $this->_getData(InquiryCustomerInterface::POSTAL_CODE);
  }

  /**
   * @inheritdoc
   */
  public function setPostalCode($data)
  {
    $this->setData(InquiryCustomerInterface::POSTAL_CODE, $data);
  }

  /**
   * Validate
   * @return boolean
   */
  public function validate()
  {
    return true;
  }
}
