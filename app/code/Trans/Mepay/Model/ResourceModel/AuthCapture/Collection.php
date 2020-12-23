<?php 
/**
 * @category Trans
 * @package  Trans_Mepay
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author  Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 *
 * Copyright © 2020 CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Mepay\Model\ResourceModel\AuthCapture;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Trans\Mepay\Api\Data\AuthCaptureInterface;
use Trans\Mepay\Model\Data\AuthCapture as MainModel;
use Trans\Mepay\Model\ResourceModel\AuthCapture as ResourceModel;

/**
 * Collection
 */
class Collection extends AbstractCollection
{
  /**
   * @var string
   */
  protected $_idFieldName = AuthCaptureInterface::ID;

  /**
   * @inheritdoc
   */
  protected function _construct() {
    $this->_init(MainModel::class, ResourceModel::class);
  }
}