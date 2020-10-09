<?php
/**
 * @category Trans
 * @package  Trans_IntegrationCatalogPrice
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Ilma Dinnia Alghani <ilma.dinnia@transdigital.co.id>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationCatalog\Api\Data;

interface ProductAssociationInterface
{

    /**
     * For Job Status Default Set
     */
    const STATUS_JOB = 1;
    
    /**
     * Constant for table name
     */
    const DEFAULT_EVENT = 'trans_integration';
    const TABLE_NAME    = 'integration_catalog_product_association';

    /**
     * Constant for field name
     */
    const ID                                = 'id';
    const ASSOCIATION_RULE_ID               = 'rule_id';
    const ASSOCIATION_LINK_ID               = 'link_id';
    const ASSOCIATION_NAME                  = 'name';
    const ASSOCIATION_STATUS_JOB_DATA       = 'status_job';
    const ASSOCIATION_DISPLAY_RULE          = "diplay_rule";
    const ASSOCIATION_DISPLAY_RULE_BY       = "display_rule_by";
    const ASSOCIATION_PRODUCT_DISPLAY_BY    = "product_display_by";
    const ASSOCIATION_PRODUCT_DISPLAY       = "product_display";
    const ASSOCIATION_EXCEPT_DISPLAY        = "except_product";
    const ASSOCIATION_DISPLAY_SEQUENCE      = "display_sequence";
    const DELETED                           = 'deleted';
    const STATUS                            = 'status';

    /**
     * Constant for field name pim
     */
    const PIM_ID                            = 'pim_id';
    const PIM_NAME                          = 'pim_name';
    const CREATED_AT                        = 'created_at';
    const UPDATED_AT                        = 'updated_at';
    const MODIFIED_AT                       = 'modified_at';

    /**
     * get id
     *
     * @return int
     */
    public function getId();

    /**
     * Set Id
     *
     * @param int $id
     * @return void
     */
    public function setId($id);

    /**
     * Get RuleId
     *
     * @return string
     */
    public function getRuleId();

    /**
     * Set RuleId
     *
     * @param string $ruleId
     * @return void
     */
    public function setRuleId($ruleId);

    /**
     * Get LinkId
     *
     * @return string
     */
    public function getLinkId();

    /**
     * Set LinkId
     *
     * @param string $linkId
     * @return void
     */
    public function setLinkId($linkId);

    /**
     * Get name
     *
     * @return string
     */
    public function getName();

    /**
     * Set name
     *
     * @param string $name
     * @return void
     */
    public function setName($name);

    /**
     * Get Status 
     *
     * @return string
     */
    public function getStatus();

    /**
     * Set Status 
     *
     * @param string $status
     * @return void
     */
    public function setStatus($status);

    /**
     * Get Status Job Id
     *
     * @return string
     */
    public function getStatusJob();

    /**
     * Set Status Job Id
     *
     * @param string $statusJob
     * @return void
     */
    public function setStatusJob($statusJob);

    /**
     * Get Display Rule
     *
     * @return string
     */
    public function getDisplayRule();

    /**
     * Set Display Rule
     *
     * @param string $displayRule
     * @return void
     */
    public function setDisplayRule($displayRule);

    /**
     * Get Display Rule by
     *
     * @return string
     */
    public function getDisplayRuleBy();

    /**
     * Set Display Rule By
     *
     * @param string $displayRuleBy
     * @return void
     */
    public function setDisplayRuleBy($displayRuleBy);

    /**
     * Get Product Display
     *
     * @return string
     */
    public function getProductDisplay();

    /**
     * Set Product Display
     *
     * @param string $productDisplay
     * @return void
     */
    public function setProductDisplay($productDisplay);

    /**
     * Get Product Display By
     *
     * @return string
     */
    public function getProductDisplayBy();

    /**
     * Set Product Display By
     *
     * @param string $productDisplay
     * @return void
     */
    public function setProductDisplayBy($productDisplayBy);

    /**
     * Get Except Product
     *
     * @return string
     */
    public function getExceptProduct();

    /**
     * Set Except Product
     *
     * @param string $exceptProduct
     * @return void
     */
    public function setExceptProduct($exceptProduct);

    /**
     * Get Display Sequence
     *
     * @return string
     */
    public function getDisplaySequence();

    /**
     * Set  Display Sequence
     *
     * @param string $displaySequence
     * @return void
     */
    public function setDisplaySequence($displaySequence);
     /**
     * Get Deleted
     *
     * @return string
     */
    public function getDeleted();

    /**
     * Set  Deleted
     *
     * @param string $deleted
     * @return void
     */
    public function setDeleted($deleted);

    /**
     * Get pim id
     *
     * @return string
     */
    public function getPimId();

    /**
     * Set pim id
     *
     * @param string $pimid
     * @return void
     */
    public function setPimId($pimId);

    /**
     * Get pim name
     *
     * @return string
     */
    public function getPimName();

    /**
     * Set pim name
     *
     * @param string $pimName
     * @return void
     */
    public function setPimName($pimName);
    

    /**
     * Get Created At
     *
     * @return string
     */
    public function getCreatedAt();
     /**
     * Set Created At
     *
     * @param string $createdAt
     * @return void
     */
    public function setCreatedAt($createdAt);
     /**
     * Get Updated At
     *
     * @return string
     */
    public function getUpdatedAt();
    /**
     * Set Updated At
     *
     * @param string $updatedAt
     * @return void
     */
    public function setUpdatedAt($updatedAt);
    /**
     * Get Modified At
     *
     * @return string
     */
    public function getModifiedAt();
    /**
     * Set Modified At
     *
     * @param string $modifiedAt
     * @return void
     */
    public function setModifiedAt($modifiedAt);

    
}
