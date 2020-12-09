<?php
/**
 * @category Trans
 * @package  Trans_IntegrationEntity
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Dwi Septha Kurniawan <septha.kurniawan@transdigital.co.id>
 * @modify   J.P <jaka.pondan@transdigital.co.id>
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationEntity\Model;


use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;

use \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory;
use \Magento\Eav\Model\ResourceModel\Entity\Attribute\Group\CollectionFactory as GroupCollection;

use Trans\IntegrationEntity\Api\IntegrationProductAttributeRepositoryInterface;
use \Trans\IntegrationEntity\Api\Data\IntegrationProductAttributeTypeInterface;

use \Trans\IntegrationEntity\Api\Data\IntegrationProductAttributeTypeInterfaceFactory as ProductAttributeTypeInterface ;

use Trans\IntegrationEntity\Api\Data\IntegrationProductAttributeSetInterface;
use Trans\IntegrationEntity\Api\Data\IntegrationProductAttributeSetInterfaceFactory;

use Trans\IntegrationEntity\Api\Data\IntegrationProductAttributeSetChildInterface;
use Trans\IntegrationEntity\Api\Data\IntegrationProductAttributeSetChildInterfaceFactory;

use Trans\IntegrationEntity\Model\ResourceModel\IntegrationProductAttributeSet as ResourceModelIntegrationProductAttributeSet;
use Trans\IntegrationEntity\Model\ResourceModel\IntegrationProductAttributeSet\CollectionFactory as GroupCollectionIntegrationProductAttributeSet;

use Trans\IntegrationEntity\Model\ResourceModel\IntegrationProductAttributeSetChild as ResourceModelAttributeSetChild;
use Trans\IntegrationEntity\Model\ResourceModel\IntegrationProductAttributeSetChild\CollectionFactory as GroupCollectionAttributeSetChild;


/**
 * @inheritdoc
 */
class IntegrationProductAttributeRepository implements IntegrationProductAttributeRepositoryInterface {
	
	/**
	 * var CollectionFactory
	 */
	protected $_attributeSetCollection;

	/**
	 * var GroupCollection
	 */
	protected $_attributeGroupCollection;

	/**
	 * var ProductAttributeTypeInterface
	 */
	protected $_productAttributeType;

	/**
	 * @var IntegrationProductAttributeSetInterfaceFactory
	 */
	protected $integrationProductAttributeSetInterfaceFactory;

	/**
	 * @var ResourceModelIntegrationProductAttributeSet
	 */
	protected $resourceModelIntegrationProductAttributeSet;

	/**
	 * @var GroupCollectionIntegrationProductAttributeSet
	 */
	protected $groupCollectionIntegrationProductAttributeSet;

    /**
     * @var IntegrationProductAttributeSetChildInterfaceFactory
     */
    protected $integrationProductAttributeSetChildInterfaceFactory;

    /**
     * @var ResourceModelAttributeSetChild
     */
    protected $resourceModelAttributeSetChild;

    /**
     * @var GroupCollectionAttributeSetChild
     */
    protected $groupCollectionAttributeSetChild;

	/**
	 * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Group\CollectionFactory
	 */
	protected $attributeGroupCollectionFactory;

	public function __construct
	(	
		CollectionFactory $_attributeSetCollection,
		GroupCollection $_attributeGroupCollection,
		ProductAttributeTypeInterface $_productAttributeType,
		IntegrationProductAttributeSetInterfaceFactory $integrationProductAttributeSetInterfaceFactory,
		ResourceModelIntegrationProductAttributeSet $resourceModelIntegrationProductAttributeSet,
		GroupCollectionIntegrationProductAttributeSet $groupCollectionIntegrationProductAttributeSet,
		\Magento\Eav\Api\Data\AttributeGroupInterfaceFactory $attributeGroupCollectionFactory,
        IntegrationProductAttributeSetChildInterfaceFactory $integrationProductAttributeSetChildInterfaceFactory,
        ResourceModelAttributeSetChild $resourceModelAttributeSetChild,
        GroupCollectionAttributeSetChild $groupCollectionAttributeSetChild
	) {	
  
		$this->_attributeSetCollection		= $_attributeSetCollection;
		$this->_attributeGroupCollection	= $_attributeGroupCollection;
		$this->_productAttributeType		= $_productAttributeType;
		$this->integrationProductAttributeSetInterfaceFactory = $integrationProductAttributeSetInterfaceFactory;
		$this->resourceModelIntegrationProductAttributeSet = $resourceModelIntegrationProductAttributeSet;
		$this->groupCollectionIntegrationProductAttributeSet = $groupCollectionIntegrationProductAttributeSet;
		$this->attributeGroupCollectionFactory = $attributeGroupCollectionFactory;
        $this->integrationProductAttributeSetChildInterfaceFactory = $integrationProductAttributeSetChildInterfaceFactory;
        $this->resourceModelAttributeSetChild = $resourceModelAttributeSetChild;
        $this->groupCollectionAttributeSetChild = $groupCollectionAttributeSetChild;
	}

