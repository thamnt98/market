<?php
/**
 * @category Trans
 * @package  Trans_IntegrationEntity
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Dwi Septha Kurniawan <septha.kurniawan@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationEntity\Api;

interface IntegrationAssignSourcesInterface {
	
	//For Job Status Default Set
	const MSG_ERROR = 'Error';
	
	const PRIORITY  = 1;
	/**
	 * @param mixed $data
	 * @return mixed
	 */
	public function assignSource($data);

    /**
     * Get website code
     *
     * @return string|null
     */
    public function getWebsiteCode();

	/**
     * Assign Source Available
     * @return mixed
     */
	public function assignSourceAvailable();    

    /**
     * Get stock id active
     * @return mixed
     */
    public function getStockIdActive();

}