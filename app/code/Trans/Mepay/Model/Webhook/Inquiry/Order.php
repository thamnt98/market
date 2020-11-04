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
use Trans\Mepay\Api\Data\InquiryOrderInterface;
use Trans\Mepay\Api\Data\InquiryOrderItemsInterface;
use Trans\Mepay\Api\Data\InquiryOrderItemsInterfaceFactory;

class Order extends DataObject implements InquiryOrderInterface
{
  /**
   * @var \InquiryOrderItemsInterfaceFactory
   */
  protected $inquiryOrderItemsFactory;

  /**
   * Constructor
   * @param InquiryOrderItemsInterfaceFactory $inquiryOrderItemsFactory
   */
  public function __construct(InquiryOrderItemsInterfaceFactory $inquiryOrderItemsFactory)
  {
    $this->inquiryOrderItemsFactory = $inquiryOrderItemsFactory;
  }

  /**
   * @inheritdoc
   */
  public function getId()
  {
    return $this->_getData(InquiryOrderInterface::ID);
  }

  /**
   * @inheritdoc
   */
  public function setId($data)
  {
    $this->setData(InquiryOrderInterface::ID, $data);
  }

  /**
   * @inheritdoc
   */
  public function getItems()
  {
    return $this->_getData(InquiryOrderInterface::ITEMS);
  }

  /**
   * @inheritdoc
   */
  public function setItems($data)
  {
    $this->setData(InquiryOrderInterface::ITEMS, $data);
  }

  /**
   * @inheritdoc
   */
  public function getDisablePromo()
  {
    return $this->_getData(InquiryOrderInterface::DISABLE_PROMO);
  }

  /**
   * @inheritdoc
   */
  public function setDisablePromo($data)
  {
    $this->setData(InquiryOrderInterface::DISABLE_PROMO, $data);
  }

  /**
   * Validate
   * @return boolean
   */
  public function validate()
  {
    return true;
  }

  /**
   * Extract items
   * @param  array $input
   * @return InquiryOrderItemsInterface[]
   */
  public function extractItems($input)
  {
    $result = [];
    foreach ($input as $key => $value) {
      $item = $this->inquiryOrderItemsFactory->create();
      foreach ($value as $index => $data) {
        $item->setData($index, $data);
      }
      $result[] = $item;
    }
    return $result;
  }
}
