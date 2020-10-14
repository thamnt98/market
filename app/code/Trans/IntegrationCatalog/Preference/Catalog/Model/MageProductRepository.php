<?php
/**
 * @category Trans
 * @package  Trans_IntegrationCatalog
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software Licenuse Trans\IntegrationCatalog\Model\ResourceModel\ConfigurableProductAttribute as ResourceModel;se (OSL 3.0)
 *
 *
 * @author   Anan Fauzi <anan.fauzi@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationCatalog\Preference\Catalog\Model;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductFactory;

use Magento\Catalog\Api\CategoryLinkManagementInterface;
use Magento\Catalog\Api\Data\ProductExtension;
use Magento\Catalog\Model\Product\Gallery\MimeTypeExtensionMap;
use Magento\Catalog\Model\ProductRepository\MediaGalleryProcessor;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Eav\Model\Entity\Attribute\Exception as AttributeException;
use Magento\Framework\Api\Data\ImageContentInterfaceFactory;
use Magento\Framework\Api\ImageContentValidatorInterface;
use Magento\Framework\Api\ImageProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\DB\Adapter\ConnectionException;
use Magento\Framework\DB\Adapter\DeadlockException;
use Magento\Framework\DB\Adapter\LockWaitException;
use Magento\Framework\EntityManager\Operation\Read\ReadExtensions;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Exception\TemporaryState\CouldNotSaveException as TemporaryCouldNotSaveException;
use Magento\Framework\Exception\ValidatorException;

/**
 * class MageProductRepository
 */
class MageProductRepository extends \Magento\Catalog\Model\ProductRepository
{
	/**
	 * logger
	 */
	protected $logger;

