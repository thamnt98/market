<?php
/**
 * Interface AmastyBrandInterface
 *
 * PHP version 7
 *
 * @category Trans
 * @package  Trans_Brand
 * @author   Muhammad Randy <muhammad.randy@transdigital.co.id>
 */
namespace Trans\Brand\Api\Data;

/**
 * Interface AmastyBrandInterface
 *
 * @category Trans
 * @package  Trans_Brand
 * @author   Muhammad Randy <muhammad.randy@transdigital.co.id>
 */
interface AmastyBrandInterface {
	/**
	 * Constants for keys of data array.
	 * Identical to the name of the getter in snake case
	 */

	// Amasty Table New Field
	const AMASTY_PIM_ID   = "pim_id";
	const AMASTY_PIM_CODE = "pim_code";

	/**
	 * Get Amasty PIM Id
	 *
	 * @return string|null
	 */
	public function getAmastyPimId();

	/**
	 * Set Amasty PIM Id
	 *
	 * @param string $amastyPimId
	 * @return string|null
	 */
	public function setAmastyPimId($amastyPimId);

	/**
	 * Get Amasty PIM Code
	 *
	 * @return string|null
	 */
	public function getAmastyPimCode();

	/**
	 * Set Amasty PIM Code
	 *
	 * @param string $amastyPimCode
	 * @return string|null
	 */
	public function setAmastyPimCode($amastyPimCode);

}
