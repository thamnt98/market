<?php
declare(strict_types=1);

namespace SM\DigitalProduct\Model;

use Exception;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\DB\Adapter\DuplicateException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\BlockFactory;
use Magento\Store\Model\StoreManagerInterface;
use SM\DigitalProduct\Api\CategoryRepositoryInterface;
use SM\DigitalProduct\Api\Data\CategoryContentInterface;
use SM\DigitalProduct\Api\Data\CategoryContentInterfaceFactory;
use SM\DigitalProduct\Api\Data\CategoryInterface;
use SM\DigitalProduct\Api\Data\CategoryInterfaceFactory;
use SM\DigitalProduct\Api\Data\SubCategoryDataInterface;
use SM\DigitalProduct\Api\Data\SubCategoryDataInterfaceFactory;
use SM\DigitalProduct\Block\Index\HowToBuy;
use SM\DigitalProduct\Helper\Category\Data;
use SM\DigitalProduct\Helper\Config;
use SM\DigitalProduct\Model\ResourceModel\Category as CategoryResource;
use SM\DigitalProduct\Model\ResourceModel\Category\Collection as CategoryCollection;
use SM\DigitalProduct\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use SM\DigitalProduct\Model\ResourceModel\CategoryContent as CategoryContentResource;
use SM\DigitalProduct\Model\ResourceModel\CategoryContent\Collection as CategoryContentCollection;
use SM\DigitalProduct\Model\ResourceModel\CategoryContent\CollectionFactory as CategoryContentCollectionFactory;

/**
 * Class CategoryRepository
 * @package SM\DigitalProduct\Model
 */
class CategoryRepository implements CategoryRepositoryInterface
{
    const TOPUP = "topup";
    const MOBILE_POSTPAID = "mobilepostpaid";
    const MOBILE_PACKAGE = "mobilepackage";
    const ELECTRICITY = "electricity";
    const BPJS = "bpjs";
    const PDAM_WATER = "pdamwater";
    const TELKOM = "telkom";

    const HOW_TO_BUY_TEMPLATE = [
        Data::TOP_UP_VALUE => "SM_DigitalProduct::topup/how_to_buy.phtml",
        Data::MOBILE_PACKAGE_INTERNET_VALUE => "SM_DigitalProduct::mobilepackage/how_to_buy.phtml",
        Data::MOBILE_PACKAGE_ROAMING_VALUE => "SM_DigitalProduct::mobilepackage/how_to_buy.phtml",
        Data::ELECTRICITY_BILL_VALUE => "SM_DigitalProduct::electricity/how_to_buy.phtml",
        Data::ELECTRICITY_TOKEN_VALUE => "SM_DigitalProduct::electricity/how_to_buy.phtml"
    ];

    const DEFAULT_TOOLTIP = [
        Data::TOP_UP_VALUE => "Your mobile number or the number used for your modem.",
        Data::MOBILE_PACKAGE_INTERNET_VALUE => "Your mobile number or the number used for your modem.",
        Data::ELECTRICITY_BILL_VALUE => "The number printed on your monthly bill or electricity meter barcode.",
        Data::ELECTRICITY_TOKEN_VALUE => "The number printed on your customer card."
    ];

    const MOBILE_PACKAGE_INTERNET_TITLE = "Internet";
    const MOBILE_PACKAGE_ROAMING_TITLE = "Roaming";
    const ELECTRICITY_TOKEN_TITLE = "Electricity Token";
    const ELECTRICITY_BILL_TITLE = "Electricity Bill";

    /**
     * @var JoinProcessorInterface
     */
    protected $extensionAttributesJoinProcessor;

    /**
     * @var CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var CategoryCollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var CategoryResource
     */
    protected $categoryResource;

    /**
     * @var ExtensibleDataObjectConverter
     */
    protected $extensibleDataObjectConverter;

    /**
     * @var CategoryContentCollectionFactory
     */
    protected $categoryContentCollectionFactory;
    /**
     * @var Data
     */
    protected $typeHelper;
    /**
     * @var Config
     */
    protected $configHelper;
    /**
     * @var CategoryInterfaceFactory
     */
    protected $categoryDataFactory;

    /**
     * @var BlockFactory
     */
    protected $blockFactory;

