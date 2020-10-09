<?php
/**
 * @category Trans
 * @package  Trans_DigitalProduct
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   hadi <ashadi.sejati@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */
namespace Trans\DigitalProduct\Model;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Exception\ValidatorException;
use Trans\DigitalProduct\Api\Data\DigitalProductStatusResponseInterface;
use Trans\DigitalProduct\Api\Data\DigitalProductStatusResponseInterfaceFactory;
use Trans\DigitalProduct\Api\DigitalProductStatusResponseRepositoryInterface;
use Trans\DigitalProduct\Model\ResourceModel\DigitalProductStatusResponse as StatusResponse;
use Trans\DigitalProduct\Model\ResourceModel\DigitalProductStatusResponse\Collection;
use Trans\DigitalProduct\Model\ResourceModel\DigitalProductStatusResponse\CollectionFactory;

/**
 * Class DigitalProductStatusResponseRepository
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class DigitalProductStatusResponseRepository implements DigitalProductStatusResponseRepositoryInterface
{
    /**
     * @var array
     */
    protected $instances = [];

    /**
     * @var StatusResponse
     */
    protected $resource;

    /**
     * @var CollectionFactory
     */
    protected $sprintResCollection;

    /**
     * @var Collection
     */
    protected $responseCollection;

    /**
     * @var DigitalProductStatusResponseInterfaceFactory
     */
    protected $statusResponseInterfaceFactory;

    /**
     * @param StatusResponse $resource
     * @param CollectionFactory $sprintResCollection
     * @param Collection $responseCollection
     * @param DigitalProductStatusResponseInterfaceFactory $statusResponseInterfaceFactory
     */
    public function __construct(
        StatusResponse $resource,
        Collection $responseCollection,
        CollectionFactory $sprintResCollection,
        DigitalProductStatusResponseInterfaceFactory $statusResponseInterfaceFactory
    ) {
        $this->resource                        = $resource;
        $this->responseCollection              = $responseCollection;
        $this->sprintResCollection             = $sprintResCollection;
        $this->statusResponseInterfaceFactory  = $statusResponseInterfaceFactory;
    }

    /**
     * Save page.
     *
     * @param \Trans\DigitalProduct\Api\Data\DigitalProductStatusResponseInterface $statusResponse
     * @return \Trans\DigitalProduct\Api\Data\DigitalProductStatusResponseInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(DigitalProductStatusResponseInterface $statusResponse)
    {
        /** @var statusResponseInterfaceFactory|\Magento\Framework\Model\AbstractModel $statusResponse */

        try {
            $this->resource->save($statusResponse);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the Digital Product Status Response: %1',
                $exception->getMessage()
            ));
        }
        return $statusResponse;
    }

    /**
     * Retrieve StatusResponse.
     *
     * @param int $statusResponseId
     * @return \Trans\DigitalProduct\Api\Data\DigitalProductStatusResponseInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($statusResponseId)
    {
        if (!isset($this->instances[$statusResponseId])) {
            /** @var \Trans\DigitalProduct\Api\Data\DigitalProductStatusResponseInterface|\Magento\Framework\Model\AbstractModel $statusResponse */
            $statuseResponse = $this->statusResponseInterfaceFactory->create();
            $this->resource->load($statusResponse, $statusResponseId);
            if (!$statusResponse->getId()) {
                throw new NoSuchEntityException(__('Requested Digital Product Status Response doesn\'t exist'));
            }
            $this->instances[$statusResponseId] = $statusResponse;
        }
        return $this->instances[$statusResponseId];
    }

    /**
     * Delete Digital Product Status Response.
     *
     * @param \Trans\DigitalProduct\Api\Data\DigitalProductStatusResponseInterface $statusResponse
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(DigitalProductStatusResponseInterface $statusResponse)
    {
        /** @var \Trans\DigitalProduct\Api\Data\DigitalProductStatusResponseInterface|\Magento\Framework\Model\AbstractModel $statusResponse */
        $statusResponseId = $statusResponse->getId();
        try {
            unset($this->instances[$statusResponseId]);
            $this->resource->delete($statusResponse);
        } catch (ValidatorException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new StateException(
                __('Unable to remove Digital Product Status Response %1', $statusResponseId)
            );
        }
        unset($this->instances[$statusResponseId]);
        return true;
    }

    /**
     * Delete Digital Product Status Response by ID.
     *
     * @param int $statusResponseId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($statusResponseId)
    {
        $statusResponse = $this->getById($statusResponseId);
        return $this->delete($statusResponse);
    }
}
