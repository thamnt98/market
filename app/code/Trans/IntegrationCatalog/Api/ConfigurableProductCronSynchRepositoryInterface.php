<?php
/**
 * @category Trans
 * @package  Trans_IntegrationCatalog
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Anan Fauzi <anan.fauzi@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationCatalog\Api;

use \Trans\IntegrationCatalog\Api\Data\ConfigurableProductCronSynchInterface;

interface ConfigurableProductCronSynchRepositoryInterface
{
  /**
	 * Get by Id
	 *
	 * @param  string $id
	 * @return ConfigurableProductCronSynchInterface
	 */
	public function getById(string $id);

  /**
	 * Get by Id
	 *
	 * @param  string $name
	 * @return ConfigurableProductCronSynchInterface
	 */
	public function getByName(string $name);

	/**
	 * Load by custom field
	 *
	 * @param  string $field
	 * @param  string $value
	 * @return ConfigurableProductCronSynchInterface[]
	 */
	public function loadBy(string $field, string $value);

	/**
	 * Save data
	 *
	 * @param  ConfigurableProductCronSynchInterface $data
	 * @return ConfigurableProductCronSynchInterface
	 */
	public function save(ConfigurableProductCronSynchInterface $data);

	/**
	 * Delete data
	 *
	 * @param  ConfigurableProductCronSynchInterface $data
	 * @return boolean
	 */
	public function delete(ConfigurableProductCronSynchInterface $data);
}
