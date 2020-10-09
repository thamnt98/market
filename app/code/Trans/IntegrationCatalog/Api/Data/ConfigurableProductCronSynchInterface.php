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

namespace Trans\IntegrationCatalog\Api\Data;

interface ConfigurableProductCronSynchInterface
{
	/**
	 * @var string
	 */
	const TABLE_NAME = 'integration_configurable_product_synch_cron';

	/**
	 * @var string
	 */
	const ID = 'row_id';

	/**
	 * @var string
	 */
	const CRON_NAME = 'cron_name';

	/**
	 * @var string
	 */
	const CRON_OFFSET = 'cron_offset';

	/**
	 * @var string
	 */
	const CRON_LENGTH = 'cron_length';

	/**
	 * @var string
	 */
	const LAST_UPDATE = 'last_updated';

	/**
	 * Get cron name
	 *
	 * @return string
	 */
	public function getCronName();

	/**
	 * Set cron name
	 *
	 * @param string $data
	 * @return void
	 */
	public function setCronName(string $data);

	/**
	 * Get offset
	 *
	 * @return string
	 */
	public function getCronOffset();

	/**
	 * Set offset
	 *
	 * @param string $data
	 * @return void
	 */
	public function setCronOffset(string $data);

	/**
	 * Get length
	 *
	 * @return string
	 */
	public function getCronlength();

	/**
	 * Set length
	 *
	 * @param string $data
	 * @return void
	 */
	public function setCronLength(string $data);

	/**
	 * Get last update
	 *
	 * @return string
	 */
	public function getLastUpdated();

	/**
	 * Set last update
	 *
	 * @param string $data
	 * @return void
	 */
	public function setLastUpdated(string $data);
}
