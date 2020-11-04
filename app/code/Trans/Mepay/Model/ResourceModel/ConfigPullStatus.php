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
namespace Trans\Mepay\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Trans\Mepay\Api\Data\ConfigPullStatusInterface;

class ConfigPullStatus extends AbstractDb
{
  /**
   * @inheritdoc
   */
  protected function _construct() {
    $this->_init(ConfigPullStatusInterface::TABLE_NAME, ConfigPullStatusInterface::ID);
  }
}