	/**
     * ProductRepository constructor.
     * @param ProductFactory $productFactory
     * @param \Magento\Catalog\Controller\Adminhtml\Product\Initialization\Helper $initializationHelper
     * @param \Magento\Catalog\Api\Data\ProductSearchResultsInterfaceFactory $searchResultsFactory
     * @param ResourceModel\Product\CollectionFactory $collectionFactory
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Catalog\Api\ProductAttributeRepositoryInterface $attributeRepository
     * @param ResourceModel\Product $resourceModel
     * @param Product\Initialization\Helper\ProductLinks $linkInitializer
     * @param Product\LinkTypeProvider $linkTypeProvider
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Api\FilterBuilder $filterBuilder
     * @param \Magento\Catalog\Api\ProductAttributeRepositoryInterface $metadataServiceInterface
     * @param \Magento\Framework\Api\ExtensibleDataObjectConverter $extensibleDataObjectConverter
     * @param Product\Option\Converter $optionConverter
     * @param \Magento\Framework\Filesystem $fileSystem
     * @param ImageContentValidatorInterface $contentValidator
     * @param ImageContentInterfaceFactory $contentFactory
     * @param MimeTypeExtensionMap $mimeTypeExtensionMap
     * @param ImageProcessorInterface $imageProcessor
     * @param \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param CollectionProcessorInterface $collectionProcessor [optional]
     * @param \Magento\Framework\Serialize\Serializer\Json|null $serializer
     * @param int $cacheLimit [optional]
     * @param ReadExtensions $readExtensions
     * @param CategoryLinkManagementInterface $linkManagement
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(
        ProductFactory $productFactory,
        \Magento\Catalog\Controller\Adminhtml\Product\Initialization\Helper $initializationHelper,
        \Magento\Catalog\Api\Data\ProductSearchResultsInterfaceFactory $searchResultsFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Catalog\Api\ProductAttributeRepositoryInterface $attributeRepository,
        \Magento\Catalog\Model\ResourceModel\Product $resourceModel,
        \Magento\Catalog\Model\Product\Initialization\Helper\ProductLinks $linkInitializer,
        \Magento\Catalog\Model\Product\LinkTypeProvider $linkTypeProvider,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        \Magento\Catalog\Api\ProductAttributeRepositoryInterface $metadataServiceInterface,
        \Magento\Framework\Api\ExtensibleDataObjectConverter $extensibleDataObjectConverter,
        \Magento\Catalog\Model\Product\Option\Converter $optionConverter,
        \Magento\Framework\Filesystem $fileSystem,
        ImageContentValidatorInterface $contentValidator,
        ImageContentInterfaceFactory $contentFactory,
        MimeTypeExtensionMap $mimeTypeExtensionMap,
        ImageProcessorInterface $imageProcessor,
        \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface $extensionAttributesJoinProcessor,
        CollectionProcessorInterface $collectionProcessor = null,
        \Magento\Framework\Serialize\Serializer\Json $serializer = null,
        $cacheLimit = 1000,
        ReadExtensions $readExtensions = null,
        CategoryLinkManagementInterface $linkManagement = null
    ) {
    	parent::__construct(
    		$productFactory,
    		$initializationHelper,
    		$searchResultsFactory,
    		$collectionFactory,
    		$searchCriteriaBuilder,
    		$attributeRepository,
    		$resourceModel,
    		$linkInitializer,
    		$linkTypeProvider,
    		$storeManager,
    		$filterBuilder,
    		$metadataServiceInterface,
    		$extensibleDataObjectConverter,
    		$optionConverter,
    		$fileSystem,
    		$contentValidator,
    		$contentFactory,
    		$mimeTypeExtensionMap,
    		$imageProcessor,
    		$extensionAttributesJoinProcessor,
    		$collectionProcessor,
    		$serializer,
    		$cacheLimit,
    		$readExtensions,
    		$linkManagement
    	);
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/integration_product.log');
        $this->logger = new \Zend\Log\Logger();
        $this->logger = $this->logger->addWriter($writer);
    }
	/**
     * Process product links, creating new links, updating and deleting existing links
     *
     * @param ProductInterface $product
     * @param \Magento\Catalog\Api\Data\ProductLinkInterface[] $newLinks
     * @return $this
     * @throws NoSuchEntityException
     */
    private function processLinks(ProductInterface $product, $newLinks)
    {
        if ($newLinks === null) {
            // If product links were not specified, don't do anything
            return $this;
        }

        // Clear all existing product links and then set the ones we want
        $linkTypes = $this->linkTypeProvider->getLinkTypes();
        foreach (array_keys($linkTypes) as $typeName) {
            $this->linkInitializer->initializeLinks($product, [$typeName => []]);
        }

        // Set each linktype info
        if (!empty($newLinks)) {
            $productLinks = [];
            foreach ($newLinks as $link) {
                $productLinks[$link->getLinkType()][] = $link;
            }

            foreach ($productLinks as $type => $linksByType) {
                $assignedSkuList = [];
                /** @var \Magento\Catalog\Api\Data\ProductLinkInterface $link */
                foreach ($linksByType as $link) {
                    $assignedSkuList[] = $link->getLinkedProductSku();
                }
                $linkedProductIds = $this->resourceModel->getProductsIdsBySkus($assignedSkuList);

                $linksToInitialize = [];
                foreach ($linksByType as $link) {
                    $linkDataArray = $this->extensibleDataObjectConverter
                        ->toNestedArray($link, [], \Magento\Catalog\Api\Data\ProductLinkInterface::class);
                    $linkedSku = $link->getLinkedProductSku();
                    if (!isset($linkedProductIds[$linkedSku])) {
                        throw new NoSuchEntityException(
                            __('The Product with the "%1" SKU doesn\'t exist.', $linkedSku)
                        );
                    }
                    $linkDataArray['product_id'] = $linkedProductIds[$linkedSku];
                    $linksToInitialize[$linkedProductIds[$linkedSku]] = $linkDataArray;
                }

                $this->linkInitializer->initializeLinks($product, [$type => $linksToInitialize]);
            }
        }

        $product->setProductLinks($newLinks);
        return $this;
    }

