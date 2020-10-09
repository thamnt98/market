<?php
declare(strict_types=1);

namespace SM\DigitalProduct\Model;

use Exception;
use Magento\Catalog\Helper\Image;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\App\Area;
use Magento\Framework\DB\Adapter\DuplicateException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\App\Emulation;
use SM\DigitalProduct\Api\Data\OperatorIconInterface;
use SM\DigitalProduct\Api\Data\OperatorIconSearchResultsInterface;
use SM\DigitalProduct\Api\Data\OperatorIconSearchResultsInterfaceFactory;
use SM\DigitalProduct\Api\OperatorIconRepositoryInterface;
use SM\DigitalProduct\Model\OperatorIcon as OperatorIconModel;
use SM\DigitalProduct\Model\ResourceModel\OperatorIcon as OperatorIconResource;
use SM\DigitalProduct\Model\ResourceModel\OperatorIcon\Collection as OperatorIconCollection;
use SM\DigitalProduct\Model\ResourceModel\OperatorIcon\CollectionFactory as OperatorIconCollectionFactory;
use Trans\DigitalProduct\Api\Data\DigitalProductOperatorListInterface;
use Trans\DigitalProduct\Model\ResourceModel\DigitalProductOperatorList\Collection as OperatorCollection;
use Trans\DigitalProduct\Model\ResourceModel\DigitalProductOperatorList\CollectionFactory as OperatorCollectionFactory;

/**
 * Class OperatorIconRepository
 * @package SM\DigitalProduct\Model
 */
class OperatorIconRepository implements OperatorIconRepositoryInterface
{
    /**
     * @var JoinProcessorInterface
     */
    protected $extensionAttributesJoinProcessor;

    /**
     * @var OperatorIconCollectionFactory
     */
    protected $operatorIconCollectionFactory;

    /**
     * @var OperatorIconSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var OperatorIconFactory
     */
    protected $operatorIconFactory;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var OperatorIconResource
     */
    protected $resource;

    /**
     * @var ExtensibleDataObjectConverter
     */
    protected $extensibleDataObjectConverter;

    /**
     * @var Image
     */
    protected $imageHelper;

    /**
     * @var OperatorCollectionFactory
     */
    protected $operatorCollectionFactory;

    /**
     * @var Emulation
     */
    protected $emulation;

    /**
     * @param OperatorIconResource $resource
     * @param OperatorIconFactory $operatorIconFactory
     * @param OperatorIconCollectionFactory $operatorIconCollectionFactory
     * @param OperatorIconSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     * @param Image $imageHelper
     * @param OperatorCollectionFactory $operatorCollectionFactory
     * @param Emulation $emulation
     */
    public function __construct(
        OperatorIconResource $resource,
        OperatorIconFactory $operatorIconFactory,
        OperatorIconCollectionFactory $operatorIconCollectionFactory,
        OperatorIconSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter,
        Image $imageHelper,
        OperatorCollectionFactory $operatorCollectionFactory,
        Emulation $emulation
    ) {
        $this->emulation = $emulation;
        $this->operatorCollectionFactory = $operatorCollectionFactory;
        $this->imageHelper = $imageHelper;
        $this->resource = $resource;
        $this->operatorIconFactory = $operatorIconFactory;
        $this->operatorIconCollectionFactory = $operatorIconCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
    }

    /**
     * {@inheritdoc}
     * @throws CouldNotSaveException
     * @throws DuplicateException
     */
    public function save(
        OperatorIconInterface $operatorIcon
    ) {
        /** @var OperatorIconCollection $operatorIconCollection */
        $operatorIconCollection = $this->operatorIconCollectionFactory->create();
        $operatorIconCollection->addFieldToFilter(
            OperatorIconInterface::BRAND_ID,
            $operatorIcon->getBrandId()
        );
        if (!is_null($operatorIcon->getOperatorIconId())) {
            $operatorIconCollection->addFieldToFilter(
                OperatorIconInterface::OPERATOR_ICON_ID,
                ["neq" => $operatorIcon->getOperatorIconId()]
            );
        }
        if ($operatorIconCollection->count()) {
            throw new DuplicateException(__("Operator Icon Data for this service is existed."));
        }

        /** @var OperatorIconInterface $operatorIconData */
        $operatorIconData = $this->extensibleDataObjectConverter->toNestedArray(
            $operatorIcon,
            [],
            OperatorIconInterface::class
        );

        /** @var OperatorIconModel $operatorIconModel */
        $operatorIconModel = $this->operatorIconFactory->create()->setData($operatorIconData);

        try {
            $this->resource->save($operatorIconModel);
        } catch (Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the operatorIcon: %1',
                $exception->getMessage()
            ));
        }
        return $operatorIconModel;
    }

    /**
     * {@inheritdoc}
     * @throws NoSuchEntityException
     */
    public function get($operatorIconId)
    {
        /** @var OperatorIconModel $operatorIcon */
        $operatorIcon = $this->operatorIconFactory->create();
        $this->resource->load($operatorIcon, $operatorIconId);
        if (!$operatorIcon->getId()) {
            throw new NoSuchEntityException(__('operator_icon with id "%1" does not exist.', $operatorIconId));
        }
        return $operatorIcon;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        SearchCriteriaInterface $criteria
    ) {
        /** @var OperatorIconCollection $collection */
        $collection = $this->operatorIconCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process(
            $collection,
            OperatorIconInterface::class
        );

        $this->collectionProcessor->process($criteria, $collection);

        /** @var OperatorIconSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $items = [];
        /** @var OperatorIconModel $model */
        foreach ($collection as $model) {
            $items[] = $model;
        }

        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     * @throws CouldNotDeleteException
     */
    public function delete(
        OperatorIconInterface $operatorIcon
    ) {
        try {
            /** @var OperatorIconModel $operatorIconModel */
            $operatorIconModel = $this->operatorIconFactory->create();
            $this->resource->load($operatorIconModel, $operatorIcon->getOperatorIconId());
            $this->resource->delete($operatorIconModel);
        } catch (Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the operator_icon: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById($operatorIconId)
    {
        return $this->delete($this->get($operatorIconId));
    }
}
