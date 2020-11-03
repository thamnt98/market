<?php
/**
 * @category Trans
 * @package  Trans_IntegrationOrder
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Randy <muhammad.randy@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationOrder\Model;

use Magento\Framework\Exception\LocalizedException as Exception;
use Magento\Framework\Message\ManagerInterface;
use Trans\IntegrationOrder\Api\Data\IntegrationOrderInterface;
use Trans\IntegrationOrder\Api\IntegrationOrderRepositoryInterface;
use Trans\IntegrationOrder\Model\ResourceModel\IntegrationOrder as IntegrationOrderResourceModel;
use Trans\IntegrationOrder\Model\ResourceModel\IntegrationOrder\CollectionFactory;

/**
 * Class IntegrationOrderRepository
 */
class IntegrationOrderRepository implements IntegrationOrderRepositoryInterface
{
    /**
     * @var array
     */
    private $instances = [];

    /**
     * @var IntegrationOrderResourceModel
     */
    private $integrationOrderResourceModel;

    /**
     * @var IntegrationOrderInterface
     */
    private $integrationOrderInterface;

    /**
     * @var IntegrationOrderFactory
     */
    private $integrationOrderFactory;

    private $collectionFactory;

    /**
     * @var ManagerInterface
     */
    private $messageManager;

    /**
     * IntegrationOrderRepository constructor.
     * @param IntegrationOrderResourceModel $integrationOrderResourceModel
     * @param IntegrationOrderInterface $integrationOrderInterface
     * @param IntegrationOrderFactory $integrationOrderFactory
     * @param CollectionFactory $collectionFactory
     * @param ManagerInterface $messageManager
     */
    public function __construct(
        IntegrationOrderResourceModel $integrationOrderResourceModel,
        IntegrationOrderInterface $integrationOrderInterface,
        IntegrationOrderFactory $integrationOrderFactory,
        CollectionFactory $collectionFactory,
        ManagerInterface $messageManager
    ) {
        $this->integrationOrderResourceModel = $integrationOrderResourceModel;
        $this->collectionFactory             = $collectionFactory;
        $this->integrationOrderInterface     = $integrationOrderInterface;
        $this->integrationOrderFactory       = $integrationOrderFactory;
        $this->messageManager                = $messageManager;
    }

    /**
     * @param IntegrationOrderInterface $integrationOrderInterface
     * @return IntegrationOrderInterface
     * @throws \Exception
     */
    public function save(IntegrationOrderInterface $integrationOrderInterface)
    {
        try {
            if (!$integrationOrderInterface->getShipmentDate()) {
                $date = new \DateTime();
                $integrationOrderInterface->setShipmentDate($date->format('Y-m-d H:i:s'));
            }
            $this->integrationOrderResourceModel->save($integrationOrderInterface);
        } catch (Exception $e) {
            $this->messageManager
                ->addExceptionMessage(
                    $e,
                    'There was a error while saving the order ' . $e->getMessage()
                );
        }

        return $integrationOrderInterface;
    }

    /**
     * @param $omsOrderId
     * @return array
     */
    public function getById($omsOrderId)
    {
        if (!isset($this->instances[$omsOrderId])) {
            $order = $this->integrationOrderFactory->create();
            $this->integrationOrderResourceModel->load($order, $omsOrderId);
            $this->instances[$omsOrderId] = $order;
        }
        return $this->instances[$omsOrderId];
    }

    /**
     * @param IntegrationOrderInterface $integrationOrderInterface
     * @return bool
     * @throws \Exception
     */
    public function delete(IntegrationOrderInterface $integrationOrderInterface)
    {
        $id = $integrationOrderInterface->getId();
        try {
            unset($this->instances[$id]);
            $this->integrationOrderResourceModel->delete($integrationOrderInterface);
        } catch (Exception $e) {
            $this->messageManager
                ->addExceptionMessage($e, 'There was a error while deleting the order');
        }
        unset($this->instances[$id]);
        return true;
    }

    /**
     * @param $omsOrderId
     * @return bool
     * @throws \Exception
     */
    public function deleteById($omsOrderId)
    {
        $order = $this->getById($omsOrderId);
        return $this->delete($order);
    }

    /**
     * {@inheritdoc}
     */
    public function loadDataByOrderId($orderId)
    {
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('order_id', $orderId);

        return $collection->getFirstItem();
    }
}
