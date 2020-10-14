<?php
/**
 * @category Trans
 * @package  Trans_IntegrationEntity
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   J.P <jaka.pondan@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationEntity\Model;


use Magento\Framework\Exception\StateException;

use \Trans\Integration\Api\IntegrationChannelMethodRepositoryInterface;
use \Trans\IntegrationEntity\Api\IntegrationStoreRepositoryInterface;
use \Trans\IntegrationEntity\Api\IntegrationJobRepositoryInterface;
use \Trans\IntegrationEntity\Api\IntegrationDataValueRepositoryInterface;
use \Trans\IntegrationEntity\Api\Data\IntegrationJobInterface;


use \Trans\Integration\Helper\Curl;


class IntegrationStoreRepository implements IntegrationStoreRepositoryInterface
{

    /**
     * @var Curl Zend Client
     */
    protected $curl;

    /**
     * @var IntegrationChannelMethodRepositoryInterface
     */
    protected $methodRepository;
    /**
     * @var IntegrationJobRepositoryInterface
     */
    protected $jobRepository;

    /**
     * @var IntegrationDataValueRepositoryInterface
     */
    protected $datavalueRepository;

    /**
     * @var \Magento\Framework\Filter\FilterManager
     */
    protected $filterManager;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;


    /**
     * IntegrationStoreRepository constructor.
     * @param Curl $curl
     * @param IntegrationChannelMethodRepositoryInterface $methodRepository
     * @param IntegrationJobRepositoryInterface $jobRepository
     * @param IntegrationDataValueRepositoryInterface $datavalueRepository
     */
    public function __construct(
        Curl $curl
        ,IntegrationChannelMethodRepositoryInterface $methodRepository
        ,IntegrationJobRepositoryInterface $jobRepository
        ,IntegrationDataValueRepositoryInterface $datavalueRepository
        ,\Magento\Framework\Filter\FilterManager $filterManager
        ,\Magento\Framework\ObjectManagerInterface $_objectManager

    ){
        $this->curl=$curl;
        $this->methodRepository     = $methodRepository;
        $this->jobRepository=$jobRepository;
        $this->datavalueRepository=$datavalueRepository;
        $this->filterManager = $filterManager;
        $this->_objectManager=$_objectManager;
    }

    /**
     * @param array $methodTag
     * @return array|mixed
     * @throws StateException
     */
    public function prepareMethod($methodTag=[]){
        $check = array_filter($methodTag);
        if(empty($check)){
            throw new StateException(
                __('Wrong Argument input must be array / array empty')
            );
        }
        $method = $this->methodRepository->getCollectionInTagByStatusActive($methodTag);
        $mdId=[];
        if(empty($method)){
            throw new StateException(
                __('Channel Method Not Available')
            );
        }
        try {
            foreach($method as $row){
                $mdId[]=$row->getId();
            }
        } catch (\Exception $ex) {
            throw new StateException(
                __($ex->getMessage())
            );
        }
        return $mdId;
    }

    /**
     * @param $mdId
     * @return array|mixed
     * @throws StateException
     */
    public function prepareData($mdId){

        $job = $this->jobRepository->getByMdIdFirstItemInMdId($mdId,IntegrationJobInterface::STATUS_READY);
        $result = [];
        if(empty($job)){
            throw new StateException(
                __("Data is not available")
            );
        }
        $data = $this->datavalueRepository->getByJobIdWithStatus($job->getId(),1);
        if($data->getSize()>0){
            foreach($data as  $row){
                $result[] = json_decode($row->getDataValue());
            }
        }
        return $result;
    }

    public function saveData($data=[]){
        $check = array_filter($data);
        if(empty($check)){
            throw new StateException(
                __('Wrong Argument input must be array / array empty')
            );
        }
        $result=[];
        foreach($data as $row){
            $result = $this->saveStore((array)$row);
        }
        return $result;
    }

    private function saveStore($store){

        $postData['store']['name']=$store['name'];
        $postData['store']['name']=$store['location_code'];
        $postData['store']['store_id'] = "";
        $storeModel = $this->_objectManager->create('Magento\Store\Model\Store');
        $postData['store']['name'] = $this->filterManager->removeTags($postData['store']['name']);
        if ($postData['store']['store_id']) {
            $storeModel->load($postData['store']['store_id']);
        }
        $storeModel->setData($postData['store']);
        if ($postData['store']['store_id'] == '') {
            $storeModel->setId(null);
            $eventName = 'store_add';
        }
        $groupModel = $this->_objectManager->create(
            'Magento\Store\Model\Group'
        )->load(
            $storeModel->getGroupId()
        );
        $storeModel->setWebsiteId($groupModel->getWebsiteId());
        if (!$storeModel->isActive() && $storeModel->isDefault()) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('The default store cannot be disabled')
            );
        }
        $storeModel->save();
    }


}