	/**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function save(ProductInterface $product, $saveOptions = false)
    {
        $assignToCategories = false;
        $tierPrices = $product->getData('tier_price');

        try {
            $existingProduct = $product->getId() ? $this->getById($product->getId()) : $this->get($product->getSku());

            $product->setData(
                $this->resourceModel->getLinkField(),
                $existingProduct->getData($this->resourceModel->getLinkField())
            );
            if (!$product->hasData(Product::STATUS)) {
                $product->setStatus($existingProduct->getStatus());
            }

            /** @var ProductExtension $extensionAttributes */
            $extensionAttributes = $product->getExtensionAttributes();
            if (empty($extensionAttributes->__toArray())) {
                $product->setExtensionAttributes($existingProduct->getExtensionAttributes());
                $assignToCategories = true;
            }
        } catch (NoSuchEntityException $e) {
            $existingProduct = null;
        }

        $productDataArray = $this->extensibleDataObjectConverter
            ->toNestedArray($product, [], ProductInterface::class);
        $productDataArray = array_replace($productDataArray, $product->getData());
        $ignoreLinksFlag = $product->getData('ignore_links_flag');
        $productLinks = null;
        if (!$ignoreLinksFlag && $ignoreLinksFlag !== null) {
            $productLinks = $product->getProductLinks();
        }
        $productDataArray['store_id'] = (int)$this->storeManager->getStore()->getId();
        $product = $this->initializeProductData($productDataArray, empty($existingProduct));

        $this->processLinks($product, $productLinks);
        if (isset($productDataArray['media_gallery'])) {
            $this->processMediaGallery($product, $productDataArray['media_gallery']['images']);
        }

        if (!$product->getOptionsReadonly()) {
            $product->setCanSaveCustomOptions(true);
        }

        $validationResult = $this->resourceModel->validate($product);
        if (true !== $validationResult) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(
                __('Invalid product data: %1', implode(',', $validationResult))
            );
        }

        if ($tierPrices !== null) {
            $product->setData('tier_price', $tierPrices);
        }

        $this->saveProduct($product);
        if ($assignToCategories === true && $product->getCategoryIds()) {
            $this->linkManagement->assignProductToCategories(
                $product->getSku(),
                $product->getCategoryIds()
            );
        }
        $this->removeProductFromLocalCacheBySku($product->getSku());
        $this->removeProductFromLocalCacheById($product->getId());

        return $this->get($product->getSku(), false, $product->getStoreId());
    }

    /**
     * Save product resource model.
     *
     * @param ProductInterface|Product $product
     * @throws TemporaryCouldNotSaveException
     * @throws InputException
     * @throws CouldNotSaveException
     * @throws LocalizedException
     */
    private function saveProduct($product): void
    {        
        $this->logger->info(get_class() . ' - ' . __FUNCTION__);

    	try {
            $this->removeProductFromLocalCacheBySku($product->getSku());
            $this->removeProductFromLocalCacheById($product->getId());
            $this->resourceModel->save($product);
        	$this->logger->info(__FUNCTION__ . ' - ' . 'SUCCESS');
        } catch (ConnectionException $exception) {
        	$this->logger->info('Error = ' . $exception->getMessage());
            throw new TemporaryCouldNotSaveException(
                __('Database connection error'),
                $exception,
                $exception->getCode()
            );
        } catch (DeadlockException $exception) {
        	$this->logger->info('Error = ' . $exception->getMessage());
            throw new TemporaryCouldNotSaveException(
                __('Database deadlock found when trying to get lock'),
                $exception,
                $exception->getCode()
            );
        } catch (LockWaitException $exception) {
        	$this->logger->info('Error = ' . $exception->getMessage());
            throw new TemporaryCouldNotSaveException(
                __('Database lock wait timeout exceeded'),
                $exception,
                $exception->getCode()
            );
        } catch (AttributeException $exception) {
        	$this->logger->info('Error = ' . $exception->getMessage());
            throw InputException::invalidFieldValue(
                $exception->getAttributeCode(),
                $product->getData($exception->getAttributeCode()),
                $exception
            );
        } catch (ValidatorException $e) {
        	$this->logger->info('Error = ' . $e->getMessage());
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (LocalizedException $e) {
        	$this->logger->info('Error = ' . $e->getMessage());
            throw $e;
        } catch (\Exception $e) {
        	$this->logger->info('Error = ' . $e->getMessage());
        	$this->logger->info('Product Data = ' . json_encode($product->getData()));

            throw new CouldNotSaveException(
                __('The product was unable to be saved. Please try again.'),
                $e
            );
        }

        $this->logger->info('END ' . get_class() . ' - ' . __FUNCTION__);
    }

    /**
     * Removes product in the local cache by sku.
     *
     * @param string $sku
     * @return void
     */
    private function removeProductFromLocalCacheBySku(string $sku): void
    {
        $preparedSku = $this->prepareSku($sku);
        unset($this->instances[$preparedSku]);
    }

    /**
     * Converts SKU to lower case and trims.
     *
     * @param string $sku
     * @return string
     */
    private function prepareSku(string $sku): string
    {
        return mb_strtolower(trim($sku));
    }

    /**
     * Removes product in the local cache by id.
     *
     * @param string|null $id
     * @return void
     */
    private function removeProductFromLocalCacheById(?string $id): void
    {
        unset($this->instancesById[$id]);
    }
}
