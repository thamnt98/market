<?php
/**
 * @category Trans
 * @package  Trans_IntegrationCatalogPrice
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Hadi <ashadi.sejati@ctcorpdigital.com>
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationCatalogPrice\Model;

use \Trans\IntegrationCatalogPrice\Api\Data\OnlinePriceInterface;
use \Trans\IntegrationCatalogPrice\Model\ResourceModel\OnlinePrice as ResourceModel;

class OnlinePrice extends \Magento\Framework\Model\AbstractModel implements
OnlinePriceInterface {

	protected function _construct() {
		$this->_init(ResourceModel::class);
	}

	/**
	 * @inheritdoc
	 */
	public function getId() {
		return $this->_getData(OnlinePriceInterface::ID);
	}

	/**
	 * @inheritdoc
	 */
	public function setId($id) {
		$this->setData(OnlinePriceInterface::ID, $id);
	}
	
	/**
	 * @inheritdoc
	 */
	public function getSku() {
		return $this->_getData(OnlinePriceInterface::SKU);
	}

	/**
	 * @inheritdoc
	 */
	public function setSku($sku) {
		$this->setData(OnlinePriceInterface::SKU, $sku);
	}

	/**
	 * @inheritdoc
	 */
	public function getOnlinePrice() {
		return $this->_getData(OnlinePriceInterface::ONLINE_SELLING_PRICE);
	}

	/**
	 * @inheritdoc
	 */
	public function setOnlinePrice($price) {
		$this->setData(OnlinePriceInterface::ONLINE_SELLING_PRICE, $price);
	}

	/**
     * @inheritdoc
     */
    public function getCreatedAt()
    {
        return $this->_getData(OnlinePriceInterface::CREATED_AT);
    }

    /**
     * @inheritdoc
     */
    public function setCreatedAt($createdAt)
    {
        $this->setData(OnlinePriceInterface::CREATED_AT, $createdAt);
    }

    /**
     * @inheritdoc
     */
    public function getUpdatedAt()
    {
        return $this->_getData(OnlinePriceInterface::UPDATED_AT);
    }

    /**
     * @inheritdoc
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->setData(OnlinePriceInterface::UPDATED_AT, $updatedAt);
    }

    /**
     * @inheritdoc
     */
    public function getIsExclusive()
    {
        return $this->_getData(OnlinePriceInterface::IS_EXCLUSIVE);
    }

    /**
     * @inheritdoc
     */
    public function setIsExclusive($isExclusive)
    {
        $this->setData(OnlinePriceInterface::IS_EXCLUSIVE, $isExclusive);
    }

    /**
     * @inheritdoc
     */
    public function getModifiedAt()
    {
        return $this->_getData(OnlinePriceInterface::MODIFIED_AT);
    }

    /**
     * @inheritdoc
     */
    public function setModifiedAt($modifiedAt)
    {
        $this->setData(OnlinePriceInterface::MODIFIED_AT, $modifiedAt);
    }

    /**
     * @inheritdoc
     */
    public function getStartDate()
    {
        return $this->_getData(OnlinePriceInterface::START_DATE);
    }

    /**
     * @inheritdoc
     */
    public function setStartDate($startDate)
    {
        $this->setData(OnlinePriceInterface::START_DATE, $startDate);
    }

    /**
     * @inheritdoc
     */
    public function getEndDate()
    {
        return $this->_getData(OnlinePriceInterface::END_DATE);
    }

    /**
     * @inheritdoc
     */
    public function setEndDate($endDate)
    {
        $this->setData(OnlinePriceInterface::END_DATE, $endDate);
    }

     /**
     * @inheritdoc
     */
    public function getStagingId()
    {
        return $this->_getData(OnlinePriceInterface::STAGING_ID);
    }

    /**
     * @inheritdoc
     */
    public function setStagingId($stagingId)
    {
        $this->setData(OnlinePriceInterface::STAGING_ID, $stagingId);
    }
}