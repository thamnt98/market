<?php
/**
 * @category Trans
 * @package  Trans_IntegrationCatalogPrice
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Ilma Dinnia A <ilma.dinnia@transdigital.co.id>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationCatalog\Model;

use \Trans\IntegrationCatalog\Api\Data\ProductAssociationInterface;
use \Trans\IntegrationCatalog\Model\ResourceModel\ProductAssociation as ResourceModel;

class ProductAssociation extends \Magento\Framework\Model\AbstractModel implements ProductAssociationInterface
{
    protected function _construct()
    {
        $this->_init(ResourceModel::class);
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->_getData(ProductAssociationInterface::ID);
    }

    /**
     * @inheritdoc
     */
    public function setId($id)
    {
        $this->setData(ProductAssociationInterface::ID, $id);
    }

    /**
     * @inheritdoc
     */
    public function getRuleId()
    {
        return $this->_getData(ProductAssociationInterface::ASSOCIATION_RULE_ID);
    }

    /**
     * @inheritdoc
     */
    public function setRuleId($ruleId)
    {
        $this->setData(ProductAssociationInterface::ASSOCIATION_RULE_ID, $ruleId);
    }

    /**
     * @inheritdoc
     */
    public function getLinkId()
    {
        return $this->_getData(ProductAssociationInterface::ASSOCIATION_LINK_ID);
    }

    /**
     * @inheritdoc
     */
    public function setLinkId($linkId)
    {
        $this->setData(ProductAssociationInterface::ASSOCIATION_LINK_ID, $linkId);
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->_getData(ProductAssociationInterface::ASSOCIATION_NAME);
    }

    /**
     * @inheritdoc
     */
    public function setName($name)
    {
        $this->setData(ProductAssociationInterface::ASSOCIATION_NAME, $name);
    }
    
    /**
     * @inheritdoc
     */
    public function getStatusJob()
    {
        return $this->_getData(ProductAssociationInterface::ASSOCIATION_STATUS_JOB_DATA);
    }

    /**
     * @inheritdoc
     */
    public function setStatusJob($statusJob)
    {
        $this->setData(ProductAssociationInterface::ASSOCIATION_STATUS_JOB_DATA, $statusJob);
    }

    /**
     * @inheritdoc
     */
    public function getStatus()
    {
        return $this->_getData(ProductAssociationInterface::STATUS);
    }

    /**
     * @inheritdoc
     */
    public function setStatus($status)
    {
        $this->setData(ProductAssociationInterface::STATUS, $status);
    }

    /**
     * @inheritdoc
     */
    public function getDisplayRule()
    {
        return $this->_getData(ProductAssociationInterface::ASSOCIATION_DISPLAY_RULE);
    }

    /**
     * @inheritdoc
     */
    public function setDisplayRule($displayRule)
    {
        $this->setData(ProductAssociationInterface::ASSOCIATION_DISPLAY_RULE, $displayRule);
    }

     /**
     * @inheritdoc
     */
    public function getDisplayRuleBy()
    {
        return $this->_getData(ProductAssociationInterface::ASSOCIATION_DISPLAY_RULE_BY);
    }

    /**
     * @inheritdoc
     */
    public function setDisplayRuleBy($displayRuleBy)
    {
        $this->setData(ProductAssociationInterface::ASSOCIATION_DISPLAY_RULE_BY, $displayRuleBy);
    }

    /**
     * @inheritdoc
     */
    public function getProductDisplay()
    {
        return $this->_getData(ProductAssociationInterface::ASSOCIATION_PRODUCT_DISPLAY);
    }

    /**
     * @inheritdoc
     */
    public function setProductDisplay($productDisplay)
    {
        $this->setData(ProductAssociationInterface::ASSOCIATION_PRODUCT_DISPLAY, $productDisplay);
    }

    /**
     * @inheritdoc
     */
    public function getProductDisplayBy()
    {
        return $this->_getData(ProductAssociationInterface::ASSOCIATION_PRODUCT_DISPLAY_BY);
    }

    /**
     * @inheritdoc
     */
    public function setProductDisplayBy($productDisplayBy)
    {
        $this->setData(ProductAssociationInterface::ASSOCIATION_PRODUCT_DISPLAY_BY, $productDisplayBy);
    }

    /**
     * @inheritdoc
     */
    public function getExceptProduct()
    {
        return $this->_getData(ProductAssociationInterface::ASSOCIATION_EXCEPT_DISPLAY);
    }

    /**
     * @inheritdoc
     */
    public function setExceptProduct($exceptProduct)
    {
        $this->setData(ProductAssociationInterface::ASSOCIATION_EXCEPT_DISPLAY,$exceptProduct);
    }

    /**
     * @inheritdoc
     */
    public function getDisplaySequence()
    {
        return $this->_getData(ProductAssociationInterface::ASSOCIATION_DISPLAY_SEQUENCE);
    }

    /**
     * @inheritdoc
     */
    public function setDisplaySequence($displaySequence)
    {
        $this->setData(ProductAssociationInterface::ASSOCIATION_DISPLAY_SEQUENCE,$displaySequence);
    }

    /**
     * @inheritdoc
     */
    public function getDeleted()
    {
        return $this->_getData(ProductAssociationInterface::DELETED);
    }

    /**
     * @inheritdoc
     */
    public function setDeleted($deleted)
    {
        $this->setData(ProductAssociationInterface::DELETED ,$deleted);
    }
     /**
     * @inheritdoc
     */
    public function getPimId()
    {
        return $this->_getData(ProductAssociationInterface::PIM_ID);
    }

   /**
     * @inheritdoc
     */
    public function setPimId($pimId)
    {
        $this->setData(ProductAssociationInterface::PIM_ID, $pimId);
    }

    /**
     * @inheritdoc
     */
    public function getPimName()
    {
        return $this->_getData(ProductAssociationInterface::PIM_NAME);
    }

    /**
     * @inheritdoc
     */
    public function setPimName($pimName)
    {
        $this->setData(ProductAssociationInterface::PIM_NAME, $pimName);
    }


    /**
     * @inheritdoc
     */
    public function getCreatedAt()
    {
        return $this->_getData(ProductAssociationInterface::CREATED_AT);
    }

    /**
     * @inheritdoc
     */
    public function setCreatedAt($createdAt)
    {
        $this->setData(ProductAssociationInterface::CREATED_AT, $createdAt);
    }


    /**
     * @inheritdoc
     */
    public function getUpdatedAt()
    {
        return $this->_getData(ProductAssociationInterface::UPDATED_AT);
    }

    /**
     * @inheritdoc
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->setData(ProductAssociationInterface::UPDATED_AT, $updatedAt);
    }

    /**
     * @inheritdoc
     */
    public function getModifiedAt()
    {
        return $this->_getData(ProductAssociationInterface::MODIFIED_AT);
    }

    /**
     * @inheritdoc
     */
    public function setModifiedAt($modifiedAt)
    {
        $this->setData(ProductAssociationInterface::MODIFIED_AT, $modifiedAt);
    }

   
}
