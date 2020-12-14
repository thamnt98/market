<?php
/**
 * @category Trans
 * @package  Trans_IntegrationEntity
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author	 J.P <jaka.pondan@ctcorpdigital.com>
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationEntity\Api;

use Trans\IntegrationEntity\Api\Data\IntegrationProductAttributeSetInterface;
use Trans\IntegrationEntity\Api\Data\IntegrationProductAttributeSetChildInterface;

interface IntegrationProductAttributeRepositoryInterface {
		
	/**
	 * Get Default Attribute Set Id
	 * @param string $attributeSetName
	 * @return int
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function getAttributeSetId($attributeSetName);	


	/**
	 * Get Default Attribute Group Id
	 * @param string $attributeCode
	 * @return int
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function getAttributeGroupId($attributeCode);

	/**
    * Get attribute
    * @param string $attributeCode
    * @return int attributeGroupId
    * @return int $attributeSetId
    */
    public function getAttributeGroupIdBySet($attributeCode, $attributeSetId);

	/**
	 * Get Attribute Type Map Data
	 * @param string $attrId
   	 * @return array $result data type
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function getAttributeTypeMap($attrId);
	
	/**
	 * load data by pim id
	 * @param string $pimId
   	 * @return array $result data type
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function loadAttributeSetByPimId($pimId);

	/**
     * load data by attribute set group
     * @param string $attributeSetGroup
     * @return array $result data type
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function loadAttributeSetByAttrGroup($attributeSetGroup);

	/**
	 * load data by pim id
	 * @param string $pimId
   	 * @return array $result data type
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function loadAttributeSetChildByPimId($pimId);

	/**
	 * load data by pim id and code
	 * @param string $pimId
	 * @param string $code
   	 * @return array $result data type
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function loadAttributeSetByPimIdCode($pimId, $code);

	/**
     * load data by attribute set group and code
     * @param string $attributeSetGroup
     * @param string $code
     * @return array $result data type
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function loadAttributeSetByPimIdAttrSetGroup($attributeSetGroup, $code);
    
	/**
	 * collection by pim id and code
	 * @param string $pimId
	 * @param string $code
   	 * @return array $result data type
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function collectionAttributeSetByPimIdCode($pimId, $code);

	
	/**
     * Save Data
     *
     * @param \Trans\IntegrationEntity\Api\Data\IntegrationProductAttributeSetInterface $data
     * @return \Trans\IntegrationEntity\Api\Data\IntegrationProductAttributeSetInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */

    public function saveAttributeSetIntegration(IntegrationProductAttributeSetInterface $data);

    /**
     * Save Data
     *
     * @param \Trans\IntegrationEntity\Api\Data\IntegrationProductAttributeSetChildInterface $data
     * @return \Trans\IntegrationEntity\Api\Data\IntegrationProductAttributeSetChildInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */

    public function saveAttributeSetChildIntegration(IntegrationProductAttributeSetChildInterface $data);

    /**
     * delete Data
     *
     * @param \Trans\IntegrationEntity\Api\Data\IntegrationProductAttributeSetInterface $data
     * @return \Trans\IntegrationEntity\Api\Data\IntegrationProductAttributeSetInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */

    public function deleteAttributeSetIntegration(IntegrationProductAttributeSetInterface $data);

    /**
     * delete Data
     *
     * @param \Trans\IntegrationEntity\Api\Data\IntegrationProductAttributeSetChildInterface $data
     * @return \Trans\IntegrationEntity\Api\Data\IntegrationProductAttributeSetChildInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */

    public function deleteAttributeSetChildIntegration(IntegrationProductAttributeSetChildInterface $data);

    /**
	 * load data atribute group id
	 * @param string $attributeSetId
   	 * @return array $result data type
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function loadAttributeGroupId($attributeSetId);

	/**
	 * load data by code
	 * @param string $code
   	 * @return array $result data type
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function loadAttributeSetByCode($code);

	/**
	 * load data attribute id by pim id
	 * @param string $pimId
   	 * @return array $result data type
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function getAttributeSetIdByPimId($pimId);

	/**
	 * load data attribute code by pim id
	 * @param string $pimId
   	 * @return array $result data type
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function getAttributeSetCodeByPimId($pimId);

	/**
	 * load data attribute set code by attribute set id
	 * @param string $attrSetId
   	 * @return array $result data type
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function getAttributeSetCodeByAttrSetId($attrSetId);
}