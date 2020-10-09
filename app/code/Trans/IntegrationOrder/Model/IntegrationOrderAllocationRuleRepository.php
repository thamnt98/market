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
use Trans\IntegrationOrder\Api\Data\IntegrationOrderAllocationRuleInterface;
use Trans\IntegrationOrder\Api\Data\IntegrationOrderAllocationRuleInterfaceFactory;
use Trans\IntegrationOrder\Api\IntegrationOrderAllocationRuleRepositoryInterface;
use Trans\IntegrationOrder\Model\ResourceModel\IntegrationOrderAllocationRule as OrderAllocationResourceModel;
use Trans\IntegrationOrder\Model\ResourceModel\IntegrationOrderAllocationRule\CollectionFactory;

/**
 * Class IntegrationOrderAllocationRuleRepository
 */
class IntegrationOrderAllocationRuleRepository implements IntegrationOrderAllocationRuleRepositoryInterface
{

    /**
     * @var array
     */
    private $instances = [];

    /**
     * @var OrderAllocationResourceModel
     */
    private $allocationRuleResourceModel;

    /**
     * @var IntegrationOrderAllocationRuleInterface
     */
    private $allocationRuleInterface;

    /**
     * @var IntegrationOrderAllocationRuleInterfaceFactory
     */
    private $integrationOrderAllocationRuleFactory;

    private $collectionFactory;

    /**
     * @var ManagerInterface
     */
    private $messageManager;

    /**
     * IntegrationOrderAllocationRuleRepository constructor.
     * @param OrderAllocationResourceModel $allocationRuleResourceModel
     * @param IntegrationOrderAllocationRuleInterface $allocationRuleInterface
     * @param CollectionFactory $collectionFactory
     * @param ManagerInterface $messageManager
     */
    public function __construct(
        OrderAllocationResourceModel $allocationRuleResourceModel,
        IntegrationOrderAllocationRuleInterface $allocationRuleInterface,
        IntegrationOrderAllocationRuleInterfaceFactory $integrationOrderAllocationRuleFactory,
        CollectionFactory $collectionFactory,
        ManagerInterface $messageManager
    ) {
        $this->allocationRuleResourceModel           = $allocationRuleResourceModel;
        $this->allocationRuleInterface               = $allocationRuleInterface;
        $this->integrationOrderAllocationRuleFactory = $integrationOrderAllocationRuleFactory;
        $this->collectionFactory                     = $collectionFactory;
        $this->messageManager                        = $messageManager;
    }

    /**
     * @param IntegrationOrderAllocationRuleInterface $allocationRuleInterface
     * @return IntegrationOrderAllocationRuleInterface
     * @throws \Exception
     */
    public function save(IntegrationOrderAllocationRuleInterface $allocationRuleInterface)
    {
        try {
            $this->allocationRuleResourceModel->save($allocationRuleInterface);
        } catch (Exception $e) {
            $this->messageManager
                ->addExceptionMessage(
                    $e,
                    'There was a error while saving the data  ' . $e->getMessage()
                );
        }

        return $allocationRuleInterface;
    }

    /**
     * Retrieve bank.
     *
     * @param int $oarId
     * @return \Trans\IntegrationOrder\Api\Data\IntegrationOrderAllocationRuleInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($oarId)
    {
        if (!isset($this->instances[$oarId])) {
            /** @var \Trans\IntegrationOrder\Api\Data\IntegrationOrderAllocationRuleInterface|\Magento\Framework\Model\AbstractModel $oar */
            $oar = $this->allocationRuleInterface->create();
            $this->allocationRuleResourceModel->load($oar, $oarId);
            if (!$bank->getOarId()) {
                throw new NoSuchEntityException(__('Data OAR doesn\'t exist'));
            }
            $this->instances[$oarId] = $bank;
        }
        return $this->instances[$oarId];
    }

    /**
     * {@inheritdoc}
     */
    public function loadDataByQuoteId($quoteId)
    {
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter(IntegrationOrderAllocationRuleInterface::QUOTE_ID, $quoteId);

        return $collection->getFirstItem();
    }

    /**
     * {@inheritdoc}
     */
    public function loadDataByAddressQuoteId($quoteId, $addressId)
    {
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter(IntegrationOrderAllocationRuleInterface::QUOTE_ID, $quoteId);
        $collection->addFieldToFilter(IntegrationOrderAllocationRuleInterface::ADDRESS_ID, $addressId);

        return $collection->getFirstItem();
    }

    /**
     * @param IntegrationOrderAllocationRuleInterface $allocationRuleInterface
     * @return bool
     * @throws \Exception
     */
    public function delete(IntegrationOrderAllocationRuleInterface $allocationRuleInterface)
    {
        $id = $allocationRuleInterface->getId();
        try {
            unset($this->instances[$id]);
            $this->allocationRuleResourceModel->delete($allocationRuleInterface);
        } catch (Exception $e) {
            $this->messageManager
                ->addExceptionMessage($e, 'There was a error while deleting the data');
        }
        unset($this->instances[$id]);
        return true;
    }

    /**
     * @param $oarId
     * @return bool
     * @throws \Exception
     */
    public function deleteById($oarId)
    {
        $order = $this->getById($oarId);
        return $this->delete($order);
    }
}
