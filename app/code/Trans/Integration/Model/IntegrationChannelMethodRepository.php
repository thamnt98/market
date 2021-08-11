<?php
/**
 * @category Trans
 * @package  Trans_Integration
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   J.P <jaka.pondan@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Integration\Model;
 
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Exception\CouldNotSaveException;

use \Trans\Integration\Api\Data\IntegrationChannelInterface;
use \Trans\Integration\Api\Data\IntegrationChannelInterfaceFactory;

use \Trans\Integration\Api\Data\IntegrationChannelMethodInterface;
use \Trans\Integration\Api\Data\IntegrationChannelMethodInterfaceFactory;
use \Trans\Integration\Api\Data\IntegrationChannelMethodSearchResultInterface;
use \Trans\Integration\Api\Data\IntegrationChannelMethodSearchResultInterfaceFactory;
use \Trans\Integration\Api\IntegrationChannelMethodRepositoryInterface;

use \Trans\Integration\Model\ResourceModel\IntegrationChannelMethod\CollectionFactory;
use \Trans\Integration\Model\ResourceModel\IntegrationChannelMethod\Collection;
use \Trans\Integration\Model\ResourceModel\IntegrationChannelMethod as ResourceModel;

use Trans\Integration\Exception\WarningException;
use Trans\Integration\Exception\ErrorException;
use Trans\Integration\Exception\FatalException;


class IntegrationChannelMethodRepository implements IntegrationChannelMethodRepositoryInterface
{
    /**
     * @var array
     */
    protected $instances = [];

    /**
     * @var ResourceModel
     */
    protected $resource;
 
    /**
     * @var IntegrationChannelMethodCollectionFactory
     */
    private $collectionFactory;
 
    /**
     * @var IntegrationChannelMethodSearchResultInterfaceFactory
     */
    private $searchResultFactory;

     /**
     * @var IntegrationChannelMethodInterface
     */
    protected $interface;

    /**
     * @var ResourceConnection
     */
    protected $dbConnection;

    /**
     * IntegrationChannelMethodRepository constructor.
     * @param ResourceModel $resource
     * @param CollectionFactory $collectionFactory
     * @param IntegrationChannelMethodSearchResultInterfaceFactory $searchResultFactory
     * @param IntegrationChannelMethodInterfaceFactory $interface
     */
    public function __construct(
        ResourceModel $resource,
        CollectionFactory $collectionFactory,
        IntegrationChannelMethodSearchResultInterfaceFactory $searchResultFactory,
        IntegrationChannelMethodInterfaceFactory $interface,
        \Magento\Framework\App\ResourceConnection $resourceConnection
    ) {
        $this->resource = $resource;

        $this->collectionFactory = $collectionFactory;
        $this->searchResultFactory = $searchResultFactory;
        $this->interface = $interface;

        $this->dbConnection = $resourceConnection->getConnection();
    }

    /**
     * {@inheritdoc}
     */
    public function getById($id)
    {
       
        if (!isset($this->instances[$id])) {
            /** @var IntegrationChannelMethodInterface|\Magento\Framework\Model\AbstractModel $data */
            $data = $this->interface->create();
            $this->resource->load($data, $id);
            if (!$data->getId()) {
                throw new NoSuchEntityException(__('Requested Data Reservation Response doesn\'t exist'));
            }
            $this->instances[$id] = $data;
        }
        return $this->instances[$id];
    }

    /**
     * {@inheritdoc}
     */
    public function save(IntegrationChannelMethodInterface $data)
    {
    	/** @var IntegrationChannelMethodInterface|\Magento\Framework\Model\AbstractModel $data */
        try {

            $this->resource->save($data);
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
    public function delete(IntegrationChannelMethodInterface $data)
    {
        /** @var IntegrationChannelMethodInterface|\Magento\Framework\Model\AbstractModel $data */
        $id = $data->getId();
       
        try {
            unset($this->instances[$id]);
            $this->resource->delete($data);
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
    public function getByStatusActive($tag="")
    {
    	if(empty($tag)){
            throw new StateException(__(
               'Parameter TAG are empty !'
            ));
        }
        $collection = $this->interface->create()->getCollection();
        $collection->addFieldToFilter(IntegrationChannelInterface::STATUS,IntegrationChannelInterface::STATUS_ACTIVE);
        $collection->addFieldToFilter(IntegrationChannelMethodInterface::TAG,$tag);
     
        $getLastCollection = $collection->getLastItem();
        try {
            $data = $this->getById($getLastCollection->getId());
        } catch (NoSuchEntityException $ex) {
            throw new StateException(__(
                $ex->getMessage()
             ));
        }
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getItemInTagByStatusActive($tag=[])
    {
        if(empty($tag)){
            throw new StateException(__(
                'Parameter TAG are empty !'
            ));
        }

        $collection = $this->interface->create()->getCollection();
        $collection->addFieldToFilter(IntegrationChannelInterface::STATUS,IntegrationChannelInterface::STATUS_ACTIVE);
        $collection->addFieldToFilter(IntegrationChannelMethodInterface::TAG,['in'=>$tag]);
        $data = Null;
        if($collection->getSize()){
            $data = $collection;
        }
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getCollectionInTagByStatusActive($tag=[])
    {
        if(empty($tag)){
            throw new StateException(__(
                'Parameter TAG are empty !'
            ));
        }
        $collection = $this->interface->create()->getCollection();
        $collection->addFieldToFilter(IntegrationChannelInterface::STATUS,IntegrationChannelInterface::STATUS_ACTIVE);
        $collection->addFieldToFilter(IntegrationChannelMethodInterface::TAG,['in'=>$tag]);


        try {
            if(!$collection->getSize()){
                return null;
            }
        } catch (NoSuchEntityException $ex) {
            throw new StateException(__(
                $ex->getMessage()
            ));
        }
        return $collection;
    }

    /**
     * @param $tag
     * @return array
     */
    public function getByStatusActiveUsingRawQuery($tag)
    {     
        try {

            if (empty($tag)) {
                throw new ErrorException('parameter tag in IntegrationChannelMethodRepository.getByStatusActiveUsingRawQuery empty !');
            }

            $str = "select `id`, `ch_id`, `tag`, `desc`, `method`, `headers`, `query_params`, `body`, `path`, `limit`, `status`, `created_at`, `updated_at`, `created_by`, `updated_by` from `integration_channel_method` where `tag` = '%s' and `status` = %d limit 1";
    
            $sql = sprintf($str, $tag, IntegrationChannelInterface::STATUS_ACTIVE);        
    
            return $this->dbConnection->fetchRow($sql);

        }
        catch (WarningException $ex) {
            throw $ex;
        }
        catch (ErrorException $ex) {
            throw $ex;
        }
        catch (FatalException $ex) {
            throw $ex;
        }
        catch (\Exception $ex) {
            throw $ex;
        }
    }    
}