	/**
   	*
   	* @param string $attributeSetName
   	* @return int attributeSetId
   	*/
  	public function getAttributeSetId($attributeSetName)
  	{
		$attributeSetCollection = $this->_attributeSetCollection->create()
		->addFieldToSelect('attribute_set_id')
		->addFieldToFilter('attribute_set_name', $attributeSetName)
		->getLastItem()
		->toArray();

		$attributeSetId = (int) $attributeSetCollection['attribute_set_id'];
		// OR (see benchmark below for make your choice)
		$attributeSetId = (int) implode($attributeSetCollection);

		return $attributeSetId;
	}
	  
	/**
   	* Get attribute
   	* @param string $attributeCode
   	* @return int attributeGroupId
   	*/
	public function getAttributeGroupId($attributeCode)
	{
		 $attributeCollection = $this->_attributeGroupCollection->create()
		 ->addFieldToSelect('attribute_group_id')
		 ->addFieldToFilter('attribute_group_code', $attributeCode)
		 ->getFirstItem()
		 ->toArray();
 
		 $attributeGroupId = (int) $attributeCollection['attribute_group_id'];
		 // OR (see benchmark below for make your choice)
		 $attributeGroupId = (int) implode($attributeCollection);
 
		 return $attributeGroupId;
	}

	/**
    * Get attribute
    * @param string $attributeCode
    * @return int attributeGroupId
    * @return int $attributeSetId
    */
    public function getAttributeGroupIdBySet($attributeCode, $attributeSetId)
    {
         $attributeCollection = $this->_attributeGroupCollection->create()
         ->addFieldToSelect('attribute_group_id')
         ->addFieldToFilter('attribute_group_code', $attributeCode)
         ->addFieldToFilter('attribute_set_id', $attributeSetId)
         ->getFirstItem()
         ->toArray();
 
         $attributeGroupId = (int) $attributeCollection['attribute_group_id'];
         // OR (see benchmark below for make your choice)
         $attributeGroupId = (int) implode($attributeCollection);
 
         return $attributeGroupId;
    }

    /**
   	*
   	* @param string $attrId
   	* @return array $result data type
   	*/
	public function getAttributeTypeMap($attrId=0)
	{
		$result = NUll;
		$collection = $this->_productAttributeType->create()->getCollection();
        $collection->addFieldToFilter(IntegrationProductAttributeTypeInterface::PIM_TYPE_ID,$attrId);
        $collection->addFieldToFilter(IntegrationProductAttributeTypeInterface::STATUS,1);

        if($collection->getSize()){
           
            try {
                $result = $collection->getFirstItem()->getData();
            } catch (\Exception $exception) {
                throw new StateException(__(
                   "Error ". __FUNCTION__." : ".$exception->getMessage()
                ));
            }
        }
		

		return $result;
	}

