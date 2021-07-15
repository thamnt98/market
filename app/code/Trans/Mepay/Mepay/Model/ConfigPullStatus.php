<?php
/**
 * @category Trans
 * @package  Trans_Mepay
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author  Anan Fauzi <anan.fauzi@transdigital.co.id>
 *
 * Copyright © 2019 CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */
namespace Trans\Mepay\Model;

use Magento\Framework\Model\AbstractModel;
use Trans\Mepay\Model\ResourceModel\ConfigPullStatus as ResourceModel;
use Trans\Mepay\Api\Data\ConfigPullStatusInterface;

class ConfigPullStatus extends AbstractModel implements ConfigPullStatusInterface
{
  /**
   * @return void
   * @SuppressWarnings(PHPMD)
   */
  protected function _construct() {
    $this->_init(ResourceModel::class);
  }

  /**
   * @inheritdoc
   */
  public function getConfigName()
  {
    return $this->_getData(ConfigPullStatusInterface::CONFIG_NAME);
  }

  /**
   * @inheritdoc
   */
  public function setConfigName(string $data)
  {
    $this->setData(ConfigPullStatusInterface::CONFIG_NAME, $data);
  }

  /**
   * @inheritdoc
   */
  public function getConfigOffset()
  {
    return $this->_getData(ConfigPullStatusInterface::CONFIG_OFFSET);
  }

  /**
   * @inheritdoc
   */
  public function setConfigOffset(int $data)
  {
    $this->setData(ConfigPullStatusInterface::CONFIG_OFFSET, $data);
  }

  /**
   * @inheritdoc
   */
  public function getConfigLimit()
  {
    return $this->_getData(ConfigPullStatusInterface::CONFIG_LIMIT);
  }

  /**
   * @inheritdoc
   */
  public function setConfigLimit(int $data)
  {
    $this->setData(ConfigPullStatusInterface::CONFIG_LIMIT, $data);
  }

}