    /**
     * @var CategoryContentInterfaceFactory
     */
    protected $contentDataFactory;

    /**
     * @var CategoryContentResource
     */
    protected $categoryContentResource;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var SubCategoryDataInterfaceFactory
     */
    protected $subCategoryDataFactory;

    /**
     * @var array
     */
    private $categoryContent;

    /**
     * @var array
     */
    private $activeCategories;

    /**
     * @param CategoryResource $resource
     * @param CategoryFactory $categoryFactory
     * @param CategoryCollectionFactory $categoryCollectionFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     * @param CategoryContentCollectionFactory $categoryContentCollectionFactory
     * @param Data $typeHelper
     * @param Config $configHelper
     * @param CategoryInterfaceFactory $categoryDataFactory
     * @param BlockFactory $blockFactory
     * @param CategoryContentInterfaceFactory $contentDataFactory
     * @param CategoryContentResource $categoryContentResource
     * @param StoreManagerInterface $storeManager
     * @param SubCategoryDataInterfaceFactory $subCategoryDataFactory
     */
    public function __construct(
        CategoryResource $resource,
        CategoryFactory $categoryFactory,
        CategoryCollectionFactory $categoryCollectionFactory,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter,
        CategoryContentCollectionFactory $categoryContentCollectionFactory,
        Data $typeHelper,
        Config $configHelper,
        CategoryInterfaceFactory $categoryDataFactory,
        BlockFactory $blockFactory,
        CategoryContentInterfaceFactory $contentDataFactory,
        CategoryContentResource $categoryContentResource,
        StoreManagerInterface $storeManager,
        SubCategoryDataInterfaceFactory $subCategoryDataFactory
    ) {
        $this->subCategoryDataFactory = $subCategoryDataFactory;
        $this->storeManager = $storeManager;
        $this->categoryContentResource = $categoryContentResource;
        $this->contentDataFactory = $contentDataFactory;
        $this->blockFactory = $blockFactory;
        $this->categoryDataFactory = $categoryDataFactory;
        $this->configHelper= $configHelper;
        $this->typeHelper = $typeHelper;
        $this->categoryContentCollectionFactory = $categoryContentCollectionFactory;
        $this->categoryResource = $resource;
        $this->categoryFactory = $categoryFactory;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
    }

