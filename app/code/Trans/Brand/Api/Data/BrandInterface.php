<?php
/**
 * Interface BrandInterface
 *
 * PHP version 7
 *
 * @category Trans
 * @package  Trans_Brand
 * @author   J.P <jaka.pondan@transdigital.co.id>
 */
namespace Trans\Brand\Api\Data;

/**
 * Interface BrandInterface
 *
 * @category Trans
 * @package  Trans_Brand
 * @author   J.P <jaka.pondan@transdigital.co.id>
 */
interface BrandInterface {
	/**
	 * Constants for keys of data array.
	 * Identical to the name of the getter in snake case
	 */

	const BRAND_ID         = 'brand_id';
	const TITLE            = 'title';
	const DESCRIPTION      = 'description';
	const META_TITLE       = 'meta_title';
	const META_KEYWORDS    = 'meta_keywords';
	const META_DESCRIPTION = 'meta_description';
	const URL_KEY          = 'url_key';
	const IMAGE            = 'image';
	const BANNER_IMAGE     = 'banner_image';
	const POSITION         = 'position';
	const STATUS           = 'status';

	// Integration
	const TABLE_NAME         = "trans_brand";
	const TABLE_NAME_PRODUCT = "trans_brand_products";

	const PIM_ID       = "pim_id";
	const PIM_CODE     = "pim_code";
	const COMPANY_CODE = "company_code";

	const IS_DEFAULT = 'is_default';
	const IS_ACTIVE  = 'is_active';
	const DELETED    = 'deleted';
	const RESP_ID    = "id";
	const RESP_CODE  = "brand_code";
	const RESP_NAME  = "brand_name";
	const RESP_DESC  = "brand_description";

	/**
	 * Get ID
	 *
	 * @return int|null
	 */
	public function getId();

	/**
	 * Get PIM ID
	 *
	 * @return string|null
	 */
	public function getPimId();

	/**
	 * Set PIM ID
	 *
	 * @param string $pimId
	 * @return string|null
	 */
	public function setPimId($pimId);

	/**
	 * Get CODE
	 *
	 * @return string|null
	 */
	public function getCode();

	/**
	 * Set CODE
	 *
	 * @param string $code
	 * @return string|null
	 */
	public function setCode($code);

	/**
	 * Get title
	 *
	 * @return string|null
	 */
	public function getTitle();

	/**
	 * Get description
	 *
	 * @return string|null
	 */
	public function getDescription();

	/**
	 * Get meta title
	 *
	 * @return string|null
	 */
	public function getMetaTitle();

	/**
	 * Get meta keywords
	 *
	 * @return string|null
	 */
	public function getMetaKeywords();

	/**
	 * Get meta description
	 *
	 * @return string|null
	 */
	public function getMetaDescription();

	/**
	 * Get url key
	 *
	 * @return string|null
	 */
	public function getUrlKey();

	/**
	 * Get Position
	 *
	 * @return string|null
	 */
	public function getPosition();

	/**
	 * Get image
	 *
	 * @return string|null
	 */
	public function getImage();

	/**
	 * Get banner image
	 *
	 * @return string|null
	 */
	public function getBannerImage();

	/**
	 * Get status
	 *
	 * @return string|null
	 */
	public function getStatus();

	/**
	 * Set Id
	 *
	 * @param int $id id
	 *
	 * @return mixed
	 */
	public function setId($id);

	/**
	 * Set Title
	 *
	 * @param string $title title
	 *
	 * @return mixed
	 */
	public function setTitle($title);

	/**
	 * Set Description
	 *
	 * @param string $description description
	 *
	 * @return mixed
	 */
	public function setDescription($description);

	/**
	 * Set meta title
	 *
	 * @param string $metaTitle metaTitle
	 *
	 * @return mixed
	 */
	public function setMetaTitle($metaTitle);

	/**
	 * Set meta keywords
	 *
	 * @param string $metaKeywords metaKeywords
	 *
	 * @return mixed
	 */
	public function setMetaKeywords($metaKeywords);

	/**
	 * Set meta description
	 *
	 * @param string $metaDescription metaDescription
	 *
	 * @return mixed
	 */
	public function setMetaDescription($metaDescription);

	/**
	 * Set Url key
	 *
	 * @param string $urlKey urlKey
	 *
	 * @return mixed
	 */
	public function setUrlKey($urlKey);

	/**
	 * Set IsActive
	 *
	 * @param isActive $isActive isActive
	 *
	 * @return mixed
	 */
	public function setIsActive($isActive);
}
