<?php
/**
 * Class ContactRepository
 * @package SM\Help\Model
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

declare(strict_types=1);

namespace SM\Help\Model;

use Magento\Framework\Api\Data\ImageContentInterface;
use Magento\Framework\Api\ImageContent;
use Magento\Framework\Exception\LocalizedException;
use SM\FileManagement\Api\UploadImageInterface;
use Magento\Framework\File\Mime;
use SM\StoreLocator\Api\Data\Response\StoreSearchResultsInterface;
use SM\StoreLocator\Api\Data\StoreInterface;
use SM\StoreLocator\Api\Data\StoreInterfaceFactory;
use SM\StoreLocator\Model\Store\Converter;
use SM\StoreLocator\Model\Store\Location;
use SM\StoreLocator\Model\Store\ResourceModel\Location\Collection as LocationCollection;
use SM\StoreLocator\Model\Store\ResourceModel\Location\CollectionFactory as LocationCollectionFactory;

class ContactRepository implements \SM\Help\Api\ContactRepositoryInterface
{
    const IMAGE_PATH = "sm/help/contactus/uploads";
    const STATUS = 'status';

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    private $eventManager;

    /**
     * @var \SM\Help\Helper\Email
     */
    private $emailHelper;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var UploadImageInterface
     */
    protected $uploadImage;

    /**
     * @var ImageContentInterface
     */
    protected $imageContent;

    /**
     * @var Mime
     */
    protected $mime;

    /**
     * @var LocationCollectionFactory
     */
    private $locationCollectionFactory;

    /**
     * @var StoreSearchResultsInterface
     */
    private $storeSearchResults;

    /**
     * @var Converter
     */
    private $converter;

    /**
     * @var StoreInterfaceFactory
     */
    protected $storeInterfaceFactory;

    /**
     * ContactRepository constructor.
     * @param StoreInterfaceFactory $storeInterfaceFactory
     * @param Converter $converter
     * @param LocationCollectionFactory $locationCollectionFactory
     * @param StoreSearchResultsInterface $storeSearchResults
     * @param Mime $mime
     * @param ImageContentInterface $imageContent
     * @param UploadImageInterface $uploadImage
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \SM\Help\Helper\Email $emailHelper
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        StoreInterfaceFactory $storeInterfaceFactory,
        Converter $converter,
        LocationCollectionFactory $locationCollectionFactory,
        StoreSearchResultsInterface $storeSearchResults,
        Mime $mime,
        ImageContentInterface $imageContent,
        UploadImageInterface $uploadImage,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \SM\Help\Helper\Email $emailHelper,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->storeInterfaceFactory = $storeInterfaceFactory;
        $this->converter = $converter;
        $this->storeSearchResults = $storeSearchResults;
        $this->locationCollectionFactory = $locationCollectionFactory;
        $this->mime = $mime;
        $this->imageContent = $imageContent;
        $this->uploadImage = $uploadImage;
        $this->eventManager = $eventManager;
        $this->emailHelper = $emailHelper;
        $this->storeManager = $storeManager;
        $this->customerRepository = $customerRepository;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     * @throws \Exception
     */
    public function submit($customerId, $data, $images)
    {
        try {
            $customer = $this->customerRepository->getById($customerId);
            if ($data['help_messages'] && $customer->getEmail()) {
                $this->emailHelper->sendEmail($this->storeManager->getStore()->getId(), $customer->getEmail());
            }

            if (isset($images)) {
                $data['images'] = $this->uploadFile($images);
            }

            $this->eventManager->dispatch(
                'trans_help_contact_submit_after',
                ['data' => $data, 'customer' => $customer]
            );
        } catch (\Exception $e) {
            $this->logger->error(get_class($this) . __($e->getMessage()));
            return false;
        }

        return true;
    }

    /**
     * @return StoreInterface[]
     */
    public function getListStore()
    {
        /** @var LocationCollection $collection */
        $collection = $this->locationCollectionFactory->create();
        $collection->addFieldToFilter(self::STATUS, '1');
        return $this->convertItems($collection->getItems());
    }

    /**
     * @param Location[] $locations
     * @return StoreInterface[]
     */
    public function convertItems(array $locations): array
    {
        $stores = [];
        foreach ($locations as $location) {
            $stores[] = $this->convert($location);
        }
        return $stores;
    }

    /**
     * @param Location $location
     * @return StoreInterface
     * @codeCoverageIgnore
     */
    protected function convert(Location $location): StoreInterface
    {
        /** @var StoreInterface $store */
        $store = $this->storeInterfaceFactory->create();
        $store->setId($location->getId());
        $store->setName($location->getData(StoreInterface::NAME));
        $store->setStoreCode($location->getData(StoreInterface::STORE_CODE));
        $store->setIsActive($location->getData(self::STATUS));
        return $store;
    }

    /**
     * @param $images
     * @return array
     */
    private function uploadFile($images)
    {
        try {
            $path = [];
            foreach ($images as $image) {
                $path[] = $this->uploadImage->uploadImage($image, "", self::IMAGE_PATH);
            }
            return $path;
        } catch (LocalizedException $e) {
        }
    }

    /**
     * @param ImageContent $imageContent
     * @return bool|string
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function uploadImage(ImageContent $imageContent)
    {
        return $this->uploadImage->uploadImage($imageContent, "", self::IMAGE_PATH);
    }
}
