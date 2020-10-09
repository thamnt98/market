<?php
/**
 * @category Trans
 * @package  Trans_IntegrationOrder
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Randy <muhammad.randy@ctcorpdigital.com>
 *
 * Copyright © 2020 PT CT CORP DIGITAL. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationOrder\Model;

use Magento\Framework\Exception\LocalizedException as Exception;
use Magento\Framework\Message\ManagerInterface;
use Trans\IntegrationOrder\Api\Data\IntegrationOrderHistoryInterface;
use Trans\IntegrationOrder\Api\IntegrationOrderHistoryRepositoryInterface;
use Trans\IntegrationOrder\Model\IntegrationOrderHistoryFactory;
use Trans\IntegrationOrder\Model\ResourceModel\IntegrationOrderHistory as HistoryResourceModel;
use Trans\IntegrationOrder\Model\ResourceModel\IntegrationOrderHistory\CollectionFactory;

/**
 * Class IntegrationOrderHistoryRepository
 */
class IntegrationOrderHistoryRepository implements IntegrationOrderHistoryRepositoryInterface
{

    /**
     * @var array
     */
    private $instances = [];

    /**
     * @var HistoryResourceModel
     */
    private $historyResourceModel;

    /**
     * @var IntegrationOrderHistoryInterface
     */
    private $integrationOrderHistoryInterface;

    /**
     * @var IntegrationOrderHistoryRepositoryInterface
     */
    private $integrationOrderHistoryRepositoryInterface;

    /**
     * @var IntegrationOrderHistoryFactory
     */
    private $integrationOrderHistoryFactory;

    private $collectionFactory;

    /**
     * @var ManagerInterface
     */
    private $messageManager;

    /**
     * IntegrationOrderHistoryRepository constructor.
     * @param HistoryResourceModel $historyResourceModel
     * @param IntegrationOrderHistoryInterface $integrationOrderHistoryInterface
     * @param IntegrationOrderHistoryRepositoryInterface $integrationOrderHistoryRepositoryInterface
     * @param IntegrationOrderHistoryFactory $integrationOrderHistoryFactory
     * @param CollectionFactory $collectionFactory
     * @param ManagerInterface $messageManager
     */
    public function __construct(
        HistoryResourceModel $historyResourceModel,
        IntegrationOrderHistoryInterface $integrationOrderHistoryInterface,
        IntegrationOrderHistoryRepositoryInterface $integrationOrderHistoryRepositoryInterface,
        IntegrationOrderHistoryFactory $integrationOrderHistoryFactory,
        CollectionFactory $collectionFactory,
        ManagerInterface $messageManager
    ) {
        $this->historyResourceModel                       = $historyResourceModel;
        $this->integrationOrderHistoryInterface           = $integrationOrderHistoryInterface;
        $this->integrationOrderHistoryRepositoryInterface = $integrationOrderHistoryRepositoryInterface;
        $this->integrationOrderHistoryFactory             = $integrationOrderHistoryFactory;
        $this->collectionFactory                          = $collectionFactory;
        $this->messageManager                             = $messageManager;
    }

    /**
     * @param IntegrationOrderHistoryInterface $integrationOrderHistoryInterface
     * @return IntegrationOrderHistoryInterface
     * @throws \Exception
     */
    public function save(IntegrationOrderHistoryInterface $integrationOrderHistoryInterface)
    {
        try {
            $this->historyResourceModel->save($integrationOrderHistoryInterface);
        } catch (Exception $e) {
            $this->messageManager
                ->addExceptionMessage(
                    $e,
                    'There was a error while saving the data  ' . $e->getMessage()
                );
        }

        return $integrationOrderHistoryInterface;
    }

    /**
     * @param IntegrationOrderHistoryInterface $integrationOrderHistoryInterface
     * @return bool
     * @throws \Exception
     */
    public function delete(IntegrationOrderHistoryInterface $integrationOrderHistoryInterface)
    {
        $id = $integrationOrderHistoryInterface->getId();
        try {
            unset($this->instances[$id]);
            $this->historyResourceModel->delete($integrationOrderHistoryInterface);
        } catch (Exception $e) {
            $this->messageManager
                ->addExceptionMessage($e, 'There was a error while deleting the data');
        }
        unset($this->instances[$id]);
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function loadByOrderId($orderId)
    {
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter(IntegrationOrderHistoryInterface::ORDER_ID, $orderId);

        return $collection->getFirstItem();
    }
}
