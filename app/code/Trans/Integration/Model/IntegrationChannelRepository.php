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

use \Trans\Integration\Api\Data\IntegrationChannelInterface;
use \Trans\Integration\Api\Data\IntegrationChannelInterfaceFactory;
use \Trans\Integration\Api\Data\IntegrationChannelSearchResultInterface;
use \Trans\Integration\Api\Data\IntegrationChannelSearchResultInterfaceFactory;
use \Trans\Integration\Api\IntegrationChannelRepositoryInterface;
use \Trans\Integration\Model\ResourceModel\IntegrationChannel\CollectionFactory as IntegrationChannelCollectionFactory;
use \Trans\Integration\Model\ResourceModel\IntegrationChannel\Collection;
use \Trans\Integration\Model\ResourceModel\IntegrationChannel as ResourceModel;

use Trans\Integration\Exception\WarningException;
use Trans\Integration\Exception\ErrorException;
use Trans\Integration\Exception\FatalException;


class IntegrationChannelRepository implements IntegrationChannelRepositoryInterface
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
     * @var IntegrationChannelFactory
     */
    private $integrationChannelFactory;
 
    /**
     * @var IntegrationChannelCollectionFactory
     */
    private $integrationChannelCollectionFactory;
 
    /**
     * @var IntegrationChannelSearchResultInterfaceFactory
     */
    private $searchResultFactory;

    /**
     * @var integrationChannelInterface
     */
    protected $integrationChannelInterface;

    /**
    * @var ResourceConnection
    */
    protected $dbConnection;


    /**
     * IntegrationChannelRepository constructor.
     * @param ResourceModel $resource
     * @param IntegrationChannelFactory $integrationChannelFactory
     * @param ResourceModel\CollectionFactory $integrationChannelCollectionFactory
     * @param IntegrationChannelSearchResultInterfaceFactory $integrationChannelSearchResultInterfaceFactory
     * @param IntegrationChannelInterfaceFactory $integrationChannelInterface
     * \Magento\Framework\App\ResourceConnection $resourceConnection
     */
    public function __construct(
        ResourceModel $resource,
        IntegrationChannelFactory $integrationChannelFactory,
        IntegrationChannelCollectionFactory $integrationChannelCollectionFactory,
        IntegrationChannelSearchResultInterfaceFactory $integrationChannelSearchResultInterfaceFactory,
        IntegrationChannelInterfaceFactory $integrationChannelInterface,
        \Magento\Framework\App\ResourceConnection $resourceConnection
    ) {
        $this->resource = $resource;
        $this->integrationChannelFactory = $integrationChannelFactory;
        $this->integrationChannelCollectionFactory = $integrationChannelCollectionFactory;
        $this->searchResultFactory = $integrationChannelSearchResultInterfaceFactory;
        $this->integrationChannelInterface = $integrationChannelInterface;

        $this->dbConnection = $resourceConnection->getConnection();
    }

    /**
     * {@inheritdoc}
     */
    public function getById($id)
    {
        if (!isset($this->instances[$id])) {
            /** @var \Trans\Integration\Api\Data\IntegrationChannelInterface|\Magento\Framework\Model\AbstractModel $data */
            $data = $this->integrationChannelInterface->create();
            $this->resource->load($data, $id);
            if (!$data->getId()) {
                throw new NoSuchEntityException(__('Requested Data doesn\'t exist'));
            }
            $this->instances[$id] = $data;
        }
        return $this->instances[$id];
    }

    /**
     * {@inheritdoc}
     */
    public function getByCode($code)
    {
        if (!isset($this->instances[$code])) {
            /** @var \Trans\Integration\Api\Data\IntegrationChannelInterface|\Magento\Framework\Model\AbstractModel $data */
            $data = $this->integrationChannelInterface->create();
            $this->resource->load($data, $code, 'code');
            
            if (!$data->getId()) {
                throw new NoSuchEntityException(__('Requested Data doesn\'t exist'));
            }
            $this->instances[$code] = $data;
        }
        return $this->instances[$code];
    }

    /**
     * {@inheritdoc}
     */
    public function save(IntegrationChannelInterface $data)
    {
    	/** @var IntegrationChannelInterface|\Magento\Framework\Model\AbstractModel $data */
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
    public function delete(IntegrationChannelInterface $data)
    {
        /** @var IntegrationChannelInterface|\Magento\Framework\Model\AbstractModel $data */
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
     * @param string $id
     * @return array
     */
    public function getByIdUsingRawQuery($id)
    {     
        try {

            if (empty($id)) {
                throw new ErrorException('parameter id in IntegrationChannelRepository.getByIdUsingRawQuery empty !');
            }

            $str = "select `id`, `name`, `code`, `url`, `env`, `status`, `created_at`, `updated_at`, `created_by`, `updated_by` from `integration_channel` where `id` = %d limit 1";
    
            $sql = sprintf($str, $id);
    
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