	/**
	 * load data by pim id
	 * @param string $pimId
   	 * @return array $result data type
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function loadAttributeSetByPimId($pimId)
	{
		if (empty($pimId)) {
            throw new StateException(__(
                'Parameter pim id are empty !'
            ));
        }

		$result = NUll;
		$collection = $this->integrationProductAttributeSetInterfaceFactory->create()->getCollection();
        $collection->addFieldToFilter(IntegrationProductAttributeSetInterface::PIM_ID,$pimId);

        if($collection->getSize()){
           
            try {
                $result = $collection->getFirstItem();
            } catch (\Exception $exception) {
                throw new StateException(__(
                   "Error ". __FUNCTION__." : ".$exception->getMessage()
                ));
            }
        }
		

		return $result;
	}

    /**
     * load data by attribute set group
     * @param string $attributeSetGroup
     * @return array $result data type
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function loadAttributeSetByAttrGroup($attributeSetGroup)
    {
        if (empty($attributeSetGroup)) {
            throw new StateException(__(
                'Parameter attribute set group are empty !'
            ));
        }

        $result = NUll;
        $collection = $this->integrationProductAttributeSetInterfaceFactory->create()->getCollection();
        $collection->addFieldToFilter(IntegrationProductAttributeSetInterface::ATTRIBUTE_SET_GROUP,$attributeSetGroup);

        if($collection->getSize()){
           
            try {
                $result = $collection;
            } catch (\Exception $exception) {
                throw new StateException(__(
                   "Error ". __FUNCTION__." : ".$exception->getMessage()
                ));
            }
        }
        

        return $result;
    }

    /**
     * load data by pim id
     * @param string $pimId
     * @return array $result data type
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function loadAttributeSetChildByPimId($pimId)
    {
        if (empty($pimId)) {
            throw new StateException(__(
                'Parameter pim id are empty !'
            ));
        }

        $result = NUll;
        $collection = $this->integrationProductAttributeSetChildInterfaceFactory->create()->getCollection();
        $collection->addFieldToFilter(IntegrationProductAttributeSetChildInterface::PIM_ID,$pimId);

        if($collection->getSize()){
           
            try {
                $result = $collection;
            } catch (\Exception $exception) {
                throw new StateException(__(
                   "Error ". __FUNCTION__." : ".$exception->getMessage()
                ));
            }
        }
        

        return $result;
    }

	/**
	 * load data by pim id and code
	 * @param string $pimId
	 * @param string $code
   	 * @return array $result data type
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function loadAttributeSetByPimIdCode($pimId, $code)
	{
		if (empty($pimId)) {
            throw new StateException(__(
                'Parameter pim id is empty !'
            ));
        }

        if (empty($code)) {
            throw new StateException(__(
                'Parameter code is empty !'
            ));
        }

		$result = NUll;
		$collection = $this->integrationProductAttributeSetChildInterfaceFactory->create()->getCollection();
        $collection->addFieldToFilter(IntegrationProductAttributeSetChildInterface::PIM_ID, $pimId);
        $collection->addFieldToFilter(IntegrationProductAttributeSetChildInterface::CODE, $code);

        if($collection->getSize()){
           
            try {
                $result = $collection->getFirstItem()->getData();
            } catch (\Exception $exception) {
                throw new StateException(__(
                   "Error ". __FUNCTION__." : ".$exception->getMessage()
                ));
            }
        }
		

		return $result;
	}

    /**
     * load data by attribute set group and code
     * @param string $attributeSetGroup
     * @param string $code
     * @return array $result data type
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function loadAttributeSetByPimIdAttrSetGroup($attributeSetGroup, $code)
    {
        if (empty($attributeSetGroup)) {
            throw new StateException(__(
                'Parameter attribute set group is empty !'
            ));
        }

        if (empty($code)) {
            throw new StateException(__(
                'Parameter code is empty !'
            ));
        }

        $result = NUll;
        $collection = $this->integrationProductAttributeSetChildInterfaceFactory->create()->getCollection();
        $collection->addFieldToFilter(IntegrationProductAttributeSetChildInterface::ATTRIBUTE_SET_GROUP, $attributeSetGroup);
        $collection->addFieldToFilter(IntegrationProductAttributeSetChildInterface::CODE, $code);
        $collection->setPageSize(1);

        if($collection->getSize()){
           
            try {
                $result = $collection->getFirstItem();
            } catch (\Exception $exception) {
                throw new StateException(__(
                   "Error ". __FUNCTION__." : ".$exception->getMessage()
                ));
            }
        }
        

        return $result;
    }

	/**
	 * collection by pim id and code
	 * @param string $pimId
	 * @param string $code
   	 * @return array $result data type
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function collectionAttributeSetByPimIdCode($pimId, $code)
	{
		if (empty($pimId)) {
            throw new StateException(__(
                'Parameter pim id is empty !'
            ));
        }

        if (empty($code)) {
            throw new StateException(__(
                'Parameter code is empty !'
            ));
        }

		$result = NUll;
		$collection = $this->integrationProductAttributeSetChildInterfaceFactory->create()->getCollection();
        $collection->addFieldToFilter(IntegrationProductAttributeSetChildInterface::PIM_ID, $pimId);
        $collection->addFieldToFilter(IntegrationProductAttributeSetChildInterface::CODE, $code);

        if($collection->getSize()){
           
            try {
                $result = $collection;
            } catch (\Exception $exception) {
                throw new StateException(__(
                   "Error ". __FUNCTION__." : ".$exception->getMessage()
                ));
            }
        }
		

		return $result;
	}

	 /**
     * {@inheritdoc}
     */
    public function saveAttributeSetIntegration(IntegrationProductAttributeSetInterface $data)
    {
        try {
            $this->resourceModelIntegrationProductAttributeSet->save($data);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the data: %1',
                $exception->getMessage()
            ));
        }
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function saveAttributeSetChildIntegration(IntegrationProductAttributeSetChildInterface $data)
    {
        try {
            $this->resourceModelAttributeSetChild->save($data);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the data: %1',
                $exception->getMessage()
            ));
        }
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteAttributeSetIntegration(IntegrationProductAttributeSetInterface $data)
    {
        $id = $data->getId();

        try {
            unset($this->instances[$id]);
            $this->resourceModelIntegrationProductAttributeSet->delete($data);
        } catch (ValidatorException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new StateException(
                __('Unable to remove data %1', $id)
            );
        }
        unset($this->instances[$id]);
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteAttributeSetChildIntegration(IntegrationProductAttributeSetChildInterface $data)
    {
        $id = $data->getId();

        try {
            unset($this->instances[$id]);
            $this->resourceModelAttributeSetChild->delete($data);
        } catch (ValidatorException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new StateException(
                __('Unable to remove data %1', $id)
            );
        }
        unset($this->instances[$id]);
        return true;
    }

    /**
	 * load data atribute group id
	 * @param string $attributeSetId
   	 * @return array $result data type
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function loadAttributeGroupId($attributeSetId)
	{
		if (empty($attributeSetId)) {
            throw new StateException(__(
                'Parameter attribute set id are empty !'
            ));
        }

		$result = NUll;
		$collection = $this->attributeGroupCollectionFactory->create()->getCollection();
        $collection->addFieldToFilter(IntegrationProductAttributeSetInterface::ATTRIBUTE_SET_ID,$attributeSetId);
        $collection->addFieldToFilter(IntegrationProductAttributeSetInterface::ATTRIBUTE_GROUP_CODE,IntegrationProductAttributeSetInterface::ATTRIBUTE_GROUP_CODE_DATA);

        if($collection->getSize()){
           
            try {
                $result = $collection->getFirstItem()->getData();
            } catch (\Exception $exception) {
                throw new StateException(__(
                   "Error ". __FUNCTION__." : ".$exception->getMessage()
                ));
            }
        }
		

		return $result;
	}

	/**
	 * load data by code
	 * @param string $code
   	 * @return array $result data type
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function loadAttributeSetByCode($code)
	{
		if (empty($code)) {
            throw new StateException(__(
                'Parameter code are empty !'
            ));
        }

		$result = NUll;
		$collection = $this->integrationProductAttributeSetChildInterfaceFactory->create()->getCollection();
        $collection->addFieldToFilter(IntegrationProductAttributeSetChildInterface::CODE, $code);

        if($collection->getSize()){
           
            try {
                $result = $collection->getFirstItem()->getData();
            } catch (\Exception $exception) {
                throw new StateException(__(
                   "Error ". __FUNCTION__." : ".$exception->getMessage()
                ));
            }
        }
		

		return $result;
	}

	/**
	 * load data attribute id by pim id
	 * @param string $pimId
   	 * @return array $result data type
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function getAttributeSetIdByPimId($pimId)
	{
		if (empty($pimId)) {
            throw new StateException(__(
                'Parameter pim id are empty !'
            ));
        }

		$result = NUll;
		$collection = $this->integrationProductAttributeSetInterfaceFactory->create()->getCollection();
        $collection->addFieldToFilter(IntegrationProductAttributeSetInterface::PIM_ID, $pimId);

        if($collection->getSize()){
           
            try {
                $result = $collection->getFirstItem()->getAttributeSetId();
            } catch (\Exception $exception) {
                throw new StateException(__(
                   "Error ". __FUNCTION__." : ".$exception->getMessage()
                ));
            }
        }
		

		return $result;
	}

    /**
     * load data attribute set code by pim id
     * @param string $pimId
     * @return array $result data type
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getAttributeSetCodeByPimId($pimId)
    {
        if (empty($pimId)) {
            throw new StateException(__(
                'Parameter pim id are empty !'
            ));
        }

        $result = NUll;
        $collection = $this->integrationProductAttributeSetInterfaceFactory->create()->getCollection();
        $collection->addFieldToFilter(IntegrationProductAttributeSetInterface::PIM_ID, $pimId);

        if($collection->getSize()){
           
            try {
                $result = $collection->getFirstItem()->getName();
            } catch (\Exception $exception) {
                throw new StateException(__(
                   "Error ". __FUNCTION__." : ".$exception->getMessage()
                ));
            }
        }
        

        return $result;
    }

    /**
     * load data attribute set code by attribute set id
     * @param string $pimId
     * @return array $result data type
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getAttributeSetCodeByAttrSetId($attrSetId)
    {
        if (empty($pimId)) {
            throw new StateException(__(
                'Parameter pim id are empty !'
            ));
        }

        $result = NUll;
        $collection = $this->integrationProductAttributeSetInterfaceFactory->create()->getCollection();
        $collection->addFieldToFilter(IntegrationProductAttributeSetInterface::ATTRIBUTE_SET_ID, $attrSetId);

        if($collection->getSize()){
           
            try {
                $result = $collection->getFirstItem()->getName();
            } catch (\Exception $exception) {
                throw new StateException(__(
                   "Error ". __FUNCTION__." : ".$exception->getMessage()
                ));
            }
        }
        

        return $result;
    }
}