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
namespace Trans\Mepay\Model\Webhook\Inquiry\Order;

use Magento\Framework\DataObject;
use Trans\Mepay\Api\Data\InquiryOrderItemsInterface;

class Items extends DataObject implements InquiryOrderItemsInterface
{
  /**
   * @inheritdoc
   */
  public function getName()
  {
    return $this->_getData(InquiryOrderItemsInterface::NAME);
  }

  /**
   * @inheritdoc
   */
  public function setName($data)
  {
    return $this->_getData(InquiryOrderItemsInterface::NAME);
  }

  /**
   * @inheritdoc
   */
  public function getQuantity()
  {
    return $this->_getData(InquiryOrderItemsInterface::QUANTITY);
  }

  /**
   * @inheritdoc
   */
  public function setQuantity($data)
  {
    return $this->_getData(InquiryOrderItemsInterface::QUANTITY);
  }

  /**
   * @inheritdoc
   */
  public function getAmount()
  {
    return $this->_getData(InquiryOrderItemsInterface::AMOUNT);
  }

  /**
   * @inheritdoc
   */
  public function setAmount($data)
  {
    return $this->_getData(InquiryOrderItemsInterface::AMOUNT);
  }
}