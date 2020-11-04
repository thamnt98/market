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

namespace Trans\Mepay\Api;

use Trans\Mepay\Api\Data\ConfigPullStatusInterface;

interface ConfigPullStatusRepositoryInterface
{
  /**
   * Get config by name
   * @param  string $name
   * @return ConfigPullStatusInterface
   */
  public function get(string $name);

  /**
   * Get config by id
   * @param  int    $id
   * @return ConfigPullStatusInterface
   */
  public function getById(int $id);

  /**
   * Save config
   * @param  ConfigPullStatusInterface $config
   * @return ConfigPullStatusInterface
   */
  public function save(ConfigPullStatusInterface $config);

  /**
   * Delete config
   * @param  ConfigPullStatusInterface $config
   * @return void
   */
  public function delete(ConfigPullStatusInterface $config);
}