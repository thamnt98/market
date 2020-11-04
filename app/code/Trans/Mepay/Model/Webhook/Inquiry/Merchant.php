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
use Trans\Mepay\Api\Data\InquiryMerchantInterface;

class Merchant extends DataObject implements InquiryMerchantInterface
{
  /**
   * @inheritdoc
   */
  public function getId()
  {
    return $this->_getData(InquiryMerchantInterface::ID);
  }

   /**
   * @inheritdoc
   */
  public function setId($data)
  {
    $this->setData(InquiryMerchantInterface::ID, $data);
  }

   /**
   * @inheritdoc
   */
  public function getName()
  {
    return $this->_getData(InquiryMerchantInterface::NAME);
  }

  /**
   * @inheritdoc
   */
  public function setName($data)
  {
    $this->setData(InquiryMerchantInterface::NAME, $data);
  }

  /**
   * @inheritdoc
   */
  public function getStatus()
  {
    return $this->_getData(InquiryMerchantInterface::STATUS);
  }

  /**
   * @inheritdoc
   */
  public function setStatus($data)
  {
    $this->setData(InquiryMerchantInterface::STATUS, $data);
  }

  /**
   * @inheritdoc
   */
  public function getPartnerId()
  {
    return $this->_getData(InquiryMerchantInterface::PARTNER_ID);
  }

  /**
   * @inheritdoc
   */
  public function setPartnerId($data)
  {
    $this->setData(InquiryMerchantInterface::PARTNER_ID, $data);
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