    /**
     * @param Category $categoryModel
     * @return Category
     * @throws CouldNotSaveException
     * @throws DuplicateException
     */
    public function save($categoryModel)
    {
        /** @var CategoryCollection $categoryCollection */
        $categoryCollection = $this->categoryCollectionFactory->create();
        $categoryCollection->addFieldToFilter(
            CategoryInterface::TYPE,
            $categoryModel->getType()
        );
        if (!is_null(($categoryModel->getData(CategoryInterface::CATEGORY_ID)))) {
            $categoryCollection->addFieldToFilter(
                CategoryInterface::CATEGORY_ID,
                ["neq" =>$categoryModel->getData(CategoryInterface::CATEGORY_ID)]
            );
        }

        if ($categoryCollection->count()) {
            throw new DuplicateException(__("Category with this type is already existed"));
        }

        try {
            $this->categoryResource->save($categoryModel);
            $categoryContentModel = $this->getContentByStore(
                $categoryModel->getId(),
                $categoryModel->getData(CategoryContentInterface::STORE_ID)
            );

            if (!$categoryContentModel->getId()) {
                $categoryModel->setData(CategoryContentInterface::CATEGORY_ID, $categoryModel->getId());
            } else {
                $categoryModel->setData(CategoryContentInterface::CATEGORY_STORE_ID, $categoryContentModel->getId());
            }

            $categoryContentModel->setData($categoryModel->getData());
            $this->categoryContentResource->save($categoryContentModel);
        } catch (Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the category: %1',
                $exception->getMessage()
            ));
        }
        return $categoryModel;
    }

    /**
     * @param int $categoryId
     * @param int $storeId
     * @return CategoryContent
     */
    public function getContentByStore($categoryId, $storeId)
    {
        /** @var CategoryContentCollection $categoryContentCollection */
        $categoryContentCollection = $this->categoryContentCollectionFactory->create()
            ->addFieldToFilter(CategoryContentInterface::CATEGORY_ID, $categoryId)
            ->addFieldToFilter(CategoryContentInterface::STORE_ID, $storeId);
        /** @var CategoryContent $categoryContentModel */
        $categoryContentModel = $categoryContentCollection->getFirstItem();
        return $categoryContentModel;
    }

    /**
     * @param int $categoryId
     * @param int $storeId
     * @return CategoryInterface|Category
     * @throws NoSuchEntityException
     */
    public function get($categoryId, $storeId = 0)
    {
        /** @var Category $category */
        $category = $this->categoryFactory->create();
        $this->categoryResource->load($category, $categoryId);

        if (!$category->getId()) {
            throw new NoSuchEntityException(__('category with id "%1" does not exist.', $categoryId));
        }

        return $category;
    }

    /**
     * {@inheritdoc}
     */
    public function getList()
    {
        $items = [];
        if ($this->configHelper->isActiveTopUp()) {
            $this->activeCategories[] = self::TOPUP;
            $items[] = $this->getCategory(
                $this->configHelper->getTopUpThumbnail(),
                $this->configHelper->getTopUpTitle(),
                self::TOPUP
            );
        }
        if ($this->configHelper->isActiveMobilePackage()) {
            $this->activeCategories[] = Data::MOBILE_PACKAGE_ROAMING_VALUE;
            $this->activeCategories[] = Data::MOBILE_PACKAGE_INTERNET_VALUE;
            $items[] = $this->getCategory(
                $this->configHelper->getMobilePackageThumbnail(),
                $this->configHelper->getMobilePackageTitle(),
                self::MOBILE_PACKAGE
            );

        }
        if ($this->configHelper->isActiveElectricity()) {
            $this->activeCategories[] = Data::ELECTRICITY_TOKEN_VALUE;
            $this->activeCategories[] = Data::ELECTRICITY_BILL_VALUE;
            $items[] = $this->getCategory(
                $this->configHelper->getElectricityThumbnail(),
                $this->configHelper->getElectricityTitle(),
                self::ELECTRICITY
            );
        }
        return $items;
    }

    /**
     * @param CategoryContentCollection $categoryContentCollection
     * @param int $storeId
     * @return CategoryContentInterface[]
     */
    private function generateContent($categoryContentCollection, $storeId)
    {
        $content = [];

        /** @var CategoryContent $categoryContent */
        foreach ($categoryContentCollection as $categoryContent) {
            $typeId = $categoryContent->getData(CategoryInterface::TYPE);
            if (isset($content[$typeId])) {
                if ($storeId != 0) {
                    if ($categoryContent->getData(CategoryContentInterface::STORE_ID) == $storeId) {
                        $content[$typeId] = $categoryContent;
                        continue;
                    }
                }
            }

            $categoryContent->setHowToBuy($this->getHowToBuyBlockContent($typeId, true));
            $categoryContent->setCategoryName($this->getCategoryTitle()[$typeId]);
            $categoryContent->setType($typeId);

            $content[$typeId] = $categoryContent;
            $this->categoryContent[$typeId] = $categoryContent;
        }

        return $content;
    }

    /**
     * @param Category $category
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete($category)
    {
        try {
            /** @var Category $categoryModel */
            $categoryModel = $this->categoryFactory->create();
            $this->categoryResource->load($categoryModel, $category->getId());
            $this->categoryResource->delete($categoryModel);
        } catch (Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the category: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($categoryId)
    {
        return $this->delete($this->get($categoryId));
    }

    /**
     * @param string $digitalCatCode
     * @param bool $all
     * @return mixed|CategoryContentInterface
     */
    public function getCategoryContent($digitalCatCode, $all = false)
    {
        /** @var CategoryContentCollection $categoryContentCollection */
        $categoryContentCollection = $this->categoryContentCollectionFactory->create();
        $categoryContentCollection->selectWithCategory();
        try {
            $storeId = $this->storeManager->getStore()->getId();
        } catch (NoSuchEntityException $e) {
            $storeId = 0;
        }
        $storeIds = ($storeId == 0) ? [0] : [0, $storeId];
        $categoryContentCollection->addFieldToFilter("main_table.store_id", ["in" => $storeIds]);

        if (!$all) {
            if ($digitalCatCode == CategoryRepository::MOBILE_PACKAGE
                || $digitalCatCode == Data::MOBILE_PACKAGE_ROAMING_VALUE) {
                $digitalCatCode = Data::MOBILE_PACKAGE_INTERNET_VALUE;
            }
            $categoryContentCollection->addFieldToFilter("category.type", $digitalCatCode);
        } else {
            $categoryContentCollection->addFieldToFilter("category.type", ['in' => $this->activeCategories]);
        }

        /** @var CategoryContentInterface[] $content */
        $content = $this->generateContent($categoryContentCollection, $storeId);
        return $this->prepareContent($content, $digitalCatCode);
    }

    /**
     * @param $digitalCatCode
     * @return CategoryContentInterface
     */
    private function createDefaultContent($digitalCatCode)
    {
        /** @var CategoryContentInterface $contentData */
        $contentData = $this->contentDataFactory->create();
        return $contentData->setTooltip(self::DEFAULT_TOOLTIP[$digitalCatCode]);
    }

    /**
     * @param string $thumbnail
     * @param string $title
     * @param string $code
     * @return CategoryInterface
     * @throws NoSuchEntityException
     */
    private function getCategory($thumbnail, $title, $code)
    {
        /** @var CategoryInterface $categoryData */
        $categoryData = $this->categoryDataFactory->create();
        $categoryData->setThumbnail($thumbnail);
        $categoryData->setType($code);
        $categoryData->setCategoryName($title);
        $this->getCategoryContent(Data::TOP_UP_VALUE, true);
        switch ($code) {
            case self::ELECTRICITY:
                $categoryData->setSubCategories([
                    $this->subCategoryDataFactory
                        ->create()
                        ->setType(Data::ELECTRICITY_BILL_VALUE)
                        ->setCategoryName($this->getCategoryTitle()[Data::ELECTRICITY_BILL_VALUE])
                        ->setTooltip($this->categoryContent[Data::ELECTRICITY_BILL_VALUE]->getTooltip())
                        ->setInfo($this->categoryContent[Data::ELECTRICITY_BILL_VALUE]->getInfo())
                        ->setHowToBuy($this->categoryContent[Data::ELECTRICITY_BILL_VALUE]->getHowToBuy()),

                $this->subCategoryDataFactory
                        ->create()
                        ->setType(Data::ELECTRICITY_TOKEN_VALUE)
                        ->setCategoryName($this->getCategoryTitle()[Data::ELECTRICITY_TOKEN_VALUE])
                        ->setTooltip($this->categoryContent[Data::ELECTRICITY_TOKEN_VALUE]->getTooltip())
                        ->setInfo($this->categoryContent[Data::ELECTRICITY_TOKEN_VALUE]->getInfo())
                        ->setHowToBuy($this->categoryContent[Data::ELECTRICITY_TOKEN_VALUE]->getHowToBuy())
                ]);
                break;
            case self::MOBILE_PACKAGE:
                $howToBuy = $this->categoryContent[Data::MOBILE_PACKAGE_INTERNET_VALUE]->getHowToBuy(Data::MOBILE_PACKAGE_INTERNET_VALUE, true);
                $categoryData->setSubCategories([
                    $this->subCategoryDataFactory
                        ->create()
                        ->setType(Data::MOBILE_PACKAGE_INTERNET_VALUE)
                        ->setCategoryName($this->getCategoryTitle()[Data::MOBILE_PACKAGE_INTERNET_VALUE])
                        ->setTooltip($this->categoryContent[Data::MOBILE_PACKAGE_INTERNET_VALUE]->getTooltip())
                        ->setInfo($this->categoryContent[Data::MOBILE_PACKAGE_INTERNET_VALUE]->getInfo())
                        ->setHowToBuy($howToBuy),
                    $this->subCategoryDataFactory
                        ->create()
                        ->setType(Data::MOBILE_PACKAGE_ROAMING_VALUE)
                        ->setCategoryName($this->getCategoryTitle()[Data::MOBILE_PACKAGE_ROAMING_VALUE])
                        ->setTooltip($this->categoryContent[Data::MOBILE_PACKAGE_ROAMING_VALUE]->getTooltip())
                        ->setInfo($this->categoryContent[Data::MOBILE_PACKAGE_ROAMING_VALUE]->getInfo())
                        ->setHowToBuy($howToBuy)
                ]);
                $categoryData->setHowToBuy($howToBuy);
                break;
            case self::TOPUP:
                $howToBuy = $this->categoryContent[Data::TOP_UP_VALUE]->getHowToBuy();
                $categoryData->setSubCategories([
                    $this->subCategoryDataFactory
                        ->create()
                        ->setType(Data::TOP_UP_VALUE)
                        ->setCategoryName($this->getCategoryTitle()[Data::TOP_UP_VALUE])
                        ->setTooltip($this->categoryContent[Data::TOP_UP_VALUE]->getTooltip())
                        ->setInfo($this->categoryContent[Data::TOP_UP_VALUE]->getInfo())
                        ->setHowToBuy($howToBuy),
                ]);
                $categoryData->setHowToBuy($howToBuy);
                break;
        }

        return $categoryData;
    }

    /**
     * @param bool $isMobile
     * @return array
     */
    private function getHowToBuyBlock($isMobile = false)
    {
        return [
            Data::TOP_UP_VALUE => $this->configHelper->getTopUpHowToBuyBlockIdentifier($isMobile),
            Data::MOBILE_PACKAGE_INTERNET_VALUE => $this->configHelper->getMobilePackageHowToBuyBlockIdentifier($isMobile),
            Data::MOBILE_PACKAGE_ROAMING_VALUE => $this->configHelper->getMobilePackageHowToBuyBlockIdentifier($isMobile),
            Data::ELECTRICITY_TOKEN_VALUE => $this->configHelper->getElectricityHowToBuyBlockIdentifier($isMobile),
            Data::ELECTRICITY_BILL_VALUE => $this->configHelper->getElectricityBillHowToBuyBlockIdentifier($isMobile)

        ];
    }

    /**
     * @param $digitalCatId
     * @param bool $isMobile
     * @return string
     */
    private function getHowToBuyBlockContent($digitalCatId, $isMobile = false)
    {
        /** @var HowToBuy $howToBuyBlock */
        $howToBuyBlock = $this->blockFactory->createBlock(HowToBuy::class);
        $howToBuyBlock->setTemplate(self::HOW_TO_BUY_TEMPLATE[$digitalCatId]);
        $howToBuyBlock->setHowToBuyBlockIdentifier($this->getHowToBuyBlock($isMobile)[$digitalCatId]);
        $html = $howToBuyBlock->toHtml();
        return $html;
    }

    /**
     * @return array
     */
    private function getCategoryTitle()
    {
        return [
            self::TOPUP => $this->configHelper->getTopUpTitle(),
            self::MOBILE_PACKAGE => $this->configHelper->getMobilePackageTitle(),
            self::ELECTRICITY => $this->configHelper->getElectricityTitle(),
            Data::ELECTRICITY_BILL_VALUE => __(self::ELECTRICITY_BILL_TITLE),
            Data::ELECTRICITY_TOKEN_VALUE => __(self::ELECTRICITY_TOKEN_TITLE),
            Data::MOBILE_PACKAGE_INTERNET_VALUE => __(self::MOBILE_PACKAGE_INTERNET_TITLE),
            Data::MOBILE_PACKAGE_ROAMING_VALUE => __(self::MOBILE_PACKAGE_ROAMING_TITLE)
        ];
    }

    /**
     * @param $content
     * @param $digitalCatId
     * @return CategoryContentInterface
     * @throws NoSuchEntityException
     */
    private function prepareContent($content, $digitalCatId)
    {
        if ($digitalCatId == self::MOBILE_PACKAGE) {
            $digitalCatId = Data::MOBILE_PACKAGE_INTERNET_VALUE;
        }

        if (isset($content[$digitalCatId])) {
            $content = $content[$digitalCatId];
        } else {
            $content = $this->createDefaultContent($digitalCatId);
        }
        if (is_null($content->getTooltip())) {
            $content->setTooltip(__(self::DEFAULT_TOOLTIP[$digitalCatId]));
        }
        $content->setHowToBuy($this->getHowToBuyBlockContent($digitalCatId, true));
        $content->setCategoryName($this->getCategoryTitle()[$digitalCatId]);
        $content->setType($digitalCatId);
        $content->setOperatorImage($this->configHelper->getMediaUrl($this->configHelper->getElectricityOperatorIcon()));
        return $content;
    }
}
