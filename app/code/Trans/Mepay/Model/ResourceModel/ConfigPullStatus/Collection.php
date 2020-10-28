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
namespace Trans\Mepay\Model\ResourceModel\ConfigPullStatus;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Trans\Mepay\Api\Data\ConfigPullStatusInterface;
use Trans\Mepay\Model\ConfigPullStatus as MainModel;
use Trans\Mepay\Model\ResourceModel\ConfigPullStatus as ResourceModel;

class Collection extends AbstractCollection
{
  /**
   * @var string
   */
  protected $_idFieldName = ConfigPullStatusInterface::ID;

  /**
   * @inheritdoc
   */
  protected function _construct() {
    $this->_init(MainModel::class, ResourceModel::class);
  }
}