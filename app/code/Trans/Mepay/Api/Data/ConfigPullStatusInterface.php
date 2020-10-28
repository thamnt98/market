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

namespace Trans\Mepay\Api\Data;

interface ConfigPullStatusInterface 
{
  /**
   * @var string
   */
  const ID = 'row_id';

  /**
   * @var string
   */
  const CONFIG_NAME = 'config_name';

  /**
   * @var string
   */
  const CONFIG_OFFSET = 'config_offset';

  /**
   * @var string
   */
  const CONFIG_LIMIT = 'config_limit';

  /**
   * @var string
   */
  const TABLE_NAME = 'trans_mepay_pull_status_config';

  /**
   * Get config name
   * @return string
   */
  public function getConfigName();

  /**
   * Set config name
   * @param string $data
   * @return  void
   */
  public function setConfigName(string $data);

  /**
   * Get config offset
   * @return int
   */
  public function getConfigOffset();

  /**
   * Set config offset
   * @param int $data
   * @return  void
   */
  public function setConfigOffset(int $data);

  /**
   * Get config limit
   * @return int
   */
  public function getConfigLimit();

  /**
   * Set config limit
   * @param int $data
   */
  public function setConfigLimit(int $data);
}